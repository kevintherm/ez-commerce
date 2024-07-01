<?php

use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UtilitiesController;
use App\Http\Controllers\DashboardShopController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\DashboardCatalogController;
use App\Http\Controllers\DashboardMakeShopController;
use App\Http\Controllers\DashboardOrderListController;
use App\Http\Controllers\DashboardProductsController;
use App\Http\Controllers\NotificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


# Untitled
Route::post('payments/midtrans-notification', [PaymentCallbackController::class, 'receive']);


## Redirect ##
Route::redirect('/dashboard/shop', '/shop');

## Global Middleware Routes ##
Route::middleware(['throttle:global', 'verified'])->group(function () {

    ## Notification ##
    Route::resource('notification', NotificationController::class)->only(['index', 'store', 'destroy']);


    ## Home ##
    Route::controller(HomeController::class)->withoutMiddleware('verified')->group(function () {
        Route::get('/{home}', 'index')->where('home', 'home|beranda|')->name('home');
        Route::get('/products', 'searchProducts');
        Route::get('/shops', 'searchShops');
    });

    ## Auth ##
    Route::controller(AuthController::class)->withoutMiddleware('verified')->group(function () {
        Route::get('/login', 'login')->name('login')->middleware('guest');
        Route::get('/register', 'register')->name('register')->middleware('guest');
        Route::post('/login', 'attemptLogin')->middleware(['guest', 'throttle:login']);
        Route::post('/register', 'storeRegister')->middleware(['guest', 'throttle:register']);
        Route::get('/logout', 'logout')->middleware('auth');
        Route::get('/forgot', 'forgot')->name('auth.forgot');
    });

    ## Auth > Email Verification ##
    Route::prefix('email')->withoutMiddleware('verified')->group(function () {
        Route::get('/verify', function () {
            return view('auth.verify_email', ['title' => 'Verify Email']);
        })->middleware('auth')->name('verification.notice');

        Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect('/home');
        })->middleware(['auth', 'signed'])->name('verification.verify');

        Route::post('/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();

            return back()->with('alert', 'Verification link sent!');
        })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
    });



    # Utilities
    Route::controller(UtilitiesController::class)->withoutMiddleware('verified')->group(function () {
        Route::prefix('utilities')->group(function () {
            Route::get('/autocomplete', 'autocomplete');
            Route::post('/inf-item', 'infiniteItem');
            Route::get('/getslug', 'getSlug');
        });
    });

    # Product Category
    Route::controller(ProductCategoryController::class)->withoutMiddleware('verified')->group(function () {
        Route::get('/category', 'index')->name('category.index');
        Route::get('/category/{category}/', function () {
            return redirect('/category');
        });
        Route::get('/category/{category}/{sub_category}', 'show');
        Route::post('/category', 'store')->name('category.store')->middleware('verified');
        Route::post('/category/subcategory', 'subcategory_store')->name('category.subcategory.store')->middleware('verified');
    });

    # Cart
    Route::controller(CartController::class)->middleware('auth')->group(function () {
        Route::get('/cart_view', 'cart_view');
        Route::prefix('cart')->group(function () {
            Route::get('/', 'cartView');
            Route::get('/delete/{slug}/{id}', 'deleteItem');
            Route::get('/{id}/edit', 'editItem');
            Route::post('/{id}', 'updateItem');
            Route::post('/', 'addToCart');
            Route::delete('/deleteall', 'deleteAll');
        });
    });

    # Wishlist
    Route::get('/wishlist_view', [WishlistController::class, 'wishlist_view']);
    Route::resource('wishlist', WishlistController::class)->only(['index', 'wishlist_view', 'store', 'destroy']);

    # Order(s)
    Route::get('/orders/{order}/invoice', [OrderController::class, 'getInvoice'])->name('orders.invoice');
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'store']);

    # Ratings
    Route::get('give-ratings/{order}/{product:id}', [RatingController::class, 'giveRatings'])
        ->name('ratings.create');
    Route::post('store-ratings/{order}/{product}', [RatingController::class, 'storeRatings'])
        ->name('ratings.store');

    # Create Shop
    Route::resource('/my-shop', DashboardMakeShopController::class)->only(['create', 'store'])->middleware(['auth', 'cors']);

    # Dashboard Shop
    Route::controller(DashboardShopController::class)->middleware(['auth', 'UserHasShop'])->group(function () {
        Route::get('/shop', 'index');
        // Route::delete('/shop', 'destroy');
    });

    ## Dashboard Shop > Products
    Route::prefix('shop')->middleware(['auth', 'UserHasShop'])->group(function () {
        Route::delete('/products/deleteall', [DashboardProductsController::class, 'snap'])->name('products.snap');
        Route::resource('/products', DashboardProductsController::class)->except('index');
        Route::resource('/order-list', DashboardOrderListController::class)->only(['index', 'update', 'destroy']);
    });

    ## Dashboard Shop > Catalog
    Route::prefix('shop')->middleware(['auth', 'UserHasShop'])->group(function () {
        Route::resource('/catalogs', DashboardCatalogController::class)->except('index');
    });

    # Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
    Route::prefix('dashboard')->middleware('auth')->group(function () {

        # Dashboard User
        Route::resource('/profile', ProfileController::class)->only(['index', 'update', 'destroy'])->withoutMiddleware('verified');
        Route::patch('/profile/{profile}/updatePassword', [ProfileController::class, 'updatePassword']);
    });

    # Shop
    Route::controller(ShopController::class)->withoutMiddleware('verified')->group(function () {
        Route::get('/{shop:url}', 'index')->name('shop.index');
        Route::get('/{shop}/products', 'all');
        Route::get('/{shop}/catalog', function () {
            return redirect('/{shop}#tabs-2');
        });
        Route::get('/{shop}/{product}', 'show')->name('products.show');
        Route::get('/{shop}/catalog/{catalog}', 'catalog');
    });
});
