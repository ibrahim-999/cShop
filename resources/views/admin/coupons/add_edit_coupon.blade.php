@extends('layouts.admin_layout.admin_layout')
@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1> Catalogues </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Coupons</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if($errors->any())
                    <div class="alert alert-danger" style="margin-top: 10px;">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{$error}}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form class="couponForm" id="couponForm"
                      @if(empty($coupon['id']))
                      action="{{url('admin/add-edit-coupon')}}"
                      @else action="{{url('admin/add-edit-coupon/'.$coupon['id'])}}"
                      @endif
                      method="post" enctype="multipart/form-data">@csrf
                    @if(Session::has('success_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{Session::get('success_message')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{$title}}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="coupon_option">Coupon Option</label><br>
                                        <span><input id="AutomaticCoupon" type="radio" name="coupon_option" value="Automatic">&nbsp; Automatic
                                            &nbsp; &nbsp;</span>
                                        <span><input id="ManualCoupon" type="radio" name="coupon_option" value="Manual">&nbsp;Manual &nbsp;
                                            &nbsp; &nbsp;</span>
                                    </div>
                                    <div class="form-group" style="display: none;" id="couponField">
                                        <label for="coupon_code">Coupon Code</label>
                                        <input type="coupon_code" class="form-control" name="coupon_code" id="coupon_code" placeholder="Enter Coupon Code">
                                    </div>
                                    <div class="form-group" >
                                        <label for="coupon_code">Select Categories</label>
                                        <select name="categories[]"  class="form-control select2" multiple="">
                                            <option value="">Select</option>
                                            @foreach( $categories as $section)
                                                <optgroup label="{{$section['name']}}"></optgroup>
                                                @foreach($section['categories'] as $category)
                                                    <option value="{{$category['id']}}" @if(!empty(@old('category_id')) && $category['id'] ==
                                                            @old('category_id')) selected="" @elseif(!empty($productdata['category_id']) && $productdata['category_id']== $category['id']) selected=""
                                                        @endif>&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;{{$category['category_name']}}</option>
                                                    @foreach($category['subcategories'] as $subcategory)
                                                        <option value="{{$subcategory['id']}}"
                                                                @if(!empty(@old('category_id')) && $subcategory['id']== @old('category_id')) selected=""
                                                                @elseif(!empty($productdata['category_id']) && $productdata['category_id']==$subcategory['id'])
                                                                selected=""@endif>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;{{$subcategory['category_name']}}</option>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

@endsection
