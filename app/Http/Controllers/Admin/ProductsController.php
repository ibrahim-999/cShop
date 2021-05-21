<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductsAttribute;
use App\Section;
use Illuminate\Http\Request;
use Session;
use Image;

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
    public function updateAttributeStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();

            if($data['status']=="Active")
            {
                $status = 0;
            }else{
                $status= 1;
            }
            ProductsAttribute::where('id',$data['attribute_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'attribute_id'=>$data['attribute_id']]);
        }
    }
    public function addEditProduct( Request $request, $id=null)
    {
        if($id=="")
        {
            $title = "Add Product";
            $product = new  Product();
            $productdata = array();
            $message = "Product has been added successfully!";
            //Add Product
        }
        else
        {
            $title = "Edit Product";
            $productdata = Product::find($id);
            $productdata = json_decode(json_encode($productdata),true);
            $product = Product::find($id);
            $message = "Product has been updated successfully!";
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

            if (empty($data['is_featured']))
            {
                $is_featured = "No";
            } else {
                $is_featured = "YES" ;
            }

            if(empty($data['product_weight']))
            {
                $data['product_weight'] = "";
            }

            //Upload Product image
            if($request->hasFile('main_image'))
            {
                $image_tmp = $request->file('main_image');
                if($image_tmp->isValid())
                {
                    // Get Original image name
                    $image_name = $image_tmp->getClientOriginalName();
                    //Get Image extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate new image name
                    $imageName = $image_name.'.'.rand(111,99999).'.'.$extension;
                    //Set path for every size
                    $large_image_path = 'images/product_images/large/'.$imageName;
                    $medium_image_path = 'images/product_images/medium/'.$imageName;
                    $small_image_path = 'images/product_images/small/'.$imageName;
                    //Upload large image
                    Image::make($image_tmp)->save($large_image_path);
                    //Upload medium and small images
                    Image::make($image_tmp)->resize(520,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(250,300)->save($small_image_path);
                    //Save image in the product table
                    $product->main_image = $imageName;

                }
            }

            //Upload Product Video

            if($request->hasFile('product_video'))
            {
                $video_tmp = $request->file('product_video');
                if($video_tmp->isValid())
                {
                    // Upload Video
                    $video_name = $video_tmp->getClientOriginalName();
                    $extension = $video_tmp->getClientOriginalExtension();
                    $videoName = $video_name.'.'.rand().'.'.$extension;
                    $video_path = 'videos/product_videos'.$videoName;
                    $video_tmp->move($video_path,$videoName);
                    // Save Video in product table
                    $product->product_video = $videoName;

                }
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
            $product->wash_care = $data['wash_care'];
            //$product->fabric = $data['fabric'];
            $product->pattern = $data['pattern'];
            $product->sleeve = $data['sleeve'];
            $product->fit = $data['fit'];
            $product->occasion = $data['occasion'];
            $product->meta_title = $data['meta_title'];
            $product->meta_keywords = $data['meta_keywords'];
            $product->meta_description = $data['meta_description'];
            $product->is_featured = $is_featured;
            $product->status = 1;
            $product->save();
            session::flash('success_message',$message);
            return redirect('admin/products');

        }

        //
        // Filter Arrays
        //$fabricArray = array('Cotton','Polyester','Wool');
        $sleeveArray = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');
        $patternArray = array('Checked','Plain','Printed','Self','Solid');
        $fitArray = array('Regular','Slim');
        $occasionArray = array('Casual','Formal');

        // Sections with Categories and Subcategories
        $categories = Section::with('Categories')->get();
        $categories = json_decode(json_encode($categories),true);
/*        echo "<pre>"; print_r($categories); die;*/
        return view ('admin.products.add_edit_product')->with(compact('title','sleeveArray','patternArray','fitArray','occasionArray','categories','productdata'));
    }
    public function deleteProductImage($id)
    {
        $product_image = Product::select('main_image')->where('id',$id)->first();

        //Get Product Image Path
        $small_image_path = 'images/product_images/small/';
        $medium_image_path = 'images/product_images/medium/';
        $large_image_path = 'images/product_images/large/';

        // Delete Product Image form category_images folder if exits

        if(file_exists($small_image_path.$product_image->main_image))
        {
            unlink($small_image_path.$product_image->main_image);
        }
        if(file_exists($medium_image_path.$product_image->main_image))
        {
            unlink($medium_image_path.$product_image->main_image);
        }
        if(file_exists($large_image_path.$product_image->main_image))
        {
            unlink($large_image_path.$product_image->main_image);
        }

        //Delete Product Image from the table

        Product::where('id',$id)->update(['main_image'=>'']);

        $message = 'Product image has been deleted!';
        session()->flash('success_message',$message);
        return redirect()->back();
    }

    public function deleteProductVideo($id)
    {
        $product_video = Product::select('product_video')->where('id',$id)->first();

        //Get Product Video Path
        $product_video_path = 'videos/product_videos/';
        $medium_image_path = 'images/product_images/medium/';
        $large_image_path = 'images/product_images/large/';

        // Delete Product Video form category_images folder if exits

        if(file_exists($product_video_path.$product_video->product_video))
        {
            unlink($product_video_path.$product_video->main_image);
        }

        //Delete Product Video from the table

        Product::where('id',$id)->update(['product_video'=>'']);

        $message = 'Product Video has been deleted!';
        session()->flash('success_message',$message);
        return redirect()->back();
    }

    public function deleteProduct($id)
    {
        //Delete Product
        Product::where('id',$id)->delete();
        $message = 'Product has been deleted!';
        session()->flash('success_message',$message);
        return redirect()->back();
    }

    public function deleteAttribute($id)
    {
        //Delete Product
        ProductsAttribute::where('id',$id)->delete();
        $message = 'Attribute has been deleted!';
        session()->flash('success_message',$message);
        return redirect()->back();
    }

    public function addAttributes(Request $request, $id)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            foreach ($data['sku'] as $key => $value)
            {
                if(!empty($value))
                {
                    // SKU already exists validation
                    $attrCountSKU = ProductsAttribute::where('sku',$value)->count();
                    if($attrCountSKU>0)
                    {
                        $message ='SKU Already Exists. Please add another SKU';
                        session()->flash('error_message',$message);
                        return redirect()->back();
                    }
                    // Size already exists validation
                    $attrCountSize = ProductsAttribute::where(['product_id'=>$id,'size'=>$data['size'][$key]])->count();
                    if($attrCountSize>0)
                    {
                        $message ='Size Already Exists. Please add another Size';
                        session()->flash('error_message',$message);
                        return redirect()->back();
                    }
                    $attributes = new ProductsAttribute;
                    $attributes->product_id = $id;
                    $attributes->sku = $value;
                    $attributes->size = $data['size'][$key];
                    $attributes->price = $data['price'][$key];
                    $attributes->stock = $data['stock'][$key];
                    $attributes->status = 1;
                    $attributes->save();
                }
            }
            $message = 'Product attributes have been added successfully!';
            session()->flash('success_message',$message);
            return redirect()->back();
        }

        $productdata = Product::select('id','product_name','product_code','product_color','main_image')
            ->with('attributes')->find($id);
        $productdata = json_decode(json_encode($productdata),true);
        $title = "Product Attributes";
        return view('admin.products.add_attributes')->with(compact('productdata','title'));
    }
    public function editAttributes(Request $request, $id)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            foreach( $data['attrId'] as $key => $attr)
            {
                if(!empty($attr))
                {
                    ProductsAttribute::where(['id'=>$data['attrId'][$key]])->update(['price'=>$data['price'][$key],
                        'stock'=>$data['stock'][$key]]);
                }
            }
            $message = 'Product attributes have been updated successfully!';
            session()->flash('success_message',$message);
            return redirect()->back();

        }
    }

}
