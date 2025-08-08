<?php
// app/Http/Controllers/Api/V1/Teacher/ProfileController.php
namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $teacher = Auth::user()->teacher->load('grades');
        
        return response()->json([
            'message' => 'Teacher profile retrieved successfully',
            'data' => [
                'user' => Auth::user(),
                'teacher' => $teacher,
                'current_grade' => $teacher->currentGrade()
            ]
        ]);
    }
}