<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        // Get Featured Items
        $featuredItemsCount = Product::where('is_featured','yes')->count();
        $featuredItems = Product::where('is_featured','yes')->get()->toArray();
        /*$featuredItems = json_decode(json_encode($featuredItems),true);
        echo "<pre>";print_r($featuredItems);die;*/
        $featuredItemsChunk = array_chunk($featuredItems,4);
        $page_name = "index";
        return view('front.index')->with(compact('page_name','featuredItemsChunk','featuredItemsCount'));
    }
}
