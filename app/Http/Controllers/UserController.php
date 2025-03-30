<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * DummyModel Constructor
     *
     *
     */
    public function __construct(protected UserService $userService)
    {
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return UserResource::collection($this->userService->getAll());
    }

    public function store(UserRequest $request): UserResource | \Illuminate\Http\JsonResponse
    {
        try {
            return new UserResource($this->userService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);

            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): UserResource
    {
        return UserResource::make($this->userService->getById($id));
    }

    public function update(UserRequest $request, int $id): UserResource | \Illuminate\Http\JsonResponse
    {
        try {
            return new UserResource($this->userService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);

            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->userService->deleteById($id);

            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);

            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
