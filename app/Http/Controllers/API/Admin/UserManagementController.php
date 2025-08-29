<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Admin\{CreateUserRequest, UpdateUserRequest};
use App\Services\Admin\UserManagementService;
use App\Repositories\UserRepository;
use Illuminate\Http\{JsonResponse, Request};

class UserManagementController extends Controller
{
    public function __construct(
        private UserManagementService $userService,
        private UserRepository $userRepo
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $users = $this->userRepo->getWithFilters(
                $request->only(['role', 'status', 'verified', 'search']),
                $request->get('per_page', 15)
            );

            return response()->json([
                'success' => true,
                'data' => UserResource::collection($users->items()),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userRepo->findOrFail($id);
            $user->load(['roles', 'bookings', 'orders']);

            return response()->json([
                'success' => true,
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $request->validate(['status' => 'required|in:active,suspended']);

        try {
            $user = $this->userService->updateStatus($id, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'data' => ['status' => $user->status]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function resetPassword(int $id, Request $request): JsonResponse
    {
        $request->validate(['new_password' => 'required|string|min:8|confirmed']);

        try {
            $this->userService->resetPassword($id, $request->new_password);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function userBookings(int $id, Request $request): JsonResponse
    {
        try {
            $bookings = $this->userRepo->getUserBookings($id, $request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $bookings->items(),
                'pagination' => [
                    'current_page' => $bookings->currentPage(),
                    'per_page' => $bookings->perPage(),
                    'total' => $bookings->total(),
                    'last_page' => $bookings->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    }

    public function userOrders(int $id, Request $request): JsonResponse
    {
        try {
            $orders = $this->userRepo->getUserOrders($id, $request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    }
}
