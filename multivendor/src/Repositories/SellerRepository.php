<?php

namespace Plugin\Multivendor\Repositories;

use Carbon\Carbon;
use Core\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Plugin\Multivendor\Models\SellerShop;
use Plugin\Refund\Models\OrderReturnRequest;
use Plugin\Multivendor\Models\SellerFollowers;
use Plugin\TlcommerceCore\Models\ProductReview;
use Plugin\TlcommerceCore\Models\ProductCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;

class SellerRepository
{

    /**
     * Will return seller list
     */
    public function sellerList($request)
    {
        $query = User::with(['shop' => function ($query) {
            $query->select(['id', 'seller_id', 'seller_phone', 'shop_slug', 'shop_name', 'shop_phone', 'logo', 'status']);
        }])
            ->where('user_type', config('tlecommercecore.user_type.seller'))
            ->select(['id', 'uid', 'name', 'email', 'image', 'status']);

        /**
         *Filter Query 
         */
        if ($request->has('search_key') && $request['search_key'] != null) {
            $query = $query->where('name', 'like', '%' . $request['search_key'] . '%')
                ->orWhere('email', 'like', '%' . $request['search_key'] . '%');
        }

        if ($request->has('seller_status') && $request['seller_status'] != null) {
            $query = $query->where('status',  $request['seller_status']);
        }


        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page != null && $per_page == 'all') {
            $seller = $query->orderBy('id', 'DESC')->paginate($query->get()->count())
                ->withQueryString();
        } else {
            $seller = $query->orderBy('id', 'DESC')->paginate($per_page)
                ->withQueryString();
        }
        return $seller;
    }

    /**
     * Will return seller details
     * 
     * @param Int $id
     * @return Collection
     */
    public function sellerDetails($id)
    {
        return  User::with(['shop' => function ($query) {
            $query->select(['id', 'seller_id', 'seller_phone', 'shop_slug', 'shop_name', 'shop_phone', 'logo', 'status', 'shop_address', 'shop_banner']);
        }])
            ->where('user_type', config('tlecommercecore.user_type.seller'))
            ->where('id', $id)
            ->select(['id', 'uid', 'name', 'email', 'image', 'status', 'created_at'])
            ->first();
    }

    /**
     * Will return active shop list
     *
     *@param Object $request
     *@return Collection
     */
    public function activeShops($request)
    {
        $query = SellerShop::with(['reviews' => function ($query) {
            $query->select('product_id', 'rating');
        }])
            ->select(['shop_name', 'shop_slug', 'shop_banner', 'seller_id', 'logo', 'id', 'status'])
            ->withCount(['products', 'orders'])
            ->where('status', config('settings.general_status.active'))
            ->orderBy('id', 'DESC');

        $query = $query->whereHas('seller', function ($q) {
            $q->where('status', config('settings.general_status.active'));
        });


        if ($request->has('seller_ids') && $request['seller_ids'] != null) {
            $seller_ids = explode(',', trim($request['seller_ids']));

            return $query->whereIn('seller_id', $seller_ids)->get();
        }

        //Filter by product brand
        if ($request->has('brand_id')) {
            $query = $query->whereHas('products', function ($q) use ($request) {
                $q->where('brand', $request['brand_id']);
            });
        }

        //Filter by product rating
        if ($request->has('rating') && $request['rating'] != null) {
            $query = $query->whereHas('reviews', function ($q) use ($request) {
                $q->select('product_id', DB::raw('avg(rating) as avg_rating'))
                    ->groupBy('product_id')
                    ->having('avg_rating', '>=', $request['rating']);
            });
        }


        //Filter by product category
        if ($request->has('category_id')) {
            $categories = [$request['category_id']];
            $sub_categories = ProductCategory::where('parent', $request['category_id'])->pluck('id')->toArray();
            $with_sub_category = array_merge($categories, $sub_categories);
            $sub_sub_categories = ProductCategory::whereIn('parent', $sub_categories)->pluck('id')->toArray();
            $with_sub_sub_category = array_merge($with_sub_category, $sub_sub_categories);

            $query = $query->whereHas('products.product_categories', function ($q) use ($with_sub_sub_category) {
                $q->whereIn('category_id', $with_sub_sub_category);
            });
        }

        //Sorting shop
        //sorting by newest items
        if ($request->has('sorting') && $request['sorting'] === 'newest') {
            $query = $query->orderBy('id', 'DESC');
        }
        //sorting by popular items
        if ($request->has('sorting') && $request['sorting'] === 'popular') {
            $query = $query->withCount('orders as number_of_order')
                ->orderBy('number_of_order', 'DESC');
        }
        return $query->paginate($request['perPage']);
    }

    /**
     * Will store seller information
     * 
     * @param Array $data
     * @return Bool
     */
    public function storeSeller($data)
    {
        try {
            DB::beginTransaction();
            $date = Carbon::now();
            $user_id = $date->format('y') . $date->format('m') . $date->format('d');
            $seller = new User();
            $seller->name = $data['name'];
            $seller->email = $data['email'];
            $seller->user_type = config('tlecommercecore.user_type.seller');
            $seller->status = getGeneralSetting('seller_auto_verification') == config('settings.general_status.active') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $seller->password = Hash::make($data['password']);
            $seller->save();
            $seller->uid = "SELLER-" . $seller->id . $user_id;
            $seller->update();
            $this->storeSellerShop($seller->id, $data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will store seller shop
     * 
     * @param Int $seller_id
     * @param Array $data
     * @return void
     */
    public function storeSellerShop($seller_id, $data)
    {
        $seller_shop = new SellerShop();
        $seller_shop->seller_id = $seller_id;
        $seller_shop->seller_phone = $data['phone'];
        $seller_shop->shop_name = $data['shop_name'];
        $seller_shop->shop_slug = Str::slug($data['shop_url']);
        $seller_shop->shop_phone = $data['shop_phone'];
        $seller_shop->status = getGeneralSetting('seller_auto_verification') == config('settings.general_status.active') ? config('settings.general_status.active') : config('settings.general_status.in_active');
        $seller_shop->save();
    }
    /**
     * Will check seller shop slug
     * 
     * @param String $slug
     * @return bool
     */
    public function checkSellerShopAvailability($slug)
    {
        return SellerShop::where('shop_slug', $slug)->doesntExist();
    }

    /**
     * Will update seller status
     * 
     * @param Int $seller_id
     * @return bool
     */
    public function updateSellerStatus($seller_id)
    {
        try {
            DB::beginTransaction();
            $seller = User::find($seller_id);
            $status = $seller->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $seller->status = $status;
            $seller->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will update seller status
     * 
     * @param Int $shop_id
     * @return bool
     */
    public function updateSellerShopStatus($shop_id)
    {
        try {
            DB::beginTransaction();
            $shop = SellerShop::find($shop_id);
            $status = $shop->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $shop->status = $status;
            $shop->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will delete seller
     * 
     * @param Int $seller_id
     * @return bool
     */
    public function deleteSeller($seller_id)
    {
        try {
            DB::beginTransaction();
            $seller = User::findOrFail($seller_id);
            $seller->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will update seller shop
     * 
     * @param Array $data
     * @return bool
     */
    public function updateSellerShop($data)
    {
        try {
            DB::beginTransaction();
            $seller_shop = SellerShop::find($data['id']);
            $seller_shop->shop_name = $data['shop_name'];
            $seller_shop->shop_slug = $data['shop_slug'];
            $seller_shop->shop_phone = $data['shop_phone'];
            $seller_shop->logo = $data['shop_logo'];
            $seller_shop->shop_banner = $data['shop_banner'];
            $seller_shop->shop_address = $data['shop_address'];
            $seller_shop->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will update seller shop seo information
     * 
     * @param Array $data
     * @return bool
     */
    public function updateSellerShopSeoInfo($data)
    {
        try {
            DB::beginTransaction();
            $seller_shop = SellerShop::find($data['id']);
            $seller_shop->meta_title = $data['meta_title'];
            $seller_shop->meta_image = $data['meta_image'];
            $seller_shop->meta_description = $data['meta_description'];
            $seller_shop->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will return shop details by slug
     * 
     * @param String $slug
     * @return Collection
     */
    public function ShopDetailsBySlug($slug)
    {
        return SellerShop::with(['products', 'reviews'])
            ->where('shop_slug', $slug)
            ->select(['id', 'shop_name', 'shop_phone', 'logo', 'shop_banner', 'shop_address', 'seller_id'])
            ->first();
    }

    /**
     * Will return shop details by slug
     * 
     * @param String $slug
     * @return Collection
     */
    public function activeShopDetailsBySlug($slug)
    {
        $query = SellerShop::with(['products', 'reviews', 'seller'])
            ->where('shop_slug', $slug)
            ->select(['id', 'shop_name', 'shop_phone', 'logo', 'shop_banner', 'shop_address', 'seller_id'])
            ->where('status', config('settings.general_status.active'));

        $query = $query->whereHas('seller', function ($q) {
            $q->where('status', config('settings.general_status.active'));
        });

        return $query->first();
    }

    /**
     * Will return seller id
     * 
     * @param String $shop_slug
     * @return Int 
     */
    public function sellerIdByShopSlug($shop_slug)
    {
        $shop = SellerShop::where('shop_slug', $shop_slug)->select(['seller_id', 'shop_slug'])->first();
        return $shop != null ? $shop->seller_id : null;
    }
    /**
     * Will return shop product
     * 
     * @param $query
     * @return Collection
     */
    public function shopProducts($query, $param = 'new')
    {
        if ($param == 'new') {
            return  $query->orderBy('id', 'DESC')
                ->take(7)
                ->get();
        }
        if ($param == 'featured') {
            return $query->where('is_featured', config('settings.general_status.active'))
                ->take(7)
                ->get();
        }
        if ($param == 'top_selling') {
            return  $query = $query->withCount('orders as number_of_order')
                ->orderBy('number_of_order', 'desc')
                ->take(7)
                ->get();
        }
    }

    /**
     * Will return seller review summary
     */
    public function sellerReviewSummary($seller_id)
    {

        $query = DB::table('tl_com_product_reviews')
            ->join('tl_com_products', 'tl_com_products.id', '=', 'tl_com_product_reviews.product_id')
            ->where('tl_com_products.supplier', $seller_id)
            ->where('tl_com_product_reviews.status', config('settings.general_status.active'))
            ->select('tl_com_product_reviews.rating', 'tl_com_products.supplier');

        $summary['avg_review'] = $query->avg('rating') != null ? number_format($query->avg('rating'), 2) : 0;

        $total_reviews = $query->count();
        $summary['total_reviews'] = $total_reviews;

        $one_star = 0;
        $two_star = 0;
        $three_star = 0;
        $four_star = 0;
        $five_star = 0;
        $reviews = $query->get();
        foreach ($reviews as $review) {
            if ($review->rating == 5) {
                $five_star += 1;
            }
            if ($review->rating == 4) {
                $four_star += 1;
            }
            if ($review->rating == 3) {
                $three_star += 1;
            }
            if ($review->rating == 2) {
                $two_star += 1;
            }
            if ($review->rating == 1) {
                $one_star += 1;
            }
        }

        $summary['one'] = $one_star;
        $summary['two'] = $two_star;
        $summary['three'] = $three_star;
        $summary['four'] = $four_star;
        $summary['five'] = $five_star;

        $positive_rating = $total_reviews > 0 ? ($five_star + $four_star + $three_star) * 100 / $total_reviews : 0;
        $summary['positive_ratings'] = number_format($positive_rating, 0);


        return $summary;
    }

    /**
     * Will return single  product reviews
     * 
     * @param Object $request
     * @param Int $Supplier
     * @return Collections
     */
    public function sellerAllReviews($request, $supplier)
    {
        $query = ProductReview::with('product')->where('status', config('settings.general_status.active'));


        $query = $query->whereHas('product', function (Builder $query) use ($supplier) {
            $query->where('supplier', $supplier);
        });

        if ($request->has('sorting')) {

            if ($request['sorting'] == 'DESC') {
                $query = $query->orderBy('id', 'DESC');
            }
            if ($request['sorting'] == 'RDESC') {
                $query = $query->orderBy('rating', 'DESC');
            }

            if ($request['sorting'] == 'RASC') {
                $query = $query->orderBy('rating', 'ASC');
            }
        }

        return $query->paginate($request['perPage']);
    }
    /**
     * Will store new  follow
     * 
     * @param Int $seller_id
     * @param Int $customer_id
     * @return bool
     */
    public function storeSellerFollower($seller_id, $customer_id)
    {
        try {
            DB::beginTransaction();
            $follower = new SellerFollowers();
            $follower->seller_id = $seller_id;
            $follower->customer_id = $customer_id;
            $follower->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will return seller refunds list
     * 
     * @param Object $request
     * @return Collection 
     */
    public function refundsList($request, $seller_id)
    {
        try {
            $query = OrderReturnRequest::with(['customer', 'product', 'order']);

            if ($request->has('payment_status') && $request['payment_status']) {
                $query = $query->where('refund_status', $request['payment_status']);
            }

            if ($request->has('return_status') && $request['return_status']) {
                $query = $query->where('return_status', $request['return_status']);
            }

            if ($request->has('search') && $request['search'] != null) {
                $query = $query->whereHas('order', function (Builder $query) use ($request) {
                    $query->where('order_code', 'like', '%' . $request['search'] . '%');
                });
            }

            $query = $query->whereHas('product', function (Builder $query) use ($seller_id) {
                $query->where('supplier', $seller_id);
            });

            $refunds = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString()->through(function ($item) {
                $item->id = $item->id;
                $item->code = $item->refund_code;
                $item->quantity = $item->quantity;
                $item->total_amount = $item->total_amount;
                $item->total_refund_amount = $item->total_refund_amount;
                $item->payment_status = $item->refund_status;
                $item->return_status = $item->return_status;
                $item->read_at = $item->read_at;
                $item->created_at = $item->created_at;
                $item->order_code = $item->order->order_code;
                $item->order_id = $item->order->id;
                $item->customer_name = $item->customer->name;
                $item->customer_id = $item->customer_id;
                $item->product_name = $item->product->name;
                return $item;
            });

            return $refunds;
        } catch (\Exception $e) {
            return [];
        } catch (\Error $e) {
            return [];
        }
    }
}
