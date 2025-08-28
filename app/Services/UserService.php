<?php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getCurrentUser()
    {
        return Auth::user();
    }

    public function register(array $data)
    {
        try {
            $data['password'] = bcrypt($data['password']);
            return $this->userRepository->create($data);
        } catch (\Exception $e) {
            Log::error("Register gagal: " . $e->getMessage());
            throw new Exception("Registrasi gagal", 500 . $e->getMessage());
        }
    }

    public function getAllUsers($perPage = 10)
    {
        try {
            return $this->userRepository->all($perPage);
        } catch (Exception $e) {
            Log::error("Gagal ambil data user: " . $e->getMessage());
            throw new Exception("Gagal mengambil data user", 500);
        }
    }

    public function getAllUsersList()
    {
        return $this->userRepository->allUsers();
    }

    public function getProfile($id)
    {
        try {
            return $this->userRepository->findById($id);
        } catch (\Exception $e) {
           throw new Exception("Profil tidak ditemukan", 404);
        }
    }

    public function updateProfile($id,array $data)
    {
        unset($data['password']);
        $user = $this->userRepository->findById($id);
     

        return $this->userRepository->update($user,$data);
    }

    public function sendResetPasswordLink(array $data)
    {
        try {
            if (!isset($data['email'])) {
                throw new Exception("Email wajib diisi", 422);
            }

            $status = Password::sendResetLink(['email' => $data['email']]);

            $messages = [
            Password::RESET_LINK_SENT => 'Link reset password telah dikirim ke email Anda.',
            Password::INVALID_USER    => 'Email tidak terdaftar.',
        ];

        return [
            'status' => $status,
            'message' => $messages[$status] ?? 'Terjadi kesalahan.',
            'success' => $status === Password::RESET_LINK_SENT
        ];

        } catch (\Exception $e) {
            Log::error("Gagal kirim link reset password: " . $e->getMessage());
            throw new Exception("Gagal kirim link reset password: " . $e->getMessage());
        }
    }

    public function resetPassword(array $data)
    {
        try {
            $status = Password::reset(
                $data,
                function ($user, $password) {
                    $user->forceFill([
                        'password' => bcrypt($password)
                    ])->save();
                }
            );

            return [
                'status' => $status,
                'message' => __($status),
                'success' => $status === Password::PASSWORD_RESET
            ];

        } catch (\Exception $e) {
            Log::error("Gagal reset password: " . $e->getMessage());
            throw new Exception("Gagal reset password", 500);
        }
    }
}