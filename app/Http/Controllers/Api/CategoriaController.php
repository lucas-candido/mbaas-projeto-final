<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Models\Categoria;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    private $categoria;

    public function __construct(Categoria $categoria)
    {
        $this->categoria = $categoria::orderBy('id');
    }

    /**
     * @OA\Get(
     *     path="/categorias",
     *     description="Retorna uma lista com todas as categorias",
     *     summary="Retorna uma lista com todas as categorias",
     *     tags={"categorias"}, 
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
        $categorias = $this->categoria->paginate(10);
        return response()->json($categorias, 200);
    }

    /**
     * @OA\Get(
     *     path="/principais",
     *     description="Retorna uma lista com as principais categorias",
     *     summary="Retorna uma lista com as principais categorias",
     *     tags={"categorias"}, 
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
    public function principais()
    {
        $categorias = $this->categoria->where('principal', '=', true)->paginate(10);
        return response()->json($categorias, 200);
    }

    /**
     * @OA\Post(
     *     path="/categorias",
     *     tags={"categorias"}, 
     *     description="Cadastra uma nova categoria",
     *     summary="Cadastra uma nova categoria",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         description="Nome da categoria",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="pai",
     *         in="query",
     *         description="ID da categoria, em caso de ser uma subcategoria",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="imagem",
     *         in="query",
     *         description="URL da imagem",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="principal",
     *         in="query",
     *         description="Define se uma categoria é principal ou não",
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

        try {

            $categoria = $this->categoria->create($data);

            return response()->json([
                'data' => [
                    'id' => $categoria->id,
                    'msg' => 'Categoria cadastrada com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/categorias/{id}",
     *     description="Retorna uma categoria via ID",
     *     summary="Retorna uma categoria via ID",
     *     tags={"categorias"}, 
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID da categoria desejada",
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

            $categoria = $this->categoria->findOrFail($id);
            return response()->json([
                'marca' =>  $categoria
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Put(
     *     path="/categorias/{id}",
     *     tags={"categorias"}, 
     *     description="Atualiza uma categoria",
     *     summary="Atualiza uma categoria",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID da categoria desejada",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         description="Nome da categoria",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="pai",
     *         in="query",
     *         description="ID da categoria, em caso de ser uma subcategoria",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="imagem",
     *         in="query",
     *         description="URL da imagem",
     *         required=false,
     *     ), 
     *     @OA\Parameter(
     *         name="principal",
     *         in="query",
     *         description="Define se uma categoria é principal ou não",
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

        try {

            $categoria = $this->categoria->findOrFail($id);
            $categoria->update($data);

            return response()->json([
                'data' => [
                    'id' => $categoria->id,
                    'msg' => 'Categoria atualizada com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/categorias-marca/{id}",
     *     description="Retorna uma lista de categorias pertencentes a marca informada como parâmetro",
     *     summary="Retorna uma lista de categorias pertencentes a marca informada como parâmetro",
     *     tags={"categorias"}, 
     *     @OA\Parameter(
     *       description="Informe o ID da marca desejada",
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
    public function categorias_marca($id){
        try {

            $categorias = DB::connection('pgsql')
            ->table('categorias')
            ->select(DB::raw('distinct categorias.*'))
            ->join('produtos', 'categorias.id', '=', 'produtos.categoria_id')
            ->where('produtos.marca_id', '=', $id)
            ->where('categorias.id', '<>', null)
            ->get();

            return response()->json($categorias, 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
