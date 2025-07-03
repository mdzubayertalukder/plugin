<?php

namespace Plugin\PageBuilder\Helpers;

use Plugin\PageBuilder\Models\PageBuilderWidget;
use Plugin\PageBuilder\Models\PageBuilderSection;

class BuilderHelper
{

    public static $widget_list = [
        'heading_tag',
        'text_editor',
        'image',
        'button',
        'blogs',
        'ads',
        'banner',
        'banner_style_two',
        'author',
        'category',
        'tag',
        'title_overlay_blog',
        'list_blog',
        'slider_blog',
        'newsletter',
        'contact',
        'feature',
        'pricing',
        'review',
    ];

    public static $preview_url = '/admin/page-preview' . '/';

    /**
     * get page builder widgets
     * @return object $widgets
     */
    public static function getPageBuilderWidgets()
    {
        $active_theme = getActiveTheme();

        $all_settings_name = self::$widget_list;

        $widgets = [];

        if ($all_settings_name) {
            foreach ($all_settings_name as $value) {
                $full_name = ucwords(str_replace('_', ' ', $value));
                $widget = PageBuilderWidget::firstOrCreate([
                    'name' => $value,
                    'full_name' => $full_name,
                    'theme_id' => $active_theme->id
                ]);
                $widgets[] = $widget->toArray();
            }
        }

        return $widgets;
    }

    /**
     * Get Page Section Layouts with widgets 
     */
    public static function getSectionLayoutWidgets($page)
    {
        $active_theme = getActiveTheme();
        $page_section_layout_widgets = PageBuilderSection::with(['layouts.layout_widgets' => function ($query) {
            $query->with('widget')->with('properties');
        }])
            ->with('properties')
            ->where([
                'page_id' => $page,
                'theme_id' => $active_theme->id,
            ])
            ->orderBy('ordering')
            ->get()
            ->map(function ($page_section) {
                foreach ($page_section->layouts as $layouts) {
                    foreach ($layouts->layout_widgets as $widgets) {
                        if ($widgets->properties != null) {
                            $widgets->properties->properties = json_encode($widgets->properties->propertiesTranslations(getFrontLocale()));
                        }
                    }
                }
                return $page_section;
            })
            ->toArray();

        return $page_section_layout_widgets;
    }

    /**
     * Return a Json Response with code
     */
    public static function jsonResponse($status, $msg, $data = '')
    {
        return response()->json([
            'message' => $msg,
            'data'    => $data
        ], $status);
    }

    /**
     * Delete CSS and Json File on Page Delete
     */
    public static function deleteBuilderCssOnPageDelete($permalink)
    {
        $active_theme = getActiveTheme();
        $css_path = base_path("themes/{$active_theme->location}/public/builder-assets/css/{$permalink}.css");
        $json_path = base_path("themes/{$active_theme->location}/public/builder-assets/css/{$permalink}.json");

        if (file_exists($css_path)) {
            unlink($css_path);
        }

        if (file_exists($json_path)) {
            unlink($json_path);
        }
    }
}
