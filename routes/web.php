<?php

use Illuminate\Support\Facades\Route;

// ----------------------
// Rotas principais SPA
// ----------------------
Route::get('/', fn() => view('app'));
Route::get('/dashboard', fn() => view('app'));
Route::get('/products', fn() => view('app'));
Route::get('/configurations', fn() => view('app'));

// ----------------------
// Rotas de autenticação SPA
// ----------------------
Route::get('/login', fn() => view('auth'))->name('login');
Route::get('/auth', fn() => view('app'));
Route::get('/auth/{any}', fn() => view('app'));

// ----------------------
// Rotas de exemplo/teste
// ----------------------
Route::get('/products-test', fn() => view('products'));

// ----------------------
// Catch-all para SPA (exceto arquivos estáticos e /api)
// ----------------------
Route::get('/{any}', fn() => view('app'))
    ->where('any', '^(?!api|storage|vendor|favicon\.ico|robots\.txt|css|js|images|fonts).*$');
