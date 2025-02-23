<?php
   
namespace App\Http\Controllers\Api;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AuthLoginRequest;
   
class AuthController extends BaseController
{   
    public function login(AuthLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Unauthorized', [], 401);
        }

        $token = $user->createToken($user->name);

        return $this->sendResponse(
            [
                'user'  => $user,
                'token' => $token->plainTextToken
            ],
            'User login successful'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->sendResponse(
            [],
            'You are logged out.'
        );
    }
}
