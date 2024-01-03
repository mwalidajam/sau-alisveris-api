<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, UsersController, UserAuth, ProductsController};

Route::controller(UserAuth::class)->group(
    function () {
        Route::post('/UserAuth/register', 'register');
        Route::post('/UserAuth/login', 'login');
    }
);


Route::get('/users/get-me', function (Request $request) {
    return response()->json([
        'status' => 'error',
    ], 401);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/users/get-me', [UsersController::class, 'getMe']);

    Route::controller(UsersController::class)->group(function () {
        Route::get('/users', 'index');
        Route::post('/users/create', 'create');
        Route::post('/users/update/{user}', 'update');
        Route::post('/users/roles', 'roles');
        Route::post('/users/permissions', 'permissions');
        Route::post('/users/roles-with-permissions', 'rolesWithPermissions');
        Route::post('/users/update-user-permissions-and-roles/{user}', 'updateUserPermissionsAndRoles');
        Route::get('/users/{user}', 'show');
    });

    Route::controller(ProductsController::class)->group(function () {
        Route::get('/products', 'index');
        Route::post('/products/create', 'create');
        Route::post('/products/update/{product}', 'update');
        Route::get('/products/{product}', 'show');
        Route::delete('/products/{product}', 'delete');
    });
});

Route::post('/login', [AuthController::class, 'login']);
