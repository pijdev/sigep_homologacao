<?php

use App\Http\Controllers\Admin\PermissionCatalogController;
use App\Http\Controllers\Admin\SectorManagementController;
use App\Http\Controllers\Admin\UnitManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ImportacaoIpenController;
use App\Http\Controllers\Admin\DadosImporta64Controller;
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
    })->name('dashboard');

    Route::get('laradminlte-welcome', function () {
        return view('laradminlte-welcome');
    })->name('ladmin_welcome');

    Route::prefix('administracao')->name('admin.')->group(function () {
        Route::resource('usuarios', UserManagementController::class)
            ->parameter('usuarios', 'user')
            ->names('users')
            ->except(['show']);
        Route::resource('setores', SectorManagementController::class)
            ->parameter('setores', 'setor')
            ->names('sectors')
            ->except(['show']);
        Route::resource('unidades', UnitManagementController::class)
            ->parameter('unidades', 'unidade')
            ->names('units')
            ->except(['show']);
        Route::resource('permissoes', PermissionCatalogController::class)
            ->parameter('permissoes', 'permission')
            ->names('permissions')
            ->except(['show']);

        // Importação iPEN
        Route::get('importacao/1-8', [ImportacaoIpenController::class, 'index'])->name('importacao.ipen.index');
        Route::post('importacao/1-8/processar', [ImportacaoIpenController::class, 'processar'])->name('importacao.ipen.processar');
        Route::get('importacao/1-8/historico', [ImportacaoIpenController::class, 'historico'])->name('importacao.ipen.historico');
        Route::get('importacao/1-8/detalhes/{id}', [ImportacaoIpenController::class, 'detalhes'])->name('importacao.ipen.detalhes');

        // Importação Laboral (Relatório 6-4)
        Route::get('importacao/laboral/6-4', [DadosImporta64Controller::class, 'index'])->name('importacao.laboral.index');
        Route::post('importacao/laboral/6-4/processar', [DadosImporta64Controller::class, 'processar'])->name('importacao.laboral.processar');
        Route::get('importacao/laboral/6-4/historico', [DadosImporta64Controller::class, 'historico'])->name('importacao.laboral.historico');
        Route::get('importacao/laboral/6-4/detalhes/{id}', [DadosImporta64Controller::class, 'detalhes'])->name('importacao.laboral.detalhes');
    });
});
