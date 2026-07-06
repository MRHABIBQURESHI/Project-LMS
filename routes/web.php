<?php

use App\Http\Controllers\Panel\AgentController;
use App\Http\Controllers\Panel\AuthController;
use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\Panel\CityController;
use App\Http\Controllers\Panel\DeveloperController;
use App\Http\Controllers\Panel\ProjectController;
use App\Http\Controllers\Panel\PropertyController;
use App\Http\Controllers\Panel\PropertyTypeController;
use App\Http\Controllers\Panel\SettingController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;


Route::controller(StaticPageController::class)->group(function () {
    Route::redirect('/', '/login.php');
    Route::get('/about-us', 'about')->name('about');
    Route::get('/contact-us', 'contact')->name('contact');

    Route::get('/city/{city_slug}', 'allProjects')->name('city.index');
    Route::get('/projects', 'allProjects')->name('projects.index');
    Route::get('/project/{project_slug}', 'projectProperties')->name('project.properties.index');
    Route::get('/project/{project_slug}/property/{property_slug}', 'property_detail')->name('project.properties.show');

    // leads
    Route::post('/lead', 'store')->name('lead.store');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::middleware(['panel.access', 'auth', 'session'])
    ->prefix('panel')->name('panel.')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.store');

        Route::prefix('teams')->name('agents.')
            ->controller(AgentController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/update/{id}', 'update')->name('update');
                Route::post('/destroy/{id}', 'destroy')->name('destroy');
                Route::post('/status-change/{id}', 'statusChange')->name('status');
            });

        Route::prefix('cities')->name('cities.')
            ->controller(CityController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/update/{id}', 'update')->name('update');
                Route::post('/destroy/{id}', 'destroy')->name('destroy');
                Route::post('/status-change/{id}', 'statusChange')->name('status');
            });

        Route::prefix('developers')->name('developers.')
            ->controller(DeveloperController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/update/{id}', 'update')->name('update');
                Route::post('/destroy/{id}', 'destroy')->name('destroy');
                Route::post('/status-change/{id}', 'statusChange')->name('status');
            });

        Route::prefix('projects')->name('projects.')->group(function () {

            Route::controller(ProjectController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/update/{id}', 'update')->name('update');
                Route::post('/destroy/{id}', 'destroy')->name('destroy');
                Route::post('/status-change/{id}', 'statusChange')->name('status');
            });

            Route::prefix('property')->name('property.')
                ->controller(PropertyController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/store', 'store')->name('store');
                    Route::get('/edit/{id}', 'edit')->name('edit');
                    Route::post('/update/{id}', 'update')->name('update');
                    Route::post('/destroy/{id}', 'destroy')->name('destroy');
                    Route::post('/status-change/{id}', 'statusChange')->name('status');

                    Route::prefix('types')->name('types.')
                        ->controller(PropertyTypeController::class)->group(function () {
                            Route::get('/', 'index')->name('index');
                            Route::get('/create', 'create')->name('create');
                            Route::post('/store', 'store')->name('store');
                            Route::get('/edit/{id}', 'edit')->name('edit');
                            Route::post('/update/{id}', 'update')->name('update');
                            Route::post('/destroy/{id}', 'destroy')->name('destroy');
                            Route::post('/status-change/{id}', 'statusChange')->name('status');
                        });
                });
        });
    });
