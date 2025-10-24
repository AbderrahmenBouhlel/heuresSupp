<?php



namespace App\Services\auth;

use App\Modules\Domain\User\V1\Entities\User;
use App\Services\user\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;



class AuthService
{
    private UserService $userService ;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }


    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !$this->userService->verifyPassword($user, $credentials['password'])) {
            throw ValidationException::withMessages([
                'auth' => ['The provided credentials are incorrect.'],
            ]);
        }

         // Update last login timestamp
        $user->update([
            'last_login_at' => now(),
        ]);

        // delete user prevous used tokens 
        $this->userService->deleteUserTokens($user);
 

        $tokenObj = $user->createToken('auth-token', ['*'], now()->addHours(2));


        $token = $tokenObj->plainTextToken;

        error_log("Successfully logged in. Token created.");
        $userProfile = $this->userService->getActiveProfile($user);
        $userDetails = $this->userService->getUserDetails($user);
        return [
            'user' => $userDetails,
            'activeProfile' => $userProfile,
            'token' => $token
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }


    public function me (Request $request)
    {
        $user = $request->user();

        if (!$user) {
            throw ValidationException::withMessages([
                'auth' => ['You must be authenticated to access this resource.'],
            ]);
        }

        $userProfile = $this->userService->getActiveProfile($user);
        $userDetails = $this->userService->getUserDetails($user);

        return [
            'user' => $userDetails,
            'activeProfile' => $userProfile,
        ];
    }
}
