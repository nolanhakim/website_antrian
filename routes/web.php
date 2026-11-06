<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QueueController::class, 'kiosk'])->name('kiosk');
Route::post('/queue', [QueueController::class, 'store'])->name('queue.store');
Route::get('/ticket/{queue}', [QueueController::class, 'ticket'])->name('ticket');
Route::get('/ticket/{queue}/status', [QueueController::class, 'ticketStatus'])->name('ticket.status');
Route::get('/queue/display', [QueueController::class, 'display'])->name('display');
Route::get('/queue/display/state', [QueueController::class, 'displayState'])->name('display.state');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/operator/counter', [OperatorController::class, 'index'])->name('operator');
    Route::post('/operator/next', [OperatorController::class, 'next'])->name('operator.next');
    Route::post('/operator/recall', [OperatorController::class, 'recall'])->name('operator.recall');
    Route::post('/operator/skip', [OperatorController::class, 'skip'])->name('operator.skip');
    Route::post('/operator/finish', [OperatorController::class, 'finish'])->name('operator.finish');
    Route::post('/operator/recall/{queue}', [OperatorController::class, 'recallSkipped'])->name('operator.recallSkipped');
    Route::get('/operator/state', [OperatorController::class, 'state'])->name('operator.state');

    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/admin/queues/reset', [AdminController::class, 'resetQueues'])->name('admin.queues.reset');
        Route::get('/admin/export', [AdminController::class, 'exportCsv'])->name('admin.export');
        Route::get('/admin/counters', [AdminController::class, 'counters'])->name('admin.counters');
        Route::post('/admin/counters', [AdminController::class, 'storeCounter'])->name('admin.counters.store');
        Route::patch('/admin/counters/{counter}', [AdminController::class, 'toggleCounter'])->name('admin.counters.toggle');
        Route::delete('/admin/counters/{counter}', [AdminController::class, 'destroyCounter'])->name('admin.counters.destroy');
        Route::get('/admin/staff', [AdminController::class, 'staff'])->name('admin.staff');
        Route::post('/admin/staff', [AdminController::class, 'storeStaff'])->name('admin.staff.store');
        Route::patch('/admin/staff/{user}', [AdminController::class, 'updateStaff'])->name('admin.staff.update');
        Route::delete('/admin/staff/{user}', [AdminController::class, 'destroyStaff'])->name('admin.staff.destroy');
    });
});
