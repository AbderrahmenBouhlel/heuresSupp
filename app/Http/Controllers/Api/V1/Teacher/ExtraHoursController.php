<?php
//app/Http/Controllers/Api/V1/Teacher/ExtraHoursController.php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExtraHoursStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExtraHoursController extends Controller
{
    public function index($academicYear)
    {
        $teacher = Auth::user()->teacher;
        
        $extraHours = ExtraHoursStatus::where('teacher_id', $teacher->id)
            ->where('academic_year', $academicYear)
            ->first();

        if (!$extraHours) {
            return response()->json([
                'message' => 'No extra hours found for this academic year',
                'data' => [
                    'academic_year' => $academicYear,
                    'course_hours_S1' => 0,
                    'td_hours_S1' => 0,
                    'tp_hours_S1' => 0,
                    'course_hours_S2' => 0,
                    'td_hours_S2' => 0,
                    'tp_hours_S2' => 0,
                    'processing_status' => 'En cours'
                ]
            ]);
        }

        return response()->json([
            'message' => 'Extra hours retrieved successfully',
            'data' => $extraHours
        ]);
    }

    public function submitVerification(Request $request, $academicYear)
    {
        $request->validate([
            'course_hours_S1' => 'required|numeric|min:0',
            'td_hours_S1' => 'required|numeric|min:0',
            'tp_hours_S1' => 'required|numeric|min:0',
            'course_hours_S2' => 'required|numeric|min:0',
            'td_hours_S2' => 'required|numeric|min:0',
            'tp_hours_S2' => 'required|numeric|min:0',
        ]);

        $teacher = Auth::user()->teacher;

        $extraHours = ExtraHoursStatus::updateOrCreate(
            ['teacher_id' => $teacher->id, 'academic_year' => $academicYear],
            [
                'course_hours_S1' => $request->course_hours_S1,
                'td_hours_S1' => $request->td_hours_S1,
                'tp_hours_S1' => $request->tp_hours_S1,
                'course_hours_S2' => $request->course_hours_S2,
                'td_hours_S2' => $request->td_hours_S2,
                'tp_hours_S2' => $request->tp_hours_S2,
                'processing_status' => 'Verifie par enseignant'
            ]
        );

        return response()->json([
            'message' => 'Verification submitted successfully',
            'data' => $extraHours
        ], 201);
    }
}
