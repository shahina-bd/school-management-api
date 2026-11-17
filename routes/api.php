<?php

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\FeeTypeController;
use App\Http\Controllers\Api\LedgerController;
use App\Http\Controllers\Api\MarkController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::apiResource('academics', AcademicController::class);
        Route::apiResource('academic-years', AcademicYearController::class);
        Route::apiResource('classes', SchoolClassController::class);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('students', StudentController::class);
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('staff', StaffController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('attendance', AttendanceController::class);
        Route::apiResource('fees', FeeController::class);
        Route::apiResource('fee-types', FeeTypeController::class);
        Route::apiResource('fee-payments', PaymentController::class);
        Route::apiResource('expenses', ExpenseController::class);
        Route::apiResource('expense-categories', ExpenseCategoryController::class);
        Route::apiResource('ledger', LedgerController::class);
        Route::apiResource('exams', ExamController::class);
        Route::apiResource('marks', MarkController::class);
        Route::apiResource('results', ResultController::class);
        Route::apiResource('notices', NoticeController::class);
        Route::apiResource('events', EventController::class);
        Route::apiResource('reports', ReportController::class);
    });
});
