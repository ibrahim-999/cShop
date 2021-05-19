<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\Section;
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
    public function addEditProduct( Request $request, $id=null)
    {
        if($id=="")
        {
            $title = "Add Product";
            $product = new  Product();
            //Add Product
        }
        else
        {
            $title = "Edit Product";
            //Edit Product
        }
        if ($request->isMethod('post'))
        {
            $data = $request->all();
            // Validation
            $rules = [
                'category_id' => 'required',
                'product_name' => 'required',
                'product_code' => 'required',
                'product_price' =>'required|numeric',
                'product_color' => 'required',

            ];
            $customMessages = [
                'category_id.required' => 'Category Name is required',
                'product_name.required' => ' Name is required',
                'product_code.required' => 'Code  is required',
                'product_price.required' => 'Price  is required',
                'product_price.numeric' => 'Valid Price is required',
                'product_color.required' => 'Color  is required',

            ];
            $this->validate($request,$rules,$customMessages);
            // Save Product Details

            if (!empty($data['is_featured']))
            {
                $is_featured = 0;
            } else {
                $is_featured = 1 ;
            }

            if(empty($data['fabric']))
            {
                $data['fabric'] = "";
            }
            if(empty($data['sleeve']))
            {
                $data['sleeve'] = "";
            }
            if(empty($data['description']))
            {
                $data['description'] = "";
            }
            if(empty($data['meta_title']))
            {
                $data['meta_title'] = "";
            }
            if(empty($data['meta_description']))
            {
                $data['meta_description'] = "";
            }
            if(empty($data['meta_keywords']))
            {
                $data['{meta_keywords}'] = "";
            }
            if(empty($data['product_video']))
            {
                $data['product_video'] = "";
            }
            if(empty($data['main_image']))
            {
                $data['main_image'] = "";
            }

            $categoriesDetails = Category::find($data['category_id']);
            $product->section_id = $categoriesDetails['section_id'];
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->product_code = $data['product_code'];
            $product->product_color = $data['product_color'];
            $product->product_price = $data['product_price'];
            $product->product_discount = $data['product_discount'];
            $product->product_weight = $data['product_weight'];
            $product->description = $data['description'];
            $product->product_video = $data['product_video'];
            $product->main_image = $data['main_image'];
            $product->wash_care = $data['wash_care'];
            $product->fabric = $data['fabric'];
            $product->pattern = $data['pattern'];
            $product->sleeve = $data['sleeve'];
            $product->fit = $data['fit'];
            $product->occasion = $data['occasion'];
            $product->meta_title = $data['meta_title'];
            $product->meta_keywords = $data['meta_keywords'];
            $product->meta_description = $data['meta_description'];
            $product->meta_description = $data['meta_description'];
            $product->is_featured = $is_featured;
            $product->status = 1;
            $product->save();
            session::flash('success_message',' Product has been added successfully!');
            return redirect('admin/products');

        }

        //
        // Filter Arrays
        $fabricArray = array('Cotton','Polyester','Wool');
        $sleeveArray = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');
        $patternArray = array('Checked','Plain','Printed','Self','Solid');
        $fitArray = array('Regular','Slim');
        $occasionArray = array('Casual','Formal');

        // Sections with Categories and Subcategories
        $categories = Section::with('Categories')->get();
        $categories = json_decode(json_encode($categories),true);
/*        echo "<pre>"; print_r($categories); die;*/
        return view ('admin.products.add_edit_product')->with(compact('title','fabricArray','sleeveArray','patternArray','fitArray','occasionArray','categories'));
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
