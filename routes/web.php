<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\TryoutController;
use App\Http\Controllers\KategoriSoalController;
use App\Http\Controllers\PackageMappingController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ScoringSettingController;
use App\Http\Controllers\SimulasiNilaiController;
use App\Http\Controllers\LaporanKemampuanController;

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index']);


//Routes Harga Paket
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');

// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('post.register');
Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('post.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('payment/process/{transaction_id}', [SubscriptionController::class, 'process'])->name('payment.process');

Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('reset-password');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('post.reset-password');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy');


// Special route for leaving impersonation (outside admin middleware)
Route::get('/leave-impersonation', [App\Http\Controllers\ImpersonateController::class, 'leave'])->name('leave.impersonation');

// Route untuk subscription dan proses Midtrans
// Route::get('subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
Route::get('subscription/process', [SubscriptionController::class, 'process'])->name('subscription.process');
Route::get('subscription/get-fee', [SubscriptionController::class, 'getFee'])->name('subscription.get-fee');
Route::post('subscription/notification', [SubscriptionController::class, 'notification'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('subscription.notification');
Route::get('subscription/finish', [SubscriptionController::class, 'finish'])->name('subscription.finish');
Route::get('subscription/unfinish', [SubscriptionController::class, 'unfinish'])->name('subscription.unfinish');
Route::get('subscription/error', [SubscriptionController::class, 'error'])->name('subscription.error');

Route::middleware(['auth'])->group(function () {
    // User Dashboard (distinct from admin dashboard)
    Route::get('/dashboard', function () {
        // Redirect to a suitable landing for regular users
        return redirect()->route('user.history.index');
    })->name('user.dashboard');

    Route::get('/profile/{userId}', [UserController::class, 'show'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::get('/profile/{userId}/paket-lengkap-summary', [UserController::class, 'paketLengkapSummary'])->name('user.profile.paket-lengkap-summary');

    // Test result detail routes
    Route::get('/test-result/{id}', [UserController::class, 'testResultDetail'])->name('user.test.result.detail');

    // API routes for location data
    Route::get('/api/provinces', [UserController::class, 'getProvinces']);
    Route::get('/api/regencies/{provinceId}', [UserController::class, 'getRegencies']);

    // Route::get('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/packages', [SubscriptionController::class, 'packages'])->name('subscription.packages');
    Route::get('/subscription/packages/admin', [SubscriptionController::class, 'packagesAdmin'])->name('subscription.packages.admin');
    Route::get('/subscription/process/{package}', [SubscriptionController::class, 'processSubscription'])->name('subscription.process.packages');
    Route::get('/subscription/checkout/{package}', [SubscriptionController::class, 'showCheckout'])->name('subscription.checkout');
    Route::get('/test', [UserController::class, 'showTest'])->name('show.test');


    Route::middleware(['subscription'])->group(function () {

        // Tryout CBT Routes - logic akses dihandle di controller
        Route::get('/tryout', [TryoutController::class, 'userIndex'])->name('user.tryout.index');
        Route::post('/tryout', [TryoutController::class, 'userIndex'])->name('user.tryout.index.post');
        Route::get('/tryout/{tryout}/start', [TryoutController::class, 'start'])->name('user.tryout.start');
        Route::get('/{tryout}/restart', [TryoutController::class, 'restart'])->name('user.tryout.restart');
        Route::get('/tryout/{tryout}/work', [TryoutController::class, 'work'])->name('user.tryout.work');
        Route::get('{tryout}/remaining-time', [TryoutController::class, 'getRemainingTime'])->name('remaining-time');
        Route::post('/tryout/{tryout}/reset-answer', [TryoutController::class, 'resetAnswer'])->name('user.tryout.reset-answer');
        Route::post('/tryout/{tryout}/submit-answer', [TryoutController::class, 'submitAnswer'])->name('user.tryout.submit-answer');
        Route::post('/tryout/{tryout}/toggle-mark', [TryoutController::class, 'toggleMark'])->name('user.tryout.toggle-mark');
        Route::get('/debug-seed/{tryout}', [TryoutController::class, 'debugSessionSeed'])->name('debug.session.seed');
        Route::get('/tryout/{tryout}/finish', [TryoutController::class, 'finish'])->name('user.tryout.finish');
        Route::get('/tryout/paket-lengkap/status', [TryoutController::class, 'paketLengkapStatus'])->name('user.tryout.paket-lengkap.status');
    });


    // History routes
    Route::get('/riwayat-tes', [HistoryController::class, 'index'])->name('user.history.index');

    // Simulasi Nilai (accessible to authenticated users)
    Route::get('/simulasi/nilai', [SimulasiNilaiController::class, 'index'])->name('simulasi.nilai');
    Route::post('/simulasi/nilai', [SimulasiNilaiController::class, 'calculate'])->name('simulasi.nilai.calculate');
    Route::post('/simulasi/nilai/reset', [SimulasiNilaiController::class, 'reset'])->name('simulasi.nilai.reset');
    Route::get('/simulasi/nilai/settings', [SimulasiNilaiController::class, 'getSettings'])->name('simulasi.nilai.settings');

});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'userList'])->name('users.index');
    Route::get('/users/{id}', [AdminController::class, 'userDetail'])->name('users.detail');
    Route::get('/subscriptions', [AdminController::class, 'subscriptionList'])->name('subscriptions.index');
    Route::get('/revenue', [AdminController::class, 'revenueReport'])->name('revenue.report');
    Route::put('/users/{id}', [AdminController::class, 'update'])->name('users.update');
    Route::get('/riwayat-tes', [AdminController::class, 'riwayatTes'])->name('riwayat-tes');
    Route::get('/riwayat-tes/user/{userId}', [AdminController::class, 'riwayatTesUser'])->name('riwayat-tes-user');
    Route::get('/test-detail/{testId}', [AdminController::class, 'getTestDetail'])->name('admin.test-detail');
    Route::get('/test-pdf/{testId}', [AdminController::class, 'downloadTestPDF'])->name('admin.test-pdf');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::put('/users/{user}/package', [AdminController::class, 'updatePackage'])->name('users.updatePackage');

    // Impersonate Routes (using custom controller for debugging)
    Route::get('/impersonate/take/{id}/{guardName?}', [App\Http\Controllers\ImpersonateController::class, 'take'])->name('impersonate');
    Route::get('/impersonate/leave', [App\Http\Controllers\ImpersonateController::class, 'leave'])->name('impersonate.leave');

    // CBT Admin Routes
    Route::resource('kategori', KategoriSoalController::class);
    Route::post('kategori/{kategori}/toggle-status', [KategoriSoalController::class, 'toggleStatus'])->name('kategori.toggle-status');

    Route::resource('soal', SoalController::class);
    Route::post('soal/upload-word', [SoalController::class, 'uploadWord'])->name('soal.upload-word');
    Route::get('soal/kepribadian-categories', [SoalController::class, 'getKepribadianCategories'])->name('soal.kepribadian-categories');
    Route::get('soal/akademik-categories', [SoalController::class, 'getAkademikCategories'])->name('soal.akademik-categories');

    Route::resource('tryout', TryoutController::class);
    Route::post('tryout/{tryout}/toggle-status', [TryoutController::class, 'toggleStatus'])->name('tryout.toggle-status');

    // Scoring Settings
    Route::get('scoring-settings', [ScoringSettingController::class, 'index'])->name('scoring-settings.index');
    Route::put('scoring-settings', [ScoringSettingController::class, 'update'])->name('scoring-settings.update');

    // Package Mapping Routes
    Route::get('package-mapping', [PackageMappingController::class, 'index'])->name('package-mapping.index');
    Route::put('package-mapping', [PackageMappingController::class, 'update'])->name('package-mapping.update');
    Route::post('package-mapping/reset', [PackageMappingController::class, 'reset'])->name('package-mapping.reset');
    Route::get('package-mapping/api', [PackageMappingController::class, 'api'])->name('package-mapping.api');

    // YouTube Video Routes
    Route::resource('youtube-videos', App\Http\Controllers\Admin\YoutubeVideoController::class);

    // Package Management Routes
    Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
    Route::post('packages/{package}/toggle-status', [App\Http\Controllers\Admin\PackageController::class, 'toggleStatus'])->name('packages.toggle-status');

    // Laporan Kemampuan Siswa (Admin Only)
    Route::get('/laporan-kemampuan', [LaporanKemampuanController::class, 'index'])->name('laporan.kemampuan.index');
    Route::get('/laporan-kemampuan/paket-lengkap', [LaporanKemampuanController::class, 'paketLengkap'])->name('laporan.kemampuan.paket-lengkap');
    Route::get('/laporan-kemampuan/per-paket', [LaporanKemampuanController::class, 'perPaket'])->name('laporan.kemampuan.per-paket');
    Route::get('/laporan-kemampuan/per-paket/{package}', [LaporanKemampuanController::class, 'perPaketDetail'])->name('laporan.kemampuan.per-paket-detail');
    Route::get('/laporan-kemampuan/kategori-by-paket', [LaporanKemampuanController::class, 'getKategoriByPaket'])->name('laporan.kemampuan.kategori-by-paket');
    Route::get('/laporan-kemampuan/siswa-by-paket', [LaporanKemampuanController::class, 'getSiswaByPaket'])->name('laporan.kemampuan.siswa-by-paket');
    Route::post('/laporan-kemampuan/generate-paket-lengkap', [LaporanKemampuanController::class, 'generateLaporanPaketLengkap'])->name('laporan.kemampuan.generate-paket-lengkap');
    Route::post('/laporan-kemampuan/generate-per-paket', [LaporanKemampuanController::class, 'generateLaporanPerPaket'])->name('laporan.kemampuan.generate-per-paket');
});
