<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/ 

Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [LoginController::class,'login']);
Route::post('register', [RegisterController::class,'register']);

Route::post('/login/attempt', [LoginController::class, 'attemptLogin'])->name('login.attempt');
Route::post('/login/verify-otp', [LoginController::class, 'verifyOtp'])->name('login.verifyOtp');

Route::get('password/forget',  function () { 
	return view('pages.forgot-password'); 
})->name('password.forget');

Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
Route::post('password/update', [PasswordController::class,'update'])->name('password.update');

Route::group(['middleware' => 'auth'], function(){
	
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');	
	
	// employees
	Route::get('/employees', [EmployeeController::class,'index'])->name('employees.list');
	Route::get('/employees/get-list', [EmployeeController::class,'getUserList'])->name('employees.getListo');
	Route::get('/employee/create', [EmployeeController::class,'create'])->name('employees.create');
	Route::post('/employee/create', [EmployeeController::class,'store'])->name('create-employee');
	Route::get('/employee/{id}', [EmployeeController::class,'edit']);
	Route::post('/employee/update', [EmployeeController::class,'update']);
	Route::get('/employee/delete/{id}', [EmployeeController::class,'delete']);

	// logout route
	Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');
	Route::get('/logout', [LoginController::class,'logout']);
	Route::get('/clear-cache', [HomeController::class,'clearCache']);

	// dashboard route  
	Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');
	Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

		//only those have manage_user permission will get access
		Route::group(['middleware' => 'can:manage_user'], function(){
			Route::get('/users', [UserController::class,'index'])->name('users.list')->middleware('can:manage_user');
			Route::get('/user/get-list', [UserController::class,'getUserList']);
			Route::get('/user/create', [UserController::class,'create'])->name('users.create')->middleware('can:manage_user');
			Route::post('/user/create', [UserController::class,'store'])->name('create-user')->middleware('can:user_create');
			Route::get('/user/{id}', [UserController::class,'edit']);
			Route::post('/user/update', [UserController::class,'update'])->middleware('can:user_update');
			Route::get('/user/delete/{id}', [UserController::class,'delete'])->middleware('can:user_delete');
		});

		//only those have manage_role permission will get access
		Route::group(['middleware' => 'can:manage_role|manage_user'], function(){
			Route::get('/roles', [RolesController::class,'index'])->name('roles.list');
			Route::get('/role/get-list', [RolesController::class,'getRoleList'])->name('roles.get-list');
			Route::post('/role/create', [RolesController::class,'create'])->name('roles.create');
			Route::get('/role/edit/{id}', [RolesController::class,'edit'])->name('roles.edit');
			Route::post('/role/update', [RolesController::class,'update'])->name('roles.update');
			Route::get('/role/delete/{id}', [RolesController::class,'delete'])->name('roles.delete');
		});

		//only those have manage_permission permission will get access
		Route::group(['middleware' => 'can:manage_permission|manage_user'], function(){
			Route::get('/permission', [PermissionController::class,'index'])->name('permissions.list');
			Route::get('/permission/get-list', [PermissionController::class,'getPermissionList'])->name('permissions.get-list');
			Route::post('/permission/create', [PermissionController::class,'create'])->name('permissions.create');
			Route::get('/permission/update', [PermissionController::class,'update'])->name('permissions.update');
			Route::get('/permission/delete/{id}', [PermissionController::class,'delete'])->name('permissions.delete');
		});

		// get permissions
		Route::get('get-role-permissions-badge', [PermissionController::class,'getPermissionBadgeByRole']);

		// Departments
		Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.list');
		Route::post('/departments/store', [DepartmentController::class, 'store'])->name('departments.store');
		Route::get('/departments/delete/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
		Route::put('/departments/update/{id?}', [DepartmentController::class, 'store'])->name('departments.update');
		Route::get('/departments/get-list', [DepartmentController::class, 'getList'])->name('departments.getList');

		// Positions
		Route::get('/positions', [PositionController::class, 'index'])->name('positions.list');
		Route::post('/positions/store', [PositionController::class, 'store'])->name('positions.store');
		Route::get('/positions/delete/{id}', [PositionController::class, 'destroy'])->name('positions.destroy');
		Route::put('/positions/update/{id?}', [PositionController::class, 'store'])->name('positions.update');
		Route::get('/positions/get-list', [PositionController::class, 'getList'])->name('positions.getList');		
		
		// Get positions by department
		Route::POST('get-positions', [PositionController::class, 'getPositions'])->name('get-positions');

		Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
		Route::post('/users/search', [UserController::class, 'search'])->name('users.search');


	// Inventory routes
	include('modules/inventory.php');
	// Accounting routes
	// include('modules/accounting.php');
});

Route::get('/register', function () { return view('pages.register'); });
Route::get('/login-1', function () { return view('pages.login'); });
