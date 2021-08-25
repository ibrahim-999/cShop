@extends('layouts.front_layout.front_layout')
@section('content')
<div class="span9">
        <ul class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a> <span class="divider">/</span></li>
            <li class="active"> THANKS</li>
        </ul>
        <h3>  THANKS</h3>
        <hr class="soft"/>
    <div align="center">
        <h3>YOUR ORDER HAS BEEN PLACED SUCCESSFULLY!</h3>
        <p>Your order number is {{Session::get('order_id')}} and your grand total is USD {{Session::get('grand_total')}}</p>
    </div>
    </div>
@endsection
<?php
\Illuminate\Support\Facades\Session::forget('grand_total');
\Illuminate\Support\Facades\Session::forget('order_id');
?>
