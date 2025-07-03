<?php

namespace Plugin\PageBuilder\Services;

use Core\Models\TlPage;
use Illuminate\Support\Facades\File;
use Plugin\PageBuilder\Helpers\BuilderWidgetHelper;
use Plugin\PageBuilder\Models\PageBuilderSectionLayoutWidget;

class PageBuilderService
{
    public $active_theme;
    public $active_theme_namespace;
    public function __construct()
    {
        $this->active_theme = getActiveTheme();
        $this->active_theme_namespace = $this->active_theme->namespace;

        //Creating The Builder Assets Directory If Not Found
        if (!File::exists(base_path("themes/{$this->active_theme->location}/public/builder-assets/css"))) {
            File::makeDirectory(base_path("themes/{$this->active_theme->location}/public/builder-assets/css"), 0755, true);
        }
    }

    /**
     * Make or update Css file for each page
     */
    public function updateBuilderPageCssFile($data)
    {
        $page = TlPage::find($data['page']);

        $type = [
            'css_type'   => $data['type_key'],
            'section_id' => 'section_' . $data['section_id'] . '_' . $page->id,
            'widget_id'  => 'section_' . $data['section_id'] . '_widget_' . $data['layout_has_widget_id']
        ];

        unset($data['type_key'], $data['section_id'], $data['layout_has_widget_id']);

        $mainCss   = [];
        $backgroundAdvance = [];
        $mediaQuery = [];

        foreach ($data as $key => $value) {

            if ($value != '0' && empty($value)) {
                continue;
            }

            if (str_contains($key, '_c_')) {
                $mainCss[$key] = $value;
                continue;
            }

            if (str_contains($key, 'mobile') || str_contains($key, 'tab')) {
                $mediaQuery[$key] = $value;
                continue;
            }

            if (str_contains($key, 'margin') || str_contains($key, 'padding') || str_contains($key, 'background')) {
                $px = str_contains($key, 'background') ? '' : 'px';
                $backgroundAdvance[$key] = $value . $px . ' !important';
                continue;
            }

            $mainCss[$key] = $value;
        }

        $advanceCss     = $this->makeBackgroundAdvanceCss($backgroundAdvance, $type, $page->permalink);
        $mediaQueryCss  = $this->makeMediaQueryCss($mediaQuery, $type, $page->permalink);
        $mainStyleCss = $this->makeMainStyleCss($mainCss, $type, $page->permalink);

        $allCss = array_merge($advanceCss, $mediaQueryCss, $mainStyleCss);

        $json_css = $this->addToJsonFile($allCss, $page->permalink);

        $this->writeToCssFile($json_css, $page->permalink);
    }

    /**
     * make background and advance css
     */
    public function makeBackgroundAdvanceCss($data, $type, $file_name)
    {
        $id = $type['css_type'] == 'section_id' ? '#' . $type['section_id'] : '#' . $type['widget_id'];
        $css = [];


        foreach ($data as $key => $value) {
            $property = str_replace('_', '-', $key);

            if ($property == 'background-image') {
                $value = "url('" . asset((getFilePath($value))) . "')";
            }

            $css[$id][$property] = $value . ';';
        }

        if (!array_key_exists('background_color', $data)) {
            $css[$id]['background-color'] = 'transparent ;';
        }

        if (empty($css)) {
            $this->deleteFromJsonFile($id, $file_name);
        }

        return $css;
    }

    /**
     * make margin padding css with media queries
     */
    public function makeMediaQueryCss($data, $type, $file_name)
    {
        $id = $type['css_type'] == 'section_id' ? '#' . $type['section_id'] : '#' . $type['widget_id'];
        $css = [];

        foreach ($data as $key => $value) {

            $property = str_replace('_', '-', $key);

            if (str_contains($property, 'mobile')) {
                $property = str_replace('mobile-', '', $property);
                $css['@media (max-width: 767px) '][$id][$property] = $value . 'px !important;';
            }

            if (str_contains($property, 'tab')) {
                $property = str_replace('tab-', '', $property);
                $css['@media (min-width: 768px) and (max-width: 991px) '][$id][$property] = $value . 'px !important;';
            }
        }

        if (empty($css)) {
            $this->deleteFromJsonFile($id, $file_name);
        }

        return $css;
    }


    /**
     * make widget or sections main css
     */
    public function makeMainStyleCss($css, $type, $page_link)
    {
        if ($type['css_type'] == 'layout_has_widget_id') {

            $layout_widget_id = explode('_widget_', $type['widget_id']);

            $widget = PageBuilderSectionLayoutWidget::find(end($layout_widget_id))->widget;

            $widget_name = $widget->name;

            $helper_class = new BuilderWidgetHelper();

            if (method_exists($helper_class, $widget_name)) {

                $main_css = BuilderWidgetHelper::$widget_name($css, $type);

                foreach ($main_css['id'] as $value) {
                    $this->deleteFromJsonFile($value, $page_link);
                }

                return $main_css['newCss'];
            }
        }
        return [];
    }

    /**
     * Add to json file
     * @return null|json
     */
    public function addToJsonFile($newCss, $file_name)
    {
        $path = base_path("themes/{$this->active_theme->location}/public/builder-assets/css/{$file_name}.json");
        $page_css = null;

        if (file_exists($path)) {
            $page_css = file_get_contents($path);
            $page_css = json_decode($page_css, true);
        }

        foreach ($newCss as $key => $value) {

            if (empty($page_css)) {
                $page_css[$key] = $newCss[$key];
                continue;
            }

            if (array_key_exists($key, $page_css) &&  str_contains($key, '@media')) {
                foreach ($value as $id => $prop) {
                    $page_css[$key][$id] = $prop;
                }
                continue;
            }

            $page_css[$key] = $newCss[$key];
        }

        file_put_contents($path, empty($page_css) ? '' : json_encode($page_css));

        return $page_css;
    }

