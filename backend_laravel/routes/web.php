<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response('Loyalty backend is running', 200);
});
