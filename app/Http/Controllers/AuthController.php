<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            \Log::info('Datos recibidos en la solicitud POST:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                \Log::info('La validación ha fallado:', $validator->errors()->toArray());
                return response()->json(['error' => $validator->errors()], 400);
            } else {
                \Log::info('La validación ha pasado correctamente.');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            //$token = $user->createToken('AppName')->accessToken;

            return response()->json(['token' => 'string'], 201);
        } catch (Exception $e) {
            return response()->json([
                'CODERESPONSE' => 500,
                'RESPONSE' => $e->getMessage(),
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('AppName')->accessToken;
                return response()->json(['token' => $token, 'role' => $user->role], 200); 
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'CODERESPONSE' => 500,
                'RESPONSE' => $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (Exception $e) {
            return response()->json([
                'CODERESPONSE' => 500,
                'RESPONSE' => $e->getMessage(),
            ]);
        }
    }

    public function user(Request $request)
    {
        try {
            return response()->json($request->user(), 200);
        } catch (Exception $e) {
            return response()->json([
                'CODERESPONSE' => 500,
                'RESPONSE' => $e->getMessage(),
            ]);
        }
    }
}
