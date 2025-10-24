<?php

namespace App\Modules\AcademicYear\V1\Http\controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\AcademicYear\V1\services\AcademicYearService;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\AcademicYear\V1\Http\request\StoreAcademicYearRequest;
use Illuminate\Database\QueryException;
use App\Modules\AcademicYear\V1\VOs\AcademicYearDetailsVO;
use GuzzleHttp\Psr7\Stream;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Support\Facades\Response; // Already imported or use the global response() helper
use Symfony\Component\HttpFoundation\StreamedResponse; // Good for type-hinting

class AcademicYearController extends Controller
{
    private $academicYearService;

    public function __construct(AcademicYearService $academicYearService) {
        $this->academicYearService = $academicYearService;
    }

    

     /**
         * Get the last N academic years.
         *
         * This endpoint is protected by `auth.token` middleware.
         * 
         * URL Parameter:
         * - n (int): Number of academic years to retrieve
         *
         * Example Request:
         * GET /teacher/last-academic-years/5
         * Authorization: Bearer {token}
         *
         * Response:
         * {
         *   "message": "Last N Academic Years retrieved successfully",
         *   "data": {
         *     "academicYears": [ ... ]
         *   }
         * }
         *
         * @param Request $request
         * @param int $n
         * @return \Illuminate\Http\JsonResponse
    */

    public function getLastNAcademicYears(Request $request){
        try {
            $n = $request->query('n', 3); // default to 3 if not provided

            // Get DTOs from service
            $lastAcademicYearsDTOs = $this->academicYearService->getLastNAcademicYears($n);

            // Map to arrays for JSON response
            $academicYearsArray = array_map(fn($dto) => $dto->toArray(), $lastAcademicYearsDTOs);

            return response()->json([
                'message' => 'Last N Academic Years retrieved successfully',
                'data' => $academicYearsArray,
            ]);
        } catch (Exception $e) {
            throw new HttpException(500, "An error occurred while retrieving the last N academic years: " . $e->getMessage());
        }
    }



    public function storeNewAcademicYear(StoreAcademicYearRequest $request){
        try {
            $data = $request->validated();
            $is_current = $request->boolean('is_current', false); // Default to false if not provided
            $academicYearVO = AcademicYearDetailsVO::fromArray($data);

            $academicYearDTO = $this->academicYearService->storeNewAcademicYear($academicYearVO, $is_current);
            return response()->json([
                'message' => 'Academic year created successfully',
                'data' => $academicYearDTO->toArray(),
            ], 201);
        } catch (QueryException $e) {
            throw new HttpException(400, "Database error: " . $e->getMessage());
        }catch (Exception $e) {
            throw new HttpException(500, "An error occurred while creating the academic year: " . $e->getMessage());
        }
    }


}
