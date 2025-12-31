<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to(auth()->check() ? '/admin' : '/admin/login');
});
