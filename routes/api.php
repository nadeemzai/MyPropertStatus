<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\PropertyMediaController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public browsing — published properties only, no auth required.
Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'index']); // list + filters
    Route::get('/{id}', [PropertyController::class, 'show']); // single property
    Route::get('/{id}/media', [PropertyMediaController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/my-properties', [PropertyController::class, 'mine']); // my own, any status

    Route::prefix('properties')->group(function () {
    Route::post('/', [PropertyController::class, 'store']); // create
    Route::put('/{id}', [PropertyController::class, 'update']); // update
    Route::delete('/{id}', [PropertyController::class, 'destroy']); // delete

    Route::post('/{id}/media', [PropertyMediaController::class, 'store']);
    Route::delete('/{id}/media/{mediaId}', [PropertyMediaController::class, 'destroy']);
    });

    Route::prefix('listings')->group(function () {
    Route::get('/', [ListingController::class, 'index']); // listings on my properties
    Route::post('/{id}/approve', [ListingController::class, 'approve']);
    Route::post('/{id}/reject', [ListingController::class, 'reject']);
    Route::post('/{id}/remove-agency', [ListingController::class, 'removeAgency']);
    });

    Route::prefix('connections')->group(function () {
    Route::get('/', [ConnectionController::class, 'index']); // connections aimed at my properties
    Route::post('/{id}/accept', [ConnectionController::class, 'accept']);
    Route::post('/{id}/reject', [ConnectionController::class, 'reject']);
    });

    Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/{id}/read', [NotificationController::class, 'markRead']);
    });

    Route::prefix('support-tickets')->group(function () {
    Route::get('/', [SupportTicketController::class, 'index']);
    Route::get('/{id}', [SupportTicketController::class, 'show']);
    Route::post('/', [SupportTicketController::class, 'store']);
    });
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('agency')->middleware('agency.api_key')->group(function () {
    Route::post('/listings', [ListingController::class, 'store']); // propose a listing
    Route::patch('/listings/{id}/status', [ListingController::class, 'updateStatus']);

    Route::prefix('connections')->group(function () {
    Route::get('/', [ConnectionController::class, 'indexForAgency']);
    Route::post('/', [ConnectionController::class, 'store']); // initiate a connection
    });
});
