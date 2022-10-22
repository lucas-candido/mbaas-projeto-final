<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user::orderBy('id');
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     description="Retorna uma lista de usuários",
     *     summary="Retorna uma lista de usuários",
     *     security={{"bearerAuth":{}}},
     *     tags={"usuarios"}, 
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
        $users = $this->user->paginate(10);
        return response()->json($users, 200);
    }

    /**
     * @OA\Post(
     *     path="/cadastro",
     *     tags={"usuarios"}, 
     *     description="Cadastra um novo usuário",
     *     summary="Cadastra um novo usuário",
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
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do usuário",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="cpfcnpj",
     *         in="query",
     *         description="CPF ou CNPJ do usuário",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="endereco",
     *         in="query",
     *         description="Endereço do usuário",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="numeroendereco",
     *         in="query",
     *         description="Número do endereço do usuário",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="bairro",
     *         in="query",
     *         description="Bairro do usuário",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="cidade",
     *         in="query",
     *         description="Cidade do usuário",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Estado do usuário",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="cep",
     *         in="query",
     *         description="CEP do usuário",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="telefone",
     *         in="query",
     *         description="Telefone do usuário",
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
    public function store(Request $request)
    {
        $data= $request->all();

        if (!$request->has('password') || !$request->get('password')) {
            $message = new ApiMessages('É necessário informar uma senha para o usuário');
            return response()->json(['error' => $message->getMessage()], 422);
        }

        try {

            $data['password'] = bcrypt($data['password']);
            $user = $this->user->create($data);
            return response()->json([
                'id' => $user->id
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     description="Retorna um usuário via ID",
     *     summary="Retorna um usuário via ID",
     *     security={{"bearerAuth":{}}},
     *     tags={"usuarios"}, 
     *     @OA\Parameter(
     *       description="Informe o ID do usuário desejado",
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

            $user = $this->user->findOrFail($id);
            return response()->json([
                'usuario' =>  $user
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/perfil",
     *     description="Retorna o perfil do usuário",
     *     summary="Retorna o perfil do usuario",
     *     security={{"bearerAuth":{}}},
     *     tags={"usuarios"}, 
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
    public function perfil()
    {
        try {

            $user = $this->user->findOrFail(auth('api')->id());
            return response()->json([
                'usuario' =>  $user
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Put(
     *     path="/edita-perfil",
     *     tags={"usuarios"}, 
     *     description="Atualiza o perfil do usuário",
     *     summary="Atualiza o perfil do usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email do usuário",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Senha do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="cpfcnpj",
     *         in="query",
     *         description="CPF ou CNPJ do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="endereco",
     *         in="query",
     *         description="Endereço do usuário",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="numeroendereco",
     *         in="query",
     *         description="Número do endereço do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="bairro",
     *         in="query",
     *         description="Bairro do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="cidade",
     *         in="query",
     *         description="Cidade do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Estado do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="cep",
     *         in="query",
     *         description="CEP do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="telefone",
     *         in="query",
     *         description="Telefone do usuário",
     *         required=false,
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
    public function edita_perfil(Request $request)
    {
        $data= $request->all();
        if ($request->has('password') && $request->get('password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        try {

            $user = $this->user->findOrFail(auth('api')->id());
            $user->update($data);
            return response()->json([
                'data' => [
                    'msg' => 'Usuário atualizado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }


    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"usuarios"}, 
     *     description="Atualiza um usuário",
     *     summary="Atualiza um usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID do usuário desejado",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email do usuário",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Senha do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="cpfcnpj",
     *         in="query",
     *         description="CPF ou CNPJ do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="endereco",
     *         in="query",
     *         description="Endereço do usuário",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="numeroendereco",
     *         in="query",
     *         description="Número do endereço do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="bairro",
     *         in="query",
     *         description="Bairro do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="cidade",
     *         in="query",
     *         description="Cidade do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Estado do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="cep",
     *         in="query",
     *         description="CEP do usuário",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="telefone",
     *         in="query",
     *         description="Telefone do usuário",
     *         required=false,
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
    public function update(Request $request, $id)
    {
        $data= $request->all();
        try {

            $user = $this->user->findOrFail($id);
            $user->update($data);

            return response()->json([
                'data' => [
                    'id' => $user->id,
                    'msg' => 'Usuário atualizado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
