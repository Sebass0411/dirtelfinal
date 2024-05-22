<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('show');
    }

    public function index()
    {
        try {
            $users = User::all();
            return response()->json($users, 200);
        } catch (Exception $e) {
            return response()->json([
                'CODERESPONSE' => 500,
                'RESPONSE' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
{
    try {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:user,admin', 
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return response()->json($user, 201);
    } catch (Exception $e) {
        return response()->json([
            'CODERESPONSE' => 500,
            'RESPONSE' => $e->getMessage(),
        ], 500);
    }
}


    public function show(User $user)
    {
        try {
            // Verificar si el usuario autenticado puede ver este recurso
            if (Auth::id() !== $user->id && Auth::user()->role !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json([
                'CODERESPONSE' => 500,
                'RESPONSE' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            if (Auth::id() !== $user->id && Auth::user()->role !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            if ($request->has('password')) {
                $request->merge(['password' => bcrypt($request->password)]);
            }

            $user->update($request->all());
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json([
                'CODERESPONSE' => 500,
                'RESPONSE' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            if (Auth::id() !== $user->id && Auth::user()->role !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $user->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'CODERESPONSE' => 500,
                'RESPONSE' => $e->getMessage(),
            ], 500);
        }
    }
}
