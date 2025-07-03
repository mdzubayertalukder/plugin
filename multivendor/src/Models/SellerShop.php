<?php

namespace Plugin\Multivendor\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Plugin\TlcommerceCore\Models\OrderHasProducts;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\ProductReview;

use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;
class SellerShop extends Model implements Sitemapable
{

    protected $table = "tl_com_seller_shop";


    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier', 'seller_id');
    }


    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(ProductReview::class, Product::class, 'supplier', 'product_id', 'seller_id', 'id');
    }

    public function orders()
    {
        return $this->hasManyThrough(OrderHasProducts::class, Product::class, 'supplier', 'product_id', 'seller_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function toSitemapTag(): Url | string | array
    {
        return Url::create('/shop' . '/' . $this->shop_slug)
            ->setLastModificationDate(Carbon::create($this->updated_at))
            ->setPriority(0.1);
    }
}
