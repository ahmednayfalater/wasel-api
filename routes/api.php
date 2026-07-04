<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GeneratorController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProviderController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ComplaintController;
use App\Http\Controllers\API\PosterController;
use App\Http\Controllers\API\AreaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
Route::post('/register/provider', [AuthController::class, 'registerProvider']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/generators', [GeneratorController::class, 'index']);
Route::get('/generators/search', [GeneratorController::class, 'search']);
Route::get('/generators/compare', [GeneratorController::class, 'compare']);
Route::get('/generators/{id}', [GeneratorController::class, 'show']);
Route::get('/posters', [PosterController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    // Customer Routes
    Route::middleware('role:customer')->group(function () {
        Route::post('/subscriptions', [SubscriptionController::class, 'store']);
        Route::get('/subscriptions/my', [SubscriptionController::class, 'mySubscriptions']);
        Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'cancel']);

        Route::get('/invoices/my', [InvoiceController::class, 'myInvoices']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);

        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments/my', [PaymentController::class, 'myPayments']);

        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::post('/complaints', [ComplaintController::class, 'store']);

        Route::get('/notifications/my', [NotificationController::class, 'myNotifications']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    });

    // Provider Routes
    Route::middleware('role:provider')->group(function () {
        Route::get('/provider/profile', [ProviderController::class, 'profile']);
        Route::put('/provider/profile', [ProviderController::class, 'updateProfile']);

        Route::get('/provider/generators', [GeneratorController::class, 'myGenerators']);
        Route::post('/generators', [GeneratorController::class, 'store']);
        Route::put('/generators/{id}', [GeneratorController::class, 'update']);
        Route::delete('/generators/{id}', [GeneratorController::class, 'destroy']);

        Route::get('/provider/subscribers', [ProviderController::class, 'subscribers']);
        Route::put('/subscriptions/{id}/approve', [SubscriptionController::class, 'approve']);
        Route::put('/subscriptions/{id}/reject', [SubscriptionController::class, 'reject']);

        Route::post('/invoices', [InvoiceController::class, 'store']);
        Route::get('/provider/invoices', [InvoiceController::class, 'providerInvoices']);
        Route::put('/payments/{id}/review', [PaymentController::class, 'review']);

        Route::get('/provider/reviews', [ReviewController::class, 'providerReviews']);
        Route::get('/provider/reports', [ProviderController::class, 'revenueReports']);

        Route::post('/posters', [PosterController::class, 'store']);
        Route::put('/posters/{id}', [PosterController::class, 'update']);
        Route::delete('/posters/{id}', [PosterController::class, 'destroy']);

        Route::post('/notifications/send', [NotificationController::class, 'send']);

        Route::post('/areas', [AreaController::class, 'store']);
        Route::post('/areas/{id}/join', [AreaController::class, 'join']);
        Route::get('/areas', [AreaController::class, 'index']);
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);

        Route::get('/admin/providers', [AdminController::class, 'providers']);
        Route::put('/admin/providers/{id}/approve', [AdminController::class, 'approveProvider']);
        Route::put('/admin/providers/{id}/suspend', [AdminController::class, 'suspendProvider']);

        Route::get('/admin/complaints', [AdminController::class, 'complaints']);
        Route::put('/admin/complaints/{id}', [AdminController::class, 'updateComplaint']);

        Route::get('/admin/reports', [AdminController::class, 'systemReports']);
    });
});
