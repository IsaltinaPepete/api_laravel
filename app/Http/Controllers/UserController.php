<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
         
        $current_page = $request->get('current_page') ?? 1;
        $skip = ($current_page - 1) * 3;

        $users = User::skip($skip)->take(3)->orderByDesc('id')->get();
        return response()->json($users->toResourceCollection(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve users'], 500);
        }   
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        try {
            $user = new User();
            $user->fill($data);
            $user->password = Hash::make(12234); // Default password
            $user->save();
            return response()->json($user->toResource(), 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Failed to create user'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user->toResource(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            $user = User::findOrFail($id);
            $user->update($data);
            return response()->json($user->toResource(), 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Failed to create user'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            
            $removed = User::destroy($id);
            if (!$removed) {
                throw new Exception('User not found');
            }
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }
}
