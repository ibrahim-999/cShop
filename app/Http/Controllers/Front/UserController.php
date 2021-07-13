<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Authenticate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                $user->status =1;
                $user->save();

                if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                    return redirect('/');
                }
            }
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

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
