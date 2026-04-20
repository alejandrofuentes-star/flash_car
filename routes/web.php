<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RentaController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SliderController;

// ============================================================
// RUTAS PÚBLICAS
// ============================================================
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : app(VehicleController::class)->catalogo();
})->name('inicio');

Route::get('/catalogo', [VehicleController::class, 'catalogo'])->name('catalogo.index');
Route::get('/catalogo/{id}', [VehicleController::class, 'detalle'])->name('catalogo.detalle');

Route::get('/rentar/{vehicleId}', [RentaController::class, 'create'])->name('reservaciones.create');
Route::post('/rentar', [RentaController::class, 'store'])->name('rentas.store');

Route::get('/buscar', [VehicleController::class, 'buscar'])->name('catalogo.buscar');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/vehiculos/{id}/images', [VehicleController::class, 'uploadImages'])->name('vehiculos.images.upload');
Route::delete('/vehiculos/images/{id}', [VehicleController::class, 'deleteImage'])->name('vehiculos.images.delete');

Route::get('/faqs', function() {
    return view('catalogo.faqs');
})->name('faqs');

// ============================================================
// RUTAS AUTENTICADAS
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Usuarios - rutas estáticas ANTES que las dinámicas
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.detalles');

    // Vehículos - rutas estáticas ANTES que las dinámicas
    Route::get('/vehiculos', [VehicleController::class, 'index'])->name('vehiculos.index');
    Route::get('/vehiculos/create_categoria', [CategoryController::class, 'create'])->name('categoria.create_categoria');
    Route::get('/vehiculos/create', [VehicleController::class, 'create'])->name('vehiculos.create');
    Route::get('/vehiculos/{id}', [VehicleController::class, 'show'])->name('vehicles.show');

    // Categorías - rutas estáticas ANTES que las dinámicas
    Route::get('/categorias/{id}/edit', [CategoryController::class, 'edit'])->name('categorias.edit');
    Route::get('/categorias/{id}', [CategoryController::class, 'show'])->name('categorias.show');

    // Vehículos editar
    Route::get('/vehicles/{id}/edit', [VehicleController::class, 'edit'])->name('vehiculos.edit');

    // Rentas
    Route::get('/rentas', [RentaController::class, 'index'])->name('rentas.index');
    Route::get('/rentas/{id}', [RentaController::class, 'show'])->name('rentas.show');
    Route::get('/rentas/{id}/edit', [RentaController::class, 'edit'])->name('rentas.edit');
    Route::put('/rentas/{id}', [RentaController::class, 'update'])->name('rentas.update');

    Route::resource('states', StateController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/states/{state}/toggle', [StateController::class, 'toggleActive'])->name('states.toggle');
    Route::post('/states/{state}/points', [StateController::class, 'storePoint'])->name('states.points.store');
    Route::delete('/points/{point}', [StateController::class, 'destroyPoint'])->name('states.points.destroy');
    Route::patch('/points/{point}/toggle', [StateController::class, 'togglePoint'])->name('states.points.toggle');
});

// ============================================================
// RUTAS ADMIN Y SUPER ADMIN
// ============================================================
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
    // Slider
    Route::get('/slider', [SliderController::class, 'index'])->name('slider.index');
    Route::post('/slider', [SliderController::class, 'store'])->name('slider.store');
    Route::patch('/slider/{id}/toggle', [SliderController::class, 'toggle'])->name('slider.toggle');
    Route::post('/slider/reorder', [SliderController::class, 'reorder'])->name('slider.reorder');
    Route::delete('/slider/{id}', [SliderController::class, 'destroy'])->name('slider.destroy');
    // Usuarios
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Categorías
    Route::post('/categorias', [CategoryController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{id}', [CategoryController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{id}', [CategoryController::class, 'destroy'])->name('categorias.destroy');

    // Vehículos
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::put('/vehicles/{id}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy'])->name('vehiculos.destroy');

    // Rentas
    Route::put('/rentas/{id}/estado', [RentaController::class, 'updateEstado'])->name('rentas.estado');
    Route::post('/rentas/{id}/reenviar-correo', [RentaController::class, 'reenviarCorreo'])->name('rentas.reenviarCorreo');
});

// ============================================================
// RUTAS SOLO SUPER ADMIN
// ============================================================
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/super-admin', function () { return view('super-admin.index'); })->name('super-admin.index');
    Route::get('/settings', function () { return view('super-admin.settings'); })->name('super-admin.settings');
    Route::get('/system/cache', [SystemController::class, 'index'])->name('system.cache');
    Route::post('/system/cache/all', [SystemController::class, 'clearAll'])->name('system.clearAll');
    Route::post('/system/cache/cache', [SystemController::class, 'clearCache'])->name('system.clearCache');
    Route::post('/system/cache/config', [SystemController::class, 'clearConfig'])->name('system.clearConfig');
    Route::post('/system/cache/routes', [SystemController::class, 'clearRoutes'])->name('system.clearRoutes');
    Route::post('/system/cache/views', [SystemController::class, 'clearViews'])->name('system.clearViews');
});