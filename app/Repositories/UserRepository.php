<?php
// app/Repositories/UserRepository.php
namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;

class UserRepository
{
    public function findOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->findOrFail($id);
        $user->update($data);
        return $user->fresh();
    }

    public function countCustomers(): int
    {
        return User::role('customer')->count();
    }

    public function countActiveCustomers(): int
    {
        return User::role('customer')->where('status', 'active')->count();
    }

    public function countNewCustomersToday(): int
    {
        return User::role('customer')->whereDate('created_at', today())->count();
    }

    public function getWithFilters(array $filters, int $perPage = 15)
    {
        $query = User::with(['roles']);

        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['verified'])) {
            if ($filters['verified']) {
                $query->verified();
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getUserBookings(int $userId, int $perPage = 10)
    {
        $user = $this->findOrFail($userId);
        return $user->bookings()
            ->with(['roomType', 'spaBooking.spaPackage', 'spayBooking.spayPackage'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUserOrders(int $userId, int $perPage = 10)
    {
        $user = $this->findOrFail($userId);
        return $user->orders()
            ->with('orderItems.product')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search customers by name, email, or phone
     */
    public function searchCustomers(string $searchTerm, int $limit = 10)
    {
        return User::role('customer')
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%");
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }
}
