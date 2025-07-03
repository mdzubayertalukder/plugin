<?php

namespace Plugin\TlcommerceCore\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\ProductTags;
use Plugin\Flashdeal\Models\FlashDealProducts;
use Plugin\TlcommerceCore\Models\ProductBrand;
use Plugin\TlcommerceCore\Models\SearchKeyword;
use Plugin\TlcommerceCore\Models\ProductHasTags;
use Plugin\TlcommerceCore\Models\ProductCategory;
use Plugin\TlcommerceCore\Models\VariantProductPrice;
use Plugin\TlcommerceCore\Http\Resources\BrandCollection;
use Plugin\TlcommerceCore\Repositories\ProductRepository;
use Plugin\TlcommerceCore\Http\Resources\ProductCollection;
use Plugin\TlcommerceCore\Models\ProductColorVariantImages;
use Plugin\TlcommerceCore\Http\Resources\CategoryCollection;
use Plugin\TlcommerceCore\Http\Resources\SingleDealsCollection;
use Plugin\TlcommerceCore\Http\Resources\ProductReviewCollection;
use Plugin\TlcommerceCore\Http\Resources\SingleProductCollection;
use Plugin\TlcommerceCore\Http\Resources\CompareProductCollection;
use Plugin\TlcommerceCore\Http\Resources\ParentCategoryCollection;
use Plugin\TlcommerceCore\Http\Resources\SingleCategoryCollection;

class ProductController extends Controller
{
    protected $product_repository;

    public function __construct(ProductRepository $product_repository)
    {
        $this->product_repository = $product_repository;
    }

