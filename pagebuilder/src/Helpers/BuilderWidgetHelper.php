<?php

namespace Plugin\PageBuilder\Helpers;

use Core\Models\TlBlogTag;
use Plugin\PageBuilder\Models\PageBuilderSectionLayoutWidgetProperties;

class BuilderWidgetHelper
{

    /**
     * Get tags by recent or popular
     */
    public static function getTagsFromBuilder($type, $count)
    {
        $order = $type == 'recent' ? 'id' : 'blogs_count';
        $locale = getFrontLocale();

        $tags = TlBlogTag::with([
            'tag_translations' => function ($query) use ($locale) {
                $query->where('lang', $locale)
                    ->select(['id', 'tag_id', 'name']);
            }
        ])
            ->where('is_publish', '1')
            ->select([
                'id',
                'permalink',
                'name'
            ])
            ->withCount('blogs')
            ->orderBy($order, 'desc')
            ->limit($count)
            ->get()
            ->map(function ($tag) {
                if (count($tag->tag_translations)) {
                    $tag->name = $tag->tag_translations->first()->name;
                }
                return $tag;
            });

        return $tags;
    }

    //------------------ Widget CSS Create Functions Start --------------------//

    // Heading Widget
    public static function heading_tag($css, $type)
    {
        $newCss = [];
        $layoutWidgetId = explode('_widget_', $type['widget_id'])[1];
        $widget_prop = PageBuilderSectionLayoutWidgetProperties::where('layout_has_widget_id', $layoutWidgetId)->first();
        $tag = $widget_prop ? $widget_prop->properties['tag'] : false;

        if ($tag) {
            $id = '#' . $type['widget_id'] . ' ' . $tag;

            foreach ($css as $key => $value) {

                if (str_contains($key, '_c_')) {

                    $property =  str_replace('_', '-', str_replace('_c_', '', $key));
                    $newCss[$id][$property] = $value . ';';
                }
            }

            return ['newCss' => $newCss, 'id' => [$id]];
        } else {

            return ['id' => []];
        }
    }

