<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Models\Favorito;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoritoController extends Controller
{

    private $favorito;

    public function __construct(Favorito $favorito)
    {
        $this->favorito = $favorito::orderBy('id');
    }

    /**
     * @OA\Post(
     *     path="/favoritos",
     *     tags={"favoritos"}, 
     *     description="Adiciona um favorito",
     *     summary="Adiciona um favorito",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID do usuário a quem o favorito pertence",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="produto_id",
     *         in="query",
     *         description="ID do produto",
     *         required=true,
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
    public function store(Request $request)
    {
        $data= $request->all();

        try {

            $favorito = $this->favorito->create($data);

            return response()->json([
                'data' => [
                    'id' => $favorito->id,
                    'msg' => 'Favorito cadastrado com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Delete(
     *     path="/favoritos/{produto_id}",
     *     description="Remove um favorito",
     *     summary="Remove um favorito",
     *     tags={"favoritos"}, 
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID do produto desejado",
     *       in="path",
     *       name="produto_id",
     *       required=true,
     *       example="456",
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
    public function destroy($produto_id)
    {
        try {

            $favorito = DB::connection('pgsql')
            ->table('favoritos')
            ->select(DB::raw('favoritos.id'))
            ->where('favoritos.user_id', auth('api')->id())
            ->where('favoritos.produto_id', $produto_id);

            $favorito = $this->favorito->findOrFail($favorito->first()->id);
            $favorito->delete();

            return response()->json([
                'data' => [
                    'msg' => 'Favorito removido com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/favoritos-usuario",
     *     description="Retorna a lista de favoritos do usuário",
     *     summary="Retorna a lista de favoritos do usuário",
     *     tags={"favoritos"},
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
    public function user_favorite()
    {
        try {

            $produtos = DB::connection('pgsql')
            ->table('produtos')
            ->select(DB::raw('produtos.*, marcas.nome AS nome_marca, categorias.nome AS nome_categoria'))
            ->leftJoin('marcas', 'marcas.id', '=', 'produtos.marca_id')
            ->leftJoin('categorias', 'categorias.id', '=', 'produtos.categoria_id')
            ->leftJoin('favoritos', 'favoritos.produto_id', '=', 'produtos.id')
            ->where('favoritos.user_id', auth('api')->id());

            return response()->json([
                'favoritos' => $produtos->get()
            ], 200);


        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
