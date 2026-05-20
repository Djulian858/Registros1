<?php

use App\Http\Controllers\PrestamosController;
use Illuminate\Support\Facades\Route;

Route::get('/',           [PrestamosController::class, 'index'])->name('prestamos.index');
Route::post('/prestamos', [PrestamosController::class, 'store'])->name('prestamos.store');
Route::get('/ver',        [PrestamosController::class, 'ver'])->name('prestamos.ver');
Route::patch('/editar',   [PrestamosController::class, 'update'])->name('prestamos.update');
Route::delete('/prestamos/{id}', [PrestamosController::class, 'destroy'])->name('prestamos.destroy');
