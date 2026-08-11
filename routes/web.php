<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReRegistrationController;
use App\Http\Controllers\XenditWebhookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReRegistrationController as AdminReRegistrationController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\RekapController as AdminRekapController;
use App\Http\Controllers\Admin\MajorController as AdminMajorController;
use App\Http\Controllers\Admin\SchoolController as AdminSchoolController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/xendit', [XenditWebhookController::class, 'handle'])->name('webhooks.xendit');

Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role?->name;
    
    return match($role) {
        'Admin' => redirect()->route('admin.dashboard'),
        'Siswa' => redirect()->route('registration.index'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Siswa'])->group(function () {
    Route::get('/applicant/profile', [ApplicantController::class, 'edit'])->name('applicant.profile');
    Route::patch('/applicant/profile', [ApplicantController::class, 'update'])->name('applicant.profile.update');
    Route::post('/applicant/profile/check-nisn', [ApplicantController::class, 'checkNisn'])->name('applicant.profile.check-nisn');
    Route::get('/applicant/profile/review', [ApplicantController::class, 'review'])->name('applicant.profile.review');
    Route::post('/applicant/profile/confirm', [ApplicantController::class, 'confirm'])->name('applicant.profile.confirm');

    Route::get('/registrations', [RegistrationController::class, 'index'])->name('registration.index');
    Route::get('/registrations/create', [RegistrationController::class, 'create'])->name('registration.create');
    Route::get('/registrations/ranking', [RegistrationController::class, 'ranking'])->name('registration.ranking');
    Route::post('/registrations', [RegistrationController::class, 'store'])->name('registration.store');
    Route::get('/registrations/review', [RegistrationController::class, 'review'])->name('registration.review');
    Route::post('/registrations/confirm', [RegistrationController::class, 'confirm'])->name('registration.confirm');
    Route::get('/registrations/{registration}', [RegistrationController::class, 'show'])->name('registration.show');
    Route::get('/registrations/{registration}/proof', [RegistrationController::class, 'proof'])->name('registration.proof');

    Route::post('/registrations/{registration}/documents', [RegistrationController::class, 'uploadDocument'])->name('registration.documents.upload');
    Route::delete('/registrations/{registration}/documents/{document}', [RegistrationController::class, 'deleteDocument'])->name('registration.documents.delete');
    
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    
    Route::get('/registrations/{registration}/re-registration', [ReRegistrationController::class, 'create'])->name('re-registration.create');
    Route::post('/registrations/{registration}/re-registration', [ReRegistrationController::class, 'store'])->name('re-registration.store');
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/school', [AdminSchoolController::class, 'edit'])->name('school.edit');
    Route::post('/school', [AdminSchoolController::class, 'update'])->name('school.update');
    Route::post('/school/levels', [AdminSchoolController::class, 'updateLevels'])->name('school.levels.update');
    
    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{registration}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
    Route::post('/registrations/{registration}/verify', [AdminRegistrationController::class, 'verify'])->name('registrations.verify');
    Route::post('/registrations/{registration}/update-payment', [AdminRegistrationController::class, 'updatePayment'])->name('registrations.update-payment');
    Route::post('/registrations/{registration}/delete-account', [AdminRegistrationController::class, 'destroyAccount'])->name('registrations.delete-account');
    
    Route::get('/accounts', [AdminAccountController::class, 'index'])->name('accounts.index');
    Route::delete('/accounts/{user}', [AdminAccountController::class, 'destroy'])->name('accounts.destroy');
    
    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    Route::get('/rekap', [AdminRekapController::class, 'index'])->name('rekap.index');

    Route::get('/majors', [AdminMajorController::class, 'index'])->name('majors.index');
    Route::get('/majors/create', [AdminMajorController::class, 'create'])->name('majors.create');
    Route::post('/majors', [AdminMajorController::class, 'store'])->name('majors.store');
    Route::get('/majors/{major}', [AdminMajorController::class, 'show'])->name('majors.show');
    Route::get('/majors/{major}/edit', [AdminMajorController::class, 'edit'])->name('majors.edit');
    Route::patch('/majors/{major}', [AdminMajorController::class, 'update'])->name('majors.update');
    
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');
    Route::post('/payments/{payment}/reset', [AdminPaymentController::class, 'reset'])->name('payments.reset');
    
    Route::patch('/documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    Route::patch('/documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');
    
    Route::get('/re-registrations', [AdminReRegistrationController::class, 'index'])->name('re-registrations.index');
    Route::get('/re-registrations/{reRegistration}', [AdminReRegistrationController::class, 'show'])->name('re-registrations.show');
    Route::post('/re-registrations/{reRegistration}/verify', [AdminReRegistrationController::class, 'verify'])->name('re-registrations.verify');
});

require __DIR__.'/auth.php';
