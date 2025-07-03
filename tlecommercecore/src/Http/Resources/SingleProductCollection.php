<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Session;
use Plugin\TlcommerceCore\Models\Colors;
use Illuminate\Http\Resources\Json\JsonResource;
use Plugin\TlcommerceCore\Models\AttributeValues;
use Plugin\TlcommerceCore\Models\ProductAttribute;
use Plugin\TlcommerceCore\Models\ProductHasColors;
use Plugin\TlcommerceCore\Models\ProductHasChoiceOption;
use Plugin\TlcommerceCore\Models\ProductShareOption;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;

class SingleProductCollection extends JsonResource
{

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'applicable_discount' => $this->applicableDiscount(),
            'name' => $this->translation('name', Session::get('api_locale')),
            'voucher_list' => $this->couponList(),
            'attribute' => $this->has_variant == config('tlecommercecore.product_variant.variable') ?  $this->productAttributes() : null,
            'selectedVariant' => $this->has_variant == config('tlecommercecore.product_variant.variable') ?  $this->selectedVariant() : null,
            'permalink' => $this->permalink,
            'oldPrice' => $this->base_price(),
            'price' => $this->current_price(),
            'has_variant' => $this->has_variant,
            'price_range_min' => $this->has_variant == config('tlecommercecore.product_variant.variable') ?  $this->priceRangeMinValue('new') : null,
            'price_range_max' => $this->has_variant == config('tlecommercecore.product_variant.variable')  ?  $this->priceRangeMaxValue('new') : null,
            'price_range_min_old' => $this->has_variant == config('tlecommercecore.product_variant.variable') ?  $this->priceRangeMinValue('old') : null,
            'price_range_max_old' => $this->has_variant == config('tlecommercecore.product_variant.variable') ?  $this->priceRangeMaxValue('old') : null,
            'quantity' => (int) $this->stock(),
            'total_reviews' => $this->totalReviews(),
            'rating' => $this->avgRating(),
            'galleryImages' => $this->galleryImages(),
            'pdf_specifications' => $this->pdf_specifications != null ? getFilePath($this->pdf_specifications) : null,
            'max_item_on_purchase' => $this->max_item_on_purchase != null ? $this->max_item_on_purchase : 0,
            'min_item_on_purchase' => $this->min_item_on_purchase != null ? $this->min_item_on_purchase : 0,
            'has_warranty' => $this->has_warranty,
            'has_replacement_warranty' => $this->has_replacement_warranty,
            'warrenty_days' => $this->warrenty_days != null ? $this->warrenty_days : 0,
            'is_authentic' => $this->is_authentic == config('settings.general_status.active') ? translate('100% Authentic', Session::get('api_locale')) : translate('Not Authentic', session::get('api_locale')),
            'is_active_cod' => $this->is_active_cod == config('settings.general_status.active') ? translate('Available', Session::get('api_locale')) : translate('Not Available', session::get('api_locale')),
            'description' => $this->translation('description', Session::get('api_locale')),
            'summary' => $this->translation('summary', Session::get('api_locale')),
            'condition' => $this->product_condition != null ? $this->product_condition->translation('name', Session::get('api_locale')) : null,
            'is_refundable' => $this->is_refundable,
            'return_option' => $this->returnOption(),
            'attatchment_title' => $this->is_active_attatchment != config('settings.general_status.active') ? null : $this->attatchment_name,
            'shareOptions' => $this->shareOptions(),
            'url' => url('/') . "/products/" . $this->permalink,
            'has_deal' => $this->hasFlashDeal(),
            'shopInfo' => $this->shopInfo(),
            'seller' => $this->supplier,
        ];
    }

    public function shopInfo()
    {
        if (isActivePluging('multivendor')) {
            if ($this->supplier != null) {
                $shop = \Plugin\Multivendor\Models\SellerShop::where('seller_id', $this->supplier)->first();
                if ($shop != null) {
                    return new \Plugin\Multivendor\Resources\ShopResource($shop);
                }
            }
        }

        return null;
    }

    public function shareOptions()
    {
        return ProductShareOption::where('status', config('settings.general_status.active'))
            ->select(['network', 'network_name as name', 'icon'])
            ->get();
    }
    public function returnOption()
    {
        if ($this->is_refundable == config('settings.general_status.active')) {
            $time_unit = SettingsRepository::getEcommerceSetting('return_order_time_limit_unit');
            $time = SettingsRepository::getEcommerceSetting('return_order_time_limit');
            return $time . ' ' . $time_unit . ' ' . translate('easy return available', Session::get('api_locale'));
        } else {
            return 'Not available';
        }
    }

    public function couponList()
    {
        if (isActivePluging('coupon')) {
            $coupon_ids = \Plugin\Coupon\Models\CouponProducts::where('product_id', $this->id)->pluck('coupon_id');
            $coupons = \Plugin\Coupon\Models\Coupons::whereIn('id', $coupon_ids)->select('code', 'id', 'discount_amount', 'expire_date', 'discount_type', 'minimum_spend_amount')->get();
            return $coupons;
        } else {
            return [];
        }
    }

    public function galleryImages()
    {
        $image_ids = [];
        $images = [];
        if ($this->video_link != null) {
            $video = [];
            $video['type'] = 'video';
            $video['video_link'] = $this->video_link;
            $video['thumbnail'] = getFilePath($this->thumbnail_image);
            array_push($images, $video);
        }
        if (count($this->color_images) > 0) {
            $color_id = $this->color_choices[0]->color_id;
            $image_ids = $this->color_images->where('color_id', $color_id)->pluck('image');
            if (count($image_ids) < 1) {
                $image_ids = [$this->thumbnail_image];
            }
        } else {
            if (count($this->gallery_images) > 0) {
                $image_ids = $this->gallery_images->pluck('image_id');
            } else {
                $image_ids = [$this->thumbnail_image];
            }
        }
        foreach ($image_ids as $image_id) {
            $image = [];
            $image['regular'] = getFilePath($image_id);
            $image['zoom'] = getFilePath($image_id);
            $image['type'] = 'image';

            array_push($images, $image);
        }



        return $images;
    }

    public function current_price()
    {

        if ($this->total_discount() > 0) {
            return $this->base_price() - $this->total_discount();
        }
        return $this->base_price();
    }

    public function base_price()
    {
        if ($this->has_variant == config('tlecommercecore.product_variant.single')) {
            return $this->single_price != null ? $this->single_price->unit_price : 0;
        } else {
            return $this->variations != null ? $this->variations[0]->unit_price : 0;
        }
    }
    public function total_discount()
    {
        $applicable_discount = $this->applicableDiscount();
        if ($applicable_discount != null && $applicable_discount['discount_amount'] > 0) {
            $base_price = 0;
            $discount = 0;
            //Get base price
            if ($this->has_variant == config('tlecommercecore.product_variant.single')) {
                $base_price = $this->single_price != null ? $this->single_price->unit_price : 0;
            } else {
                $base_price = $this->variations != null ? $this->variations[0]->unit_price : 0;
            }
            //Calculate discount
            if ($applicable_discount['discountType'] == config('tlecommercecore.amount_type.flat')) {
                $discount = $applicable_discount['discount_amount'];
            } else {
                $discount = ($base_price * $applicable_discount['discount_amount']) / 100;
            }
            return $discount;
        } else {
            return 0;
        }
    }
    public function priceRangeMinValue($type)
    {
        $min_price = $this->variations->min('unit_price');
        if ($type == 'old') {
            return $min_price;
        } else {
            $applicable_discount = $this->applicableDiscount();
            if ($applicable_discount != null && $applicable_discount['discount_amount'] > 0) {
                $new_min_price = 0;

                if ($applicable_discount['discountType'] == config('tlecommercecore.amount_type.flat')) {
                    $discount = $applicable_discount['discount_amount'];
                    $new_min_price = (float) $min_price - $discount;
                } else {
                    $min_discount = ($min_price * $applicable_discount['discount_amount']) / 100;
                    $new_min_price = (float) $min_price - $min_discount;
                }
                return $new_min_price;
            } else {
                return $min_price;
            }
        }
    }
    public function priceRangeMaxValue($type)
    {
        $max_price = $this->variations->max('unit_price');
        if ($type == 'old') {
            return $max_price;
        } else {
            $applicable_discount = $this->applicableDiscount();
            if ($applicable_discount != null && $applicable_discount['discount_amount'] > 0) {
                $new_max_price = 0;

                if ($applicable_discount['discountType'] == config('tlecommercecore.amount_type.flat')) {
                    $discount = $applicable_discount['discount_amount'];
                    $new_max_price = (float) $max_price - $discount;
                } else {

                    $max_discount = ($max_price * $applicable_discount['discount_amount']) / 100;
                    $new_max_price = (float) $max_price - $max_discount;
                }
                return $new_max_price;
            } else {
                return $max_price;
            }
        }
    }
    public function stock()
    {
        if ($this->has_variant == config('tlecommercecore.product_variant.single')) {
            return $this->single_price != null ? $this->single_price->quantity : 0;
        } else {
            return $this->variations != null ? $this->variations[0]->quantity : 0;
        }
    }
    public function selectedVariant()
    {
        return  rtrim($this->variations->first()->variant, "/");
    }
    public function productAttributes()
    {
        $productAttributes = [];
        $choices = $this->choices;
        foreach ($choices as $choice) {
            $singleproductAttribute = [];
            $productAttr = ProductAttribute::where('id', $choice->choice_id)->first();
            $singleproductAttribute['id'] = $choice->choice_id;
            $singleproductAttribute['title'] = $productAttr->translation('name', session::get('api_locale'));
            $singleproductAttribute['options'] = AttributeValues::whereIn('id', ProductHasChoiceOption::where('product_id', $this->id)->where('choice_id', $productAttr->id)->pluck('option_id'))->select('id', 'attribute_id as parent', 'name as title',)->get();
            array_push($productAttributes, $singleproductAttribute);
        }
        if (count($this->color_choices) > 0) {
            $colorAttributes = [];
            $colorAttributes['id'] = 'color';
            $colorAttributes['title'] = 'color';
            $colors = [];
            $color_ids = ProductHasColors::where('product_id', $this->id)->pluck('color_id');
            foreach ($color_ids as $color_id) {
                $option = Colors::where('id', $color_id)->first();
                $color_options = [];
                $image_id = $this->color_images->where('color_id', $option->id)->pluck('image');
                $color_options['id'] = $color_id;
                $color_options['parent'] = 'color';
                $color_options['name'] = $option->translation('name', session::get('api_locale'));
                $color_options['value'] = $option->code;
                $color_options['image'] = count($image_id) > 0 ? getFilePath($image_id[0]) : null;
                array_push($colors, $color_options);
            }
            $colorAttributes['options'] = $colors;
            array_push($productAttributes, $colorAttributes);
        }

        return  $productAttributes;
    }
    public function totalReviews()
    {
        $total_reviews = count($this->reviews);
        return  $total_reviews;
    }
    public function avgRating()
    {
        $avg = ($this->reviews->avg('rating'));
        return $avg != null ? $avg : 0;
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
