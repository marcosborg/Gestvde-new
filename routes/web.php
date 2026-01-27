<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to(auth()->check() ? '/admin' : '/admin/login');
});

Route::post('/locale/{locale}', function (string $locale) {
    if (! in_array($locale, ['pt', 'en'], true)) {
        abort(404);
    }

    session(['locale' => $locale]);

    return back();
})->name('locale.switch');
