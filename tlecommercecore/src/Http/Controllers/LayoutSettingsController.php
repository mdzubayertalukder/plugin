<?php

namespace Plugin\TlcommerceCore\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class LayoutSettingsController extends Controller
{
    public $menus = [];
    public $already_visited_menus = [];
    public $index = 0;

    /**
     * Get header bottom middle menus
     */
    public function headerBottomMiddleMenus()
    {
        $this->menus = [];
        $data = getMenuStructure(getMenuPositionId('Header Bottom Middle Menu'));
        $this->createMenuStructure($data);

        return [
            'success' => true,
            'menus' => $this->menus
        ];
    }

    /**
     * Get header top right menus
     */
    public function headerTopRightMenus()
    {
        $this->menus = [];
        $data = getMenuStructure(getMenuPositionId('Header Top Right Menu'));

        $this->createMenuStructure($data);

        return [
            'success' => true,
            'menus' => $this->menus
        ];
    }

    /**
     * Get footer widget left menus
     */
    public function footerWidgetLeftMenus($menu_group_id)
    {
        $menus = [];

        $data = getMenuStructureByGroupId($menu_group_id);
        for ($i = 0; $i < sizeof($data); $i++) {
            $menus[$i]['url'] = $data[$i]->url;
            $menus[$i]['name'] = $data[$i]->title;
        }

        return $menus;
    }

    /**
     * get footer widget right menus
     */
    public function footerWidgetRightMenus($menu_group_id)
    {
        $menus = [];

        $data = getMenuStructureByGroupId($menu_group_id);
        for ($i = 0; $i < sizeof($data); $i++) {
            $menus[$i]['url'] = $data[$i]->url;
            $menus[$i]['name'] = $data[$i]->title;
        }

        return $menus;
    }

