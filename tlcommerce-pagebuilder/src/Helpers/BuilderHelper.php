<?php

namespace Plugin\TlPageBuilder\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\ProductCategory;
use Theme\TLCommerce\Http\Resources\DealResource;
use Plugin\TlPageBuilder\Models\PageBuilderWidget;
use Plugin\TlPageBuilder\Models\PageBuilderSection;
use Plugin\Multivendor\Repositories\SellerRepository;
use Plugin\Multivendor\Resources\ShopResourceCollection;
use Plugin\TlcommerceCore\Repositories\ProductRepository;

class BuilderHelper
{

    public static $widget_list = [
        'heading_tag',
        'text_editor',
        'image',
        'button',
        'blogs',
        'list_blog',
        'ads',
        'banner',
        'newsletter',
        'category_slider',
        'flash_deal',
        'product_collection',
        'featured_product',
        'custom_product',
        'seller_list'
    ];

    public static $preview_url = '/page-preview/';

    public static $lang;

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
    public static function getSectionLayoutWidgets($page, $lang)
    {
        self::$lang = $lang;

        $active_theme = getActiveTheme();

        $page_builder_data = Cache::remember("page_builder_data_{$page}_{$lang}", 3600, function () use ($page, $active_theme, $lang) {

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
                ->map(function ($page_section) use ($lang) {
                    // Page Section Layouts
                    foreach ($page_section->layouts as $layouts) {
                        foreach ($layouts->layout_widgets as $widgets) {
                            if ($widgets->properties != null) {

                                $properties = $widgets->properties->propertiesTranslations($lang);

                                foreach ($properties as $key => $value) {
                                    if (str_contains($key, 'image')) {
                                        $widget_properties[$key] = getFilePath($value);
                                        continue;
                                    }
                                    $widget_properties[$key] = $value;
                                }
                                $widgets->properties->properties = json_encode($widget_properties);
                                unset($widget_properties);
                            }
                        }
                    }

                    // Page Section Properties
                    if ($page_section->properties != null) {
                        foreach ($page_section->properties->properties as $key => $value) {
                            if (str_contains($key, 'image')) {
                                $section_properties[$key] = getFilePath($value);
                                continue;
                            }
                            $section_properties[$key] = $value;
                        }
                        $page_section->properties->properties = json_encode($section_properties);
                    }

                    return $page_section;
                })
                ->toArray();

            return self::modifiedWidgets($page_section_layout_widgets);
        });

        return $page_builder_data;
    }

    /**
     * Widget Modified
     */
    private static function modifiedWidgets($page_section_layout_widgets)
    {
        $modified_widgets = ['category_slider', 'flash_deal', 'product_collection', 'custom_product', 'seller_list'];

        $updated = array_map(function ($page_section) use ($modified_widgets) {
            foreach ($page_section['layouts'] as $layout_key => $layout) {
                foreach ($layout['layout_widgets'] as $widget_key => $widget) {
                    if ($widget['properties'] != null) {

                        $name =  $widget['widget']['name'];

                        if ($name == 'flash_deal' && !isActivePluging('flashdeal')) {
                            $page_section['layouts'][$layout_key]['layout_widgets'][$widget_key] = [];
                            continue;
                        }

                        if ($name == 'seller_list' && !isActivePluging('multivendor')) {
                            $page_section['layouts'][$layout_key]['layout_widgets'][$widget_key] = [];
                            continue;
                        }

                        if (in_array($name, $modified_widgets)) {
                            $data = self::getWidgetData($name, $widget['properties']['properties']);
                            $page_section['layouts'][$layout_key]['layout_widgets'][$widget_key]['properties']['properties'] = $data;
                        }
                    }
                }
            }
            return $page_section;
        }, $page_section_layout_widgets);

        return $updated;
    }

    /**
     * Get Widget Data For Page Builder
     */
    private static function getWidgetData(string $name, array $properties): array
    {

        $new_prop = array_filter($properties, function ($value, $key) {
            return !(str_contains($key, 'margin_') || str_contains($key, 'padding_') || str_contains($key, 'background_'));
        }, ARRAY_FILTER_USE_BOTH);

        switch ($name) {
            case 'category_slider':
                $result['categories'] = self::categorySlider($new_prop);
                break;

            case 'flash_deal':
                $result['deal-details'] = self::flashDeals($new_prop)['deal'];
                $result['products'] = self::flashDeals($new_prop)['prod'];
                break;

            case 'product_collection':
                $result['products'] = self::productCollection($new_prop);
                break;

            case 'custom_product':
                $result['products'] = self::customProduct($new_prop);
                break;

            case 'seller_list':
                $result['sellers'] = self::getSeller($new_prop);
                break;

            default:
                $result = [];
                break;
        }

        return array_merge($new_prop, $result);
    }

    /**
     * Category Slider Data
     */
    private static function categorySlider(array $new_prop): array
    {
        $lang = self::$lang;
        $type = isset($new_prop['type']) ? $new_prop['type'] : 'latest';
        $count = isset($new_prop['count']) ? $new_prop['count'] : null;

        $categories = ProductCategory::with(['category_translations'])
            ->where('status', config('settings.general_status.active'))
            ->when($type, function ($query, $type) {
                switch ($type) {
                    case 'latest':
                        $query->orderBy('id', 'DESC');
                        break;

                    case 'featured':
                        $query->where('is_featured', 1)->orderBy('id', 'ASC');
                        break;

                    case 'top':
                        $query->where('parent', NULL)->orderBy('id', 'ASC');
                        break;
                }
            })
            ->when($count, function ($query, $count) {
                $query->take($count);
            })
            ->select(['id', 'permalink', 'name', 'icon', 'status', 'parent', 'is_featured'])
            ->get()
            ->map(function ($category) use ($lang) {
                $category->name = $category->translation('name', $lang);
                $category->slug = $category->permalink;
                $category->icon = getFilePath($category->icon);
                unset($category->category_translations);
                return $category;
            })
            ->toArray();

        return $categories;
    }

