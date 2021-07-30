<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
use App\Category;

//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


//admin routes
Route::prefix('/admin')->namespace('Admin')->group(function(){
    Route::match(['get','post'],'/', 'AdminController@login');
    Route::group(['middleware'=>['admin']], function (){
        Route::get('dashboard', 'AdminController@dashboard');
        Route::get('settings', 'AdminController@settings');
        Route::get('logout', 'AdminController@logout');
        Route::post('check-current-pwd','AdminController@chkCurrentPassword');
        Route::post('update-current-pwd','AdminController@updateCurrentPassword');
        Route::match(['get','post'],'update-admin-details','AdminController@updateAdminDetails');

        //Sections
        Route::get('sections','SectionController@sections');
        Route::post('update-section-status','SectionController@updateSectionStatus');

        //Categories
        Route::get('categories', 'CategoryController@categories');
        Route::post('update-category-status','CategoryController@updateCategoryStatus');
        Route::match(['get','post'],'add-edit-category/{id?}','CategoryController@addEditCategory');
        Route::post('append-categories-level','CategoryController@appendCategoryLevel');
        Route::get('delete-category-image/{id}','CategoryController@deleteCategoryImage');
        Route::get('delete-category/{id}','CategoryController@deleteCategory');

        //Products
        Route::get('products', 'ProductsController@products');
        Route::post('update-product-status','ProductsController@updateProductStatus');
        Route::match(['get','post'],'add-edit-product/{id?}','ProductsController@addEditProduct');
        Route::get('delete-product/{id}','ProductsController@deleteProduct');
        Route::get('delete-product-image/{id}','ProductsController@deleteProductImage');
        Route::get('delete-product-video/{id}','ProductsController@deleteProductVideo');


        //Attributes Route
        Route::match(['get','post'],'add-attributes/{id}','ProductsController@addAttributes');
        Route::post('edit-attributes/{id}','ProductsController@editAttributes');
        Route::post('update-attribute-status','ProductsController@updateAttributeStatus');
        Route::get('delete-attribute/{id}','ProductsController@deleteAttribute');

        //Images Routes
        Route::match(['get','post'],'add-images/{id}','ProductsController@addImages');
        Route::post('update-image-status','ProductsController@updateImageStatus');
        Route::get('delete-image/{id}','ProductsController@deleteImage');

        //Brand Routes
        Route::get('brands','BrandController@brands');
        Route::post('update-brand-status','BrandController@updateBrandStatus');
        Route::match(['get','post'],'add-edit-brand/{id?}','BrandController@addEditBrand');
        Route::get('delete-brand/{id}','BrandController@deleteBrand');

        //Banner Routes
        Route::get('banners','BannerController@banners');
        Route::post('update-banner-status','BannerController@updateBannerStatus');
        Route::match(['get','post'],'add-edit-banner/{id?}','BannerController@addEditBanner');
        Route::get('delete-banner/{id}','BannerController@deleteBanner');


    });
});


// Front Routes
Route::namespace('Front')->group(function (){
    //HomePage Route
    Route::get('/','IndexController@index');
    //Listing/Categories Route
    $catUrls = Category::select('url')->where('status',1)->get()->pluck('url')->toArray();
    foreach($catUrls as $url)
    {
        Route::get('/'.$url,'ProductsController@listing');
    }
    // Product Details Route
    Route::get('/product/{id}','ProductsController@detail');

    //Get Product Attribute Price
    Route::post('/get-product-price','ProductsController@getProductPrice');

    // Add To Cart Route
    Route::post('/add-to-cart','ProductsController@addToCart');
    // Shopping Cart Route
    Route::get('/cart','ProductsController@cart');

    //Update Cart Item Qty
    Route::post('/update-cart-item-qty','ProductsController@updateCartItemQty');

    //Delete Cart Item
    Route::post('/delete-cart-item','ProductsController@deleteCartItem');

    //Login/Register Page
    Route::get('/login-register','UserController@loginRegister');

    //Login User
    Route::post('/login','UserController@loginUser');

    //Register User
    Route::post('/register','UserController@registerUser');

    //Check if email already exists
    Route::match(['get','post'],'/check-email','UserController@checkEmail');

    //Logout User
    Route::get('/logout', 'UserController@logout');

    //Confirm Account
    Route::match(['get','post'],'/confirm/{code}','UserController@confirmAccount');
});
