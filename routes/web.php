<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $registeredWorkshops = [];
    if (auth()->check()) {
        $registeredWorkshops = auth()->user()->registrations()
            ->with('workshop')
            ->get()
            ->pluck('workshop');
    }
    return view('welcome', ['registeredWorkshops' => $registeredWorkshops]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::middleware(['auth'])->group(function () {
            Route::view('admin/users', 'pages.admin.users')->name('admin.users');
            Route::view('student/registrations', 'pages.student.registrations')->name('student.registrations');
        }
        );    });

require __DIR__ . '/settings.php';
