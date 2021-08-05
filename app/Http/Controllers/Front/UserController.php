<?php

namespace App\Http\Controllers\Front;

use App\Cart;
use App\Country;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Authenticate;
use App\Sms;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function loginRegister()
    {
        return view('front.users.login_register');
    }

    public function registerUser(Request $request)
    {
        if($request->isMethod('post'))
        {
            Session::forget('error_message');
            Session::forget('success_message');
            $data = $request->all();
            //dd($data); die;

            $userCount = User::where('email',$data['email'])->count();

            if($userCount>0)
            {
                $message="Email already exists";
                session()->flash('error_message',$message);
                return redirect()->back();
            }
            else
            {
                $user = new User();
                $user->name = $data['name'];
                $user->mobile = $data['mobile'];
                $user->email = $data['email'];
                $user->password =bcrypt($data['password']);
                $user->status = 0 ;
                $user->save();

                // Send Email Confirmation
                $email = $data['email'];
                $messageData = [
                    'email'=>$data['email'],
                    'name'=>$data['name'],
                    'code'=>base64_encode($data['email']),
                ];

                Mail::send('emails.confirmation',$messageData,function($message) use($email){
                   $message->to($email)->subject('Confirm Your Email Account');
                });

                //Redirect Back With Success Message
                $message = "Please confirm your email to activate your account!";
                Session::put('success_message',$message);
                return redirect()->back();


               /* if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                    // Update User Cart with user_id
                    if(!empty(Session::get('session_id')))
                    {
                        $user_id = Auth::user()->id;
                        $session_id = Session::get('session_id');
                        Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);
                    }

                    // Send Register Sms
                    $message = "Dear Customer, you have been successfully registered with cShop Website. Login in to your account to access orders and offers";
                    $mobile = $data['mobile'];
                    Sms::sendSms($message, $mobile);

                    // Send Register with gmail
                    $email = $data['email'];
                    $messageData = ['name'=>$data['name'],'mobile'=>$data['mobile'],'email'=>$data['email']];
                    Mail::send('emails.register',$messageData,function($message) use ($email){
                        $message->to($email)->subject('Welcome to cShop Website');
                    });

                    return redirect('/cart');
                }*/
            }
        }
    }

    public function loginUser(Request $request)
    {
        if($request->method('post'))
        {
            Session::forget('error_message');
            Session::forget('success_message');
            $data = $request->all();
            if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']]))
            {
                //Check Email is activated or not
                $userStatus = User::where('email',$data['email'])->first();
                if($userStatus->status == 0)
                {
                    Auth::logout();
                    $message = "Your account is not activated yet! Please confirm your email to activate";
                    Session::put('error_message',$message);
                    return redirect()->back();
                }
                // Update User Cart with user_id
                if(!empty(Session::get('session_id')))
                {
                    $user_id = Auth::user()->id;
                    $session_id = Session::get('session_id');
                    Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);
                }
                return redirect('/cart');
            }
            else
            {
                $message = "Invalid Email or Password";
                Session::flash('error_message',$message);
                return redirect()->back();
            }
        }
    }

    public function confirmAccount($email)
    {
        Session::forget('error_message');
        Session::forget('success_message');
        //Decode User Email
        $email = base64_decode($email);

        //Check if User Email Exists

        $userCount = User::where('email',$email)->count();

        if($userCount>0)
        {
            //User Email is already activated or not
            $userDetails = User::where('email',$email)->first();
            if($userDetails->status == 1)
            {
                $message = "Your Email account is already activated. Please login";
                Session::put('error_message',$message);
                return redirect('login-register');

            }else {
                // Update user status to 1
                User::where('email',$email)->update(['status'=>1]);

                    // Send Register with gmail
                    $messageData = [
                        'name' => $userDetails['name'],
                        'mobile' => $userDetails['mobile'],
                        'email' =>$email];
                    Mail::send('emails.register', $messageData, function ($message) use ($email) {
                        $message->to($email)->subject('Welcome to cShop Website');
                    });
                    // Redirect User to Login Register Page with Success Message
                    $message = "Your Email is activated successfully! You can login now";
                    Session::put('success_message',$message);
                    return redirect('login-register');
            }
        }
        else {
            abort(404);
        }
    }

    public function checkEmail(Request $request)
    {
        $data = $request->all();
        $emailCount = User::where('email',$data['email'])->count();
        if($emailCount>0)
        {
            return "false";
        }
        else
            return "true";
    }

    public function forgotPassword(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            $emailCount = User::where('email',$data['email'])->count();
            if($emailCount == 0)
            {
                $message = "Email does not exist";
                Session::put('error_message',$message);
                Session::forget('success_message');
                return redirect()->back();
            }

            // Generate Random Password
                $random_password = str_random(8);
            // Encode/Secure Password
                $new_password = bcrypt($random_password);
            // Update Password
            User::where('email',$data['email'])->update(['password'=>$new_password]);
            //Get User Name
            $userName = User::select('name')->where('email',$data['email'])->first();
            // Send Forgot Password Email
            $email = $data['email'];
            $name = $userName->name;
            $messageData = [
              'email'=>$email,
                'name'=>$name,
                'password'=>$random_password,
            ];
            Mail::send('emails.forgot_password',$messageData,function($message) use ($email){
                $message->to($email)->subject('New Password - cShop Website');
            });
            $message="Please check your email for new password!";
            Session::put('success_message',$message);
            Session::forget('error_message');
            return redirect('login-register');

        }
        return view('front.users.forgot_password');
    }

    public function myAccount(Request $request)
    {
        $user_id = Auth::user()->id;
        $userDetails = User::find($user_id)->toArray();

        $countries = Country::where('status',1)->get()->toArray();

        if($request->isMethod('post'))
        {
            $data = $request->all();

            Session::forget('error_message');
            Session::forget('success_message');

            $rules = [
              'name'=>'required|regex:/^[\pL\s\-]+$/u',
              'mobile'=>'required|numeric'
            ];

            $customRules = [
              'name.required'=>'Name is required',
              'name.regex'=>'Valid name is required',
              'mobile.required'=>'Mobile is required'
            ];

            $this->validate($request,$rules,$customRules);

            $user = User::find($user_id);
            $user->name = $data['name'];
            $user->address = $data['address'];
            $user->city = $data['city'];
            $user->country = $data['country'];
            $user->postcode = $data['postcode'];
            $user->mobile = $data['mobile'];
            $user->save();
            $message = "Your account details has been updated successfully!";
            Session::put('success_message',$message);
            return redirect()->back();

        }

        return view('front.users.my_account')->with(compact('userDetails','countries'));
    }

    //Check User Current Password
    public function chkUserPassword(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            $user_id = Auth::user()->id;
            $chkPassword = User::select('password')->where('id',$user_id)->first();
            if(Hash::check($data['current_pwd'],$chkPassword->password)){
                return "true";
            }else {
                return "false";
            }
        }
    }

    //Update User  Password
    public function updateUserPassword(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            $user_id = Auth::user()->id;
            $chkPassword = User::select('password')->where('id',$user_id)->first();
            if(Hash::check($data['current_pwd'],$chkPassword->password)){
                // Update Current Password
                $new_pwd = bcrypt($data['new_pwd']);
                User::where('id',$user_id)->update(['password'=>$new_pwd]);
                $message = "Password updated successfully";
                Session::put('success_message',$message);
                Session::forget('error_message');
                return redirect()->back();
            }else {
                $message = "Current password is incorrect";
                Session::put('error_message',$message);
                Session::forget('success_message');
                return redirect()->back();
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
