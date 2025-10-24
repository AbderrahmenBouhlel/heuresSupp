<?php

namespace App\Modules\Teacher\V1\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Teacher\V1\Services\TeacherService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException ;
use App\Http\Requests\ReclaimOvertimeProcessRequest;


class TeacherController extends Controller
{
    public function __construct(private TeacherService $teacherServices){
        $this->teacherServices = $teacherServices;
    }

    public function getTeacherAssignment(Request $request){

        try {
            $teacherID = $request->user()->teacher->id ;
            $academicYearID = $request->query('academicYearID'); // default to null if not provided
    
            if(!$teacherID || !$academicYearID){
                throw new \Exception("Both teacherID and academicYearID are required");
            }
    
            $teachingYearData = $this->teacherServices->getTeacherAssignment($teacherID, $academicYearID);
            return response()->json([
                'message' => 'Teacher year data retrieved successfully',
                'data' => $teachingYearData->toArray(),
            ]);

        } catch (\Exception $e) {
            return throw new HttpException(500, "An error occurred while retrieving the teacher year data: " . $e->getMessage());
        }
       
    }


    public function verifyTeacherOvertimeProcess(Request $request ){
        try {
            $processID = $request->input('processId');
            if (!$processID) {
                throw new \Exception("Process ID is required");
            }

            $newProcessDTO = $this->teacherServices->verifyTeacherProcess($processID);
            return response()->json([
                'message' => 'Overtime process verified successfully',
                'data' => $newProcessDTO->toArray(),
            ]);
        } catch (\Exception $e) {
            return throw new HttpException(500, "An error occurred while verifying the overtime process: " . $e->getMessage());
        }
    }

    public function reclaimOvertimeProcess(ReclaimOvertimeProcessRequest $request){
        try {
            $data = $request->validated();
            $processID = $data['processId'];
            $details = $data['details'];


            $newProcessDTO = $this->teacherServices->reclaimOvertimeProcess($processID, $details);
            return response()->json([
                'message' => 'Overtime process reclaimed successfully',
                'data' => $newProcessDTO->toArray(),
            ]);
        } catch (\Exception $e) {
            return throw new HttpException(500, "An error occurred while reclaiming the overtime process: " . $e->getMessage());
        }
    }

}
