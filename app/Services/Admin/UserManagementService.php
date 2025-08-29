<?php
// app/Services/Admin/UserManagementService.php
namespace App\Services\Admin;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    public function __construct(
        private UserRepository $userRepo
    ) {}

    public function createUser(array $data): User
    {
        $userData = array_merge($data, [
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        $user = $this->userRepo->create($userData);
        $user->assignRole($data['role']);

        return $user;
    }

    public function updateUser(int $id, array $data): User
    {
        return $this->userRepo->update($id, $data);
    }

    public function updateStatus(int $id, string $status): User
    {
        $user = $this->userRepo->findOrFail($id);

        if ($user->id === auth()->id()) {
            throw new \Exception('You cannot change your own status');
        }

        $user = $this->userRepo->update($id, ['status' => $status]);

        if ($status === 'suspended') {
            $user->tokens()->delete();
        }

        return $user;
    }

    public function resetPassword(int $id, string $newPassword): User
    {
        $user = $this->userRepo->findOrFail($id);

        $user = $this->userRepo->update($id, [
            'password' => Hash::make($newPassword)
        ]);

        $user->tokens()->delete();
        return $user;
    }
}
