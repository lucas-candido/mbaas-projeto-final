<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\PedidoRequest;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    private $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    /**
     * @OA\Get(
     *     path="/pedidos",
     *     description="Retorna todos os pedidos do usuário",
     *     summary="Retorna todos os pedidos do usuário",
     *     security={{"bearerAuth":{}}},
     *     tags={"pedidos"}, 
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
        $pedidos = auth('api')->user()->pedido()->with('itens')->orderBy('id', 'desc');
        return response()->json($pedidos->get(), 200);
    }


    /**
     * @OA\Post(
     *     path="/pedidos",
     *     tags={"pedidos"}, 
     *     description="Cria um novo pedido",
     *     summary="Cria um novo pedido",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID do usuário a que o pedido pertence",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="pagamento",
     *         in="query",
     *         description="ID do tipo de pagamento",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="obs",
     *         in="query",
     *         description="Observaçoes do pedido",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="parcelas",
     *         in="query",
     *         description="Número de parcelas",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status do pedido",
     *         required=true,
     *     ), 
     *     @OA\Parameter(
     *         name="motivocancel",
     *         in="query",
     *         description="Mensagem de motivo do cancelamento do pedido",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="cancelusu",
     *         in="query",
     *         description="Indica se o pedido foi cancelado pelo usuário",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="cancelempresa",
     *         in="query",
     *         description="Indica se o pedido foi cancelado pela empresa",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="vrfrete",
     *         in="query",
     *         description="Valor do frete do pedido",
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
    public function store(PedidoRequest $request)
    {
        $data= $request->all();

        try {

            $data['user_id'] = auth('api')->user()->id;

            // Checa se o usuário tem pedidos em aberto
            $em_aberto = DB::connection('pgsql')
            ->table('pedidos')
            ->select(DB::raw('count(*) as em_aberto'))
            ->where('status', '=', 0)
            ->where('user_id', '=', auth('api')->user()->id)
            ->get();

            if($em_aberto[0]->em_aberto > 0) {

                // Se tiver, pega o código do pedido e retorna na consulta
                $pedidoAberto = DB::connection('pgsql')
                ->table('pedidos')
                ->select(DB::raw('id'))
                ->where('status', '=', 0)
                ->where('user_id', '=', auth('api')->user()->id)
                ->get();


                return response()->json([
                        'data' => [
                            'pedido' => $pedidoAberto[0]->id
                        ]
                ], 200);
            }

            // Se não, cria um novo pedido e retorna o ID
            $pedido = $this->pedido->create($data);

            return response()->json([
                'data' => [
                    'pedido' => $pedido->id
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/carrinho",
     *     description="Retorna o carrinho de compras do usuário",
     *     summary="Retorna o carrinho de compras do usuário",
     *     security={{"bearerAuth":{}}},
     *     tags={"pedidos"}, 
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
    public function carrinho()
    {
        try {

            $pedidoAberto = DB::connection('pgsql')
            ->table('pedidos')
            ->select(DB::raw('id'))
            ->where('status', '=', 0)
            ->where('user_id', '=', auth('api')->user()->id)
            ->get();

            $pedido = auth('api')->user()->pedido()->with('itens')->findOrFail($pedidoAberto[0]->id);
            return response()->json([
                'pedido' =>  $pedido
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/pedidos/{id}",
     *     description="Retorna um pedido pelo seu ID",
     *     summary="Retorna um pedido pelo seu ID",
     *     security={{"bearerAuth":{}}},
     *     tags={"pedidos"}, 
     *     @OA\Parameter(
     *       description="Informe o ID do pedido desejado",
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

            $pedido = $this->pedido->with('itens')->with('user')->findOrFail($id);
            return response()->json([
                'pedido' =>  $pedido
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Put(
     *     path="/pedidos/{id}",
     *     tags={"pedidos"}, 
     *     description="Atualiza um pedido do usuário",
     *     summary="Atualiza um pedido do usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID do pedido desejado",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
     *     @OA\Parameter(
     *         name="pagamento",
     *         in="query",
     *         description="ID do tipo de pagamento",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="obs",
     *         in="query",
     *         description="Observaçoes do pedido",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="parcelas",
     *         in="query",
     *         description="Número de parcelas",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status do pedido",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="motivocancel",
     *         in="query",
     *         description="Mensagem de motivo do cancelamento do pedido",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="cancelusu",
     *         in="query",
     *         description="Indica se o pedido foi cancelado pelo usuário",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="cancelempresa",
     *         in="query",
     *         description="Indica se o pedido foi cancelado pela empresa",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="vrfrete",
     *         in="query",
     *         description="Valor do frete do pedido",
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
    public function update(PedidoRequest $request, $id)
    {
        $data= $request->all();

        try {

            $pedido = auth('api')->user()->pedido()->findOrFail($id);
            $pedido->update($data);

            return response()->json([
                'data' => [
                    'msg' => 'Pedido atualizado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/pedidos-completo",
     *     description="Retorna todos os pedidos, independente do usuário",
     *     summary="Retorna todos os pedidos, independente do usuário",
     *     tags={"pedidos"}, 
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
    public function pedidos_completo()
    {
        // Mostra todos os pedidos, independente do usuário
        $pedidos = $this->pedido->get();
        return response()->json($pedidos, 200);
    }

    /**
     * @OA\Get(
     *     path="/concluidos",
     *     description="Retorna uma lista de pedidos concluídos",
     *     summary="Retorna uma lista de pedidos concluídos",
     *     security={{"bearerAuth":{}}},
     *     tags={"pedidos"}, 
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
    public function concluidos()
    {
        // Mostra todos os pedidos finalizados pelos usuários
        $pedidos = $this->pedido
        ->where('status', '=', 1)->get();
        return response()->json($pedidos, 200);
    }

    /**
     * @OA\Get(
     *     path="/cancelados",
     *     description="Retorna uma lista de pedidos cancelados",
     *     summary="Retorna uma lista de pedidos cancelados",
     *     tags={"pedidos"}, 
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
    public function cancelados()
    {
        // Mostra todos os pedidos cancelados pelos usuários
        $pedidos = $this->pedido
        ->where('status', '=', 3)
        ->where('cancelusu', true)->get();
        return response()->json($pedidos, 200);
    }

    /**
     * @OA\Put(
     *     path="/pedido-update/{id}",
     *     tags={"pedidos"}, 
     *     description="Atualiza um pedido (apenas para admins)",
     *     summary="Atualiza um pedido (apenas para admins)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID do pedido desejado",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID do usuário a que o pedido pertence",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="pagamento",
     *         in="query",
     *         description="ID do tipo de pagamento",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="obs",
     *         in="query",
     *         description="Observaçoes do pedido",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="parcelas",
     *         in="query",
     *         description="Número de parcelas",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status do pedido",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="motivocancel",
     *         in="query",
     *         description="Mensagem de motivo do cancelamento do pedido",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="cancelusu",
     *         in="query",
     *         description="Indica se o pedido foi cancelado pelo usuário",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="cancelempresa",
     *         in="query",
     *         description="Indica se o pedido foi cancelado pela empresa",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="vrfrete",
     *         in="query",
     *         description="Valor do frete do pedido",
     *         required=false,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */    
    public function pedido_update(PedidoRequest $request, $id)
    {
        $data= $request->all();

        try {

            $pedido = $this->pedido->findOrFail($id);
            $pedido->update($data);

            return response()->json([
                'data' => [
                    'msg' => 'Pedido atualizado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
