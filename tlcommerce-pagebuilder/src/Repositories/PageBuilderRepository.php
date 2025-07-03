<?php

namespace Plugin\TlPageBuilder\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Plugin\TlPageBuilder\Helpers\BuilderHelper;
use Plugin\TlPageBuilder\Models\PageBuilderSection;
use Plugin\TlPageBuilder\Services\PageBuilderService;
use Plugin\TlPageBuilder\Models\PageBuilderSectionLayout;
use Plugin\TlPageBuilder\Models\PageBuilderSectionProperties;
use Plugin\TlPageBuilder\Models\PageBuilderSectionLayoutWidget;
use Plugin\TlPageBuilder\Models\PageBuilderSectionLayoutWidgetProperties;
use Plugin\TlPageBuilder\Models\PageBuilderSectionLayoutWidgetTranslation;

class PageBuilderRepository
{
    public $active_theme;
    public function __construct(private PageBuilderService $builder_service)
    {}

    /**
     * Get Page sections
     * @param object $data
     * @return View
     */
    public function getPageSections($data)
    {
        $this->active_theme = getActiveTheme();

        $widgets = BuilderHelper::getPageBuilderWidgets();
        $sections = PageBuilderSection::with('layouts.layout_widgets.widget')
            ->where([
                'page_id' => $data['id'],
                'theme_id' => $this->active_theme->id
            ])
            ->orderBy('ordering')
            ->select(['id', 'status'])
            ->get();

        return view('plugin/tlcommerce-pagebuilder::page-builder.sections')->with(
            [
                'sections' => $sections,
                'data' => $data,
                'widgets' => $widgets
            ]
        );
    }

