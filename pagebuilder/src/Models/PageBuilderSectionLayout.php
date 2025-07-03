<?php

namespace Plugin\PageBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class PageBuilderSectionLayout extends Model
{
    protected $table = 'page_builder_section_layouts';
    protected $guarded = [];
    public $timestamps = false;

    public function layout_widgets(){
        return $this->hasMany(PageBuilderSectionLayoutWidget::class, 'section_layout_id', 'id')->orderBy('serial');
    }
}
