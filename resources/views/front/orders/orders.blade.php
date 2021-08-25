@extends('layouts.front_layout.front_layout')
@section('content')
    <!-- Sidebar end=============================================== -->
    <div class="span9">
        <ul class="breadcrumb">
            <li><a href="/">Home</a> <span class="divider">/</span></li>
            <li class="active">Orders</li>
        </ul>
        <h3> My Orders</h3>
        <hr class="soft"/>
        <div class="row">
            <div class="span8">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Items</th>
                        <th>Payment Method</th>
                        <th>Grand Total</th>
                        <th>Ordered at</th>
                        <th>Details</th>
                    </tr>
                    @foreach($orders as $order)
                        <tr>
                            <td><a href="{{url('orders/'.$order['id'])}}">{{$order['id']}}</a></td>
                            <td>
                                @foreach($order['orders_products'] as $product)
                                    {{$product['product_code']}}<br>
                                @endforeach
                            </td>
                            <td>{{$order['payment_method']}}</td>
                            <td>${{$order['grand_total']}}</td>
                            <td>{{date ('d-m-Y', strtotime($order['created_at']))}}</td>
                            <td><a style="text-decoration: underline;" href="{{url('orders/'.$order['id'])}}">View Details</a></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

    </div>
@endsection
