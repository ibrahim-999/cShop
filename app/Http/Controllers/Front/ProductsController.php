<?php

namespace App\Http\Controllers\Front;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function listing($url)
    {
        $categoryCount = Category::where(['url'=>$url, 'status'=>1])->count();
        if($categoryCount >0)
        {
            $categoryDetails = Category::catDetails($url);
            $categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])
                ->where('status',1);

            // Check if sort option selected by user
            if(isset($_GET['sort']) && !empty($_GET['sort']))
            {
                if($_GET['sort']== "product_latest")
                {
                    $categoryProducts->orderBy('id','Desc');
                }
                else if ($_GET['sort']== "product_name_a_z")
                {
                    $categoryProducts->orderBy('product_name','Asc');
                }
                else if ($_GET['sort']== "product_name_z_a")
                {
                    $categoryProducts->orderBy('product_name','Desc');
                }
                else if ($_GET['sort']== "price_lowest")
                {
                    $categoryProducts->orderBy('product_price','Asc');
                }
                else if ($_GET['sort']== "price_highest")
                {
                    $categoryProducts->orderBy('product_price','Desc');
                }
                else {
                    $categoryProducts->orderBy('id','Desc');
                }
            }
            $categoryProducts= $categoryProducts->simplePaginate(9);

            return view('front.products.listing')->with(compact('categoryDetails','categoryProducts'));
        }else {
            abort(404);
        }
    }
}
