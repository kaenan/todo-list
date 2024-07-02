<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoListController;

Route::get('/', [TodoListController::class, 'index']);

Route::post('/createList', [TodoListController::class, 'createList'])->name('createList');

Route::post('/saveItem', [TodoListController::class, 'saveItem'])->name('saveItem');

Route::post('/markComplete', [TodoListController::class, 'markComplete'])->name('markComplete');

Route::post('/archiveList', [TodoListController::class, 'archiveList'])->name('archiveList');

Route::post('/deleteItem', [TodoListController::class, 'deleteItem'])->name('deleteItem');