    /**
     * Create new page sections
     * @param object $request
     * @return JsonResponse
     */
    public function createNewPageSections($request)
    {
        try {
            $this->active_theme = getActiveTheme();

            DB::beginTransaction();
            $section = PageBuilderSection::create([
                'page_id' => $request['page_id'],
                'theme_id' => $this->active_theme->id,
                'ordering' => $request['order']
            ]);
            $layout_ids = $this->createSectionLayout($section->id, $request['layout']);
            DB::commit();
            $data = [
                'layout_ids' => $layout_ids,
                'section_id' => $section->id
            ];
            return BuilderHelper::jsonResponse(200, translate('New Section Created Successfully'), $data);
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('New Section Creating Failed'));
        }
    }

    /**
     * remove a page sections
     * @param object $request
     * @return JsonResponse
     */
    public function removePageSection($request)
    {
        try {
            DB::beginTransaction();
            PageBuilderSection::where('id', $request['id'])->delete();
            DB::commit();

            $this->builder_service->removeSectionFromCSS($request);
            return BuilderHelper::jsonResponse(200, translate('Section Removed Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Section Removing Failed'));
        }
    }


    /**
     * remove a page sections
     * @param object $request
     * @return JsonResponse
     */
    public function sortingPageSection($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['section'] as $key => $value) {
                PageBuilderSection::find($value)->update(['ordering' => $key + 1]);
            }
            DB::commit();
            return BuilderHelper::jsonResponse(200, translate('Section Sorted Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Section Sorting Failed'));
        }
    }


    /**
     * Get properties for sections
     * @param object $request
     * @return JsonResponse
     */
    public function getSectionProperties($request)
    {
        try {
            $section_properties = PageBuilderSectionProperties::where('section_id', $request->section_id)->first();
            $properties = $section_properties ? $section_properties->properties : null;

            $data = view('plugin/tlcommerce-pagebuilder::page-builder.section-properties', ['section_properties' => $properties])->render();

            return BuilderHelper::jsonResponse(200, 'Success', $data);
        } catch (\Exception $e) {
            return BuilderHelper::jsonResponse(500, translate('Section Properties Failed'));
        }
    }


    /**
     * update properties of sections/widgets
     * @param object $request
     * @return JsonResponse
     */
    public function updatePageSectionProperties($request)
    {
        try {
            DB::beginTransaction();
            $input = $request->except(['lang', 'type_key', 'section_id', 'layout_has_widget_id']);

            $section_properties = PageBuilderSectionProperties::firstOrNew(['section_id' => $request->section_id]);
            $section_properties->properties = xss_clean(json_encode($input));
            $section_properties->save();

            DB::commit();

            $this->builder_service->updateBuilderPageCssFile($request->except('lang'));

            return BuilderHelper::jsonResponse(200, translate('Section Properties Updated Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Section Properties Update Failed'));
        }
    }


    /**
     * Create new page sections
     * @param object $request
     * @return JsonResponse
     */
    public function createSectionLayout($section_id, $layout)
    {
        $array_layouts = explode('_', $layout);
        $layout_ids = [];

        foreach ($array_layouts as $key => $value) {
            $layout = PageBuilderSectionLayout::create([
                'section_id' => $section_id,
                'col_index' => $key + 1,
                'col_value' => $value
            ]);
            array_push($layout_ids, $layout->id);
        }

        return $layout_ids;
    }

    /**
     * add widget to section layouts
     * @param object $request
     * @return JsonResponse
     */
    public function addWidgetToSectionLayout($request)
    {
        try {
            DB::beginTransaction();
            $layout_widget = PageBuilderSectionLayoutWidget::create([
                'section_layout_id' => $request['section_layout_id'],
                'page_widget_id' => $request['widget_id'],
            ]);
            DB::commit();

            $data = [
                'id' => $layout_widget->id
            ];
            return BuilderHelper::jsonResponse(200, translate('Widget Added Successfully'), $data);
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Widget Adding Failed'));
        }
    }

    /**
     * remove widget from section layouts
     * @param object $request
     * @return JsonResponse
     */
    public function removeWidgetFromSectionLayout($request)
    {
        try {
            DB::beginTransaction();
            PageBuilderSectionLayoutWidget::find($request['layout_widget_id'])->delete();
            DB::commit();

            $this->builder_service->removeWidgetFromCSS($request);
            return BuilderHelper::jsonResponse(200, translate('Widget Removed Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Widget Removing Failed'));
        }
    }

    /**
     * update widgets position on layout
     * @param object $request
     * @return JsonResponse
     */
    public function updateWidgetPositionOnSections($request)
    {
        try {

            DB::beginTransaction();
            PageBuilderSectionLayoutWidget::where([
                'id'                => $request['layout_widget_id'],
                'section_layout_id' => $request['prev_layout_id'],
                'page_widget_id'    => $request['widget_id']
            ])->update(['section_layout_id' => $request['new_layout_id']]);
            DB::commit();

            $this->builder_service->updateWidgetCssOnLayoutChange($request->all());
            return BuilderHelper::jsonResponse(200, translate('Widget Position Updated Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Widget Position Updating Failed'));
        }
    }

    /**
     * update widget order on layouts
     * @param object $request
     * @return JsonResponse
     */
    public function orderWidgetOnLayout($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request['layout_widget_ids'] as $key => $value) {
                PageBuilderSectionLayoutWidget::find($value)->update(['serial' => $key + 1]);
            }
            DB::commit();
            return BuilderHelper::jsonResponse(200, translate('Widget Order Updated Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Widget Order Updating Failed'));
        }
    }

    /**
     * Get properties for widgets
     * @param object $request
     * @return JsonResponse
     */
    public function getWidgetProperties($request)
    {
        try {

            $widget_properties = PageBuilderSectionLayoutWidgetProperties::where('layout_has_widget_id', $request->layout_widget_id)->first();

            $properties = $widget_properties ? $widget_properties->propertiesTranslations($request->lang) : null;

            $widgets = BuilderHelper::$widget_list;

            $widget_form = "plugin/tlcommerce-pagebuilder::builders.builder-widget-forms." . $request->widget_name;

            if (in_array($request->widget_name, $widgets)) {

                if($request->widget_name == 'flash_deal' && !isActivePluging('flashdeal')){
                    return BuilderHelper::jsonResponse(500, translate('Flash Deal Plugin Is Not Active'));
                }

                if($request->widget_name == 'seller_list' && !isActivePluging('multivendor')){
                    return BuilderHelper::jsonResponse(500, translate('Multivendor Plugin Is Not Active'));
                }

                $data = '';
                if (view()->exists($widget_form)) {
                    $data = view($widget_form, ['widget_properties' => $properties, 'lang' => $request->lang])->render();
                }

                return BuilderHelper::jsonResponse(200, 'Success', $data);
            } else {
                return BuilderHelper::jsonResponse(500, translate('Widget Properties Failed'));
            }
        } catch (\Exception $e) {
            return BuilderHelper::jsonResponse(500, translate('Widget Properties Failed'));
        }
    }

    /**
     * update properties of sections/widgets
     * @param object $request
     * @return JsonResponse
     */
    public function updateLayoutWidgetProperties($request)
    {
        try {
            DB::beginTransaction();
            $lang = $request->lang;
            $input = $request->except(['lang', 'type_key', 'section_id', 'layout_has_widget_id']);

            if (!empty($lang) && $lang != getDefaultLang()) {

                $translated_keys = array_filter($input, function ($value, $key) {
                    return strpos($key, '_t_') !== false;
                }, ARRAY_FILTER_USE_BOTH);

                $layout_widget_properties = PageBuilderSectionLayoutWidgetProperties::firstOrCreate(['layout_has_widget_id' => $request->layout_has_widget_id]);
                $widget_translation = PageBuilderSectionLayoutWidgetTranslation::firstOrNew(['layout_widget_properties_id' => $layout_widget_properties->id, 'lang' => $lang]);
                $widget_translation->properties = xss_clean(json_encode($translated_keys));
                $widget_translation->save();
                DB::commit();
            } else {

                $layout_widget_properties = PageBuilderSectionLayoutWidgetProperties::firstOrNew(['layout_has_widget_id' => $request->layout_has_widget_id]);
                $layout_widget_properties->properties = xss_clean(json_encode($input));
                $layout_widget_properties->save();
                DB::commit();

                $this->builder_service->updateBuilderPageCssFile($request->except('lang'));
            }

            return BuilderHelper::jsonResponse(200, translate('Widget Properties Updated Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Widget Properties Update Failed'));
        }
    }

    /**
     * Save Slider
     * @param Request $request
     * @return Response
     */
    public function saveBannerSlide($request)
    {
        try {
            DB::beginTransaction();
            $layout_widget_properties = PageBuilderSectionLayoutWidgetProperties::firstOrNew(['layout_has_widget_id' => $request->layout_widget_id]);

            $sliders = isset($layout_widget_properties->properties) ? $layout_widget_properties->properties : [];

            $sliders[$request->key] = [
                'title' => $request->title,
                'url' => $request->url,
                'desktop_image' => [
                    'path' => getFilePath($request->desktop_image_id),
                    'id' => $request->desktop_image_id
                ],
                'mobile_image' => [
                    'path' => getFilePath($request->mobile_image_id),
                    'id' => $request->mobile_image_id
                ]
            ];

            $layout_widget_properties->properties = xss_clean(json_encode($sliders));
            $layout_widget_properties->save();

            DB::commit();

            $data = [
                'title' => $request->title,
                'details' => $sliders[$request->key]
            ];

            return BuilderHelper::jsonResponse(200, translate('Banner Slide Saved Successfully'), $data);
        } catch (\Exception $e) {
            DB::rollback();
            return BuilderHelper::jsonResponse(500, translate('Banner Slide Saving Failed'));
        }
    }

    /**
     * Delete Banner Slider
     */
    public function deleteBannerSlide($request)
    {
        try {
            DB::beginTransaction();
            $layout_widget_properties = PageBuilderSectionLayoutWidgetProperties::where('layout_has_widget_id', $request->layout_widget_id)->first();

            if ($layout_widget_properties) {

                $sliders = isset($layout_widget_properties->properties) ? $layout_widget_properties->properties : [];

                if (array_key_exists($request->key, $sliders)) {

                    unset($sliders[$request->key]);
                    $layout_widget_properties->properties = xss_clean(json_encode($sliders));
                    $layout_widget_properties->save();
                }
            } else {
                return BuilderHelper::jsonResponse(200, translate('Banner Slide Deleted Successfully'));
            }

            DB::commit();
            return BuilderHelper::jsonResponse(200, translate('Banner Slide Deleted Successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return BuilderHelper::jsonResponse(500, translate('Banner Slide Deleting Failed'));
        }
    }

    /**
     * Cache Delete
     */
    public function __destruct()
    {
        // Cache Clear And Get For The Properties
        if (session()->has('page_id')) {
            $page_id = session()->get('page_id');
            $langs = DB::table('tl_languages')->where('status', '=', config('settings.general_status.active'))->select(['code'])->get();

            foreach ($langs as $value) {
                Cache::forget("page_builder_data_{$page_id}_{$value->code}");
                \Plugin\TlPageBuilder\Helpers\BuilderHelper::getSectionLayoutWidgets($page_id, $value->code);
            }
        }
    }
}
