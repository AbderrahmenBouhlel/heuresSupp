<?php

namespace App\Http\Controllers\Api\V1\auth;

use App\Http\Controllers\Controller;
use App\Services\auth\AuthService;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;
    public function __construct( AuthService $authService){
        $this->authService = $authService;
    }

    public function login(Request $request){
        try{
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string', //|min:8
            ]);

            $authData = $this->authService->login($credentials);

            return response()->json([
                'message' => 'Login successful',
                'data' => $authData,
            ]);

        } catch (ValidationException $e){
            error_log("Validation error: " . $e->getMessage());
            throw $e;
        }
    }

    public function logout(Request $request){
        $this->authService->logout($request);
        return response()->json(['message' => 'Logout successful']);
    }


    /**
     * Get the currently authenticated user's session.
     *
     * This endpoint is protected by auth.token middleware.
     * The client should send the token in the Authorization header:
     * Authorization: Bearer {token}
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request){
        $userData = $this->authService->me($request);

        return response()->json([
            'message' => 'User profile retrieved successfully',
            'data' => $userData,
        ]);
    
    }


}
