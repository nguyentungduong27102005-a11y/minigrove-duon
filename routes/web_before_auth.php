<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ComponentController;
use App\Http\Controllers\Admin\ComponentCompatibilityRuleController;
use App\Http\Controllers\StudioController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
         * ---------------------------------------------------------------
         * Category
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/categories/{category}/toggle-status',
            [CategoryController::class, 'toggleStatus']
        )->name('categories.toggle-status');

        Route::resource(
            'categories',
            CategoryController::class
        )->except('show');


        /*
         * ---------------------------------------------------------------
         * Product
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/products/{product}/toggle-status',
            [ProductController::class, 'toggleStatus']
        )->name('products.toggle-status');

        Route::patch(
            '/products/{product}/images/{image}/primary',
            [ProductController::class, 'setPrimaryImage']
        )->name('products.images.primary');

        Route::delete(
            '/products/{product}/images/{image}',
            [ProductController::class, 'deleteImage']
        )->name('products.images.destroy');

        Route::resource(
            'products',
            ProductController::class
        )->except('show');


        /*
         * ---------------------------------------------------------------
         * Component
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/components/{component}/toggle-status',
            [ComponentController::class, 'toggleStatus']
        )->name('components.toggle-status');

        Route::patch(
            '/components/{component}/images/{image}/primary',
            [ComponentController::class, 'setPrimaryImage']
        )->name('components.images.primary');

        Route::delete(
            '/components/{component}/images/{image}',
            [ComponentController::class, 'deleteImage']
        )->name('components.images.destroy');

        Route::resource(
            'components',
            ComponentController::class
        )->except('show');


        /*
         * ---------------------------------------------------------------
         * Component Compatibility Rule
         * ---------------------------------------------------------------
         */

        Route::patch(
            '/compatibility-rules/{compatibilityRule}/toggle-status',
            [
                ComponentCompatibilityRuleController::class,
                'toggleStatus'
            ]
        )->name('compatibility-rules.toggle-status');

        Route::resource(
    'compatibility-rules',
    ComponentCompatibilityRuleController::class
)
    ->except('show')
    ->parameters([
        'compatibility-rules' => 'compatibilityRule',
    ]);

    });
/*
 * ---------------------------------------------------------------
 * MiniGrove Studio - Customer
 * ---------------------------------------------------------------
 */

Route::get(
    '/studio',
    [StudioController::class, 'index']
)->name('studio.index');

Route::post(
    '/studio/preview',
    [StudioController::class, 'preview']
)->name('studio.preview');

/*
 * Trang chủ
 */
Route::get('/', function () {
    return view('welcome');
});