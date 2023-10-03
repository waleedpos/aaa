<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend as FRONTEND;


Auth::routes();

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/register/{id}', 		[FRONTEND\PricingController::class,'register'])->middleware('guest');
Route::post('/register-plan/{id}',  [FRONTEND\PricingController::class,'registerPlan'])->middleware('guest');

Route::resource('install',    App\Http\Controllers\Installer\InstallerController::class);
Route::post('install/verify', [App\Http\Controllers\Installer\InstallerController::class,'verify'])->name('install.verify');
Route::post('install/migrate', [App\Http\Controllers\Installer\InstallerController::class,'migrate'])->name('install.migrate');

?>