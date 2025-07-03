<?php

namespace Plugin\PageBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PageBuilderSectionProperties extends Model
{
    protected $table = 'page_builder_sections_properties';
    protected $guarded = [];

    /**
     * Return Section properties by encode
     */
    protected function properties(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  json_decode($value, true)
        );
    }
}
