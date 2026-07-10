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

// LMS Controllers
use App\Http\Controllers\Lms\HomeController as LmsHomeController;
use App\Http\Controllers\Lms\AuthController as LmsAuthController;
use App\Http\Controllers\Lms\DashboardController as LmsDashboardController;
use App\Http\Controllers\Lms\VerificationController as LmsVerificationController;
use App\Http\Controllers\Lms\PaymentController as LmsPaymentController;
use App\Http\Controllers\Lms\CertificateController as LmsCertificateController;
use App\Http\Controllers\Lms\PageController as LmsPageController;


Route::controller(StaticPageController::class)->group(function () {
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

Route::name('lms.')->group(function () {
    Route::get('/', [LmsHomeController::class, 'index'])->name('home');
    
    Route::get('/login.php', [LmsAuthController::class, 'showLogin']);
    Route::get('/login', [LmsAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [LmsAuthController::class, 'login']);
    
    Route::get('/register.php', [LmsAuthController::class, 'showRegister']);
    Route::get('/register', [LmsAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [LmsAuthController::class, 'register']);
    
    Route::get('/dashboard.php', [LmsDashboardController::class, 'index']);
    Route::get('/dashboard', [LmsDashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard', [LmsDashboardController::class, 'handleAction']);
    
    Route::get('/forgot-password.php', [LmsAuthController::class, 'showForgotPassword']);
    Route::get('/forgot-password', [LmsAuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [LmsAuthController::class, 'forgotPassword']);
    
    Route::get('/reset-password.php', [LmsAuthController::class, 'showResetPassword']);
    Route::get('/reset-password', [LmsAuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('/reset-password', [LmsAuthController::class, 'resetPassword']);
    
    Route::get('/verification.php', [LmsVerificationController::class, 'index']);
    Route::get('/verification', [LmsVerificationController::class, 'index'])->name('verification');
    Route::post('/verification', [LmsVerificationController::class, 'search']);
    
    Route::get('/verify.php', [LmsVerificationController::class, 'verifyView']);
    Route::get('/verify', [LmsVerificationController::class, 'verifyView'])->name('verify');
    Route::post('/verify', [LmsVerificationController::class, 'verify']);
    
    Route::get('/remittance.php', [LmsPaymentController::class, 'showRemittance']);
    Route::get('/remittance', [LmsPaymentController::class, 'showRemittance'])->name('remittance');
    Route::post('/remittance', [LmsPaymentController::class, 'submitRemittance']);
    
    Route::get('/checkout.php', [LmsPaymentController::class, 'showCheckout']);
    Route::get('/checkout', [LmsPaymentController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout', [LmsPaymentController::class, 'processCheckout']);
    
    Route::get('/certificate.php', [LmsCertificateController::class, 'show']);
    Route::get('/certificate', [LmsCertificateController::class, 'show'])->name('certificate');
    
    Route::get('/contact.php', [LmsPageController::class, 'contact']);
    Route::get('/contact', [LmsPageController::class, 'contact'])->name('contact');
    Route::post('/contact', [LmsPageController::class, 'applyAffiliate']);
    
    Route::get('/terms.php', [LmsPageController::class, 'terms']);
    Route::get('/terms', [LmsPageController::class, 'terms'])->name('terms');
    
    Route::get('/privacy.php', [LmsPageController::class, 'privacy']);
    Route::get('/privacy', [LmsPageController::class, 'privacy'])->name('privacy');
    
    Route::get('/logout.php', [LmsAuthController::class, 'logout']);
    Route::get('/logout', [LmsAuthController::class, 'logout'])->name('logout');
});
