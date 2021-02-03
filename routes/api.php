<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\TodoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post("register", [UserController::class, "register"]);

Route::post("login", [UserController::class, "login"]);

Route::middleware('auth:api')->group(function() {

    Route::get("user", [UserController::class, "user"]);

    # Route::resource("todo", TodoController::class);

    Route::post("todo", [TodoController::class, "createTodo"]);

    Route::get("todo", [TodoController::class, "getTodoList"]);

    Route::get("todo/{id}", [TodoController::class, "getTodo"]);

    Route::post("todo/{id}", [TodoController::class, "updateTodo"]);

    Route::delete("todo/{id}", [TodoController::class, "deleteTodo"]);

    Route::post("todo/{id}/change-status", [TodoController::class, "changeTodoStatus"]);

    Route::post("logout", [UserController::class, "logout"]);

});
