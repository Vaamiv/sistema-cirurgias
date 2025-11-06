<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurgeryController;

// Home vai direto pra lista
Route::redirect('/', '/surgeries');

// CRUD público (sem auth)
Route::resource('surgeries', SurgeryController::class)->except(['show']);
Route::get('surgeries/archived', [SurgeryController::class, 'archived'])->name('surgeries.archived');

// Relatório mensal (se você usa)
Route::get('/surgeries/report', [SurgeryController::class,'monthlyReport'])->name('surgeries.report');