    // Image Widget
    public static function image($css, $type)
    {
        $newCss = [];
        $id = '#' . $type['widget_id'] . ' img';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));
                $newCss[$id][$property] = $value . 'px !important;';
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id]
        ];
    }

    // Button Widget
    public static function button($css, $type)
    {
        $newCss = [];
        $id = '#' . $type['widget_id'] . ' a';
        $id_hover = '#' . $type['widget_id'] . ' a:hover ';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'padding')) {
                    $property = str_replace('button-', '', $property);
                    $newCss[$id][$property] = $value . 'px !important;';
                    continue;
                }

                if (str_contains($property, 'hover')) {
                    $property = str_replace('hover-', '', $property);
                    $newCss[$id_hover][$property] = $value . ' !important;';
                    continue;
                }

                if (str_contains($property, 'radius') || str_contains($property, 'size')) {
                    $newCss[$id][$property] = $value . 'px !important;';
                    continue;
                }

                $newCss[$id][$property] = $value . ' !important;';
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id, $id_hover]
        ];
    }

    // Newsletter Widget
    public static function tag($css, $type)
    {
        $newCss = [];
        $id_tag = '#' . $type['widget_id'] . ' .tagcloud a';
        $id_tag_hover = '#' . $type['widget_id'] . ' .tagcloud a:hover ';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'tag')) {

                    if (str_contains($property, 'hover')) {
                        $property = str_replace('tag-hover-', '', $property);
                        $newCss[$id_tag_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('tag-', '', $property);
                        $newCss[$id_tag][$property] = $value . ' !important;';
                    }

                    continue;
                }

                if (str_contains($property, 'font-size')) {
                    $newCss[$id_tag][$property] = $value . 'px !important;';
                    continue;
                }
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_tag, $id_tag_hover]
        ];
    }

    // Newsletter Widget
    public static function newsletter($css, $type)
    {
        $newCss = [];
        $id_p = '#' . $type['widget_id'] . ' p';
        $id_button = '#' . $type['widget_id'] . ' button';
        $id_button_hover = '#' . $type['widget_id'] . ' button:hover ';
        $id_input = '#' . $type['widget_id'] . ' form input ';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'btn')) {

                    if (str_contains($property, 'hover')) {
                        $property = str_replace('btn-hover-', '', $property);
                        $newCss[$id_button_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('btn-', '', $property);
                        $newCss[$id_button][$property] = $value . ' !important;';
                    }

                    continue;
                }

                if (str_contains($property, 'font-size')) {
                    $newCss[$id_p][$property] = $value . 'px !important;';
                    $newCss[$id_button][$property] = $value . 'px !important;';
                    $newCss[$id_input][$property] = $value . 'px !important;';
                    continue;
                }

                $newCss[$id_p][$property] = $value . ' !important;';
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_p, $id_button, $id_button_hover, $id_input]
        ];
    }

    //Blog Widget
    public static function blogs($css, $type)
    {
        $newCss = [];
        $id_title = '#' . $type['widget_id'] . ' .title h2 a';
        $id_title_hover = '#' . $type['widget_id'] . ' .title h2 a:hover';
        $id_description = '#' . $type['widget_id'] . ' .desc p';
        $id_post = '#' . $type['widget_id'] . ' .post-data';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'title')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('title-hover-', '', $property);
                        $newCss[$id_title_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('title-', '', $property);
                        $newCss[$id_title][$property] = $value . ' !important;';
                    }
                    continue;
                }

                if (str_contains($property, 'description')) {
                    $property = str_replace('description-', '', $property);
                    $newCss[$id_description][$property] = $value . ' !important;';
                    continue;
                }

                if (str_contains($property, 'post')) {
                    $property = str_replace('post-', '', $property);
                    $newCss[$id_post][$property] = $value . ' !important;';
                    continue;
                }
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_title, $id_title_hover, $id_description, $id_post]
        ];
    }

    //List Blog Widget
    public static function list_blog($css, $type)
    {
        $newCss = [];
        $id_title = '#' . $type['widget_id'] . ' a';
        $id_title_hover = '#' . $type['widget_id'] . ' a:hover';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'title')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('title-hover-', '', $property);
                        $newCss[$id_title_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('title-', '', $property);
                        $newCss[$id_title][$property] = $value . ' !important;';
                    }
                    continue;
                }
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_title, $id_title_hover]
        ];
    }

    //Title Overlay Blog Widget
    public static function title_overlay_blog($css, $type)
    {
        $newCss = [];
        $id_title = '#' . $type['widget_id'] . ' a';
        $id_title_hover = '#' . $type['widget_id'] . ' a:hover';
        $id_title_overlay = '#' . $type['widget_id'] . ' .featured-post-title';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'title')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('title-hover-', '', $property);
                        $newCss[$id_title_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('title-', '', $property);
                        $newCss[$id_title][$property] = $value . ' !important;';
                    }
                    continue;
                }

                if (str_contains($property, 'overlay')) {
                    $property = str_replace('overlay-', '', $property);
                    $newCss[$id_title_overlay][$property] = $value . 'e6 !important;';
                    continue;
                }
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_title, $id_title_hover]
        ];
    }

    //Slider Blog Widget
    public static function slider_blog($css, $type)
    {
        $newCss = [];
        $id_title = '#' . $type['widget_id'] . ' a';
        $id_title_hover = '#' . $type['widget_id'] . ' a:hover';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'title')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('title-hover-', '', $property);
                        $newCss[$id_title_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('title-', '', $property);
                        $newCss[$id_title][$property] = $value . ' !important;';
                    }
                    continue;
                }
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_title, $id_title_hover]
        ];
    }

    //List Blog Widget
    public static function banner($css, $type)
    {
        $newCss = [];
        $id_category       = '#' . $type['widget_id'] . ' .cats a';
        $id_category_hover = '#' . $type['widget_id'] . ' .cats a:hover';
        $id_category_after = '#' . $type['widget_id'] . ' .cats a:after';
        $id_title          = '#' . $type['widget_id'] . ' .title h2 a';
        $id_title_hover    = '#' . $type['widget_id'] . ' .title h2 a:hover';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'title')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('title-hover-', '', $property);
                        $newCss[$id_title_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('title-', '', $property);
                        $newCss[$id_title][$property] = $value . ' !important;';
                        $newCss[$id_title][$property] = $value . ' !important;';
                    }
                    continue;
                }

                if (str_contains($property, 'category')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('category-hover-', '', $property);
                        $newCss[$id_category_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('category-', '', $property);
                        $newCss[$id_category][$property] = $value . ' !important;';
                        $newCss[$id_category_after][$property] = $value . ' !important;';
                    }
                    continue;
                }
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_title, $id_title_hover, $id_category, $id_category_hover, $id_category_after]
        ];
    }

    // Banner style two
    public static function banner_style_two($css, $type)
    {
        $newCss = [];
        $id_primary_text = '#' . $type['widget_id'] . ' .content h1';
        $id_secondery_text = '#' . $type['widget_id'] . ' .content p';
        $id_primary_button = '#' . $type['widget_id'] . ' .btn-crs.plug.s-btn';
        $id_primary_button_hover = '#' . $type['widget_id'] . ' .btn-crs.plug.s-btn:hover';
        $id_secondery_button = '#' . $type['widget_id'] . ' .btn-book.line-btn';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'primary-text')) {
                    $property = str_replace('primary-text-', '', $property);
                    $newCss[$id_primary_text][$property] = $value . ' !important;';
                }

                if (str_contains($property, 'secondary-text')) {
                    $property = str_replace('secondary-text-', '', $property);
                    $newCss[$id_secondery_text][$property] = $value . ' !important;';
                }

                if (str_contains($property, 'primary-button')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('primary-button-hover-', '', $property);
                        $newCss[$id_primary_button_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('primary-button-', '', $property);
                        $newCss[$id_primary_button][$property] = $value . ' !important;';
                    }
                }

                if (str_contains($property, 'secondary-button')) {
                    $property = str_replace('secondary-button-', '', $property);
                    $newCss[$id_secondery_button][$property] = $value . ' !important;';
                }
            }
        }
        return [
            'newCss' => $newCss,
            'id' => [$id_primary_text, $id_secondery_text, $id_primary_button, $id_primary_button_hover, $id_secondery_button]
        ];
    }

    // Feature Widget
    public static function feature($css, $type)
    {
        $newCss = [];
        $id_feature = '#' . $type['widget_id'] . ' #feature';
        $id_feature_a = '#' . $type['widget_id'] . ' #feature .custom-title';
        $id_feature_p = '#' . $type['widget_id'] . ' #feature p';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {

                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'color')) {
                    $newCss[$id_feature_a][$property] = $value . '!important;';
                    $newCss[$id_feature_p][$property] = $value . '!important;';
                    continue;
                }

                if (str_contains($property, 'width')) {
                    $newCss[$id_feature][$property] = $value . 'px !important;';
                    continue;
                }

                if (str_contains($property, 'height')) {
                    $newCss[$id_feature][$property] = $value . 'px !important;';
                    continue;
                }
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_feature, $id_feature_a, $id_feature_p]
        ];
    }
}
