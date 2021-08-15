<?php

namespace App\Http\Controllers\Front;

use App\Cart;
use App\Category;
use App\Coupon;
use App\ProductsAttribute;
use App\User;
use http\Header;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use PhpParser\Node\Scalar\String_;

class ProductsController extends Controller
{
    public function listing( Request $request)
    {
        Paginator::useBootstrap();
        if($request->ajax())
        {
            $data = $request->all();
            $url = $data['url'];
            $categoryCount = Category::where(['url'=>$url, 'status'=>1])->count();
            if($categoryCount >0)
            {
                $categoryDetails = Category::catDetails($url);
                $categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])
                    ->where('status',1);

                // Check if fabric is selected by user
                if(isset($data['fabric']) && !empty($data['fabric']))
                {
                    $categoryProducts->whereIn('products.fabric',$data['fabric']);
                }
                // Check if sleeve is selected by user
                if(isset($data['sleeve']) && !empty($data['sleeve']))
                {
                    $categoryProducts->whereIn('products.sleeve',$data['sleeve']);
                }
                // Check if pattern is selected by user
                if(isset($data['pattern']) && !empty($data['pattern']))
                {
                    $categoryProducts->whereIn('products.pattern',$data['pattern']);
                }
                // Check if fit is selected by user
                if(isset($data['fit']) && !empty($data['fit']))
                {
                    $categoryProducts->whereIn('products.fit',$data['fit']);
                }
                // Check if occasion is selected by user
                if(isset($data['occasion']) && !empty($data['occasion']))
                {
                    $categoryProducts->whereIn('products.occasion',$data['occasion']);
                }

                // Check if sort option selected by user
                if(isset($data['sort']) && !empty($data['sort']))
                {
                    if($_GET['sort']== "product_latest")
                    {
                        $categoryProducts->orderBy('id','Desc');
                    }
                    else if ($data['sort']== "product_name_a_z")
                    {
                        $categoryProducts->orderBy('product_name','Asc');
                    }
                    else if ($data['sort']== "product_name_z_a")
                    {
                        $categoryProducts->orderBy('product_name','Desc');
                    }
                    else if ($data['sort']== "price_lowest")
                    {
                        $categoryProducts->orderBy('product_price','Asc');
                    }
                    else if ($data['sort']== "price_highest")
                    {
                        $categoryProducts->orderBy('product_price','Desc');
                    }
                    else {
                        $categoryProducts->orderBy('id','Desc');
                    }
                }
                $categoryProducts= $categoryProducts->simplePaginate(9);

                return view('front.products.ajax_products_listing')->with(compact('categoryDetails','categoryProducts','url'));
            }else {
                abort(404);
            }
        }else{
            $url = Route::getFacadeRoot()->current()->uri();
            $categoryCount = Category::where(['url'=>$url, 'status'=>1])->count();
            if($categoryCount >0)
            {
                $categoryDetails = Category::catDetails($url);
                $categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])
                    ->where('status',1);
                $categoryProducts= $categoryProducts->simplePaginate(9);

                //Product Filters
                $productFilters = Product::productFilters();
                $fabricArray = $productFilters['fabricArray'];
                $sleeveArray = $productFilters['sleeveArray'];
                $patternArray = $productFilters['patternArray'];
                $fitArray = $productFilters['fitArray'];
                $occasionArray = $productFilters['occasionArray'];
                $page_name = "listing";
                return view('front.products.listing')->with(compact('categoryDetails','categoryProducts','url','fabricArray',
                    'sleeveArray','patternArray',
                    'fitArray','occasionArray','page_name'));
            }else {
                abort(404);
            }
        }

    }

    public function detail($id)
    {
        $productDetails = Product::with(['category','brand','attributes'=>function($query){
            $query->where('status',1);
    },'images'])->find($id)->toArray();
        $total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');
        $relatedProducts = Product::where('category_id',$productDetails['category']['id'])->
            where('id','!=',$id)->limit(6)->inRandomOrder()->get()->toArray();
        return view('front.products.detail')->with(compact('productDetails','total_stock','relatedProducts'));
    }

    public function getProductPrice(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            $getDiscountedAttrPrice = Product::getDiscountedAttrPrice($data['product_id'],$data['size']);
            return $getDiscountedAttrPrice;
        }
    }
    public function addToCart(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();

            if($data['quantity']<=0 || $data['quantity']=="")
            {
                $data['quantity']=1;
            }
            /*else{
                 $data['quantity'] =  $data['quantity'];
            }*/

            //Check Product Stock is available or not
            $getProductStock = ProductsAttribute::where(['product_id'=>$data['product_id'],'size'=>$data['size']])
                ->first()->toArray();
            //Check the amount of quantity compare to want user wants
            if($getProductStock['stock']< $data['quantity'])
            {
                $message = "Required Quantity is not available";
                session::flash('error_message',$message);
                return redirect()->back();
            }

            //Generate Session Id if not exists
            $session_id = Session::get('session_id');
            if(empty($session_id))
            {
                $session_id = Session::getId();
                Session::put('session_id',$session_id);
            }
            //Check if already exists in the cart
            if(Auth::check())
            {
                // User is logged in
                $countProducts = Cart::where(['product_id'=>$data['product_id'],
                    'size'=>$data['size'],'user_id'=>Auth::user()->id])->count();
            }else {
                //User is not logged in
                $countProducts = Cart::where(['product_id'=>$data['product_id'],
                    'size'=>$data['size'],'session_id'=>Session::get('session_id')])->count();
            }

            if($countProducts>0){
                $message = "Product already exists in cart";
                session::flash('error_message',$message);
                return redirect()->back();
            }

            if(Auth::check())
            {
                $user_id = Auth::user()->id;
            }
            else {
                $user_id = 0;
            }
            //Save Product in Cart
            //Cart::insert(['session_id'=>$session_id,'product_id'=>$data['product_id'],'size'=>$data['size'],'quantity'=>$data['quantity']]);
            $cart = new Cart;
            $cart->session_id = $session_id;
            $cart->user_id = $user_id;
            $cart->product_id = $data['product_id'];
            $cart->size = $data['size'];
            $cart->quantity = $data['quantity'];
            $cart->save();
            $message = "Product has been added to cart";
            session::flash('success_message',$message);
            return redirect('cart');
        }
    }
    public function cart()
    {
        $userCartItems = Cart::userCartItems();
        return view ('front.products.cart')->with(compact('userCartItems'));

    }
    public function updateCartItemQty(Request $request)
    {
        if($request->ajax())
        {
            $data=$request->all();

            // Get Cart Details
            $cartDetails = Cart::find($data['cartid']);

            //Get available Product Stock

            $availableStock = ProductsAttribute::select('stock')->where(['product_id'=>$cartDetails['product_id'],
                'size'=>$cartDetails['size']])->first()->toArray();

            // Check Stock is available or not
            if($data['qty']>$availableStock['stock'])
            {
                $userCartItems = Cart::userCartItems();
                return response()->json(['status'=>false,'message'=>'Product Stock is not available',
                    'view'=>(String)View::make('front.products.cart_items')
                    ->with(compact('userCartItems'))
                    ]);
            }

            // Check Size is available
            $availableSize = ProductsAttribute::where(['product_id'=>$cartDetails['product_id'],
                'size'=>$cartDetails['size'],'status'=>1])->count();
            if($availableSize==0)
            {
                $userCartItems = Cart::userCartItems();
                return response()->json(['status'=>false,'message'=>'Product Size is not available',
                    'view'=>(String)View::make('front.products.cart_items')
                    ->with(compact('userCartItems'))
                ]);
            }

            Cart::where('id',$data['cartid'])->update(['quantity'=>$data['qty']]);
            $userCartItems = Cart::userCartItems();
            $totalCartItems = totalCartItems();
            return response()->json(['status'=>true,
                'totalCartItems'=>$totalCartItems,
                'view'=>(String)View::make('front.products.cart_items')
                ->with(compact('userCartItems'))]);
        }
    }

    public function deleteCartItem(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            Cart::where('id',$data['cartid'])->delete();
            $userCartItems = Cart::userCartItems();
            $totalCartItems= totalCartItems();
            return response()->json([
                'totalCartItems'=>$totalCartItems,
                'view'=>(String)View::make('front.products.cart_items')
                ->with(compact('userCartItems'))]);
        }
    }

    public function appluCoupon(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            $userCartItems = Cart::userCartItems();
            $totalCartItems = totalCartItems();
            $couponCount = Coupon::where('coupon_code',$data['code'])->count();
            if($couponCount==0)
            {
                return response()->json(['status'=>false,
                    'message'=>"This coupon is not valid!",
                    'totalCartItems'=>$totalCartItems,
                    'view'=>(String)View::make('front.products.cart_items')
                        ->with(compact('userCartItems'))]);
            }else {
                // Check for other coupon conditions

                //Get coupon details
                $couponDetails = Coupon::where('coupon_code',$data['code'])->first();

                //Check if Coupon is Inactive
                if($couponDetails->status==0)
                {
                    $message = "This coupon is not active!";
                }
                // Check if the coupon is expired
                $expiry_date = $couponDetails->expiry_date;
                $current_date = date('Y-m-d');
                if($expiry_date<$current_date)
                {
                    $message="This coupon is expired!";
                }

                //Check if coupon is form selected categories
                //Get all selected categories form coupon
                $cateArr = explode(',',$couponDetails->categories);

                //Get cart items
                $userCartItems = Cart::userCartItems();

                //Check if coupon is belong to the logged users
                //Get all selected users from coupon
                $usersArr = explode(',',$couponDetails->users);
                //Get user ID's of all selected users
                foreach ($usersArr as $key =>$user)
                {
                    $getUserId = User::select('id')->where('email',$user)->first()->toArray();
                    $userID[] = $getUserId['id'];
                }

                //Get Cart Total Amount

                $total_amount = 0;


                foreach ($userCartItems as $key => $item)
                {
                    //Check if any item is belong to coupon category
                    if(!in_array($item['product']['category_id'],$cateArr))
                    {
                        $message = "This coupon code is not for one of the selected products!";
                    }
                    if(!in_array($item['user_id'],$userID))
                    {
                        $message = "This coupon code is not for you!";
                    }

                    $attrPrice = Product::getDiscountedAttrPrice($item['product_id'],$item['size']);
                    $total_amount = $total_amount + ($attrPrice['final_price']*$item['quantity']);
                }


                if(isset($message))
                {
                    $userCartItems = Cart::userCartItems();
                    $totalCartItems = totalCartItems();
                    return response()->json([
                        'status'=>false,
                        'message'=>$message,
                        'totalCartItems'=>$totalCartItems,
                        'view'=>(String)View::make('front.products.cart_items')
                            ->with(compact('userCartItems'))]);
                }
                else {
                    // Check if amount is fixed or parentage
                    if($couponDetails->amount_type == "Fixed")
                    {
                        $couponAmount = $couponDetails->amount;
                    }
                    else
                    {
                        $couponAmount = $total_amount * ($couponDetails->amount/100);
                    }

                    $grand_total = $total_amount - $couponAmount;
                    //Add coupon code and amount is session variables
                    Session::put('couponAmount',$couponAmount);
                    Session::put('couponCode',$data['code']);

                    $message = "Coupon code successfully applied. You are availing discount!";
                    $totalCartItems = totalCartItems();
                    $userCartItems = Cart::userCartItems();
                    return response()->json([
                        'status'=>true,
                        'message'=>$message,
                        'totalCartItems'=>$totalCartItems,
                        'couponAmount'=>$couponAmount,
                        'grand_total'=>$grand_total,
                        'view'=>(String)View::make('front.products.cart_items')
                            ->with(compact('userCartItems'))]);
                }

            }
        }
    }
}