    /**
     * Will return product configuration
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function productConfiguration()
    {
        $data = $this->product_repository->productConfiguration();
        if ($data != NULL) {
            return response()->json(
                [
                    'success' => true,
                    'config' => $data
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * Will return product list
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(Request $request)
    {
        $query = $this->product_repository->filterProducts($request);

        //sorting by newest items
        if ($request->has('sorting') && $request['sorting'] == 'newest') {
            $query = $query->orderBy('id', 'DESC');
        }
        //sorting by popular items
        if ($request->has('sorting') && $request['sorting'] == 'popular') {
            $query = $query->withCount('orders as number_of_order')
                ->orderBy('number_of_order', 'desc');
        }
        //Price low to high
        if ($request->has('sorting') && $request['sorting'] == 'lowToHigh') {
            $products = $query->paginate($request->perPage);
            $sortedResult = $products->getCollection()->sortBy('unit_price')->values();
            $products->setCollection($sortedResult);
            return new ProductCollection($products);
        }
        //Price high to low
        if ($request->has('sorting') && $request['sorting'] == 'highToLow') {
            $products = $query->paginate($request->perPage);
            $sortedResult = $products->getCollection()->sortByDesc('unit_price')->values();
            $products->setCollection($sortedResult);
            return new ProductCollection($products);
        }

        $products = $query->paginate($request->perPage);
        return new ProductCollection($products);
    }

    /**
     * Will return product search results
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProducts(Request $request)
    {
        $product_ids = [];
        if ($request->has('tag')) {
            $this->storeUserSearchKey($request['tag']);
            $tag_id = ProductTags::where('permalink', $request['tag'])->first();
            if ($tag_id) {
                $product_ids = ProductHasTags::where('tag_id', $tag_id->id)->pluck('product_id');
            }
        }
        if ($request->has('search_key')) {
            $this->storeUserSearchKey($request['search_key']);
            $product_ids = Product::where('name', "like", "%$request->search_key%")->where('status', config('settings.general_status.active'))->pluck('id');
        }
        $query = $this->product_repository->filterProducts($request, null, $product_ids);
        //sorting by newest items
        if ($request->has('sorting') && $request['sorting'] === 'newest') {
            $query = $query->orderBy('id', 'DESC');
        }
        //sorting by popular items
        if ($request->has('sorting') && $request['sorting'] === 'popular') {
            $query = $query->withCount('orders as number_of_order')
                ->orderBy('number_of_order', 'desc');
        }
        //Price low to high
        if ($request->has('sorting') && $request['sorting'] == 'lowToHigh') {
            $query = $query->whereHas('single_price', function ($q) {
                $q->orderBy('unit_price', 'ASC');
            })->orWhereHas('variations', function ($q) {
                $q->orderBy('unit_price', 'ASC');
            });
        }
        //Price high to low
        if ($request->has('sorting') && $request['sorting'] == 'highToLow') {
            $query = $query->whereHas('single_price', function ($q) {
                $q->orderBy('unit_price', 'DESC');
            })->orWhereHas('variations', function ($q) {
                $q->orderBy('unit_price', 'DESC');
            });
        }
        $products = $query->paginate($request->perPage);

        return new ProductCollection($products);
    }
    /**
     * Will return single product details
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function productDetails(Request $request)
    {
        if ($request->has('preview') && $request['preview'] == 1) {
            $product = Product::with(['single_price', 'variations', 'choices', 'reviews', 'gallery_images'])
                ->where('permalink', $request['permalink'])
                ->first();
        } else {
            $product = Product::with(['single_price', 'variations', 'choices', 'reviews', 'gallery_images'])
                ->where('permalink', $request['permalink'])
                ->where('status', config('settings.general_status.active'))
                ->where('is_approved', config('settings.general_status.active'))
                ->first();


            if ($product == null) {
                return response()->json(
                    [
                        'success' => false,
                    ]
                );
            }

            if (isActivePluging('multivendor')) {

                if ($product->seller->status != config('settings.general_status.active')) {
                    return response()->json(
                        [
                            'success' => false,
                        ]
                    );
                }

                if ($product->seller->shop == null) {
                    return response()->json(
                        [
                            'success' => false,
                        ]
                    );
                }

                if ($product->seller->shop->status != config('settings.general_status.active')) {
                    return response()->json(
                        [
                            'success' => false,
                        ]
                    );
                }
            }
        }

        if ($product != null) {
            return new SingleProductCollection($product);
        }
    }
    /**
     * Will return compare items details
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function compareItems(Request $request)
    {
        try {
            return new CompareProductCollection(Product::whereIn('id', json_decode($request['items'], true))->get());
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will return single variant information
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function singleVariantInfo(Request $request)
    {
        try {
            $old_variant_array = explode('/', $request->variant);
            $new_variant_array = [];
            foreach ($old_variant_array as $variant) {
                $item_array = explode(':', $variant);
                $choice = $item_array[0];
                if ($choice == $request->choice) {
                    $new_variant = $choice . ':' . $request->option;
                    array_push($new_variant_array, $new_variant);
                } else {
                    array_push($new_variant_array, $variant);
                }
            }
            $new_variant = implode('/', $new_variant_array);

            $m_variant = $new_variant;
            $variant_info = VariantProductPrice::where('product_id', $request->id)->where('variant', $m_variant)->select('unit_price', 'quantity')->first();

            $product_info = Product::where('id', $request->id)->select('id', 'discount_amount', 'discount_type')->first();
            $applicable_discount = $product_info->applicableDiscount();

            if ($applicable_discount['discount_amount'] > 0) {
                if ($applicable_discount['discountType'] == config('tlecommercecore.amount_type.flat')) {
                    $discount = $applicable_discount['discount_amount'];
                } else {
                    $discount = ($variant_info->unit_price * $applicable_discount['discount_amount']) / 100;
                }
                $base_price = $variant_info->unit_price - $discount;
            } else {
                $base_price = $variant_info->unit_price;
            }
            $oldPrice = $variant_info->unit_price;
            $quantity = $variant_info->quantity;

            return response()->json(
                [
                    'success' => true,
                    'old' => $request->variant,
                    'new_variant' => $new_variant,
                    'base_price' => $base_price,
                    'oldPrice' => $oldPrice,
                    'quantity' => $quantity
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will load color variant images
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function colorVariantImages(Request $request)
    {
        try {
            $image_ids = ProductColorVariantImages::where('color_id', $request['color_id'])->where('product_id', $request['product_id'])->pluck('image');
            $images = [];
            $video_link = Product::where('id', $request['product_id'])->select('video_link', 'thumbnail_image')->first();
            if ($video_link->video_link != null) {
                $video = [];
                $video['type'] = 'video';
                $video['video_link'] = $video_link->video_link;
                $video['thumbnail'] = getFilePath($video_link->thumbnail_image);
                array_push($images, $video);
            }
            if (count($image_ids) > 0) {
                foreach ($image_ids as $id) {
                    $image['regular'] = getFilePath($id);
                    $image['zoom'] = getFilePath($id);
                    $image['type'] = 'image';
                    array_push($images, $image);
                }
                return response()->json(
                    [
                        'success' => true,
                        'images' => $images
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                    ]
                );
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }
    /**
     * Will return Related Products
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function relatedProducts(Request $request)
    {
        $products = $this->product_repository->relatedProducts($request->id);
        return new ProductCollection($products);
    }
    /**
     * Will return top selling products
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topSellingProducts(Request $request)
    {
        $products = $this->product_repository->topSellingProducts();
        return new ProductCollection($products);
    }
    /**
     * Will return product reviews
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productReviews(Request $request)
    {
        return new ProductReviewCollection($this->product_repository->productReviews($request, $request['product_id']));
    }
    /**
     * Will return product brands
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function brands()
    {
        try {
            $brands = ProductBrand::where('status', config('settings.general_status.active'))->latest()->get();
            return new BrandCollection($brands);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'status' => 500
                ]
            );
        }
    }
    /**
     * Will return categories
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories()
    {
        try {
            $categories = ProductCategory::where('status', config('settings.general_status.active'))->orderBy('id', 'ASC')->get();
            return new CategoryCollection($categories);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'status' => 500
                ]
            );
        }
    }
    /**
     * Will return only parent category
     * 
     */
    public function parentCategories()
    {
        try {
            $categories = ProductCategory::with('category_translations')->where('status', config('settings.general_status.active'))->where('parent', null)->orderBy('id', 'ASC')->get();
            return new ParentCategoryCollection($categories);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'status' => 500
                ]
            );
        }
    }
    /**
     * Will return mega categories
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function megaCategories()
    {
        $categories = Cache::remember('mega-categories', 60 * 60 * 24, function () {
            return  ProductCategory::with(['category_translations', 'childs'])
                ->whereNull('parent')
                ->select('id', 'name', 'permalink', 'icon', 'parent', 'status')
                ->where('status', config('settings.general_status.active'))
                ->orderBy('id', 'ASC')
                ->get();
        });

        return new CategoryCollection($categories);
    }
    /**
     * Will return category details
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categoryDetails(Request $request)
    {
        try {
            $category_info = ProductCategory::where('permalink', $request['permalink'])->first();
            return new SingleCategoryCollection($category_info);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'status' => 500
                ]
            );
        }
    }
    /**
     * Will return deals details
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dealsDetails(Request $request)
    {
        try {
            $deals_info = \Plugin\Flashdeal\Models\FlashDeal::where('permalink', $request['permalink'])->first();
            return new SingleDealsCollection($deals_info);
        } catch (\Exception $e) {

            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will return deals product
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dealsProducts(Request $request)
    {
        try {
            $products = Product::whereIn('id', FlashDealProducts::where('deal_id', $request['deal_id'])->pluck('product_id'))->paginate($request['perPage']);
            return new ProductCollection($products);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will return search suggestions
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchSuggestions(Request $request)
    {
        try {
            $category_items = [];
            $product_items = [];
            $categories = ProductCategory::where('name', "like", "%$request->search_key%")->where('status', config('settings.general_status.active'))->get()->take(5);
            $products = Product::where('name', "like", "%$request->search_key%")->where('status', config('settings.general_status.active'))->get()->take(10);
            $tags = ProductTags::where('name', "like", "%$request->search_key%")->where('status', config('settings.general_status.active'))->select('name', 'permalink')->get()->take(10);
            if (count($categories) > 0) {
                $category_items = new CategoryCollection($categories);
            }
            if (count($products) > 0) {
                $product_items = new ProductCollection($products);
            }

            return response()->json(
                [
                    'success' => true,
                    'categories' => $category_items,
                    'products' => $product_items,
                    'tags' => $tags
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    /**
     * Will store user search key
     * 
     * @param String $search_key
     * @return void 
     */
    public function storeUserSearchKey($key)
    {
        try {
            if ($key != null) {
                $search_keyword = new SearchKeyword;
                $search_keyword->key_word = $key;
                $search_keyword->ip = getUserIpAddr();
                $search_keyword->browser = get_browser_name();
                $search_keyword->os = get_operating_system();
                $search_keyword->save();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
