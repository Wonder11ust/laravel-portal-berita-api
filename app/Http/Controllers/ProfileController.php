<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;


class ProfileController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show(Request $request)
    {
        $user = $this->userService->getProfile($request->user()->id);
        return new UserResource($user);
    }
  
    public function update(ProfileUpdateRequest $request)
    {
        $user = $this->userService->updateProfile($request->user()->id, $request->validated());
        return response()->json([
            'status'=>200,
            'message'=>'Profile berhasil diperbarui',
            'data'=> new UserResource($user)
        ]);
   }

}
