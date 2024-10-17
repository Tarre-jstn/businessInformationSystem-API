<?php

use App\Http\Controllers\EditWebsiteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\GetIdController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use App\Http\Controllers\AnalyticsController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// //Pwede idelete
// Route::get('data', function(){
//     $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));
//     dd($analyticsData);
// });

Route::get('/analytics', [AnalyticsController::class, 'index']);


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/homepage', function () {
        return Inertia::render('Customer/Homepage');
    })->name('homepage');

    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::get('/website', function () {
        return Inertia::render('Website');
    })->name('website');

    Route::get('/chats', function () {
        return Inertia::render('Chats');
    })->name('chats');

    Route::get('/inventory', function () {
        return Inertia::render('Inventory');
    })->name('inventory');

    Route::get('/invoice', function () {
        return Inertia::render('Invoice');
    })->name('invoice');

    Route::get('/finance', function () {
        return Inertia::render('Finance');
    })->name('finance');

    Route::get('/settings', function () {
        return Inertia::render('Settings');
    })->name('settings');

    Route::get('/preview-homepage', function () {
        return Inertia::render('Preview_Homepage');
    })->name('preview_homepage');

    Route::get('/editWebsite1', function () {
        return Inertia::render('EditWebsite1');
    })->name('editWebsite1');

    Route::get('/editWebsite2', function () {
        return Inertia::render('EditWebsite2');
    })->name('editWebsite2');

    Route::get('/editWebsite3', function () {
        return Inertia::render('EditWebsite3');
    })->name('editWebsite3');

    Route::get('/editWebsite4', function () {
        return Inertia::render('EditWebsite4');
    })->name('editWebsite4');

    Route::get('/user-id', [GetIdController::class, 'getUserId']);
    Route::get('/business-id', [GetIdController::class, 'getBusinessId']);

    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
   
});

require __DIR__.'/auth.php';


/*Route::get('/api/products', [ProductController::class, 'index']);
Route::post('/api/products', [ProductController::class, 'store']);*/


