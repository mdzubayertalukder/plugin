<?php

namespace Plugin\PageBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class PageBuilderSection extends Model
{
    protected $table = 'page_builder_sections';
    protected $guarded = [];

    public function layouts()
    {
        return $this->hasMany(PageBuilderSectionLayout::class, 'section_id', 'id')->orderBy('col_index', 'asc');
    }

    /**
     * Section Properties
     */
    public function properties()
    {
        return $this->hasOne(PageBuilderSectionProperties::class, 'section_id');
    }
}
