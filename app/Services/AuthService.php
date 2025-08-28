<?php 
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthService
{
    public function login(array $data)
    {
        $user = User::where('email',$data['email'])->first();
        if(!$user || ! Hash::check($data['password'],$user->password)){
            throw new Exception("Email atau password salah",401);
        }

        if(is_null($user->email_verified_at)){
            throw new Exception("Email belum terverifikasi",403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user'=>$user,
            'token'=>$token
        ];
    }

    public function logout(User $user)
    {
        $user->tokens()->delete();
    }
}