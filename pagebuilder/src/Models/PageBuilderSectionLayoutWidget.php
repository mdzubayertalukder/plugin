<?php

namespace Plugin\PageBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class PageBuilderSectionLayoutWidget extends Model
{
    protected $table = 'page_builder_section_layout_widgets';
    protected $guarded = [];
    public $timestamps = false;

    public function widget()
    {
        return $this->belongsTo(PageBuilderWidget::class, 'page_widget_id');
    }

    public function properties()
    {
        return $this->hasOne(PageBuilderSectionLayoutWidgetProperties::class, 'layout_has_widget_id');
    }
}
