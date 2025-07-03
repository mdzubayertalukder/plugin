<?php

namespace Plugin\PageBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PageBuilderSectionLayoutWidgetProperties extends Model
{
    protected $table = 'page_builder_sections_layout_widget_properties';
    protected $guarded = [];
    public $timestamps = false;


    /**
     * Return Widget Properties by decode
     */
    protected function properties(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  json_decode($value, true)
        );
    }

    /**
     * Layout Widget has many translation
     */
    public function translations()
    {
        return $this->hasMany(PageBuilderSectionLayoutWidgetTranslation::class, 'layout_widget_properties_id', 'id');
    }

    /**
     * Get Properties With Specific Translations
     */
    public function propertiesTranslations($lang)
    {
        $translation = $this->translations()->where('lang', $lang)->first();
        
        if(isset($translation)){
            $default_properties = empty($this->properties) ? [] : $this->properties;
            return array_replace($default_properties, $translation->properties);
        } else {
            return $this->properties;
        }

    }
}
