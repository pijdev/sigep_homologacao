<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Rota principal - redireciona conforme sessão
Route::get('/', function () {
    if (Auth::check()) {
        // Se está logado, vai para dashboard (home_path do LaradminLTE)
        return redirect('/laradminlte-welcome');
    } else {
        // Se não está logado, vai para login
        return redirect('/login');
    }
});

// Rotas autenticadas
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return view('laradminlte-welcome');
    });

    Route::get('laradminlte-welcome', function () {
        return view('laradminlte-welcome');
    });
});
