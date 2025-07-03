<?php

namespace Plugin\TlcommerceCore\Models;

use Carbon\Carbon;
use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\Orders;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Observer\SellerEarningObserver;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;

class OrderHasProducts extends Model
{

    protected $table = "tl_com_ordered_products";

    protected $casts = ['delivery_time' => 'datetime'];

    protected static function boot()
    {
        parent::boot();

        OrderHasProducts::observe(SellerEarningObserver::class);
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function product_details()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_tracking()
    {
        return $this->hasMany(OrderPackageTracking::class, 'order_package_id')->select(
            [
                'message',
                'created_at as date'
            ]
        )->orderBy('id', 'DESC');
    }

    public function shipping_rate_info()
    {
        return $this->belongsTo(ShippingRate::class, 'shipping_rate')->select([
            'name',
            'carrier_id',
            'shipping_medium',
            'id',
            'delivery_time'
        ]);
    }

    public function soldBy()
    {
        if (isActivePluging('multivendor')) {
            $shop = \Plugin\Multivendor\Models\SellerShop::where('seller_id', $this->seller_id)
                ->select(['shop_name', 'logo as shop_logo', 'shop_slug', 'id as shop_id'])
                ->first();
            return $shop;
        }

        return null;
    }

    public function seller()
    {
        if (isActivePluging('multivendor')) {
            $seller = User::where('id', $this->seller_id)
                ->select(['name', 'id'])
                ->first();
            return $seller;
        }

        return null;
    }

    public function totalPayableAmount()
    {
        $unit_price = $this->unit_price * $this->quantity;

        return ($unit_price + $this->delivery_cost + $this->tax) - $this->couponDiscountedAmount();
    }

    public function couponDiscountedAmount()
    {
        $total_coupon_discount = $this->order->total_discount;
        $order_subtotal = $this->order->sub_total;
        $unit_price = $this->unit_price * $this->quantity;
        $discounted_amount = ($total_coupon_discount * $unit_price) / $order_subtotal;
        return $discounted_amount;
    }

    public function delivery_status_label()
    {
        if ($this->delivery_status == config('tlecommercecore.order_delivery_status.delivered')) {
            return 'delivered';
        } else if ($this->delivery_status == config('tlecommercecore.order_delivery_status.pending')) {
            return "pending";
        } else if ($this->delivery_status == config('tlecommercecore.order_delivery_status.processing')) {
            return "processing";
        } else if ($this->delivery_status == config('tlecommercecore.order_delivery_status.ready_to_ship')) {
            return "Ready to ship";
        } else if ($this->delivery_status == config('tlecommercecore.order_delivery_status.shipped')) {
            return "shipped";
        } else {
            return 'cancelled';
        }
    }

    public function payment_status_label()
    {
        if ($this->payment_status == config('tlecommercecore.order_payment_status.paid')) {
            return 'paid';
        } else {
            return 'unpaid';
        }
    }

    public function canReturn()
    {
        //check delivery status
        if ($this->delivery_status != config('tlecommercecore.order_delivery_status.delivered')) {
            return config('settings.general_status.in_active');
        }
        //Check return status is available
        if ($this->return_status != config('tlecommercecore.product_return_status.available')) {
            return config('settings.general_status.in_active');
        }

        //check time settings of product return
        $time_unit = SettingsRepository::getEcommerceSetting('return_order_time_limit_unit');
        $time = SettingsRepository::getEcommerceSetting('return_order_time_limit');
        if ($time_unit == config('tlecommercecore.time_unit.Days')) {
            $present_date = Carbon::now()->startOfDay();
            $expire_date = $this->delivery_time->addDay($time);
            if ($present_date <= $expire_date) {
                return config('settings.general_status.active');
            } else {
                return config('settings.general_status.in_active');
            }
        } else if ($time_unit == config('tlecommercecore.time_unit.Hours')) {
            $present_date = Carbon::now();
            $expire_date = $this->delivery_time->addHour($time);
            if ($present_date <= $expire_date) {
                return config('settings.general_status.active');
            } else {
                return config('settings.general_status.in_active');
            }
        } else if ($time_unit == config('tlecommercecore.time_unit.Minutes')) {
            $present_date = Carbon::now();
            $expire_date = $this->delivery_time->addMinute($time);
            if ($present_date <= $expire_date) {
                return config('settings.general_status.active');
            } else {
                return config('settings.general_status.in_active');
            }
        } else {
            return config('settings.general_status.in_active');
        }

        return config('settings.general_status.in_active');
    }

    public function returnStatus()
    {
        if ($this->return_status == config('tlecommercecore.product_return_status.not_available')) {
            return
                [
                    'status' => $this->return_status,
                    'class' => 'warning',
                    'label' => 'Not available',
                ];
        } else if ($this->return_status == config('tlecommercecore.product_return_status.available') && $this->canReturn() == config('settings.general_status.active')) {
            return
                [
                    'status' => $this->return_status,
                    'class' => 'primary',
                    'label' => 'Available',
                ];
        } else if ($this->return_status == config('tlecommercecore.product_return_status.available') && $this->canReturn() != config('settings.general_status.active')) {
            return
                [
                    'status' => $this->return_status,
                    'class' => 'danger',
                    'label' => 'Not Available',
                ];
        } else if ($this->return_status == config('tlecommercecore.product_return_status.returned')) {
            return
                [
                    'status' => $this->return_status,
                    'class' => 'success',
                    'label' => 'Returned',
                ];
        } else if ($this->return_status == config('tlecommercecore.product_return_status.processing')) {
            return
                [
                    'status' => $this->return_status,
                    'class' => 'info',
                    'label' => 'Return processing',
                ];
        } else if ($this->return_status == config('tlecommercecore.product_return_status.return_cancel')) {
            return
                [
                    'status' => $this->return_status,
                    'class' => 'danger',
                    'label' => 'Cancelled',
                ];
        }
    }

    public function estimateDeliveryTime()
    {
        if ($this->shipping_rate_info != null && $this->shipping_rate_info->shipping_time != null) {
            $min_time = "";
            if ($this->shipping_rate_info->shipping_time->min_unit == config('tlecommercecore.time_unit.Days')) {
                $min_time = $this->created_at->addDays($this->shipping_rate_info->shipping_time->min_value);
            }
            if ($this->shipping_rate_info->shipping_time->min_unit == config('tlecommercecore.time_unit.Hours')) {
                $min_time = $this->created_at->addHours($this->shipping_rate_info->shipping_time->min_value);
            }
            if ($this->shipping_rate_info->shipping_time->min_unit == config('tlecommercecore.time_unit.Minutes')) {
                $min_time = $this->created_at->addMinutes($this->shipping_rate_info->shipping_time->min_value);
            }
            $max_time = "";
            if ($this->shipping_rate_info->shipping_time->max_unit == config('tlecommercecore.time_unit.Days')) {
                $max_time = $this->created_at->addDays($this->shipping_rate_info->shipping_time->max_value);
            }
            if ($this->shipping_rate_info->shipping_time->max_unit == config('tlecommercecore.time_unit.Hours')) {
                $max_time = $this->created_at->addHours($this->shipping_rate_info->shipping_time->max_value);
            }
            if ($this->shipping_rate_info->shipping_time->max_unit == config('tlecommercecore.time_unit.Minutes')) {
                $max_time = $this->created_at->addMinutes($this->shipping_rate_info->shipping_time->max_value);
            }
            $time = $min_time->format('D d M') . ' - ' . $max_time->format('D d M');
            return $time;
        } else {
            return null;
        }
    }

    public function canCancel()
    {
        if ($this->delivery_status == config('tlecommercecore.order_delivery_status.cancelled') || $this->delivery_status != config('tlecommercecore.order_delivery_status.pending')) {
            return config('settings.general_status.in_active');
        }
        $time_unit = SettingsRepository::getEcommerceSetting('cancel_order_time_limit_unit');
        $time = SettingsRepository::getEcommerceSetting('cancel_order_time_limit');
        if ($time_unit == config('tlecommercecore.time_unit.Days')) {
            $present_date = Carbon::now()->startOfDay();
            $expire_date = $this->created_at->addDay($time);
            if ($present_date <= $expire_date) {
                return config('settings.general_status.active');
            } else {
                return config('settings.general_status.in_active');
            }
        } else if ($time_unit == config('tlecommercecore.time_unit.Hours')) {
            $present_date = Carbon::now();
            $expire_date = $this->created_at->addHour($time);
            if ($present_date <= $expire_date) {
                return config('settings.general_status.active');
            } else {
                return config('settings.general_status.in_active');
            }
        } else if ($time_unit == config('tlecommercecore.time_unit.Minutes')) {
            $present_date = Carbon::now();
            $expire_date = $this->created_at->addMinute($time);
            if ($present_date <= $expire_date) {
                return config('settings.general_status.active');
            } else {
                return config('settings.general_status.in_active');
            }
        } else {
            return config('settings.general_status.in_active');
        }

        return config('settings.general_status.in_active');
    }
}
