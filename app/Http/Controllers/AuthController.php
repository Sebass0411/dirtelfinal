<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Registro de un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
{
    try{
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

        'password' =>$request->password,

    ]);



    //$token = $user->createToken('AppName')->accessToken;



    return response()->json(['token' => 'string'], 201);
    }catch(Exception $e){
        return response()->json(
            [
                'CODERESPONSE'    => 500,
                'RESPONSE' => $e->getMessage(),
            ]
        );
    }
}


    /**
     * Inicio de sesión de un usuario existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try{
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $token = Auth::user()->createToken('AppName')->accessToken;
                return response()->json(['token' => $token], 200);
            }
            else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

        }catch(Exception $e){
            return response()->json(
                [
                    'CODERESPONSE'    => 500,
                    'RESPONSE' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Cierre de sesión del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try{
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }catch(Exception $e){
            return response()->json(
                [
                    'CODERESPONSE'    => 500,
                    'RESPONSE' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Obtener los datos del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        try{
            return response()->json($request->user(), 200);
        }catch(Exception $e){
            return response()->json(
                [
                    'CODERESPONSE'    => 500,
                    'RESPONSE' => $e->getMessage(),
                ]
            );
        }
    }
}
