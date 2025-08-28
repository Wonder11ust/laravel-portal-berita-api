<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
 
    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());
            return response()->json([
                'status'=>200,
                'message'=>'Login Berhasil',
                'data'=> new UserResource($result['user']),
                'token'=>$result['token']
            ],200);
        } catch (\Exception $e) {
             return response()->json([
                'status'  => $e->getCode() ?: 500,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function logout(Request $request)
    {
         $this->authService->logout($request->user());

        return response()->json([
            'status'  => 200,
            'message' => 'Logout berhasil'
        ], 200);
    }

}
