@extends('layouts.front_layout.front_layout')
@section('content')
    <!-- Sidebar end=============================================== -->
    <div class="span9">
        <ul class="breadcrumb">
            <li><a href="/">Home</a> <span class="divider">/</span></li>
            <li class="active"><a href="{{url('/orders')}}">Orders</a></li>
        </ul>
        <h3> My Order #{{$orderDetails['id']}} Details</h3>
        <hr class="soft"/>
        <div class="row">
            <div class="span4">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td colspan="2"><strong>Order Details</strong></td>
                    </tr>
                    <tr>
                        <td>Order Date</td>
                        <td>{{date ('d-m-Y', strtotime($orderDetails['created_at']))}}</td>
                    </tr>
                    <tr>
                        <td>Order Status</td>
                        <td>{{$orderDetails['order_status']}}</td>
                    </tr>
                    <tr>
                        <td>Order Total</td>
                        <td>${{$orderDetails['grand_total']}}</td>
                    </tr>
                    <tr>
                        <td>Shipping Charges</td>
                        <td>${{$orderDetails['shipping_charges']}}</td>
                    </tr>
                    <tr>
                        <td>Coupon Code</td>
                        <td>{{$orderDetails['coupon_code']}}</td>
                    </tr>
                    <tr>
                        <td>Coupon Amount</td>
                        <td>{{$orderDetails['coupon_amount']}}</td>
                    </tr>
                    <tr>
                        <td>Payment Method</td>
                        <td>{{$orderDetails['payment_method']}}</td>
                    </tr>
                </table>
            </div>
            <div class="span4">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td colspan="2"><strong>Delivery Address</strong></td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>{{$orderDetails['name']}}</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>{{$orderDetails['address']}}</td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td>{{$orderDetails['city']}}</td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td>{{$orderDetails['country']}}</td>
                    </tr>
                    <tr>
                        <td>Post Code</td>
                        <td>{{$orderDetails['postcode']}}</td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>{{$orderDetails['mobile']}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="span8">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Product Size</th>
                        <th>Product Color</th>
                        <th>Product Qty</th>
                    </tr>
                    @foreach($orderDetails['orders_products'] as $product)
                        <tr>
                            <td>{{$product['product_code']}}</td>
                            <td>{{$product['product_name']}}</td>
                            <td>{{$product['product_size']}}</td>
                            <td>{{$product['product_color']}}</td>
                            <td>{{$product['product_qty']}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

    </div>
@endsection
