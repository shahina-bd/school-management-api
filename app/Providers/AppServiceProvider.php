<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\LedgerEntry;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentFee;
use App\Models\Subject;
use App\Policies\AcademicYearPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\ExpenseCategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\FeeTypePolicy;
use App\Policies\LedgerPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\SchoolClassPolicy;
use App\Policies\SectionPolicy;
use App\Policies\StudentFeePolicy;
use App\Policies\SubjectPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(AttendanceRecord::class, AttendancePolicy::class);
        Gate::policy(AcademicYear::class, AcademicYearPolicy::class);
        Gate::policy(SchoolClass::class, SchoolClassPolicy::class);
        Gate::policy(Section::class, SectionPolicy::class);
        Gate::policy(Subject::class, SubjectPolicy::class);
        Gate::policy(StudentFee::class, StudentFeePolicy::class);
        Gate::policy(FeeType::class, FeeTypePolicy::class);
        Gate::policy(FeePayment::class, PaymentPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(ExpenseCategory::class, ExpenseCategoryPolicy::class);
        Gate::policy(LedgerEntry::class, LedgerPolicy::class);
    }
}
