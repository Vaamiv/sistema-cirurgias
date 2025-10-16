<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurgeryController;

Route::get('/surgeries', [SurgeryController::class,'apiIndex']);
