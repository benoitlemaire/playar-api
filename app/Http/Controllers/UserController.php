<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $users = User::all();
        return UserResource::collection($users->load('offers', 'roles'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return response()->json([
            'user' => new UserResource($user->load('roles', 'offers'))
        ],201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user)
    {
        // Todo : Faire une route pour update une company et une autre pour update un freelance et supprimer l'image dans S3 comme pour ooffers
        $user->update($request->all());

        return response()->json([
            'user' => new UserResource($user->load('roles', 'offers'))
        ],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([],200);
    }

    public function toggleValidationUser(User $user)
    {
        $user->validated = !$user->validated;
        $user->save();

        return response()->json($user);
    }
}
