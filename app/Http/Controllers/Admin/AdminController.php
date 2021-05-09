<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;
use Session;
use Image;


class AdminController extends Controller
{
    public function dashboard()
    {
        Session::put('page', 'dashboard');
        return view('admin.admin_dashboard');
    }
    public function settings()
    {
        Session::put('page', 'settings');
       $adminDetails= Admin::where('email',Auth::guard('admin')->user()->email)->first();
        return view('admin.admin_settings')->with(compact('adminDetails'));
    }
    public function login(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;

            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required'
            ];

            $customMessage = [
              'email.required' => 'Email Address is required',
                'email.email' => 'Valid Email is required',
                'password.required' => 'Password is required'
            ];

            $this->validate($request,$rules,$customMessage);

            if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password']]))
            {
                return redirect('admin/dashboard');
            }
            else{
                Session::flash('error_message', ' Invalid Email or Password');
                return redirect()->back();
            }
        }
        return view('admin.admin_login');
    }
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin');
    }

    public function chkCurrentPassword(Request $request)
    {
        $data = $request->all();
        if(Hash::check($data['current_pwd'],Auth::guard('admin')->user()->password)){
          echo "true";
        }else {
            echo "false";
        }
    }

    public function updateCurrentPassword(Request $request)
    {
        Session::put('page', 'settings');
        if($request->isMethod('post'))
        {
            $data = $request->all();
            //Check if current password is correct
            if(Hash::check($data['current_pwd'],Auth::guard('admin')->user()->password))
            {
                //check if new and confirm password is matching
                if($data['new_pwd'] == $data['confirm_pwd'])
                {
                    Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_pwd'])]);
                    Session::flash('success_message',' Password has been updated successfully!');
                }
                else{
                    Session::flash('error_message', ' New password and confirm password are not matching');
                    return redirect()->back();
                }
            }else {
                Session::flash('error_message', ' Your current password is incorrect');
            }
            return redirect()->back();
        }
    }

    public function updateAdminDetails(Request $request)
    {
        Session::put('page', 'update-admin-details');
        if($request->isMethod('post'))
        {
            $data = $request->all();
            $rules = [
                'admin_name' => 'required|regex:/^[\pL\s\-]+$/U',
                'admin_mobile' => 'required|numeric',
                'admin_image' => 'image|mimes:jpeg,jpg,png,gif,svg|max:2048',

            ];
            $customMessages = [
                'admin_name.required' => 'Name is required',
                'admin_name.regex' => 'Valid Name is required',
                'admin_mobile.required' => 'Mobile is required',
                'admin_name.numeric' => 'Valid Mobile is required',
                'admin_image.image' => 'Valid image is required',

            ];
            $this->validate($request,$rules,$customMessages);
            //Upload Image
            if($request->hasFile('admin_image'))
            {
                $image_temp = $request->file('admin_image');
                if($image_temp->isValid())
                {
                    //Get image extension
                    $extension = $image_temp->getClientOriginalExtension();
                    //Generate new image name
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'images/admin_images/admin_photos/'.$imageName;
                    //Upload image
                    Image::make($image_temp)->save($imagePath);
                }else if(!empty($data['current_admin_image']))
                {
                    $imageName = $data['current_admin_image'];
                }
                else {
                    $imageName="";
                }
            }
            //Update Admin Details
            Admin::where('email',Auth::guard('admin')->user()->email)
                ->update(['name'=>$data['admin_name'],'mobile'=>$data['admin_mobile'],'image'=>$imageName]);
            Session::flash('success_message',' Admin details updated successfully!');
            return redirect()->back();
        }
        return view('admin.update_admin_details');
    }
}
