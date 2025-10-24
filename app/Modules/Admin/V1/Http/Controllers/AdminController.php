<?php

namespace App\Modules\Admin\V1\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Admin\V1\Services\AdminService;
use Illuminate\Http\Request;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use \Illuminate\Http\JsonResponse;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    public function __construct(private AdminService $adminServices) {}

    public function getActiveTeachersForAcademicYear(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academicYearID' => 'required|string',
        ]);

        try {
            $dtos = $this->adminServices->getActiveTeachersForAcademicYear($validated['academicYearID']);
            $data = array_map(fn($dto) => $dto->toArray(), $dtos);
            
            return response()->json([
                'message' => 'Teachers year data retrieved successfully',
                'data' => $data,
            ], 200);
        } catch (Exception $e) {
            // Optional: add context or log
            error_log("Failed to fetch teachers for academicYearID " . $validated['academicYearID']);
            // Re-throw the exception so Laravel handles it globally
            throw $e;
        }
    }


    public function getTeacherFullProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'userID' => 'required|string',
        ]);

        try {
            $dto = $this->adminServices->getTeacherFullProfile($validated['userID']);
            return response()->json([
                'message' => 'Teacher full profile retrieved successfully',
                'data' => $dto->toArray(),
            ], 200);
        } catch (Exception $e) {
            // Optional: add context or log
            error_log("Failed to fetch teacher full profile for teacherID " . $validated['teacherID']);
            // Re-throw the exception so Laravel handles it globally
            return throw new HttpException(500, "An error occurred while retrieving the teacher full profile : " . $e->getMessage());
        }
    }

    public function getTeacherAnnualReport(Request $request): JsonResponse{
        $validated = $request->validate([
            'teacherID' => 'required|string',
            'academicYearID' => 'required|string',
        ]);

        try {
            $dto = $this->adminServices->getTeacherAnnualReport($validated['teacherID'], $validated['academicYearID']);
            return response()->json([
                'message' => 'Teacher annual report retrieved successfully',
                'data' => $dto->toArray(),
            ], 200);
        } catch (Exception $e) {
            // Re-throw the exception so Laravel handles it globally
            return throw new HttpException(500, "An error occurred while retrieving the teacher annual report : " . $e->getMessage());
        }
    }

    public function getTeacherHistory(Request $request): JsonResponse {
        $validated = $request->validate([
            'teacherID' => 'required|string',
        ]);
        try {
            $dtos = $this->adminServices->getTeacherHistory($validated['teacherID']);
            $data = array_map(fn($dto) => $dto->toArray(), $dtos);
            return response()->json([
                'message' => 'Teacher history retrieved successfully',
                'data' => $data,
            ], 200);
        } catch (Exception $e) {
            // Optional: add context or log
            error_log("Failed to fetch teacher history for teacherID " . $validated['teacherID'] . ": " . $e->getMessage());
            // Re-throw the exception so Laravel handles it globally
            return throw new HttpException(500, "An error occurred while retrieving the teacher history : " . $e->getMessage());
        }
    }



    public function rejectReclamation(Request $request): JsonResponse {
        $validated = $request->validate([
            'reclamationID' => 'required|string',
            'reclamationMessage' => 'required|string',
            'notificationMessage' => 'required|string',
        ]);
        try {
            $this->adminServices->rejectReclamation(
                $validated['reclamationID'],
                $validated['reclamationMessage'],
                $validated['notificationMessage']
            );

            return response()->json([
                'message' => 'Reclamation rejected and teacher notified successfully',
            ], 200);
        } catch (Exception $e) {
            // Optional: add context or log
            error_log("Failed to reject reclamation ID " . $validated['reclamationID'] . ": " . $e->getMessage());
            // Re-throw the exception so Laravel handles it globally
            return throw new HttpException(500, "An error occurred while rejecting the reclamation : " . $e->getMessage());
        }
    }


    public function acceptReclamation(Request $request): JsonResponse {
        $validated = $request->validate([
            'reclamationID' => 'required|string',
            'changes' => 'required|array',
            'reclamationMessage' => 'required|string',
            'notificationMessage' => 'required|string',
        ]);
        try {
            $this->adminServices->acceptReclamation(
                $validated['reclamationID'],
                $validated['changes'],
                $validated['reclamationMessage'],
                $validated['notificationMessage']
            );

            return response()->json([
                'message' => 'Reclamation accepted, changes applied, and teacher notified successfully',
            ], 200);
        } catch (Exception $e) {
            // Optional: add context or log
            error_log("Failed to accept reclamation ID " . $validated['reclamationID'] . ": " . $e->getMessage());
            // Re-throw the exception so Laravel handles it globally
            return throw new HttpException(500, "An error occurred while accepting the reclamation : " . $e->getMessage());
        }
    }


    /**
     * send an excel template file in one response (not a streamed response )
     */
    public function downloadAssignmentTemplate(Request $request){
        try {
            $spreadsheet = $this->adminServices->downloadAssignmentTemplate();
            $writer = new Xlsx($spreadsheet);
            $fileName = 'assignment_template_' . date('Ymd_His') . '.xlsx';

            // 1. Start capturing all output
            ob_start();
            
            // 2. Write the file content to the capture buffer
            $writer->save('php://output');
            
            // 3. Get the content from the buffer and clean it
            $content = ob_get_clean();

            // 4. Return a standard Laravel Response with the captured content
            return Response::make($content, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=$fileName",
                'Access-Control-Expose-Headers' => 'Content-Disposition', // tels the browser to expose this header to the frontend
            ]);
            
        } catch (Exception $e) {
            throw new HttpException(500, "An error occurred while generating the template: " . $e->getMessage());
        }
    }
            


    public function getExcelValidationRules(): JsonResponse {
        try {
            $dto = $this->adminServices->getExcelValidationRules();
            return response()->json([
                'message' => 'Excel validation rules retrieved successfully',
                'data' => $dto->toArray(),
            ], 200);
        } catch (Exception $e) {
            // Optional: add context or log
            error_log("Failed to fetch excel validation rules: " . $e->getMessage());
            // Re-throw the exception so Laravel handles it globally
            return throw new HttpException(500, "An error occurred while retrieving the excel validation rules : " . $e->getMessage());
        }
    }
    
}

