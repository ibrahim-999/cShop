<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Session;

class ProductsController extends Controller
{
    public function products()
    {
        Session::put('page', 'products');
        $products = Product::with(['category'=>function($query){
            $query->select('id','category_name');
        },'section'=>function($query){
            $query->select('id','name');
        }])->get();

        $products = json_decode(json_encode($products));

        return view('admin.products.products')->with(compact('products'));
    }
    public function updateProductStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();

            if($data['status']=="Active")
            {
                $status = 0;
            }else{
                $status= 1;
            }
            Product::where('id',$data['product_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'product_id'=>$data['product_id']]);
        }
    }
    public function deleteProduct($id)
    {
        //Delete Product
        Product::where('id',$id)->delete();
        $message = 'Product has been deleted!';
        session()->flash('success_message',$message);
        return redirect()->back();
    }
}
