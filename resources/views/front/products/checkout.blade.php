<?php use App\Product; ?>
@extends('layouts.front_layout.front_layout')
@section('content')
    <div class="span9">
        <ul class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a> <span class="divider">/</span></li>
            <li class="active"> CHECKOUT</li>
        </ul>
        <h3>  CHECKOUT [ <small><span class="totalCartItems">{{totalCartItems()}}</span> Item(s) </small>]
            <a href="{{url('/cart')}}" class="btn btn-large pull-right"><i class="icon-arrow-left"></i> Back to Cart </a></h3>
        <hr class="soft"/>

        @if(Session::has('success_message'))
            <div class="alert alert-success alert-success" role="alert" >
                {{Session::get('success_message')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php Session::forget('success_message'); ?>
        @endif
        @if(Session::has('error_message'))
            <div class="alert alert-danger " role="alert">
                {{Session::get('error_message')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php Session::forget('error_message'); ?>
        @endif

        <table class="table table-bordered">
            <tr><td><strong> DELIVERY ADDRESSES  </strong> |
                    <a href="{{url('add-edit-delivery-address')}}">Add</a></td></tr>
            @foreach($deliveryAddresses as $address)
            <tr>
                <td>
                    <div class="control-group" style="float: left; margin-top: -2px; margin-right: 5px;">
                        <input type="radio" id="address{{$address['id']}}" name="address_id" value="{{$address['id']}}"/>
                    </div>
                        <div class="control-group">
                            <label class="control-label">{{$address['name']}},{{$address['address']}},
                                {{$address['city']}},{{$address['country']}}.<span style="float: right"> P:{{$address['mobile']}}</span>
                            </label>
                        </div>
                </td>
                <td>
                    <a href="{{url('/add-edit-delivery-address/'.$address['id'])}}">Edit</a> | <a href="{{url('/delete-delivery-address/'.$address['id'])}}" class="addressDelete">Delete</a>
                </td>
            </tr>
            @endforeach
        </table>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Product</th>
                <th colspan="2">Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Category/Product <br>Discount</th>
                <th>Sub Total</th>
            </tr>
            </thead>
            <tbody>
            <?php $total_price = 0; ?>
            @foreach($userCartItems as $item)
                <?php $attrPrice = Product::getDiscountedAttrPrice($item['product_id'],$item['size']); ?>
                <tr>
                    <td> <img width="60" src="{{ asset('images/product_images/small/'.$item['product']['main_image'])}}" alt=""/></td>
                    <td colspan="2">{{$item['product']['product_name']}}<br/>
                        Color : {{$item['product']['product_color']}}<br/>
                        Code : {{$item['product']['product_code']}}<br/>
                        Size : {{$item['size']}}<br/>
                    </td>
                    <td>{{$item['quantity']}}</td>
                    <td>USD.{{$attrPrice['product_price']}}</td>
                    <td>USD.{{$attrPrice['discount']}}</td>
                    <td>USD.{{$attrPrice['final_price'] * $item['quantity']}}</td>
                </tr>
                <?php $total_price = $total_price + ($attrPrice['final_price'] * $item['quantity']); ?>
            @endforeach
            <tr>
                <td colspan="6" style="text-align:right">Sub Total :	</td>
                <td> US.{{$total_price}}</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:right">Coupon Discount:	</td>
                <td>
                    @if(Session::has('couponAmount'))
                        -  USD. {{Session::get('couponAmount')}}
                    @else
                        USD. 0
                    @endif

                </td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:right"><strong>GRAND TOTAL (USD.{{$total_price}} - <span class="couponAmount">USD.0</span>) =</strong></td>
                <td class="label label-important" style="display:block"> <strong class="grand_total"> USD.{{$total_price - Session::get('couponAmount')}}</strong></td>
            </tr>
            </tbody>
        </table>

        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>
                    <form id="ApplyCoupon" method="post" action="javascript:void(0);" class="form-horizontal"
                          @if(Auth::check()) user="1" @endif > @csrf
                        <div class="control-group">
                            <label class="control-label"><strong> PAYMENT METHODS: </strong> </label>
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                </td>
            </tr>

            </tbody>
        </table>

        <!-- <table class="table table-bordered">
         <tr><th>ESTIMATE YOUR SHIPPING </th></tr>
         <tr>
         <td>
            <form class="form-horizontal">
              <div class="control-group">
                <label class="control-label" for="inputCountry">Country </label>
                <div class="controls">
                  <input type="text" id="inputCountry" placeholder="Country">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPost">Post Code/ Zipcode </label>
                <div class="controls">
                  <input type="text" id="inputPost" placeholder="Postcode">
                </div>
              </div>
              <div class="control-group">
                <div class="controls">
                  <button type="submit" class="btn">ESTIMATE </button>
                </div>
              </div>
            </form>
          </td>
          </tr>
        </table> -->
        <a href="{{url('/cart')}}" class="btn btn-large"><i class="icon-arrow-left"></i> Back to Cart </a>
        <a href="{{url('checkout')}}" class="btn btn-large pull-right">Place Order <i class="icon-arrow-right"></i></a>

    </div>
@endsection
