<?php

namespace Plugin\TlPageBuilder\Helpers;

use Core\Models\TlBlogTag;
use Illuminate\Support\Facades\Session;
use Plugin\TlPageBuilder\Models\PageBuilderSectionLayoutWidgetProperties;

class BuilderWidgetHelper
{

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
        $id_title = '#' . $type['widget_id'] . ' .blog-card-title a';
        $id_title_hover = '#' . $type['widget_id'] . ' .blog-card-title a:hover';
        $id_button = '#' . $type['widget_id'] . ' .btn-underline';
        $id_button_hover = '#' . $type['widget_id'] . ' .btn-underline:hover';

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


                if (str_contains($property, 'button')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('button-hover-', '', $property);
                        $newCss[$id_button_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('button-', '', $property);
                        $newCss[$id_button][$property] = $value . ' !important;';
                    }
                    continue;
                }
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_title, $id_title_hover, $id_button, $id_button_hover]
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

    //Flash Deal Product
    public static function flash_deal($css, $type)
    {
        $newCss = [];
        $id_title = '#' . $type['widget_id'] . ' .section-title h2';
        $id_countdown = '#' . $type['widget_id'] . ' .countdown li';

        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {
                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if (str_contains($property, 'count')) {
                    $property = str_replace('count-', '', $property);
                    $newCss[$id_countdown][$property] = $value . ' !important;';
                    continue;
                }

                $newCss[$id_title][$property] = $value . ' !important;';
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [$id_title, $id_countdown]
        ];
    }

    //Flash Deal Product
    public static function featured_product($css, $type)
    {
        $newCss = [];
        $id_meta_title = '#' . $type['widget_id'] . ' .cta-content span';
        $id_title = '#' . $type['widget_id'] . ' .cta-content h2';
        $id_paragraph = '#' . $type['widget_id'] . ' .cta-content p';
        $id_play_button = '#' . $type['widget_id'] . ' .video-card .btn_play';
        $id_button = '#' . $type['widget_id'] . ' .cta-content a';
        $id_button_hover = '#' . $type['widget_id'] . ' .cta-content a:hover';


        foreach ($css as $key => $value) {

            if (str_contains($key, '_c_')) {
                $property =  str_replace('_', '-', str_replace('_c_', '', $key));

                if(str_contains($property, 'text')){
                    $property =  str_replace('text-', '', $property);
                    $newCss[$id_title][$property] = $value . ' !important;';
                    $newCss[$id_meta_title][$property] = $value . ' !important;';
                    $newCss[$id_paragraph][$property] = $value . ' !important;';
                    continue;
                }

                if (str_contains($property, 'play')) {
                    $property = str_replace('play-button-', '', $property);
                    $newCss[$id_play_button][$property] = $value . ' !important;';
                    continue;
                }

                if (str_contains($property, 'button')) {
                    if (str_contains($property, 'hover')) {
                        $property = str_replace('button-hover-', '', $property);
                        $newCss[$id_button_hover][$property] = $value . ' !important;';
                    } else {
                        $property = str_replace('button-', '', $property);
                        $newCss[$id_button][$property] = $value . ' !important;';
                    }
                    continue;
                }
                
            }
        }

        return [
            'newCss' => $newCss,
            'id' => [
                $id_meta_title,
                $id_title,
                $id_paragraph,
                $id_play_button,
                $id_button,
                $id_button_hover
            ]
        ];
    }
}
