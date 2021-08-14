<?php

namespace App\Http\Controllers\Admin;

use App\Coupon;
use App\Http\Controllers\Controller;
use App\Section;
use App\User;
use Illuminate\Database\Eloquent\Model;
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
            $coupon = new Coupon();
            $selCats = array();
            $selUsers = array();
            $title = "Add Coupon";
            $message = "Coupon has been added successfully!";
        }
        else {
            $coupon = Coupon::find($id);
            $title = "Edit Coupon";
            $selCats = explode(',', $coupon['categories']);
            $selUsers = explode(',' , $coupon['users']);
            $message= "Coupon has been updated successfully!";
        }

        if($request->isMethod('post'))
        {
            $data = $request->all();

            $rules = [
                'categories'=>'required',
                'coupon_option'=>'required',
                'coupon_type'=>'required',
                'amount_type'=>'required',
                'amount'=>'required|numeric',
                'expiry_date'=>'required',

            ];
            $customMessages = [
                'categories.required'=>'Select Category',
                'coupon_option.required'=>'Select Coupon Option',
                'coupon_type.required'=>'Select Coupon Type',
                'amount_type.required'=>'Select Amount Type',
                'amount_type.numeric'=>'Enter Valid Amount',
                'expiry_date.required'=>'Enter Expiry Date',

            ];
            $this->validate($request,$rules,$customMessages);

            if(isset($data['users']))
            {
                $users = implode(',',$data['users']);
            }else {
                $users="";
            }

            if(isset($data['categories']))
            {
                $categories = implode(',',$data['categories']);
            }

            if($data['coupon_option']=="Automatic")
            {
                $coupon_code = str_random(8);
            }else {
                $coupon_code = $data['coupon_code'];
            }
            $coupon->coupon_option = $data['coupon_option'];
            $coupon->coupon_code = $coupon_code;
            $coupon->categories = $categories;
            $coupon->users = $users;
            $coupon->coupon_type = $data['coupon_type'];
            $coupon->amount_type = $data['amount_type'];
            $coupon->amount = $data['amount'];
            $coupon->expiry_date = $data['expiry_date'];

            $coupon->status = 1;
            $coupon->save();
            session::flash('success_message',$message);
            return redirect('admin/coupons');


        }
        // Sections with Categories and Subcategories
        $categories = Section::with('Categories')->get();
        $categories = json_decode(json_encode($categories),true);

        //Users
        $users = User::select('email')->where('status',1)->get()->toArray();

        return view('admin.coupons.add_edit_coupon')->with(compact('title',
            'coupon',
                      'categories',
                      'users',
                      'selCats',
                      'selUsers'));
    }


    public function deleteCoupon($id)
    {
        //Delete Coupon
        Coupon::where('id',$id)->delete();
        $message = 'Coupon has been deleted!';
        session()->flash('success_message',$message);
        return redirect()->back();
    }
}
