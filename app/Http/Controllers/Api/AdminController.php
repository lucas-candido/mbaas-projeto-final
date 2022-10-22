<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    private $admin;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin::orderBy('id');
    }

    /**
     * @OA\Get(
     *     path="/admins",
     *     description="Retorna uma lista de administradores",
     *     summary="Retorna uma lista de administradores",
     *     tags={"administradores"}, 
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
    public function index()
    {
        $admins = $this->admin->paginate(10);
        return response()->json($admins, 200);
    }


    /**
     * @OA\Post(
     *     path="/admins",
     *     tags={"administradores"}, 
     *     description="Cadastra um novo administrador",
     *     summary="Cadastra um novo administrador",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do administrador",
     *         required=true,
     *     ),
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
     *     @OA\Parameter(
     *         name="inativo",
     *         in="query",
     *         description="Define se um administrador está ativo ou não",
     *         required=true,
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado. Apenas administradores podem cadastrar produtos"
     *     )
     * )
     */     
    public function store(Request $request)
    {
        $data= $request->all();

        if (!$request->has('password') || !$request->get('password')) {
            $message = new ApiMessages('É necessário informar uma senha para o administrador');
            return response()->json(['error' => $message->getMessage()], 422);
        }

        try {

            $data['password'] = bcrypt($data['password']);
            $admin = $this->admin->create($data);
            return response()->json([
                'data' => [
                    'id' => $admin->id,
                    'msg' => 'Administrador cadastrado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/admins/{id}",
     *     description="Retorna um administrador via ID",
     *     summary="Retorna um administrador via ID",
     *     tags={"administradores"}, 
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID do administradores desejado",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
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
    public function show($id)
    {
        try {

            $admin = $this->admin->findOrFail($id);
            return response()->json([
                'administrador' =>  $admin
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Put(
     *     path="/admins/{id}",
     *     tags={"administradores"}, 
     *     description="Atualiza um administrador",
     *     summary="Atualiza um administrador",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID do administradores desejado",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do administrador",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email do administrador",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Senha do administrador",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="inativo",
     *         in="query",
     *         description="Define se um administrador está ativo ou não",
     *         required=false,
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado. Apenas administradores podem cadastrar produtos"
     *     )
     * )
     */         
    public function update(Request $request, $id)
    {
        $data= $request->all();
        if ($request->has('password') && $request->get('password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        try {

            $admin = $this->admin->findOrFail($id);
            $admin->update($data);
            return response()->json([
                'data' => [
                    'msg' => 'Administrador atualizado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
