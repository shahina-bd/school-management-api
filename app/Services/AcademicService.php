<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;

class AcademicService
{
    public function __construct(
        protected AcademicYearService $academicYearService,
        protected SchoolClassService $schoolClassService,
        protected SectionService $sectionService,
        protected SubjectService $subjectService,
    ) {}

    public function getAcademicSummary(): array
    {
        return [
            'academic_years' => AcademicYear::all(),
            'classes' => SchoolClass::with(['sections', 'subjects'])->get(),
            'sections' => Section::with('schoolClass')->get(),
            'subjects' => Subject::all(),
        ];
    }

    public function academicYearService(): AcademicYearService
    {
        return $this->academicYearService;
    }

    public function schoolClassService(): SchoolClassService
    {
        return $this->schoolClassService;
    }

    public function sectionService(): SectionService
    {
        return $this->sectionService;
    }

    public function subjectService(): SubjectService
    {
        return $this->subjectService;
    }
}
