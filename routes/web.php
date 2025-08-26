<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\OrderWizardController;

/**
 * Root → preusmeri na podrazumevani jezik
 * npr. / → /sr-Latn-RS
 */
Route::get('/', function () {
    return redirect()->to('/' . config('i18n.default'));
});

/**
 * SVE javne rute pod {locale} prefiksom.
 * Middleware "set.locale" postavlja aktivni jezik na osnovu prvog segmenta.
 */
Route::group([
    'prefix'     => '{locale}',
    'where'      => ['locale' => '[A-Za-z-]+'],
    'middleware' => 'set.locale',
], function () {
    // Početna (za sada koristimo postojeću Breeze "Welcome" stranu da ništa ne puca)
    Route::get('/', function ($locale) {
        return Inertia::render('Welcome', [
            'canLogin'      => Route::has('login'),
            'canRegister'   => Route::has('register'),
            'laravelVersion'=> Application::VERSION,
            'phpVersion'    => PHP_VERSION,
        ]);
    })->name('welcome');

    Route::get('/stranica/{slug}', [\App\Http\Controllers\PageController::class, 'show'])->name('page.show');


    // Kada dodaš svoje Inertia stranice, otkomentariši / prilagodi:
    // Route::get('/planovi', fn () => Inertia::render('PlansPage'));
    // Route::get('/planovi/{slug}', fn ($locale, $slug) => Inertia::render('PlanDetail', ['slug' => $slug]));
    // Route::get('/naruci', fn () => Inertia::render('OrderWizard'));
    // Route::get('/jelovnik', fn () => Inertia::render('MenuWeek'));
    // Route::get('/checkout', fn () => Inertia::render('CheckoutPage'));

    // 301 preusmerenja za stare WP/Woo putanje (SEO migracija; zadržava aktivni locale)
    Route::get('/shop', function ($locale) {
        return redirect()->to("/{$locale}/naruci", 301);
    });

    Route::get('/product/{slug}', function ($locale, $slug) {
        return redirect()->to("/{$locale}/planovi/{$slug}", 301);
    });
    
    Route::get('/naruci', [OrderWizardController::class,'index'])->name('wizard.index');
    Route::post('/naruci/price', [OrderWizardController::class,'price'])->name('wizard.price');
    Route::post('/naruci/confirm', [OrderWizardController::class,'confirm'])->name('wizard.confirm');
    Route::get('/narudzbina/{id}/potvrda', [OrderWizardController::class,'confirmation'])->name('wizard.confirmation');
    });

/**
 * Auth/Dashboard/Profile rute mogu ostati BEZ locale prefiksa
 * (admin i auth deo mogu koristiti jezik iz session-a).
 */
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
