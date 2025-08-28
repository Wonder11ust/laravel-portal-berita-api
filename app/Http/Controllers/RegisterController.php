<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Events\Verified;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $user = $this->userService->getAllUsers($perPage);
        return UserResource::collection($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterRequest $request)
    {
         $user = $this->userService->register($request->validated());

         $user->sendEmailVerificationNotification();

        return response()->json([
            'status'  => 200,
            'message' => 'Berhasil Registrasi',
            'data'    => new UserResource($user)
        ]);
    }

    public function emailVerification(Request $request, $id, $hash)
    {
        $user =$this->userService->getProfile($id);
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['status' => 400, 'message' => 'Link verifikasi tidak valid.']);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => 200, 'message' => 'Email sudah diverifikasi.']);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['status' => 200, 'message' => 'Email berhasil diverifikasi!']);
    }

    public function sendVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Link verifikasi email telah dikirim!']);
    }

}
