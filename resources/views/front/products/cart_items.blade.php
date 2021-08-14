<?php use App\Product; ?>
    <table class="table table-bordered">
    <thead>
    <tr>
        <th>Product</th>
        <th colspan="2">Description</th>
        <th>Quantity/Update</th>
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
            <td>
                <div class="input-append">
                    <input class="span1" style="max-width:34px" value="{{$item['quantity']}}"
                           id="appendedInputButtons" size="16" type="text">
                    <button class="btn btnItemUpdate qtyMinus" type="button" data-cartId="{{$item['id']}}">
                        <i class="icon-minus"></i>
                    </button>
                    <button class="btn btnItemUpdate qtyPlus" type="button" data-cartId="{{$item['id']}}">
                        <i class="icon-plus"></i>
                    </button>
                    <button class="btn btn-danger btnItemDelete" type="button" data-cartId="{{$item['id']}}">
                        <i class="icon-remove icon-white"></i>
                    </button>
                </div>
            </td>
            <td>US.{{$attrPrice['product_price']}}</td>
            <td>US.{{$attrPrice['discount']}}</td>
            <td>US.{{$attrPrice['final_price'] * $item['quantity']}}</td>
        </tr>
        <?php $total_price = $total_price + ($attrPrice['final_price'] * $item['quantity']); ?>
    @endforeach
    <tr>
        <td colspan="6" style="text-align:right">Sub Total :	</td>
        <td> US.{{$total_price}}</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align:right">Coupon Discount:	</td>
        <td> US.0.00</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align:right"><strong>GRAND TOTAL (US.{{$total_price}} - US.0) =</strong></td>
        <td class="label label-important" style="display:block"> <strong> US.{{$total_price}}</strong></td>
    </tr>
    </tbody>
</table>
