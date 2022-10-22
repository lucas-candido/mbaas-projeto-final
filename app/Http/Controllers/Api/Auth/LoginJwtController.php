<?php

namespace App\Http\Controllers\Api\Auth;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginJwtController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login-usuario",
     *     tags={"usuarios"}, 
     *     description="Efetua o login do usuário",
     *     summary="Efetua o login do usuário",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email do usuário",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Senha do usuário",
     *         required=true,
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Missing Data"
     *     )
     * )
     */    
    public function login_usuario(Request $request)
    {
        $credentials = $request->all(['email', 'password']);

        $validator = Validator::make($credentials, [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(!$token = auth('api')->attempt($credentials)){
            $message = new ApiMessages('Login e/ou senha inválidos');
            return response()->json(['error' => $message->getMessage()], 401);
        };

        return response()->json([
            'id' => auth('api')->id(),
            'token' => $token
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout-usuario",
     *     tags={"usuarios"}, 
     *     description="Efetua o logout do usuário",
     *     summary="Efetua o logout do usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Missing Data"
     *     )
     * )
     */    
    public function logout_usuario()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logout realizado com sucesso!'], 200);
    }

    /**
     * @OA\Post(
     *     path="/logout-admin",
     *     tags={"administradores"}, 
     *     description="Efetua o logout do administrador",
     *     summary="Efetua o logout do administrador",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Missing Data"
     *     )
     * )
     */       
    public function logout_admin()
    {
        auth('admin')->logout();
        return response()->json(['message' => 'Logout realizado com sucesso!'], 200);
    }

    /**
     * @OA\Post(
     *     path="/login-admin",
     *     tags={"administradores"}, 
     *     description="Efetua o login do administrador",
     *     summary="Efetua o login do administrador",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email do administrador",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Senha do administrador",
     *         required=true,
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Missing Data"
     *     )
     * )
     */ 
    public function login_administrador(Request $request)
    {
        $credentials = $request->all(['email', 'password']);

        $validator = Validator::make($credentials, [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(!$token = auth('admin')->attempt($credentials)){
            $message = new ApiMessages('Email e/ou senha inválidos');
            return response()->json(['error' => $message->getMessage()], 401);
        };

        return response()->json([
            'token' => $token
        ]);
    }
}
