<?php

namespace Plugin\TlcommerceCore\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\ProductSeo;
use Plugin\TlcommerceCore\Models\ProductTags;
use Plugin\TlcommerceCore\Models\ProductReview;
use Plugin\TlcommerceCore\Models\ProductHasTags;
use Plugin\TlcommerceCore\Models\ProductCategory;
use Plugin\TlcommerceCore\Models\ProductCodState;
use Plugin\TlcommerceCore\Models\ProductCodCities;
use Plugin\TlcommerceCore\Models\ProductHasColors;
use Plugin\TlcommerceCore\Models\ProductHasChoices;
use Plugin\TlcommerceCore\Models\ProductTranslation;
use Plugin\TlcommerceCore\Models\SingleProductPrice;
use Plugin\TlcommerceCore\Models\ProductCodCountries;
use Plugin\TlcommerceCore\Models\ProductShippingInfo;
use Plugin\TlcommerceCore\Models\VariantProductPrice;
use Plugin\TlcommerceCore\Models\ProductGalleryImages;
use Plugin\TlcommerceCore\Models\ProductHasCategories;
use Plugin\TlcommerceCore\Models\CollectionHasProducts;
use Plugin\TlcommerceCore\Models\ProductHasChoiceOption;
use Plugin\TlcommerceCore\Models\ShippingProfileProducts;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;
use Plugin\TlcommerceCore\Models\ProductColorVariantImages;
use Plugin\TlcommerceCore\Models\ProductVariationCombination;
use Plugin\TlcommerceCore\Repositories\EcommerceNotification;

