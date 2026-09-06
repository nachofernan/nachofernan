<?php

use App\Http\Controllers\Admin\EntradaController as AdminEntradaController;
use App\Http\Controllers\Admin\SesionController;
use App\Http\Controllers\EntradaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EntradaController::class, 'index'])->name('inicio');
Route::get('/categoria/{categoria:slug}', [EntradaController::class, 'porCategoria'])->name('categoria');

Route::middleware('guest')->group(function () {
    Route::get('/admin/iniciar-sesion', [SesionController::class, 'mostrarFormulario'])->name('admin.login');
    Route::post('/admin/iniciar-sesion', [SesionController::class, 'iniciarSesion']);
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/cerrar-sesion', [SesionController::class, 'cerrarSesion'])->name('logout');
    Route::redirect('/', '/admin/entradas')->name('inicio');
    Route::resource('entradas', AdminEntradaController::class)->except('show');
});

Route::get('/{slug}', [EntradaController::class, 'mostrar'])->name('entrada');
