<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/user/extended-profile', function () {
        return view('profile.extended-profile');
    })->name('profile.extended');

    Route::get('/user/activity-hub', function () {
        return view('user-activity.hub');
    })->name('user.activity.hub');

    // Service Request Routes
    Route::get('/service-requests/create', function () {
        return view('service-requests.create');
    })->name('service-requests.create');

    Route::get('/service-requests', function () {
        return view('service-requests.index');
    })->name('service-requests.index');

    Route::get('/service-requests/{serviceRequest}', function (App\Models\ServiceRequest $serviceRequest) {
        return view('service-requests.show', ['serviceRequest' => $serviceRequest]);
    })->name('service-requests.show');

    // QR Scanner Page
    Route::get('/qr-scanner', function () {
        return view('qr-scanner.scan');
    })->name('qr.scanner');
});

// Admin Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // User Management
        Route::get('/users', \App\Livewire\Admin\Users\UserList::class)->name('users.index');
        Route::get('/users/{user}', \App\Livewire\Admin\Users\UserDetail::class)->name('users.show');

        // Skill Management
        Route::get('/skills', \App\Livewire\Admin\Skills\SkillList::class)->name('skills.index');

        // Service Category Management
        Route::get('/service-categories', \App\Livewire\Admin\ServiceCategories\CategoryList::class)->name('service-categories.index');

        // Service Request Management
        Route::get('/service-requests', \App\Livewire\Admin\ServiceRequests\RequestManager::class)->name('service-requests.index');

        // Service Assignment Management
        Route::get('/service-assignments', \App\Livewire\Admin\ServiceAssignments\AssignmentManager::class)->name('service-assignments.index');
        // More admin routes will be added here for users, skills, etc.
    });
