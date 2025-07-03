<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGalleryImages extends Model
{
    protected $table = "tl_com_product_gallery_images";

    protected $fillable = ['product_id', 'image_id'];
}
