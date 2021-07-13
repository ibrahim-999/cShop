var $=jQuery.noConflict();
$(document).ready(function (){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
/*   $("#sort").on('change',function(){
      this.form.submit();
   });*/

    $("#sort").on('change',function () {
       //alert("test");
        var sort = $(this).val();
        var url = $("#url").val();
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');

        $.ajax({
            url:url,
            method:"post",
            data:{fabric :fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function (data){
                $('.filter_products').html(data);
            }
        })
        //alert("test");
    });

    $(".fabric").on('click',function () {
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").val();
        var url = $("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric :fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function (data){
                $('.filter_products').html(data);
            }
        })
    });
    $(".sleeve").on('click',function () {
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").val();
        var url = $("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric :fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function (data){
                $('.filter_products').html(data);
            }
        })
    });

    $(".pattern").on('click',function () {
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").val();
        var url = $("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric :fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function (data){
                $('.filter_products').html(data);
            }
        })
    });
    $(".fit").on('click',function () {
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").val();
        var url = $("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric :fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function (data){
                $('.filter_products').html(data);
            }
        })
    });
    $(".occasion").on('click',function () {
        var fabric = get_filter('fabric');
        var sleeve = get_filter('sleeve');
        var pattern = get_filter('pattern');
        var fit = get_filter('fit');
        var occasion = get_filter('occasion');
        var sort = $("#sort option:selected").val();
        var url = $("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric :fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success:function (data){
                $('.filter_products').html(data);
            }
        })
    });

    function get_filter(class_name){
        var filter = [];
        $('.'+class_name+':checked').each(function () {
            filter.push($(this).val());
        });
        return filter
    }

    $("#getPrice").change(function () {
        var size = $(this).val();
        if(size ==""){
            alert("Please select Size");
            return false;
        }
        var product_id = $(this).attr("product-id");
        $.ajax({
            url:'/get-product-price',
            data:{size:size,product_id:product_id},
            type:'post',
            success:function (resp) {
                if(resp['discount']>0)
                {
                    $(".getAttrPrice").html("<del>US. "+resp['product_price']+"</del> US."+resp['final_price']);
                }else {
                    $(".getAttrPrice").html("US. "+resp['product_price']);
                }

            },
            error:function () {
                alert("Error")
            }
        });
    });

    //Update Cart Items
    $(document).on('click','.btnItemUpdate',function () {
        if($(this).hasClass('qtyMinus'))
            {
                // if qtyMinus button gets clicked
                var quantity = $(this).prev().val();
                if(quantity<= 1){
                    alert("Item quantity must be 1 or greater!");
                    return false;
                }
                else
                {
                    new_qty = parseInt(quantity)-1;
                }
            }
        if ($(this).hasClass('qtyPlus'))
        {
            // if qtyPlus button gets clicked
            var quantity = $(this).prev().prev().val();
            new_qty = parseInt(quantity)+1;
        }
        var cartid = $(this).data('cartid');
        $.ajax({
           data:{"cartid":cartid,"qty":new_qty},
            url:'/update-cart-item-qty',
            type:'post',
            success:function (resp) {
               if(resp.status==false)
               {
                   alert(resp.message);
               }
                $("#AppendCartItems").html(resp.view);
            },
            error:function () {
                alert("Error");
            }
        });
    });

    //Delete Cart Items
    $(document).on('click','.btnItemDelete',function () {
        var cartid = $(this).data('cartid');
        var result = confirm("Want to delete this Cart Item");
        if (result)
        {
            $.ajax({
                data:{"cartid":cartid},
                url:'/delete-cart-item',
                type:'post',
                success:function (resp) {
                    $("#AppendCartItems").html(resp.view);
                },
                error:function () {
                    alert("Error");
                }
            });
        }

    });
    // validate signup form on keyup and submit
    $("#registerForm").validate({
        rules: {
            name: "required",
            mobile: {
                required: true,
                minlength:11,
                maxlength:14,
                digits:true
            },
            password: {
                required: true,
                minlength: 8
            },
            email: {
                required: true,
                email: true,
                remote:"check-email"
            },
        },
        messages: {
            name: "Please enter your name",
            mobile: {
                required:"Please enter your mobile",
                minlength: "Your mobile must be at least 11 digits long",
                maxlength: "Your mobile must be at most 14 digits long",
                digits: "Please enter a valid mobile",
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 8 characters long"
            },
            email: {
                required:"Please enter your email",
                email:"Please enter a valid email",
                remote:"Email already exists"

            }
        }
    });
    $("#loginForm").validate({
        rules: {
            password: {
                required: true,
                minlength: 8
            },
            email: {
                required: true,
                email: true,

            },
        },
        messages: {
            password: {
                required: "Please enter your password",
                minlength: "Your password must be at least 8 characters long"
            },
            email: {
                required:"Please enter your email",
                email:"Please enter a valid email",
                

            }
        }
    });

});
