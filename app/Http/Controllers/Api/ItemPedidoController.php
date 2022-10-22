<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemPedidoRequest;
use App\Models\ItemPedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemPedidoController extends Controller
{
    private $itemPedido;

    public function __construct(ItemPedido $itemPedido)
    {
        $this->itemPedido = $itemPedido;
    }

    /**
     * @OA\Post(
     *     path="/item-pedidos",
     *     tags={"itens"}, 
     *     description="Adiciona um item a um pedido existente",
     *     summary="Adiciona um item a um pedido existente",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="pedido_id",
     *         in="query",
     *         description="ID do pedido a que o item pertence",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="produto_id",
     *         in="query",
     *         description="ID do produto",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="quantidade",
     *         in="query",
     *         description="Quantiade comprada do item",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="unitario",
     *         in="query",
     *         description="Valor unitário do item",
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
    public function store(ItemPedidoRequest $request)
    {
        $data= $request->all();

        try {

            $pedidoAberto = DB::connection('pgsql')
            ->table('pedidos')
            ->select(DB::raw("id"))
            ->where('status', '=', 0)
            ->where('user_id', '=', auth('api')->user()->id)
            ->get();

            if ($pedidoAberto[0]->id <> null) {
                $data['pedido_id'] = $pedidoAberto[0]->id;
            }

            $itemPedido = $this->itemPedido->create($data);
            $produto = Produto::where('id', $itemPedido->produto_id);

            $estoque = $produto->first()->quantidade - $request->quantidade;
            $produto->update(array('quantidade' => $estoque));

            return response()->json([
                'data' => [
                    'msg' => 'Item incluído com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/item-pedidos/{id}",
     *     description="Retorna um produto do pedido pelo ID",
     *     summary="Retorna um produto do pedido pelo ID",
     *     security={{"bearerAuth":{}}},
     *     tags={"itens"}, 
     *     @OA\Parameter(
     *       description="Informe o ID do item desejado",
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

            $itemPedido = $this->itemPedido->findOrFail($id);
            return response()->json([
                'data' =>  $itemPedido
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    
    /**
     * @OA\Put(
     *     path="/item-pedidos",
     *     tags={"itens"}, 
     *     description="Edita um item de um pedido existente",
     *     summary="Edita um item de um pedido existente",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID do item desejado",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
     *     @OA\Parameter(
     *         name="produto_id",
     *         in="query",
     *         description="ID do produto",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="quantidade",
     *         in="query",
     *         description="Quantiade comprada do item",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="unitario",
     *         in="query",
     *         description="Valor unitário do item",
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
    public function update(ItemPedidoRequest $request, $id)
    {
        $data= $request->all();
        
        try {

            $itemPedido = $this->itemPedido->findOrFail($id);
            $produto = Produto::where('id', $itemPedido->produto_id);
            $estoque = ($produto->first()->quantidade + $itemPedido->quantidade)
                - $request->quantidade;

            $itemPedido->update($data);
            $produto->update(array('quantidade' => $estoque));



            return response()->json([
                'data' => [
                    'msg' => 'Item atualizado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Delete(
     *     path="/item-pedidos/{id}",
     *     description="Remove um produto do pedido pelo ID",
     *     summary="Remove um produto do pedido pelo ID",
     *     security={{"bearerAuth":{}}},
     *     tags={"itens"}, 
     *     @OA\Parameter(
     *       description="Informe o ID do item desejado",
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
    public function destroy($id)
    {
        try {

            $itemPedido = $this->itemPedido->findOrFail($id);
            $produto = Produto::where('id', $itemPedido->produto_id);
            $estoque = $produto->first()->quantidade + $itemPedido->quantidade;
            $itemPedido->delete();
            $produto->update(array('quantidade' => $estoque));

            return response()->json([
                'data' => [
                    'msg' => 'Item removido com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
