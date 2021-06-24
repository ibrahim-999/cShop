<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Category','category_id');
    }

    public function section()
    {
        return $this->belongsTo('App\Section','section_id');
    }
    public function brand()
    {
        return $this->belongsTo('App\Brand','brand_id');
    }
    public function attributes()
    {
        return $this->hasMany('App\ProductsAttribute');
    }
    public function images()
    {
        return $this->hasMany('App\ProductsImage');
    }
    public static function productFilters()
    {
        // Filter Arrays
        $productFilters['fabricArray'] = array('Cotton','Polyester','Wool');
        $productFilters['sleeveArray'] = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');
        $productFilters['patternArray'] = array('Checked','Plain','Printed','Self','Solid');
        $productFilters['fitArray'] = array('Regular','Slim');
        $productFilters['occasionArray'] = array('Casual','Formal');

        return $productFilters;
    }


}
