<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    public function suspend(User $user)
    {
        $user->update([
            'suspended_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'User suspended successfully.']);
    }

    public function unsuspend(User $user)
    {
        $user->update([
            'suspended_at' => null,
        ]);

        return response()->json(['message' => 'User unsuspended successfully.']);
    }

    public function index()
    {
    }

    public function store(UserRequest $request)
    {
    }

    public function show(int $id)
    {
    }

    public function update(UserRequest $request, int $id)
    {
    }

    public function destroy(int $id)
    {
    }
}
