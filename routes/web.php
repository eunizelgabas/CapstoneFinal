<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispensingController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedCategoryController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\MedTypeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RadiologicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SampleAppController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        // 'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
    ]);
    // return Inertia::render('Auth.Login');
})->middleware('checkUserStatus')->name('login');

Route::get('/appointment/create', [AppointmentController::class,  'create'])->name('appointment.create');
Route::post('/appointment', [AppointmentController::class,  'store']);

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['checkUserStatus', 'auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Medicine Category
    Route::middleware('can:manage-category')->group(function() {
    Route::get('/category', [MedCategoryController::class, 'index'])->name('category.index');
    Route::post('/category',[MedCategoryController::class, 'store']);
    Route::put('/category/{medcategory}',[MedCategoryController::class, 'update']);
    Route::delete('/category/{medcategory}', [MedCategoryController::class, 'destroy']);
    });

    //Medicine Type
    Route::middleware('can:manage-type')->group(function() {
        Route::get('/type', [MedTypeController::class, 'index'])->name('type.index');
        Route::post('/type',[MedTypeController::class, 'store']);
        Route::put('/type/{medtype}',[MedTypeController::class, 'update']);
        Route::delete('/type/{medtype}', [MedTypeController::class, 'destroy']);
    });


    //Medicine
    Route::middleware('can:manage-medicine')->group(function() {
    Route::get('/medicine', [MedicineController::class, 'index'])->name('medicine.index');
    Route::put('/medicine/{medicine}',[MedicineController::class, 'update']);
    Route::post('/medicine',[MedicineController::class, 'store']);
    Route::delete('/medicine/{medicine}', [MedicineController::class, 'destroy']);
    });

    //Medicine Inventory
    Route::middleware('can:manage-inventory')->group(function() {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    });

    //Medicine Stock
    Route::middleware('can:manage-purchase')->group(function() {
    Route::get('/stock',[StockController::class , 'index'])->name('stock.index');
    Route::get('/stock/create', [StockController::class, 'create'])->name('inventory.create');
    Route::post('/stock',[StockController::class, 'store'])->name('stock.create');
    Route::get('/stock/edit/{stock}', [StockController::class, 'edit']);
    Route::put('/stock/{stock}',[StockController::class, 'update']);
    Route::delete('/stock/{stock}', [StockController::class, 'destroy']);
    });

    //Medicine Dispense
    Route::middleware('can:manage-dispense')->group(function() {
    Route::get('/dispense', [DispensingController::class, 'index'])->name('dispense.index');
    Route::put('/dispense/{dispense}',[DispensingController::class, 'update']);
    Route::post('/dispense',[DispensingController::class, 'store']);
    Route::delete('/dispense/{dispense}', [DispensingController::class, 'destroy']);
    });

    //User
    Route::middleware('can:manage-user')->group(function() {
    Route::get('/users',[UserController::class, 'index'])->name('user.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/users',[UserController::class, 'store'])->name('user.store');
    Route::get('/users/edit/{user}', [UserController::class, 'edit']);
    Route::put('/users/{user}',[UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    //Doctor
    Route::middleware('can:manage-doctors')->group(function() {
    Route::get('/doctor/create', [DoctorController::class, 'create'])->name('doctor.create');
    Route::get('/doctor',[DoctorController::class, 'index'])->name('doctor.index');
    Route::post('/doctor',[DoctorController::class, 'store']);
    Route::get('/doctor/edit/{doctor}', [DoctorController::class, 'edit']);
    Route::put('/doctor/{doctor}',[DoctorController::class, 'update']);
    Route::delete('/doctor/{doctor}', [DoctorController::class, 'destroy']);
    Route::get('/doctor/show/{doctor}', [DoctorController::class, 'show']);
    Route::post('/doctor/{doctor}/activate', [DoctorController::class, 'activate'])->name('doctor.activate');
    Route::post('/doctor/{doctor}/deactivate', [DoctorController::class, 'deactivate'])->name('doctor.deactivate');
    Route::get('/doctor/pdf', [DoctorController::class, 'doctorPdf'])->name('doctor.pdf');
    Route::post('/doctor/deactivate/{doctor}', [DoctorController::class, 'deactivateDoctor']);
    Route::post('/doctor/activate/{doctor}', [DoctorController::class, 'activateDoctor']);
    });

    //Services for Doctor
    Route::middleware('can:manage-service')->group(function() {
    Route::get('/service/create', [ServiceController::class, 'create'])->name('service.create');
    Route::get('/service',[ServiceController::class, 'index'])->name('service.index');
    Route::post('/service',[ServiceController::class, 'store']);
    Route::get('/service/edit/{service}', [ServiceController::class, 'edit']);
    Route::put('/service/{service}',[ServiceController::class, 'update']);
    Route::delete('/service/{service}', [ServiceController::class, 'destroy']);
    });

    //Patient
    Route::middleware('can:manage-patient')->group(function() {
    Route::get('/patient',[PatientController::class, 'index'])->name('patient.index');
    Route::get('/patient/create', [PatientController::class, 'create'])->name('patient.create');
    Route::post('/patient',[PatientController::class, 'store'])->name('patient.store');
    Route::get('/patient/edit/{patient}', [PatientController::class, 'edit']);
    Route::put('/patient/{patient}',[PatientController::class, 'update']);
    Route::delete('/patient/{patient}', [PatientController::class, 'destroy']);
    Route::get('/patient/show/{patient}', [PatientController::class, 'show']);
    Route::get('/patient/pdf', [PatientController::class, 'patientPdf'])->name('patient.pdf');
    });

    //Appointment for Admin/Doctor
    Route::middleware('can:manage-appointment')->group(function() {
    Route::get('/appointment/index', [AppointmentController::class,  'index'])->name('appointment.index');
    Route::get('/appointment/create-for-patient', [AppointmentController::class,  'createForPatient'])->name('appointment.createForPatient');
    Route::get('/appointment/create-for-patient2/{patient}', [AppointmentController::class,  'createForPatient2']);
    Route::post('/appointment/store-for-patient', [AppointmentController::class, 'storeForPatient'])->name('appointments.storeForPatient');
    Route::get('/appointment/show/{appointment}', [AppointmentController::class, 'show']);
    Route::get('/appointment/edit/{appointment}', [AppointmentController::class, 'edit']);
    Route::delete('/appointment/{appointment}', [AppointmentController::class, 'destroy']);
    Route::put('/appointment/{appointment}',[AppointmentController::class, 'update']);
    Route::post('/appointment/accept/{appointment}', [AppointmentController::class, 'accept']);
    Route::post('/appointment/cancel/{appointment}', [AppointmentController::class, 'cancel']);
    Route::get('/appointment/create/{patient}', [AppointmentController::class, 'createForPatient']);
    });

    //HealthForm
    Route::middleware('can:manage-healthForm')->group(function() {
    Route::get('/healthForm/create/{patient}', [FormController::class, 'create']);
    Route::post('/healthForm',[FormController::class, 'store']);
    Route::get('/healthForm/show/{form}',[FormController::class, 'show']);
    Route::get('/healthForm/pdf/{form}',[FormController::class, 'formPdf']);
    Route::get('/generate-pdf/{form}', [FormController::class, 'medCert']);
    });

    //Radiologic
    Route::get('/radiologic/create/{patient}', [RadiologicController::class, 'create']);
    Route::post('/radiologic', [RadiologicController::class, 'store']);
    //Reports
    Route::get('/report', [ReportController::class, 'index'])->name('report.chart');
});

require __DIR__.'/auth.php';
