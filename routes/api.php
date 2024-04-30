<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\TaskController;
use App\Http\Controllers\Frontend\FileController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\DictController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\IssueVehicleController;

/**
 * ID正则
 */
define('API_ID_REGEX', '[0-9]+');

/**
 * slug正则
 */
define('API_SLUG_REGEX', '[0-9a-zA-Z\-_]+');

/**
 * training正则
 */
define('API_TRAINING_REGEX', 'safe|skill|multiple');

/**
 * UUID正则
 */
define('API_UUID_REGEX', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/first', [AuthController::class, 'first']);
    Route::get('/option', [DictController::class, 'option']);

    Route::group(['prefix' => '/user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/statistic', [UserController::class, 'statistic']);
        Route::post('/avatar', [UserController::class, 'avatar']);
        Route::get('/skill', [UserController::class, 'skill']);
        Route::get('/department', [UserController::class, 'department']);
        Route::post('/setting', [UserController::class, 'setting']);
        Route::get('/birth', [UserController::class, 'birth']);
        Route::get('/profile', [UserController::class, 'profile']);
        Route::get('/notice', [UserController::class, 'notice']);
    });

    Route::group(['prefix' => '/file'], function () {
        Route::get('/', [FileController::class, 'index']);
        Route::post('/{id}', [FileController::class, 'view'])->where('id', API_UUID_REGEX);
    });

    //考核任务
    Route::group(['prefix' => '/task'], function () {
        Route::group(['prefix' => '/vehicle'], function () {
            Route::get('/', [TaskController::class, 'vehicle']);
            Route::get('/{id}', [TaskController::class, 'vehicleDetail']);
        });
    });

    //问题追踪
    Route::group(['prefix' => '/issue'], function () {
        Route::group(['prefix' => '/vehicle'], function () {
            Route::post('/', [IssueVehicleController::class, 'create']);
            Route::get('/list', [IssueVehicleController::class, 'list']);
            Route::get('/block', [IssueVehicleController::class, 'block']);
            Route::get('/{id}', [IssueVehicleController::class, 'detail'])->where('id', API_UUID_REGEX);
            Route::post('/{id}', [IssueVehicleController::class, 'update'])->where('id', API_UUID_REGEX);
            Route::post('/poster/{id}', [IssueVehicleController::class, 'poster'])->where('id', API_UUID_REGEX);
            Route::post('/block/{id}', [IssueVehicleController::class, 'updateBlock'])->where('id', API_UUID_REGEX);
        });
    });

    /*
    Route::group(['prefix' => '/issue'], function () {
        Route::post('/vehicle', [IssueController::class, 'vehicle']);
        Route::get('/vehicle', [IssueController::class, 'getVehicle']);
        Route::get('/vehicle/block', [IssueController::class, 'getVehicleBlock']);
        Route::get('/vehicle/{id}', [IssueController::class, 'getVehicleDetail'])->where('id', API_UUID_REGEX);
        Route::post('/vehicle/{id}', [IssueController::class, 'updateVehicle'])->where('id', API_UUID_REGEX);
        Route::post('/vehicle/block/{id}', [IssueController::class, 'updateVehicleBlock'])->where('id', API_UUID_REGEX);
        Route::post('/vehicle/poster/{id}', [IssueController::class, 'updateVehiclePoster'])->where('id', API_UUID_REGEX);
    });

    Route::group(['prefix' => '/task'], function () {
        Route::get('/vehicle', [TaskController::class, 'vehicle']);
        Route::get('/inline', [TaskController::class, 'inline']);
        Route::get('/product', [TaskController::class, 'product']);
        Route::get('/vehicle/{id}', [TaskController::class, 'vehicleDetail'])->where('id', API_UUID_REGEX);
        Route::get('/inline/{id}', [TaskController::class, 'inlineDetail'])->where('id', API_UUID_REGEX);
        Route::get('/product/{id}', [TaskController::class, 'productDetail'])->where('id', API_UUID_REGEX);
        Route::post('/vehicle/{id}', [TaskController::class, 'vehicleUpdate'])->where('id', API_UUID_REGEX);
        Route::post('/inline/{id}', [TaskController::class, 'inlineUpdate'])->where('id', API_UUID_REGEX);
        Route::post('/product/{id}', [TaskController::class, 'productUpdate'])->where('id', API_UUID_REGEX);
        Route::post('/vehicle/{id}/all', [TaskController::class, 'vehicleAll'])->where('id', API_UUID_REGEX);
        Route::post('/inline/{id}/all', [TaskController::class, 'inlineAll'])->where('id', API_UUID_REGEX);
        Route::post('/product/{id}/all', [TaskController::class, 'productAll'])->where('id', API_UUID_REGEX);
        Route::post('/vehicle/{id}/reset', [TaskController::class, 'vehicleReset'])->where('id', API_UUID_REGEX);
        Route::post('/inline/{id}/reset', [TaskController::class, 'inlineReset'])->where('id', API_UUID_REGEX);
        Route::post('/product/{id}/reset', [TaskController::class, 'productReset'])->where('id', API_UUID_REGEX);
        Route::get('/vehicle/{id}/{item_id}', [TaskController::class, 'vehicleItemDetail'])->where(['id' => API_UUID_REGEX, 'item_id' => API_UUID_REGEX]);
        Route::get('/inline/{id}/{item_id}', [TaskController::class, 'inlineItemDetail'])->where(['id' => API_UUID_REGEX, 'item_id' => API_UUID_REGEX]);
        Route::get('/product/{id}/{item_id}', [TaskController::class, 'productItemDetail'])->where(['id' => API_UUID_REGEX, 'item_id' => API_UUID_REGEX]);
        Route::get('/inline/standard', [TaskController::class, 'inlineStandard']);
        Route::get('/inline/gluing', [TaskController::class, 'inlineGluing']);
        Route::get('/inline/dynamic', [TaskController::class, 'inlineDynamic']);
        Route::get('/product/overhaul', [TaskController::class, 'productOverhaul']);
        Route::get('/product/assembling', [TaskController::class, 'productAssembling']);
        Route::get('/product/dynamic', [TaskController::class, 'productDynamic']);
        Route::post('/product/enter', [TaskController::class, 'productEnter']);
    });
    */
});