    /**
     * Get all menus
     */
    public function getAllMenusForEcommerceHome()
    {
        try {

            $header_bottom_middle_menus = Cache::rememberForever('header-bottom-middle-menus', function () {
                return $this->headerBottomMiddleMenus();
            });

            if ($header_bottom_middle_menus['success']) {
                array_shift($header_bottom_middle_menus);
            }

            $header_top_right_menus = Cache::rememberForever('header-top-right-menus', function () {
                return $this->headerTopRightMenus();
            });

            if ($header_top_right_menus['success']) {
                array_shift($header_top_right_menus);
            }

            $header_top_left_menus = Cache::rememberForever('header-top-left-menus', function () {
                return $this->headerTopLeftMenus();
            });

            if ($header_top_left_menus['success']) {
                array_shift($header_top_left_menus);
            }


            return response()->json([
                'success' => true,
                'header_bottom_middle_menus' => $header_bottom_middle_menus,
                'header_top_right_menus' => $header_top_right_menus,
                'header_top_left_menus' => $header_top_left_menus
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Get header top left menus
     */
    public function headerTopLeftMenus()
    {
        $this->menus = [];
        $data = getMenuStructure(getMenuPositionId('Header Top Left Menu'));
        $this->createMenuStructure($data);

        return
            [
                'success' => true,
                'menus' => $this->menus
            ];
    }

    /**
     * Get all footer widgets
     */
    public function getFooterWidgets(Request $request)
    {
        try {

            $sidebar_id = getThemeSidebarId('footer_sidebar');
            $all_blog_sidebar_widgets = getAllSidebarWidgets($sidebar_id);

            $widget_options = [];
            foreach ($all_blog_sidebar_widgets as $widget) {
                $sidebar_has_widget = $widget->sidebar_has_widget_id;
                $widget_key = implode('_', explode(' ', strtolower($widget->widget_name)));
                if ($widget_key == 'featured_blog_widget') {
                    $widget_values = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $featured_blogs = $widget_values;
                    $featured_blogs_array = (array)$featured_blogs;
                    if (sizeof($featured_blogs_array)) {
                        $widget_options[$widget_key] = [
                            'widget_title' => $featured_blogs_array['widget_title'],
                            'featured_blogs' => frontendSidebarFeaturedBlogs($featured_blogs_array['number_of_featured_blog'])
                        ];
                    } else {
                        $widget_options[$widget_key] = null;
                    }
                } elseif ($widget_key == 'recent_blog_widget') {
                    $widget_values = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $recent_blogs = $widget_values;
                    $recent_blogs_array = (array)$recent_blogs;
                    if (sizeof($recent_blogs_array)) {
                        $widget_options[$widget_key] = [
                            'widget_title' => $recent_blogs_array['widget_title'],
                            'recent_blog' => frontendSidebarRecentBlogs($recent_blogs_array['number_of_recent_blog'])
                        ];
                    } else {
                        $widget_options[$widget_key] = null;
                    }
                } elseif ($widget_key == 'footer_left_menu') {
                    $widget_values = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $footer_left_menu = $widget_values;
                    $footer_left_menu_array = (array)$footer_left_menu;
                    if (sizeof($footer_left_menu_array) > 0) {
                        $footer_left_menu = $this->footerWidgetLeftMenus($footer_left_menu_array['menu_group_id']);
                        $widget_options[$widget_key] = [
                            'widget_title' => $footer_left_menu_array['widget_title'],
                            'footer_left_menu' => $footer_left_menu
                        ];
                    } else {
                        $widget_options[$widget_key] = [
                            'widget_title' => '',
                            'footer_right_menu' => []
                        ];
                    }
                } elseif ($widget_key == 'footer_right_menu') {
                    $widget_values = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $footer_right_menu = $widget_values;
                    $footer_right_menu_array = (array)$footer_right_menu;
                    if (sizeof($footer_right_menu_array) > 0) {
                        $footer_right_menu = $this->footerWidgetRightMenus($footer_right_menu_array['menu_group_id']);
                        $widget_options[$widget_key] = [
                            'widget_title' => $footer_right_menu_array['widget_title'],
                            'footer_right_menu' => $footer_right_menu
                        ];
                    } else {
                        $widget_options[$widget_key] = [
                            'widget_title' => '',
                            'footer_right_menu' => []
                        ];
                    }
                } elseif ($widget_key == 'address_widget') {
                    $widget_options[$widget_key] = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $widget_options[$widget_key]['social_links'] = $this->getSocialIcons();
                } else {
                    $widget_options[$widget_key] = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                }
            }
            return response()->json(
                [
                    'success' => true,
                    'widget_options' => $widget_options,
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }

    /**
     * Get blog sidebar widgets
     */
    public function getBlogSidebarWidgets()
    {
        try {

            $sidebar_id = getThemeSidebarId('blog_sidebar');
            $all_blog_sidebar_widgets = getAllSidebarWidgets($sidebar_id);

            $widget_options = [];
            foreach ($all_blog_sidebar_widgets as $widget) {
                $sidebar_has_widget = $widget->sidebar_has_widget_id;
                $widget_key = implode('_', explode(' ', strtolower($widget->widget_name)));
                if ($widget_key == 'featured_blog_widget') {
                    $widget_values = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $featured_blogs = $widget_values;
                    $featured_blogs_array = (array)$featured_blogs;
                    if (sizeof($featured_blogs_array)) {
                        $widget_options[$widget_key] = [
                            'widget_title' => $featured_blogs_array['widget_title'],
                            'featured_blogs' => frontendSidebarFeaturedBlogs($featured_blogs_array['number_of_featured_blog'])
                        ];
                    } else {
                        $widget_options[$widget_key] = null;
                    }
                } elseif ($widget_key == 'recent_blog_widget') {
                    $widget_values = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $recent_blogs = $widget_values;
                    $recent_blogs_array = (array)$recent_blogs;
                    if (sizeof($recent_blogs_array)) {
                        $widget_options[$widget_key] = [
                            'widget_title' => $recent_blogs_array['widget_title'],
                            'recent_blog' => frontendSidebarRecentBlogs($recent_blogs_array['number_of_recent_blog'])
                        ];
                    } else {
                        $widget_options[$widget_key] = null;
                    }
                } elseif ($widget_key == 'footer_left_menu') {
                    $widget_values = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $footer_left_menu = $widget_values;
                    $footer_left_menu_array = (array)$footer_left_menu;
                    if (sizeof($footer_left_menu_array) > 0) {
                        $footer_left_menu = $this->footerWidgetLeftMenus($footer_left_menu_array['menu_group_id']);
                        $widget_options[$widget_key] = [
                            'widget_title' => $footer_left_menu_array['widget_title'],
                            'footer_left_menu' => $footer_left_menu
                        ];
                    } else {
                        $widget_options[$widget_key] = null;
                    }
                } elseif ($widget_key == 'footer_right_menu') {
                    $widget_values = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $footer_right_menu = $widget_values;
                    $footer_right_menu_array = (array)$footer_right_menu;
                    if (sizeof($footer_right_menu_array) > 0) {
                        $footer_right_menu = $this->footerWidgetRightMenus($footer_right_menu_array['menu_group_id']);
                        $widget_options[$widget_key] = [
                            'widget_title' => $footer_right_menu_array['widget_title'],
                            'footer_right_menu' => $footer_right_menu
                        ];
                    } else {
                        $widget_options[$widget_key] = null;
                    }
                } elseif ($widget_key == 'address_widget') {
                    $widget_options[$widget_key] = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                    $widget_options[$widget_key]['social_links'] = $this->getSocialIcons();
                } else {
                    $widget_options[$widget_key] = getSidebarWidgetValues($sidebar_has_widget, Session::get('api_locale'));
                }
            }
            return response()->json(
                [
                    'success' => true,
                    'widget_options' => $widget_options,
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }

    /**
     * Create menu structure
     */
    public function createMenuStructure($data)
    {
        $this->index = 0;
        while ($this->index < sizeof($data)) {
            if ($data[$this->index]->level == 1) {
                if ($this->index + 1 < sizeof($data) && $data[$this->index + 1]->level > $data[$this->index]->level) {
                    $temp_array = [
                        'name' => $data[$this->index]->title,
                        'url' => $data[$this->index]->url
                    ];
                    $temp_array['submenu'] = $this->setSubMenus($data);
                    array_push($this->menus, $temp_array);
                } else {
                    $temp_array = [
                        'name' => $data[$this->index]->title,
                        'url' => $data[$this->index]->url
                    ];
                    array_push($this->menus, $temp_array);
                }
            }
            $this->index++;
        }
    }

    /**
     * Create sub menu structure
     */
    public function setSubMenus($data)
    {
        $this->index++;
        $i = $this->index;
        $temp_menus = [];
        while ($i < sizeof($data)) {
            if ($i + 1 < sizeof($data) &&  $data[$i + 1]->level > $data[$i]->level) {
                $temp_array = [
                    'name' => $data[$i]->title,
                    'url' => $data[$i]->url
                ];

                $temp_array['submenu'] = $this->setSubMenus($data);
                array_push($temp_menus, $temp_array);
                if ($this->index + 1 >= sizeof($data) ||  $data[$this->index + 1]->level != $data[$i]->level) {
                    return $temp_menus;
                } else {
                    $this->index++;
                    $i = $this->index;
                }
            } elseif ($i + 1 < sizeof($data) &&  $data[$i + 1]->level == $data[$i]->level) {
                $temp_array = [
                    'name' => $data[$i]->title,
                    'url' => $data[$i]->url
                ];

                array_push($temp_menus, $temp_array);
                $i++;
                if ($i < sizeof($data) &&  $data[$i]->level == $data[$this->index]->level) {
                    $this->index++;
                }
            } else {
                $temp_array = [
                    'name' => $data[$i]->title,
                    'url' => $data[$i]->url
                ];
                array_push($temp_menus, $temp_array);
                return $temp_menus;
            }


            if ($i < sizeof($data) && $data[$i]->level == 1) {
                break;
            }
        }
        return $temp_menus;
    }

    /**
     * Will return all social icons
     */
    public function getSocialIcons()
    {
        $theme = getActiveTheme();
        $style = getThemeOption('social', $theme->id);
        $socialIcons = [];

        if ($style != null) {
            $socialIcons = json_decode($style['social_field']);
            return $socialIcons;
        }

        return null;
    }
}
