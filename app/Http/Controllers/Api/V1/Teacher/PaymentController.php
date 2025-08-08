<?php
//app/Http/Controllers/Api/V1/Teacher/PaymentController.php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExtraHoursStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function getPaymentDetails($academicYear)
    {
        $teacher = Auth::user()->teacher;
        
        $extraHours = ExtraHoursStatus::where('teacher_id', $teacher->id)
            ->where('academic_year', $academicYear)
            ->where('processing_status', 'Payé')
            ->first();

        if (!$extraHours) {
            return response()->json([
                'message' => 'No payment details found for this academic year',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Payment details retrieved successfully',
            'data' => $extraHours
        ]);
    }

    public function getPaymentHistory()
    {
        $teacher = Auth::user()->teacher;
        
        $payments = ExtraHoursStatus::where('teacher_id', $teacher->id)
            ->where('processing_status', 'Payé')
            ->orderBy('academic_year', 'desc')
            ->get();

        return response()->json([
            'message' => 'Payment history retrieved successfully',
            'data' => $payments
        ]);
    }
}