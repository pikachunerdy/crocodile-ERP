<?php
use Illuminate\Support\Facades\Route;
Route::get('store-inventory', 'StoreInventoryController@welcome');


Route::group(['middleware' => ['auth:web']], function () {

    Route::group(['prefix' => 'store-inventory/categories'], function () {
        Route::get('/', 'CategoryController@index')->name('storeInventory.categories.index');
        Route::get('/create', 'CategoryController@create')->name('storeInventory.categories.create');
        Route::post('/store', 'CategoryController@store')->name('storeInventory.categories.store');
        Route::get('/{id}/edit', 'CategoryController@edit')->name('storeInventory.categories.edit');
        Route::post('/update', 'CategoryController@update')->name('storeInventory.categories.update');
        Route::get('/{id}/delete', 'CategoryController@delete')->name('storeInventory.categories.delete');
    });

    Route::group(['prefix' => 'store-inventory/brand'], function () {
        Route::get('/', 'BrandController@index')->name('storeInventory.brands.index');
        Route::get('students/list', [StudentController::class, 'getStudents'])->name('students.list');
        Route::get('/create', 'BrandController@create')->name('storeInventory.brands.create');
        Route::post('/store', 'BrandController@store')->name('storeInventory.brands.store');
        Route::get('/{id}/edit', 'BrandController@edit')->name('storeInventory.brands.edit');
        Route::post('/update', 'BrandController@update')->name('storeInventory.brands.update');
        Route::get('/{id}/delete', 'BrandController@delete')->name('storeInventory.brands.delete');
    });
});
