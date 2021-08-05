@extends('layouts.front_layout.front_layout')
@section('content')
    <!-- Sidebar end=============================================== -->
    <div class="span9">
        <ul class="breadcrumb">
            <li><a href="/">Home</a> <span class="divider">/</span></li>
            <li class="active">Login</li>
        </ul>
        <h3> My Account</h3>
        <hr class="soft"/>
        @if(Session::has('success_message'))
            <div class="alert alert-success alert-success" role="alert" >
                {{Session::get('success_message')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div class="alert alert-danger" role="alert">
                {{Session::get('error_message')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="margin-top: 10px;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="span4">
                <div class="well">
                    <h5>CONTACT DETAILS</h5><br/>
                    Enter your contact details to create an account.<br/><br/>
                    <form id="accountForm" action="{{url('/account')}}" method="post">@csrf
                        <div class="control-group">
                            <label class="control-label" for="name">Name</label>
                            <div class="controls">
                                <input class="span3"  type="text" name="name" id="name"
                                       placeholder="Enter Name" value="{{$userDetails['name']}}">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="address">Address</label>
                            <div class="controls">
                                <input class="span3"  type="text" name="address" id="address"
                                       placeholder="Enter Address" value=" {{$userDetails['address']}}">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="postalcode">Postal Code</label>
                            <div class="controls">
                                <input class="span3"  type="text" name="postcode" id="postcode"
                                       placeholder="Enter Postal Code" value=" {{$userDetails['postcode']}}">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="city">City</label>
                            <div class="controls">
                                <input class="span3"  type="text" name="city" id="city"
                                       placeholder="Enter City" value=" {{$userDetails['city']}}">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="country">Country</label>
                            <div class="controls">
                                <select class="span3" id="country" name="country">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country['country_name']}}"
                                                @if($country['country_name']==$userDetails['country']) selected="" @endif>
                                                {{$country['country_name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="mobile">Mobile</label>
                            <div class="controls">
                                <input class="span3"  type="text" name="mobile" id="mobile"
                                       placeholder="Enter Mobile" value="{{$userDetails['mobile']}}" >
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="email">E-mail</label>
                            <div class="controls">
                                <input class="span3" value="{{$userDetails['email']}}" readonly="">
                            </div>
                        </div>
                        <div class="controls">
                            <button type="submit" class="btn block">Update</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="span1"> &nbsp;</div>
            <div class="span4">
                <div class="well">
                    <h5>Update Password</h5>
                    <form id="passwordForm" action="{{url('/update-user-pwd')}}" method="post">@csrf
                        <div class="control-group">
                            <label class="control-label" for="current_pwd">Current Password</label>
                            <div class="controls">
                                <input class="span3"  type="password" name="current_pwd" id="current_pwd" placeholder="Enter Current Password"><br>
                                <span id="chkPwd"></span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="new_pwd">New Password</label>
                            <div class="controls">
                                <input class="span3"  type="password" name="new_pwd" id="new_pwd" placeholder="Enter New Password">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="confirm_pwd">Confirm Password</label>
                            <div class="controls">
                                <input class="span3"  type="password" name="confirm_pwd" id="confirm_pwd" placeholder="Confirm Password">
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
