<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarcaController extends Controller
{

    private $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca::orderBy('nome');
    }

    /**
     * @OA\Get(
     *     path="/principais-marcas",
     *     description="Retorna uma lista com as principais marcas",
     *     summary="Retorna uma lista com as principais marcas",
     *     tags={"marcas"}, 
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
    public function principais_marcas()
    {
        $marcas = $this->marca->where('principal', '=', true)->paginate(10);
        return response()->json($marcas, 200);
    }

    /**
     * @OA\Get(
     *     path="/marcas",
     *     description="Retorna uma lista com todas as marcas",
     *     summary="Retorna uma lista com todas as marcas",
     *     tags={"marcas"}, 
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
        $marcas = $this->marca->get();
        return response()->json($marcas, 200);
    }

    /**
     * @OA\Post(
     *     path="/marcas",
     *     tags={"marcas"}, 
     *     description="Cadastra uma nova marca",
     *     summary="Cadastra uma nova marca",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         description="Nome da marca",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="pai",
     *         in="query",
     *         description="ID da marca, em caso de ser uma submarca",
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
     *         description="Define se uma marca é principal ou não",
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

            $marca = $this->marca->create($data);

            return response()->json([
                'data' => [
                    'id' => $marca->id,
                    'msg' => 'Marca cadastrada com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/marcas/{id}",
     *     description="Retorna uma marca via ID",
     *     summary="Retorna uma marca via ID",
     *     security={{"bearerAuth":{}}},
     *     tags={"marcas"}, 
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
    public function show($id)
    {
        try {

            $marca = $this->marca->findOrFail($id);
            return response()->json([
                'marca' =>  $marca
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Put(
     *     path="/marcas/{id}",
     *     tags={"marcas"}, 
     *     description="Atualiza uma marca",
     *     summary="Atualiza uma marca",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *       description="Informe o ID da marca desejada",
     *       in="path",
     *       name="id",
     *       required=true,
     *       example="123",
     *    ),
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         description="Nome da marca",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="pai",
     *         in="query",
     *         description="ID da marca, em caso de ser uma submarca",
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
     *         description="Define se uma marca é principal ou não",
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

            $marca = $this->marca->findOrFail($id);
            $marca->update($data);

            return response()->json([
                'data' => [
                    'id' => $marca->id,
                    'msg' => 'Marca atualizada com sucesso!'
                ]
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/marcas-categoria/{id}",
     *     description="Retorna uma lista de marcas pertencentes na categoria informada como parâmetro",
     *     summary="Retorna uma lista de marcas pertencentes na categoria informada como parâmetro",
     *     tags={"marcas"}, 
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
    public function marca_categoria($id){
        try {

            $marcas = DB::connection('pgsql')
            ->table('marcas')
            ->select(DB::raw('distinct marcas.*'))
            ->join('produtos', 'marcas.id', '=', 'produtos.marca_id')
            ->where('produtos.categoria_id', '=', $id)
            ->where('marcas.id', '<>', null)
            ->get();

            return response()->json($marcas, 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
