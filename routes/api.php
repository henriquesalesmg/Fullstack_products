
<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, SettingController, ProductController};


// Rotas de autenticação
Route::middleware('api')->group(function () {
    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/request-password-reset', [AuthController::class, 'requestPasswordReset']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    // Recuperação customizada
    Route::post('/auth/verify-user', [AuthController::class, 'verifyUserForReset']);
    Route::post('/auth/reset-password', [AuthController::class, 'customResetPassword']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

    // Rotas protegidas por autenticação
    Route::middleware('auth:sanctum')->group(function () {
        // Produtos
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/stats', [ProductController::class, 'stats']);
        Route::get('/products/earnings', [ProductController::class, 'earnings']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Dashboard
        Route::get('/dashboard', [ProductController::class, 'dashboard']);

        // Usuário
        Route::get('/user', [AuthController::class, 'user']);

        // Configurações
        Route::put('/settings', [SettingController::class, 'update']);
        Route::delete('/settings/delete', [SettingController::class, 'delete']);
    });
});
