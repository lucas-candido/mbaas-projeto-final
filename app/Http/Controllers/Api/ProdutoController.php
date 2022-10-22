<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProdutoRequest;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{

    private $produto;

    public function __construct(Produto $produto)
    {
        $this->produto = $produto::orderBy('id');
    }

    public function index()
    {
        $produtos = $this->produto->with('categoria', 'marca')->paginate(10);
        return response()->json($produtos, 200);
    }

    /**
     * @OA\Post(
     *     path="/produtos",
     *     tags={"produtos"}, 
     *     description="Cadastra um novo produto",
     *     summary="Cadastra um novo produto",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="produto",
     *         in="query",
     *         description="Código de referencia do produto",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         description="Nome do produto",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="quantidade",
     *         in="query",
     *         description="Quantidade em estoque do produto",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="preco",
     *         in="query",
     *         description="Preço de venda do produto",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="tamanho",
     *         in="query",
     *         description="Tamanho do produto",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="cor",
     *         in="query",
     *         description="Cor do produto",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="inativo",
     *         in="query",
     *         description="Flag que define se o produto está ativo ou não",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="imagem",
     *         in="query",
     *         description="URL da imagem do produto",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="destaque",
     *         in="query",
     *         description="Flag que define se um produto é destaque ou não",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="preco_promocional",
     *         in="query",
     *         description="Preço promocional do produto",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="descricao",
     *         in="query",
     *         description="Descrição do produto",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="marca_id",
     *         in="query",
     *         description="ID da marca",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="categoria_id",
     *         in="query",
     *         description="ID da categoria",
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
    public function store(ProdutoRequest $request)
    {
        $data= $request->all();

        try {

            $produto = $this->produto->create($data);

            return response()->json([
                'data' => [
                    'id' => $produto->id,
                    'msg' => 'Produto cadastrado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Put(
     *     path="/produtos/{id}",
     *     tags={"produtos"}, 
     *     description="Atualiza um produto",
     *     summary="Atualiza um produto",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="ID do produto",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
     *     @OA\Parameter(
     *         name="produto",
     *         in="query",
     *         description="Código de referencia do produto",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         description="Nome do produto",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="quantidade",
     *         in="query",
     *         description="Quantidade em estoque do produto",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="preco",
     *         in="query",
     *         description="Preço de venda do produto",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="tamanho",
     *         in="query",
     *         description="Tamanho do produto",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="cor",
     *         in="query",
     *         description="Cor do produto",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="inativo",
     *         in="query",
     *         description="Flag que define se o produto está ativo ou não",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="imagem",
     *         in="query",
     *         description="URL da imagem do produto",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="destaque",
     *         in="query",
     *         description="Flag que define se um produto é destaque ou não",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="preco_promocional",
     *         in="query",
     *         description="Preço promocional do produto",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="descricao",
     *         in="query",
     *         description="Descrição do produto",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="marca_id",
     *         in="query",
     *         description="ID da marca",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="categoria_id",
     *         in="query",
     *         description="ID da categoria",
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
    public function update(ProdutoRequest $request, $id)
    {
        $data= $request->all();

        try {

            $produto = $this->produto->findOrFail($id);
            $produto->update($data);

            return response()->json([
                'data' => [
                    'msg' => 'Produto atualizado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