class ProductRepository
{
    /**
     *Will return product settings
     */
    public function productConfiguration()
    {
        try {
            $data = [
                'enable_product_reviews'              => SettingsRepository::getEcommerceSetting('enable_product_reviews'),
                'enable_product_star_rating'          => SettingsRepository::getEcommerceSetting('enable_product_star_rating'),
                'required_product_star_rating'        => SettingsRepository::getEcommerceSetting('required_product_star_rating'),
                'verified_customer_on_product_review' => SettingsRepository::getEcommerceSetting('verified_customer_on_product_review'),
                'only_varified_customer_left_review'  => SettingsRepository::getEcommerceSetting('only_varified_customer_left_review'),
                'enable_product_compare'              => SettingsRepository::getEcommerceSetting('enable_product_compare'),
                'enable_product_discount'             => SettingsRepository::getEcommerceSetting('enable_product_discount'),
            ];

            return $data;
        } catch (\Exception $e) {
            return NULL;
        } catch (\Error $e) {
            return NULL;
        }
    }
    /**
     * Will return product list
     * 
     *@param Object $request 
     *@return Collections
     */
    public function productManagement($request, $seller_id = null, $product_owner = null)
    {

        $query = Product::with(['seller', 'product_translations', 'variations', 'reviews', 'single_price', 'unit_info']);


        //seller products
        if ($product_owner != null && $product_owner == 'seller') {
            $query = $query->whereHas('seller', function ($q) {
                $q->where('user_type', config('tlecommercecore.user_type.seller'));
            });
        }
        //In house products
        if ($product_owner != null && $product_owner == 'inhouse') {
            $query = $query->whereHas('seller', function ($q) {
                $q->whereNull('user_type')
                    ->orWhere('user_type', config('tlecommercecore.user_type.admin'));
            });
        }


        //Specific seller products
        if ($seller_id != null) {
            $query = $query->where('supplier', $seller_id);
        }


        if ($request->has('search_key') && $request['search_key'] != null) {
            $query = $query->where('name', 'like', '%' . $request['search_key'] . '%');
        }

        if ($request->has('product_status') && $request['product_status'] != null) {
            $query = $query->where('status',  $request['product_status']);
        }

        if ($request->has('has_variation') && $request['has_variation'] != null) {
            $query = $query->where('has_variant',  $request['has_variation']);
        }

        if ($request->has('discount') && $request['discount'] != null) {

            if ($request['discount'] == config('settings.general_status.active')) {
                $query = $query->where('discount_amount', '>', 0);
            } else {
                $query = $query->where('discount_amount', '=', 0)->orWhere('discount_amount', '=', null);
            }
        }

        if ($request->has('product_featured') && $request['product_featured'] != null) {
            $query = $query->where('is_featured',  $request['product_featured']);
        }

        $query = $query->orderBy('id', 'DESC');

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page != null && $per_page == 'all') {
            $products = $query->paginate($query->get()->count())
                ->withQueryString();
        } else {
            $products = $query->paginate($per_page)
                ->withQueryString();
        }
        return $products;
    }

    /**
     * Will return product list
     *@return Collections
     */
    public function productList()
    {

        return Product::orderBy('id', 'DESC')->get();
    }
    /**
     * Will return  active product list
     *@return Collections
     */
    public function activeProducts()
    {
        return Product::orderBy('id', 'DESC')->where('status', config('settings.general_status.active'))->get();
    }
    /**
     * Will return product details
     * 
     * @param Int $id
     * @return Collection
     */
    public function productDetails($id)
    {
        return Product::findOrFail($id);
    }
    /**
     * Will return product details
     * 
     * @param Int $id
     * @return Collection
     */
    public function editProduct($id, $seller_id = null)
    {
        $query = Product::with([
            'product_cats' => function ($q) {
                $q->select('tl_com_categories.id', 'tl_com_categories.name');
            },
            'brand_info.brand_translations',
            'tagItems' => function ($q) {
                $q->select('tl_com_product_tags.id', 'tl_com_product_tags.name');
            },
            'codCountryList' => function ($q) {
                $q->select('tl_countries.id', 'tl_countries.name', 'tl_countries.code');
            },
            'codStateList' => function ($q) {
                $q->select('tl_com_state.id', 'tl_com_state.name');
            },
            'codCityList' => function ($q) {
                $q->select('tl_com_cities.id', 'tl_com_cities.name');
            },
            'color_choices',
            'choices',
            'choice_options',
            'single_price',
            'variations',
            'product_seo',
            'shipping_info'
        ]);

        if ($seller_id != null) {
            $query = $query->where('supplier', $seller_id);
        }
        $product = $query->findOrFail($id);

        return $product;
    }
    /**
     * Will return single  product reviews
     * 
     * @param Object $request
     * @param Int $product_id
     * @return Collections
     */
    public function productReviews($request, $product_id)
    {
        $query = ProductReview::where('product_id', $product_id)->where('status', config('settings.general_status.active'));
        if ($request->has('sorting')) {
            if ($request['sorting'] == 'DESC') {
                $query = $query->orderBy('id', 'DESC');
            } else if ($request['sorting'] == 'ASC') {
                $query = $query->orderBy('rating', 'ASC');
            } else if ($request['sorting'] == 'DESC') {
                $query = $query->orderBy('rating', 'DESC');
            } else {
                $query = $query;
            }
        }

        return $query->paginate($request['perPage']);
    }

    /**
     * Will return all reviews
     * 
     * @param Object $request
     * @return Collections
     * 
     */
    public function reviewList($request, $seller_id = null)
    {
        try {
            $data = [
                'tl_com_product_reviews.id',
                'tl_com_product_reviews.status',
                'tl_com_product_reviews.created_at',
                'tl_com_customers.name as customer_name',
                'tl_com_customers.id as customer_id',
                'tl_com_orders.order_code as order_code',
                'tl_com_orders.id as order_id',
                'tl_com_product_reviews.rating',
                'tl_com_products.name as product_name',
                'tl_com_products.id as product_id'
            ];

            $query = ProductReview::query()
                ->join('tl_com_customers', 'tl_com_customers.id', '=', 'tl_com_product_reviews.customer_id')
                ->join('tl_com_products', 'tl_com_products.id', '=', 'tl_com_product_reviews.product_id')
                ->join('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_product_reviews.order_id')
                ->orderBy('tl_com_product_reviews.id', 'DESC')
                ->select($data);

            if ($seller_id != null) {
                $query = $query->where('tl_com_products.supplier', $seller_id);
            }

            if ($request->has('search') && $request['search'] != null) {
                $query = $query->where('tl_com_customers.name', 'like', '%' . $request['search'] . '%')
                    ->orWhere('tl_com_products.name', 'like', '%' . $request['search'] . '%')
                    ->orWhere('tl_com_orders.order_code', 'like', '%' . $request['search'] . '%');
            }

            if ($request->has('rating') && $request['rating'] != null) {
                $query = $query->where('tl_com_product_reviews.rating', $request['rating']);
            }

            if ($request->has('status') && $request['status'] != null) {
                $query = $query->where('tl_com_product_reviews.status', $request['status']);
            }
            $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

            if ($per_page != null && $per_page == 'all') {
                $reviews = $query->paginate($query->get()->count())
                    ->withQueryString();
            } else {
                $reviews = $query->paginate($per_page)
                    ->withQueryString();
            }
            return $reviews;
        } catch (\Exception $e) {
            return [];
        }
    }
    /**
     * Will return customer wise reviews list
     * 
     * @param Int $customer id
     * @return Collection
     */
    public function customerReviewList($customer_id)
    {
        try {
            $data = [
                'tl_com_product_reviews.id',
                'tl_com_product_reviews.status',
                'tl_com_product_reviews.created_at',
                'tl_com_customers.id as customer_id',
                'tl_com_orders.order_code as order_code',
                'tl_com_orders.id as order_id',
                'tl_com_product_reviews.rating',
                'tl_com_products.name as product_name',
                'tl_com_products.id as product_id'
            ];

            $query = ProductReview::query()
                ->join('tl_com_customers', 'tl_com_customers.id', '=', 'tl_com_product_reviews.customer_id')
                ->join('tl_com_products', 'tl_com_products.id', '=', 'tl_com_product_reviews.product_id')
                ->join('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_product_reviews.order_id')
                ->orderBy('tl_com_product_reviews.id', 'DESC')
                ->select($data);


            $reviews = $query->where('tl_com_customers.id', $customer_id)->get();
            return $reviews;
        } catch (\Exception $e) {
            return [];
        }
    }
    /**
     * Will update review status
     * 
     * @param Int $review_id
     * @return bool
     */
    public function updateReviewStatus($review_id)
    {
        try {
            $review = ProductReview::find($review_id);
            if ($review != null) {
                $updated_status = $review->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
                $review->status = $updated_status;
                $review->save();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will return review details
     * 
     * @param Int $id
     * @return collection
     */
    public function productReviewDetails($id)
    {
        return ProductReview::find($id);
    }
    /**
     * Will delete product review
     * 
     * @param Int $id
     * @return bool
     */
    public function productReviewDelete($id)
    {
        try {
            $review = ProductReview::find($id);
            if ($review != null) {
                $review->delete();

                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Update product Status
     * 
     * @param Int $id
     * @return boolean
     */
    public function changeStatus($id, $new_status = null)
    {
        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);
            if ($new_status != null) {
                $product->status = $new_status;
            } else {
                $status = config('settings.general_status.active');
                if ($product->status == config('settings.general_status.active')) {
                    $status = config('settings.general_status.in_active');
                }
                $product->status = $status;
            }

            $product->save();
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
     * Update product Status
     * 
     * @param Int $id
     * @return boolean
     */
    public function changeApprovalStatus($id, $updated_status = null)
    {
        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);

            if ($updated_status != null) {
                $product->is_approved = $updated_status;
            } else {
                $status = config('settings.general_status.active');
                if ($product->is_approved == config('settings.general_status.active')) {
                    $status = config('settings.general_status.in_active');
                }
                $product->is_approved = $status;
            }

            $product->save();
            DB::commit();

            //Send Notification to seller
            $message = "";
            if ($product->is_approved == config('settings.general_status.active')) {
                $message = "Your product has been approved";
            }
            if ($product->is_approved == config('settings.general_status.in_active')) {
                $message = "Your product has been removed from approval items";
            }

            EcommerceNotification::sendUpdateProductApprovalStatusNotificationToSeller($product->supplier, $message);

            return $product->is_approved;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will update product featured status
     * 
     * @param Int $id
     * @return bool
     */
    public function updateFeaturedStatus($id, $new_status = null)
    {
        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);
            if ($new_status != null) {
                $product->is_featured = $new_status;
            } else {
                $status = config('settings.general_status.active');
                if ($product->is_featured == config('settings.general_status.active')) {
                    $status = config('settings.general_status.in_active');
                }
                $product->is_featured = $status;
            }

            $product->save();
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
     * Will delete a product
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteProduct($id)
    {
        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);

            $product->color_images()->delete();
            $product->variations()->delete();
            $product->variant_combination()->delete();
            $product->color_images()->delete();
            $product->single_price()->delete();

            $product->product_translations()->delete();
            $product->shipping_info()->delete();
            $product->gallery_images()->delete();
            $product->tags()->delete();
            $product->cod_countries()->delete();
            $product->cod_states()->delete();
            $product->cod_cities()->delete();
            $product->delete();

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
     * Will store new product
     * 
     * @param Object $request
     * @return bool
     */
    public function storeNewProduct($request)
    {
        try {
            $is_authenthic = $request->has('is_authenthic') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $has_warranty = $request->has('has_warranty') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $has_replacement_warranty = $request->has('replacement_warranty') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $is_active_cod = $request->has('cash_on_delivery') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $is_active_attatchment = $request->has('is_active_attatchment') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $is_refundable = $request->has('is_refundable') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $is_featured = $request->has('is_featured') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $free_shipping = $request->has('free_shipping') ? config('settings.general_status.active') : config('settings.general_status.in_active');

            $is_approved = config('settings.general_status.active');
            if (isActivePluging('multivendor') && auth()->user()->user_type == config('tlecommercecore.user_type.seller') && getGeneralSetting('product_auto_approve') != config('settings.general_status.active')) {
                $is_approved = config('settings.general_status.in_active');
            }

            DB::beginTransaction();
            $product = new Product;
            $product->name = $request['name'];
            $product->summary = $request['summary'];
            $product->brand = $request['brand'];
            $product->description = $request['description'];
            $product->product_type  = config('tlecommercecore.product_type.physical_product');
            $product->has_variant  = $request['product_type'];
            $product->permalink = $request['permalink'];
            $product->unit = $request['unit'];
            $product->conditions = $request['condition'];

            //Product Shipping Cost
            $product->is_active_free_shipping = $free_shipping;
            $product->shipping_cost = $request->has('product_shipping_cost') ? $request['product_shipping_cost'] : 0;
            $product->is_apply_multiple_qty_shipping_cost = $request->has('is_product_quantity_multiple') ? config('settings.general_status.active') : config('settings.general_status.in_active');

            //Product Tax
            $product->is_enable_tax = $request->has('taxable') ? $request['taxable'] : config('settings.general_status.in_active');
            $product->tax_profile = $request->has('tax_profile') ? $request['tax_profile'] : null;

            //Discount
            $product->discount_type = $request['discount_amount_type'];
            $product->discount_amount = $request['discount_amount'];

            //Images & Video
            $product->pdf_specifications = $request['pdf_specification'];
            $product->thumbnail_image = $request['thumbnail_image'];
            $product->video_link = $request['video'];

            //Quantity
            $product->max_item_on_purchase = $request['max_purchase_qry'];
            $product->min_item_on_purchase = $request['min_purchase_qty'];
            $product->low_stock_quantity_alert = $request['qty_alert'];

            //Featured and Authentic
            $product->is_authentic = $is_authenthic;
            $product->is_featured = $is_featured;

            //warranty
            $product->has_warranty = $has_warranty;
            $product->has_replacement_warranty = $has_replacement_warranty;
            $product->warrenty_days = $request['warranty_day'];

            $product->is_refundable = $is_refundable;
            $product->shipping_location_type = $request['shipping_location'];
            $product->is_active_cod = $is_active_cod;
            $product->cod_location_type = $request['cod_location'];
            $product->is_active_attatchment = $is_active_attatchment;
            $product->attatchment_name = $request['attatchment_name'];
            $product->status = $request['status'];
            $product->is_approved = $is_approved;
            $product->supplier = $request->has('seller_id') && $request['seller_id'] != null ? $request['seller_id'] : getSupperAdminId();
            $product->save();

            //store product categories
            if ($request['categories'] != null) {
                $this->storeProductCategories($product->id, $request);
            }

            //store product seo
            $this->storeProductSeo($product->id, $request);

            //store product price
            if ($request['product_type'] == config('tlecommercecore.product_variant.single')) {
                $this->storeSingleProductPrice($product->id, $request);
            } else {
                if ($request->has('variations')) {
                    $this->storeProductVariantPrice($product->id, $request);
                    //store color variant  image
                    if ($request->has('selected_colors')) {
                        $this->storeColorVariantImages($request, $product->id);
                    }
                }
            }

            //Store shipping info
            if (getEcommerceSetting('shipping_option') == config('tlecommercecore.shipping_cost_options.profile_wise_rate')) {
                $this->storeProductShippingInfo($product->id, $request);
            }


            //Store cod areas
            if ($request->has('cash_on_delivery') && $request['cod_location'] == config('tlecommercecore.cod_location.custom')) {
                $this->storeProductCodAreas($product->id, $request);
            }
            //Store product tags
            $this->storeProductTags($product->id, $request);

            //store product gallery image
            if ($request->has('gallery_images') && $request['gallery_images'] != null) {
                $this->storeProductGalleryImages($product->id, $request);
            }

            //store product collection
            if ($request->has('product_colletions') && $request['product_colletions'] != null) {
                $this->storeCollectionProducts($product->id, $request);
            }

            //Set shipping  profile of product
            if ($request->has('shipping_profile') && $request['shipping_profile'] != null) {
                $product_profile = new ShippingProfileProducts();
                $product_profile->profile_id = $request['shipping_profile'];
                $product_profile->product_id = $product->id;
                $product_profile->save();
            }

            //Send seller product adding notification to admin
            if (auth()->user()->user_type == config('tlecommercecore.user_type.seller')) {
                EcommerceNotification::sendSellerCreateProductNotificationToAdmin($product->id);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        } catch (\Error $e) {
            DB::rollback();
            return false;
        }
    }
    /**
     * Will update product
     * 
     * @param Object $request
     * 
     * @return bool
     */
    public function updateProduct($request)
    {
        try {
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $product_translation = ProductTranslation::firstOrNew(['product_id' => $request['id'], 'lang' => $request['lang']]);
                $product_translation->name = $request['name'];
                $product_translation->summary = $request['summary'];
                $product_translation->description = $request['description'];
                $product_translation->save();
            } else {
                $is_authenthic = $request->has('is_authenthic') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $has_warranty = $request->has('has_warranty') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $has_replacement_warranty = $request->has('replacement_warranty') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $is_active_cod = $request->has('cash_on_delivery') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $is_active_attatchment = $request->has('is_active_attatchment') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $is_refundable = $request->has('is_refundable') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $is_featured = $request->has('is_featured') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $free_shipping = $request->has('free_shipping') ? config('settings.general_status.active') : config('settings.general_status.in_active');

                DB::beginTransaction();
                $product = Product::findOrFail($request['id']);
                $product->name = $request['name'];
                $product->summary = $request['summary'];
                $product->brand = $request['brand'];
                $product->description = $request['description'];
                $product->product_type  = config('tlecommercecore.product_type.physical_product');
                $product->has_variant  = $request['product_type'];
                $product->permalink = $request['permalink'];
                $product->unit = $request['unit'];
                $product->conditions = $request['condition'];

                //Product Shipping Cost
                $product->is_active_free_shipping = $free_shipping;
                $product->shipping_cost = $request->has('product_shipping_cost') ? $request['product_shipping_cost'] : 0;
                $product->is_apply_multiple_qty_shipping_cost = $request->has('is_product_quantity_multiple') ? config('settings.general_status.active') : config('settings.general_status.in_active');

                //Product Tax
                $product->is_enable_tax = $request->has('taxable') ? $request['taxable'] : config('settings.general_status.in_active');
                $product->tax_profile = $request->has('tax_profile') ? $request['tax_profile'] : null;

                //discount
                $product->discount_type = $request['discount_amount_type'];
                $product->discount_amount = $request['discount_amount'];

                //Images & Video
                $product->pdf_specifications = $request['pdf_specification'];
                $product->thumbnail_image = $request['thumbnail_image'];
                $product->video_link = $request['video'];

                //Inventory & Quantity
                $product->max_item_on_purchase = $request['max_purchase_qry'];
                $product->min_item_on_purchase = $request['min_purchase_qty'];
                $product->low_stock_quantity_alert = $request['qty_alert'];
                //warranty
                $product->has_warranty = $has_warranty;
                $product->has_replacement_warranty = $has_replacement_warranty;
                $product->warrenty_days = $request['warranty_day'];

                $product->is_authentic = $is_authenthic;
                $product->is_featured = $is_featured;
                $product->is_refundable = $is_refundable;

                $product->shipping_location_type = $request['shipping_location'];
                $product->is_active_cod = $is_active_cod;
                $product->cod_location_type = $request['cod_location'];
                $product->is_active_attatchment = $is_active_attatchment;
                $product->attatchment_name = $request['attatchment_name'];
                $product->status = $request['status'];
                $product->save();

                // store product categories
                $product->product_categories()->delete();
                if ($request['categories'] != null) {
                    $this->storeProductCategories($product->id, $request);
                }

                //store product seo
                $this->storeProductSeo($product->id, $request);

                //store product price
                if ($request['product_type'] == config('tlecommercecore.product_variant.single')) {
                    $this->storeSingleProductPrice($product->id, $request);
                } else {
                    if ($request->has('variations')) {
                        $this->storeProductVariantPrice($product->id, $request);
                        //store color variant  image
                        if ($request->has('selected_colors')) {
                            $this->storeColorVariantImages($request, $product->id);
                        }
                    }
                }

                //Store shipping info
                if (getEcommerceSetting('shipping_option') == config('tlecommercecore.shipping_cost_options.profile_wise_rate')) {
                    $this->storeProductShippingInfo($product->id, $request);
                }


                //Store cod areas
                if ($request->has('cash_on_delivery') && $request['cod_location'] == config('tlecommercecore.cod_location.custom')) {
                    $this->storeProductCodAreas($product->id, $request);
                }

                //Store product tags
                $product->tags()->delete();
                $this->storeProductTags($product->id, $request);

                //store product gallery image
                $product->gallery_images()->delete();
                if ($request->has('gallery_images') && $request['gallery_images'] != null) {
                    $this->storeProductGalleryImages($product->id, $request);
                }
                //store product collection
                CollectionHasProducts::where('product_id', $product->id)->delete();
                $this->storeCollectionProducts($product->id, $request);

                //Set shipping  profile of product
                if ($request->has('shipping_profile') && $request['shipping_profile'] != null) {
                    ShippingProfileProducts::where('product_id', $product->id)->delete();
                    $product_profile = ShippingProfileProducts::firstOrCreate(['product_id' => $product->id]);
                    $product_profile->profile_id = $request['shipping_profile'];
                    $product_profile->save();
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        } catch (\Error $e) {
            DB::rollback();
            return false;
        }
    }
    /**
     * Will update product discount
     * 
     * @param Object $request
     * @return bool
     */
    public function updateProductDiscount($request, $id = null, $discount = null)
    {
        try {
            DB::beginTransaction();
            if ($id != null) {
                $product = Product::find($id);
                if ($product != null) {
                    $product->discount_amount = $discount;
                    $product->save();
                } else {
                    DB::rollBack();
                    return false;
                }
            } else {
                $product = Product::find($request['id']);
                if ($product != null) {
                    $product->discount_type = $request['discount_amount_type'];
                    $product->discount_amount = $request['discount_amount'];
                    $product->save();
                } else {
                    DB::rollBack();
                    return false;
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will update product Price
     * 
     * @param Object $request
     * @return bool
     */
    public function updateProductPrice($request)
    {
        try {
            DB::beginTransaction();
            if ($request['has_variant'] == config('tlecommercecore.product_variant.single')) {
                $single_product_price = SingleProductPrice::where('product_id', $request['id'])->first();
                $single_product_price->purchase_price = $request['purchase_price'];
                $single_product_price->unit_price = $request['unit_price'];
                $single_product_price->save();
            } else {
                if (count($request['variations'])) {
                    foreach ($request['variations'] as $variation) {
                        $variant_price = VariantProductPrice::find($variation['id']);
                        if ($variant_price != null) {
                            $variant_price->purchase_price = $variation['purchase_price'];
                            $variant_price->unit_price = $variation['unit_price'];
                            $variant_price->save();
                        }
                    }
                } else {
                    DB::rollBack();
                    return false;
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will update product Stock
     * 
     * @param Object $request
     * @return bool
     */
    public function updateProductStock($request)
    {
        try {
            DB::beginTransaction();
            if ($request['has_variant'] == config('tlecommercecore.product_variant.single')) {
                $single_product_price = SingleProductPrice::where('product_id', $request['id'])->first();
                $single_product_price->quantity = $request['quantity'];
                $single_product_price->save();
            } else {
                if (count($request['variations'])) {
                    foreach ($request['variations'] as $variation) {
                        $variant_price = VariantProductPrice::find($variation['id']);
                        if ($variant_price != null) {
                            $variant_price->quantity = $variation['quantity'];
                            $variant_price->save();
                        }
                    }
                } else {
                    DB::rollBack();
                    return false;
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will store product category
     * 
     * @param Int $product_id
     * @param Object $request
     * @return void
     */
    public function storeProductCategories($product_id, $request)
    {
        foreach ($request['categories'] as $category) {
            $p_cat = ProductHasCategories::firstOrCreate(['product_id' => $product_id, 'category_id' => $category]);
            $p_cat->save();
        }
    }
    /**
     * Will store product Seo
     * 
     * @param Int $id
     * @param Object $request
     * @return void
     */
    public function storeProductSeo($id, $request)
    {
        $seo = ProductSeo::firstOrCreate(['product_id' => $id]);
        $seo->meta_title = $request['meta_title'];
        $seo->meta_description = $request['meta_description'];
        $seo->meta_image = $request['meta_image'];
        $seo->save();
    }
    /**
     * Will store single product Price
     * 
     * @param Int $id
     * @param Object $request
     * @return void
     */
    public function storeSingleProductPrice($id, $request)
    {
        $price = SingleProductPrice::firstOrCreate(['product_id' => $id]);
        $price->sku = $request['sku'];
        $price->purchase_price = $request['purchase_price'];
        $price->unit_price = $request['unit_price'];
        $price->quantity = $request['quantity'];
        $price->save();
    }
    /**
     * Will store product variant and variant price
     * 
     * @param Int $product_id
     * @param Object $request
     * @return void
     */
    public function storeProductVariantPrice($product_id, $request)
    {
        $product = Product::findOrFail($product_id);

        $product->variant_combination()->delete();
        $product->variations()->delete();
        $product->choices()->delete();
        $product->choice_options()->delete();
        $product->color_choices()->delete();

        foreach ($request->variations as $variations) {
            $v_price = VariantProductPrice::firstOrCreate(['product_id' => $product_id, 'variant' => trim($variations['code'], '/')]);
            $v_price->purchase_price = $variations['purchase_price'];
            $v_price->unit_price = $variations['unit_price'];
            $v_price->sku = $variations['sku'];
            $v_price->quantity = $variations['quantity'];
            $v_price->save();

            foreach (array_filter(explode("/", $variations['code'])) as $combination) {
                if (explode(":", $combination)[0] == 'color') {
                    $color_id = explode(":", $combination)[1];
                    $attribute_id = null;

                    $product_has_color = ProductHasColors::firstOrCreate(['product_id' => $product_id, 'color_id' => explode(":", $combination)[1]]);
                    $product_has_color->save();
                } else {
                    $color_id = null;
                    $attribute_id =  explode(":", $combination)[0];

                    $choice = ProductHasChoices::firstOrCreate(['product_id' => $product_id, 'choice_id' => $attribute_id]);
                    $choice->save();

                    $choice_option = ProductHasChoiceOption::firstOrCreate(['product_id' => $product_id, 'choice_id' => explode(":", $combination)[0], 'option_id' => explode(":", $combination)[1]]);
                    $choice_option->save();
                }
                $p_variation_comb                         = new ProductVariationCombination;
                $p_variation_comb->product_id             = $product_id;
                $p_variation_comb->product_variation_id   = $v_price->id;
                $p_variation_comb->attribute_id           = $attribute_id;
                $p_variation_comb->attribute_value_id     = explode(":", $combination)[1];
                $p_variation_comb->color_id     = $color_id;
                $p_variation_comb->save();
            }
        }
    }
    /**
     * Will store product shipping information
     * 
     * @param Int $id
     * @param Object $request
     * @return void
     */
    public function storeProductShippingInfo($id, $request)
    {
        $shipping = ProductShippingInfo::firstOrCreate(['product_id' => $id]);
        $shipping->weight = $request['weight'];
        $shipping->height = $request['height'];
        $shipping->width = $request['width'];
        $shipping->length = $request['length'];
        $shipping->save();
    }
    /**
     * Store product shipping areas
     * 
     * @param Int $id
     * @return void
     * 
     */
    public function storeProductCodAreas($id, $request)
    {
        $product = Product::findOrFail($id);
        $product->cod_countries()->delete();
        //store countries
        if ($request['cod_selected_countries'] != null) {
            foreach ($request['cod_selected_countries'] as $country) {
                $shipping_country = ProductCodCountries::firstOrCreate(['product_id' => $id, 'country_id' => $country]);
                $shipping_country->save();
            }
        }
        $product->cod_states()->delete();
        //store states
        if ($request['cod_selected_states'] != null) {
            foreach ($request['cod_selected_states'] as $state) {
                $shipping_state = ProductCodState::firstOrCreate(['product_id' => $id, 'state_id' => $state]);
                $shipping_state->save();
            }
        }

        $product->cod_cities()->delete();
        //store cities
        if ($request['cod_selected_cities'] != null) {
            foreach ($request['cod_selected_cities'] as $city) {
                $shipping_city = ProductCodCities::firstOrCreate(['product_id' => $id, 'city_id' => $city]);
                $shipping_city->save();
            }
        }
    }
    /**
     * Store product tags
     * 
     * @param Int $id
     * @param Object $request
     * @return void
     */
    public function storeProductTags($id, $request)
    {
        if ($request['tags'] != null) {
            foreach ($request['tags'] as $tag) {

                if (ProductTags::find($tag)) {
                    $tag_id = $tag;
                } else {
                    $new_tag = new ProductTags;
                    $new_tag->name = $tag;
                    $new_tag->permalink = $tag;
                    $new_tag->save();
                    $tag_id = $new_tag->id;
                }
                $product_tag = ProductHasTags::firstOrCreate(['product_id' => $id, 'tag_id' => $tag_id]);
                $product_tag->save();
            }
        }
    }
    /**
     * Will store product tags
     * 
     * @param Object $request
     * @param Int $product_id
     * @return void
     */
    public function storeColorVariantImages($request, $product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->color_images()->delete();
        foreach ($request['selected_colors'] as $color) {
            $image_input = 'color_' . $color . '_image';
            if ($request->has($image_input)) {
                $image_array = explode(',', $request[$image_input]);
                foreach ($image_array as $image) {
                    if ($image != null) {
                        $color_image = new ProductColorVariantImages;
                        $color_image->product_id = $product_id;
                        $color_image->color_id = $color;
                        $color_image->image = $image;
                        $color_image->save();
                    }
                }
            }
        }
    }
    /**
     * Will store product gallery image
     * 
     * @param Int $product_id
     * @param Object $request
     * @return mixed
     */
    public function storeProductGalleryImages($product_id, $request)
    {
        $gallery_images = explode(',', $request['gallery_images']);
        foreach ($gallery_images as $image) {
            $p_gallery_img = ProductGalleryImages::firstOrCreate(['product_id' => $product_id, 'image_id' => $image]);
            $p_gallery_img->save();
        }
    }
    /**
     * Will Store collection products
     * 
     * @param Object $request
     * @param Int $product_id
     * @return bool
     */
    public function storeCollectionProducts($product_id, $request)
    {
        if ($request->has('product_colletions') && $request['product_colletions'] != null) {
            foreach ($request['product_colletions'] as $collection) {
                $collection_product = CollectionHasProducts::firstOrCreate(['collection_id' => $collection, 'product_id' => $product_id]);
                $collection_product->save();
            }
        }
    }
    /**
     * Will return product query
     */
    public function productQuery()
    {
        $data = [
            'id',
            'unit',
            'name',
            'status',
            'supplier',
            'permalink',
            'has_variant',
            'discount_type',
            'thumbnail_image',
            'discount_amount',
            'max_item_on_purchase',
            'min_item_on_purchase',
        ];

        $query = Product::with(['unit_info' => function ($q) {
            $q->with(['unit_translations' => function ($tq) {
                $tq->select('name', 'unit_id', 'lang');
            }])->select('id', 'name');
        }, 'single_price' => function ($q) {
            $q->select('product_id', 'purchase_price', 'unit_price', 'quantity');
        }, 'variations' => function ($q) {
            $q->select('product_id', 'purchase_price', 'unit_price', 'quantity', 'variant');
        }, 'product_translations' => function ($q) {
            $q->select('product_id', 'name', 'lang');
        }, 'reviews' => function ($q) {
            $q->select('product_id', 'rating');
        }, 'seller' => function ($query) {
            $query->select('id', 'status', 'name');
        }])->select($data);

        return $query;
    }
    /**
     * Product filter with seller
     */
    public function productQueryFilterWithSeller($query, $seller_id = null)
    {
        if ($seller_id == null) {
            $query = $query->whereHas('seller', function ($q) {
                $q->with(['shop' => function ($sq) {
                    $sq->select('id', 'seller_id', 'shop_slug', 'shop_name');
                }])
                    ->whereHas('shop', function ($ssq) {
                        $ssq->where('status', config('settings.general_status.active'));
                    })
                    ->where('status', config('settings.general_status.active'));
            });
        }
        if ($seller_id != null) {
            $query = $query->where('supplier', $seller_id);
        }
        return $query;
    }

    /**
     * Filter products
     * 
     * @param Object $request
     * @return Query
     */
    public function filterProducts($request, $seller_id = null, $product_ids = [])
    {

        $query = $this->productQuery();

        if (isActivePluging('multivendor')) {
            $query = $this->productQueryFilterWithSeller($query, $seller_id);
        }

        if (!isActivePluging('multivendor') && $seller_id == null) {
            $query = $query->whereHas('seller', function ($q) {
                $q->where('user_type', config('tlecommercecore.user_type.admin'));
            });
        }


        if (!isActivePluging('multivendor') && $seller_id != null) {
            $query = $query->where('supplier', $seller_id);
        }

        //Filter by product rating
        if ($request->has('rating') && $request['rating'] != null) {
            $query = $query->addSelect(['avg_rating' => ProductReview::selectRaw('avg(rating)')->whereColumn('product_id', 'tl_com_products.id')->groupBy('product_id')])
                ->having('avg_rating', '>=', $request['rating']);
        }
        //Filter by product brand
        if ($request->has('brand_id')) {
            $query = $query->where('brand', $request['brand_id']);
        }
        //Filter by product category
        if ($request->has('category_id')) {
            $categories = [$request['category_id']];
            $sub_categories = ProductCategory::where('parent', $request['category_id'])->pluck('id')->toArray();
            $with_sub_category = array_merge($categories, $sub_categories);
            $sub_sub_categories = ProductCategory::whereIn('parent', $sub_categories)->pluck('id')->toArray();
            $with_sub_sub_category = array_merge($with_sub_category, $sub_sub_categories);

            $query = $query->whereHas('product_categories', function ($q) use ($with_sub_sub_category) {
                $q->whereIn('category_id', $with_sub_sub_category);
            });
        }

        if (!empty($product_ids)) {
            $query = $query->whereIn('id', $product_ids);
        }

        $priceCondition = function ($q, $request, $product_ids) {
            $q->whereBetween('unit_price', [$request['min_price'], $request['max_price']]);

            if (!empty($product_ids)) {
                $q->whereIn('product_id', $product_ids);
            }
        };

        //Filter by product price
        if ($request->has('min_price') && $request->has('max_price')) {
            $query = $query->whereHas('single_price', function ($q) use ($request) {
                $q->whereBetween('unit_price', [$request['min_price'], $request['max_price']])->orderBy('unit_price', 'ASC');
            })->orWhereHas('variations', function ($q) use ($request, $priceCondition, $product_ids) {
                $priceCondition($q, $request, $product_ids);
            });
        }


        $query = $query->where('status', config('settings.general_status.active'));

        if (isActivePluging('multivendor')) {
            $query = $query->where('is_approved', config('settings.general_status.active'));
        }

        return $query;
    }

    /**
     * Will return related product
     * 
     * @param Int $id
     * @return Collections
     */
    public function relatedProducts($id, $number = 6)
    {
        //todo
        return Product::where('status', config('settings.general_status.active'))->take($number)->get();
    }
    /**
     * Will return top selling products
     * 
     * @return Collections
     */
    public function topSellingProducts($number = 3)
    {   //todo
        return Product::where('status', config('settings.general_status.active'))->take($number)->get();
    }
}
