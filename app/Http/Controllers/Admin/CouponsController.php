<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use App\Http\Controllers\Controller;
use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CouponsController extends Controller
{
    public function coupons()
    {
        Session::put('page', 'coupons');
        $coupons = Coupon::get()->toArray();
        return view('admin.coupons.coupons')->with(compact('coupons'));
    }

    public function updateCouponStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            if($data['status']=="Active")
            {
                $status = 0;
            }else{
                $status= 1;
            }
            Coupon::where('id',$data['coupon_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'coupon_id'=>$data['coupon_id']]);
        }
    }

    public function addEditCoupon( Request $request, $id=null)
    {
        Session::put('page', 'coupons');

        if($id=="")
        {
            $title = "Add Coupon";
            $coupon = new Coupon();
            $message = "Coupon has been added successfully!";
        }
        else {
            $title = "Edit Coupon";
            $coupon = Coupon::find($id);
            $message= "Coupon has been updated successfully!";
        }
        // Sections with Categories and Subcategories
        $categories = Section::with('Categories')->get();
        $categories = json_decode(json_encode($categories),true);
        return view('admin.coupons.add_edit_coupon')->with(compact('title','coupon','categories'));
    }
}
