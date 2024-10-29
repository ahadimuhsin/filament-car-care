<?php

use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Route;

Route::controller(FrontController::class)->group(function(){
    Route::group(["as" => "front."], function(){
        Route::get("/", "index")->name("index");

        Route::get("/transactions", "transactions")->name("transactions");

        Route::post("transactions/details", "transactionDetails")->name("transactions.detail");

        Route::get("search", "search")->name("search");

        Route::get("store/detail/{carStore:slug}", "detail")->name("detail");

        Route::prefix("booking")->name("booking.")->group(function(){

            Route::post("payment/submit", "storeBookingPayment")->name("payment.store");
            Route::get("/{carStore:slug}", "booking")->name("list");
            Route::post("/{carStore:slug}/{carService:slug}", "storeBooking")->name("store");
            Route::get("/{carStore}/{carService}/payment", 'bookingPayment')->name("payment");
            Route::get("success/{bookingTransaction}", "success")->name("success");
        });
    });
});
