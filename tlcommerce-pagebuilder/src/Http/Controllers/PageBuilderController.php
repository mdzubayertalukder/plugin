<?php

namespace Plugin\TlPageBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Plugin\TlPageBuilder\Helpers\BuilderHelper;
use Plugin\TlPageBuilder\Repositories\PageBuilderRepository;

class PageBuilderController extends Controller
{
    public $active_theme;
    public function __construct(protected PageBuilderRepository $builder_repository)
    {
        $this->active_theme = getActiveTheme();
    }

    /**
     * Will return home page sections
     *
     * @return View
     */
    public function pageSections(Request $request)
    {
        session()->put('page_id', $request['id']);
        return $this->builder_repository->getPageSections($request);
    }

    /**
     * add new section to page
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function newSection(Request $request)
    {
        return $this->builder_repository->createNewPageSections($request);
    }

    /**
     * add new section to page
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function removeSection(Request $request)
    {
        return $this->builder_repository->removePageSection($request);
    }


    /**
     * will sort and update ordering of sections
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sortingSection(Request $request)
    {
        return $this->builder_repository->sortingPageSection($request);
    }


    /**
     * Will return section property options
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSectionProperties(Request $request)
    {
        return $this->builder_repository->getSectionProperties($request);
    }

    /**
     * Update Section Properties
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSectionProperties(Request $request)
    {
        return $this->builder_repository->updatePageSectionProperties($request);
    }


    /**
     * Add widget to section layouts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addWidget(Request $request)
    {
        return $this->builder_repository->addWidgetToSectionLayout($request);
    }

    /**
     * remove widget from section layouts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function removeWidget(Request $request)
    {
        return $this->builder_repository->removeWidgetFromSectionLayout($request);
    }

    /**
     * update widgets position on layout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateWidgetPosition(Request $request)
    {
        return $this->builder_repository->updateWidgetPositionOnSections($request);
    }

    /**
     * order widgets in layout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function orderWidget(Request $request)
    {
        return $this->builder_repository->orderWidgetOnLayout($request);
    }


    /**
     * Will return widget property options
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getWidgetProperties(Request $request)
    {
        return $this->builder_repository->getWidgetProperties($request);
    }


    /**
     * Will update Widget properties
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateWidgetProperties(Request $request)
    {
        return $this->builder_repository->updateLayoutWidgetProperties($request);
    }


    /**
     * Text Editor Image Upload
     */
    public function imageUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,jpeg|max:1040',
        ]);

        if ($validator->passes()) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $image = 'text_editor_image' . time() . rand() . '.' . $extension;

            $active_theme = getActiveTheme();

            $file->move("themes/{$active_theme->location}/public/builder-assets/text-editor/", $image);
            $path = "/themes/{$active_theme->location}/public/builder-assets/text-editor/" . $image;

            return response()->json(['url' => $path]);
        }
        return response()->json(['error' => $validator->errors()->all()]);
    }

    /**
     * Banner Modal Show
     * @param Request $request (Ajax Request)
     * @return Response
     */
    public function showModal(Request $request)
    {
        try {
            $details = empty($request->details) ? null : $request->details;
            $modal = view('plugin/tlcommerce-pagebuilder::builders.includes.banner-modal', ['details' => $details, 'key' => $request->key])->render();
            return BuilderHelper::jsonResponse(200, '', $modal);
        } catch (\Exception $e) {
            return BuilderHelper::jsonResponse(500, translate('New Banner Slide Open Failed'));
        }
    }

    /**
     * Save Slider
     * @param Request $request
     * @return Response
     */
    public function saveSlider(Request $request)
    {
        return $this->builder_repository->saveBannerSlide($request);
    }


    public function deleteSlider(Request $request)
    {
        return $this->builder_repository->deleteBannerSlide($request);
    }
}
