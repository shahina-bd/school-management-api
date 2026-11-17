<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'students' => [
                'total' => 20,
                'male' => 0,
                'female' => 20,
            ],

            'attendance' => [
                'present' => 0,
                'absent' => 20,
                'percentage' => 0,
            ],

            'fees' => [
                'today_collection' => 0,
                'pending' => 37300,
                'month_collection' => 4700,
            ],

            'staff' => [
                'total' => 34,
            ],

            'expenses' => [
                'month' => 0,
            ],

            'cash' => [
                'net' => 4700,
            ],

            'exams_evaluated' => 2,

            'recent_transactions' => [],

            'notices' => [],

            'exams' => [],
        ]);
    }
}
