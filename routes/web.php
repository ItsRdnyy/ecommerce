<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PreorderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\LandingPageController;




use App\Http\Controllers\ContactController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/calculate-price', [ProductController::class, 'calculatePrice'])->name('products.calculate_price');


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'handle']);

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'handle']);

Route::get('/verify-account', [RegisterController::class, 'showVerifyForm'])->name('verify.show');
Route::post('/verify-account', [RegisterController::class, 'verifyAccount'])->name('verify.submit')->middleware('throttle:6,1');
Route::post('/resend-verification', [RegisterController::class, 'resendVerification'])->name('verify.resend')->middleware('throttle:3,1');

Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'handle'])->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'handle'])->name('password.update');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
    Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/items/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/orders/{order}/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/receive', [OrderController::class, 'receive'])->name('orders.receive');
    Route::post('/orders/{order}/items/{product}/review', [OrderController::class, 'storeReview'])->name('orders.review.store');

    
    Route::get('/preorders/create/{product}', [PreorderController::class, 'create'])->name('preorder.create');
    Route::post('/preorders/{product}', [PreorderController::class, 'store'])->name('preorder.store');
    Route::get('/preorders/{preorder}', [PreorderController::class, 'show'])->name('preorder.show');
    Route::post('/preorders/{preorder}/cancel', [PreorderController::class, 'cancel'])->name('preorder.cancel');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
});

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->isBusiness()) {
        return redirect()->route('business.dashboard');
    }
    return redirect('/');
})->middleware('auth');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/businesses', [AdminController::class, 'businesses'])->name('admin.businesses');
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::patch('/products/{product}/status', [AdminController::class, 'updateProductStatus'])->name('admin.products.status');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::patch('/reviews/{review}/status', [AdminController::class, 'updateReviewStatus'])->name('admin.reviews.status');
    Route::get('/revenue', [AdminController::class, 'revenue'])->name('admin.revenue');
    Route::post('/revenue', [AdminController::class, 'updateRevenueSettings'])->name('admin.revenue.update');
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
    Route::get('/disputes', [AdminController::class, 'disputes'])->name('admin.disputes');
    Route::patch('/disputes/{dispute}', [AdminController::class, 'updateDispute'])->name('admin.disputes.update');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::patch('/users/{user}/status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.status');
    Route::post('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::post('/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('admin.users.reject');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    Route::get('/messages', [AdminController::class, 'messages'])->name('admin.messages');
    Route::patch('/messages/{message}/status', [AdminController::class, 'updateMessageStatus'])->name('admin.messages.status');
    Route::get('/verifications', [AdminController::class, 'verifications'])->name('admin.verifications');
    Route::patch('/verifications/{profile}', [AdminController::class, 'updateVerification'])->name('admin.verifications.update');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/transactions', [AdminController::class, 'transactionLogs'])->name('admin.transactions');
});

Route::middleware(['auth', 'business'])->prefix('business')->group(function () {
    Route::get('/', function () {
        return redirect()->route('business.dashboard');
    });
    Route::get('/dashboard', [BusinessController::class, 'index'])->name('business.dashboard');
    Route::get('/profile', [BusinessController::class, 'profile'])->name('business.profile');
    Route::post('/profile', [BusinessController::class, 'updateProfile'])->name('business.profile.update');
    Route::get('/settings', [BusinessController::class, 'settings'])->name('business.settings');
    Route::post('/settings/password', [BusinessController::class, 'updatePassword'])->name('business.settings.password');
    Route::get('/products', [BusinessController::class, 'products'])->name('business.products');
    Route::get('/products/filter', [BusinessController::class, 'filterProducts'])->name('business.products.filter');
    Route::post('/products', [BusinessController::class, 'storeProduct'])->name('business.products.store');
    Route::get('/products/{product}/edit', [BusinessController::class, 'editProduct'])->name('business.products.edit');
    Route::put('/products/{product}', [BusinessController::class, 'updateProduct'])->name('business.products.update');
    Route::post('/products/{product}/archive', [BusinessController::class, 'archiveProduct'])->name('business.products.archive');
    Route::get('/products/archived', [BusinessController::class, 'archivedProducts'])->name('business.products.archived');
    Route::post('/products/{product}/restore', [BusinessController::class, 'restoreProduct'])->name('business.products.restore');
    Route::delete('/products/{product}', [BusinessController::class, 'deleteProduct'])->name('business.products.delete');
    Route::get('/products/{product}/variants', [BusinessController::class, 'productVariants'])->name('business.products.variants');
    Route::post('/products/{product}/variants', [BusinessController::class, 'storeVariant'])->name('business.products.variants.store');
    Route::get('/orders', [BusinessController::class, 'orders'])->name('business.orders');
    Route::patch('/orders/{order}/status', [BusinessController::class, 'updateOrderStatus'])->name('business.orders.status');
    Route::get('/sales', [BusinessController::class, 'sales'])->name('business.sales');
    Route::get('/discount-tiers', [BusinessController::class, 'discountTiers'])->name('business.discount_tiers');
    Route::post('/discount-tiers', [BusinessController::class, 'storeDiscountTier'])->name('business.discount_tiers.store');
    Route::patch('/discount-tiers/{tier}/toggle', [BusinessController::class, 'toggleDiscountTier'])->name('business.discount_tiers.toggle');
    Route::get('/shipping-rules', [BusinessController::class, 'shippingRules'])->name('business.shipping_rules');
    Route::post('/shipping-rules', [BusinessController::class, 'storeShippingRule'])->name('business.shipping_rules.store');
    Route::patch('/shipping-rules/{rule}/toggle', [BusinessController::class, 'toggleShippingRule'])->name('business.shipping_rules.toggle');
    Route::get('/inventory', [BusinessController::class, 'inventory'])->name('business.inventory');
    Route::post('/products/{product}/inventory', [BusinessController::class, 'updateInventory'])->name('business.inventory.update');
    Route::get('/preorders', [BusinessController::class, 'preorders'])->name('business.preorders');
    Route::post('/preorders/{preorder}/fulfill', [BusinessController::class, 'fulfillPreorder'])->name('business.preorders.fulfill');
    Route::get('/messages', [BusinessController::class, 'messages'])->name('business.messages');
    Route::patch('/messages/{message}/status', [BusinessController::class, 'updateMessageStatus'])->name('business.messages.status');
    Route::get('/analytics', [AnalyticsController::class, 'businessDashboard'])->name('business.analytics');
    Route::post('/shipments/{order}', [ShippingController::class, 'update'])->name('business.shipments.update');
    Route::post('/shipments/{shipment}/timeline', [ShippingController::class, 'addTimeline'])->name('business.shipments.timeline');
});

