<?php

namespace Plugin\Flashdeal\Models;


use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\Flashdeal\Models\FlashDealProducts;

use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;
class FlashDeal extends Model implements Sitemapable
{

    protected $table = "tl_com_flash_deal";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $deal_translations = $this->deal_translations->where('lang', $lang)->first();
        return $deal_translations != null ? $deal_translations->$field : $this->$field;
    }

    public function deal_translations()
    {
        return $this->hasMany(DealTranslation::class, 'deal_id');
    }

    public function deal_products()
    {
        return $this->hasMany(FlashDealProducts::class, 'deal_id')->orderBy('id', 'DESC');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, FlashDealProducts::class, 'deal_id', 'id', 'id', 'product_id');
    }

    public function toSitemapTag(): Url | string | array
    {
        return Url::create('/deals' . '/' . $this->permalink)
            ->setLastModificationDate(Carbon::create($this->updated_at))
            ->setPriority(0.1);
    }
}