    /**
     * Flash Deals and Deals Product
     */
    private static function flashDeals(array $new_prop): mixed
    {
        if (isset($new_prop['flash_deal_id'])) {
            $data = \Plugin\Flashdeal\Models\FlashDeal::with(['products' => function ($query) {
                $query->where('status', config('settings.general_status.active'))
                    ->where('is_approved', config('settings.general_status.active'));
            }, 'deal_translations'])
                ->where('id', $new_prop['flash_deal_id'])
                ->first();

            if (!$data) {
                return ['deal' => [], 'prod' => []];
            }

            $count = isset($new_prop['count']) ? (int)$new_prop['count'] : 6;

            $dealsDetails = new DealResource($data);
            $products = new \Plugin\TlcommerceCore\Http\Resources\ProductCollection($data->products()->take($count)->get());

            return ['deal' => $dealsDetails, 'prod' => $products];
        } else {
            return ['deal' => [], 'prod' => []];
        }
    }

    /**
     * Collection and Collection Products
     */
    private static function productCollection(array $new_prop): mixed
    {
        if(!isset($new_prop['collection_id'])){
            return [];
        }
        
        $count = isset($new_prop['count']) ? (int)$new_prop['count'] : 6;

        $product_ids = \Plugin\TlcommerceCore\Models\CollectionHasProducts::where('collection_id', $new_prop['collection_id'])
            ->pluck('product_id');

        $products = Product::whereIn('id', $product_ids)
            ->where('status', config('settings.general_status.active'))
            ->where('is_approved', config('settings.general_status.active'))
            ->take($count)
            ->get();

        return new \Plugin\TlcommerceCore\Http\Resources\ProductCollection($products);
    }

    /**
     * Custom Products
     */
    private static function customProduct(array $new_prop): mixed
    {
        $count = isset($new_prop['count']) ? (int)$new_prop['count'] : 6;

        $products = self::getFilteredProducts($new_prop, $count);
        $products =  new \Plugin\TlcommerceCore\Http\Resources\ProductCollection($products);

        return $products;
    }

    /**
     * Get All The Products For Custom Product Sections
     */
    private static function getFilteredProducts($new_prop, $count)
    {
        $products = [];
        $content = $new_prop['content'];

        $product_repository = new ProductRepository();
        $query = $product_repository->productQuery();
        if (isActivePluging('multivendor')) {
            $query = $product_repository->productQueryFilterWithSeller($query);
        }

        if ($content == 'new_arrival') {
            $products = $query->orderBy('id', 'DESC')
                ->where('status', config('settings.general_status.active'))
                ->where('is_approved', config('settings.general_status.active'))
                ->take($count)
                ->get();

            return $products;
        }

        if ($content == 'featured') {
            $products = $query->where('status', config('settings.general_status.active'))
                ->where('is_featured', config('settings.general_status.active'))
                ->where('is_approved', config('settings.general_status.active'))
                ->orderBy('id', 'DESC')
                ->take($count)->get();

            return $products;
        }

        if ($content == 'top_selling') {
            $products = $query->withCount(['orders as total_sales' => function ($query) {
                $query->select(DB::raw('coalesce(sum(quantity),0)'));
            }])->orderByDesc('total_sales')
                ->where('status', config('settings.general_status.active'))
                ->where('is_approved', config('settings.general_status.active'))
                ->take($count)
                ->get();

            return $products;
        }

        if ($content == 'top_reviewed') {
            $products = $query->withCount(['reviews as average_rating' => function ($query) {
                $query->select(DB::raw('coalesce(avg(rating),0)'));
            }])->orderByDesc('average_rating')
                ->where('status', config('settings.general_status.active'))
                ->where('is_approved', config('settings.general_status.active'))
                ->take($count)
                ->get();

            return $products;
        }

        if ($content == 'category') {
            $category_id = $new_prop['category'];
            $products = $query->whereHas('product_categories', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })
                ->where('status', config('settings.general_status.active'))
                ->where('is_approved', config('settings.general_status.active'))
                ->take($count)
                ->get();

            return $products;
        }

        return $products;
    }

    /**
     * Get Top Seller List
     */
    private static function getSeller(array $new_prop)
    {
        if (isset($new_prop['sellers'])) {
            $seller_ids = implode(',', $new_prop['sellers']);
            $request = new Request();
            $request->merge(['seller_ids' => $seller_ids]);

            $shop_list = (new SellerRepository())->activeShops($request);
            return new ShopResourceCollection($shop_list);
        }
        return [];
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
    public static function deleteBuilderCssOnPageDelete($permalink, $page_id)
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

        $langs = DB::table('tl_languages')->where('status', '=', config('settings.general_status.active'))->select(['code'])->get();
        foreach ($langs as $value) {
            Cache::forget("page_builder_data_{$page_id}_{$value->code}");
        }
    }
}