    /**
     * Delete From Json File
     */
    public function deleteFromJsonFile($id, $file_name)
    {
        $path = base_path("themes/{$this->active_theme->location}/public/builder-assets/css/{$file_name}.json");
        $page_css = null;

        if (file_exists($path)) {
            $page_css = file_get_contents($path);
            $page_css = json_decode($page_css, true);

            if (!empty($page_css)) {
                foreach ($page_css as $key => $value) {

                    if (str_contains($key, '@media')) {
                        unset($page_css[$key][$id]);

                        if (empty($page_css[$key])) {
                            unset($page_css[$key]);
                        }
                        continue;
                    }

                    unset($page_css[$id]);
                }
            }

            file_put_contents($path, empty($page_css) ? '' : json_encode($page_css));
        }
        return $page_css;
    }

    /**
     * Delete From Json File When Section Is Deleted And Remove Widget
     */
    public function deleteFromJsonFileOnSectionDelete($id, $file_name)
    {
        $path = base_path("themes/{$this->active_theme->location}/public/builder-assets/css/{$file_name}.json");
        $page_css = null;

        if (file_exists($path)) {
            $page_css = file_get_contents($path);
            $page_css = json_decode($page_css, true);

            if (!empty($page_css)) {
                foreach ($page_css as $key => $value) {

                    if (str_contains($key, '@media')) {

                        unset($page_css[$key][$id]);

                        foreach ($value as $media_key => $value) {
                            if (!str_contains($id, '_widget_') && str_contains($media_key, '_widget_') && str_contains($id, explode('_widget_', $media_key)[0])) {
                                unset($page_css[$key][$media_key]);
                            }
                        }

                        if (empty($page_css[$key])) {
                            unset($page_css[$key]);
                        }
                        continue;
                    }

                    if (!str_contains($id, '_widget_') && str_contains($key, '_widget_') && str_contains($id, explode('_widget_', $key)[0])) {
                        unset($page_css[$key]);
                    }

                    unset($page_css[$id]);
                }
            }

            file_put_contents($path, empty($page_css) ? '' : json_encode($page_css));
        }
        return $page_css;
    }

    /**
     * When a widget position is changed then update the json file data
     */
    public function updateWidgetCssOnLayoutChange($data)
    {
        $path = base_path("themes/{$this->active_theme->location}/public/builder-assets/css/{$data['page_permalink']}.json");
        $prev_id = '#section_' . $data['prev_section_id'] . '_widget_' . $data['layout_widget_id'];
        $new_id  = '#section_' . $data['new_section_id'] . '_widget_' . $data['layout_widget_id'];

        if (file_exists($path)) {
            $page_css = file_get_contents($path);
            $page_css = json_decode($page_css, true);

            if (!empty($page_css)) {
                foreach ($page_css as $key => $value) {

                    if (str_contains($key, '@media')) {

                        if (array_key_exists($prev_id, $value)) {
                            $page_css[$key][$new_id] = $page_css[$key][$prev_id];
                            if ($prev_id != $new_id) {
                                unset($page_css[$key][$prev_id]);
                            }
                        }
                        continue;
                    }

                    if ($prev_id == $key) {

                        $page_css[$new_id] = $page_css[$prev_id];
                        if ($prev_id != $new_id) {
                            unset($page_css[$prev_id]);
                        }
                    }
                }
            }
            file_put_contents($path, empty($page_css) ? '' : json_encode($page_css));

            $this->writeToCssFile($page_css, $data['page_permalink']);
        }
    }

    /**
     * Remove Section From CSS
     */
    public function removeSectionFromCSS($data)
    {
        $id = "#section_{$data['id']}_{$data['page_id']}";

        $updateJsonCss = $this->deleteFromJsonFileOnSectionDelete($id, $data['page_permalink']);

        $this->writeToCssFile($updateJsonCss, $data['page_permalink']);
    }

    /**
     * Remove Widget From CSS
     */
    public function removeWidgetFromCSS($data)
    {
        $widget_name = $data['widget_name'];
        $widget_id = ['widget_id' => "section_{$data['section_id']}_widget_{$data['layout_widget_id']}"];

        $helper_class = new BuilderWidgetHelper();

        if (method_exists($helper_class, $widget_name)) {

            $widget_info = BuilderWidgetHelper::$widget_name([], $widget_id);

            foreach ($widget_info['id'] as $value) {
                $this->deleteFromJsonFile($value, $data['page_permalink']);
            }
        }

        $updateJsonCss = $this->deleteFromJsonFile("#section_{$data['section_id']}_widget_{$data['layout_widget_id']}", $data['page_permalink']);

        $this->writeToCssFile($updateJsonCss, $data['page_permalink']);
    }


    /**
     * Write To Css File with updated json data
     */
    public function writeToCssFile($json_data, $file_name)
    {
        $css_path = base_path("themes/{$this->active_theme->location}/public/builder-assets/css/{$file_name}.css");

        $json_data =  empty($json_data) ? '' : $json_data;

        $css_properties = makeCssProperties($json_data);

        setFolderPermissions($css_path);

        file_put_contents($css_path, $css_properties);
    }
}
