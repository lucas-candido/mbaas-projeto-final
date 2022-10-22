<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Repository\ProdutoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BuscaProdutoController extends Controller
{
    private $produto;

    public function __construct(Produto $produto)
    {
        $this->produto = $produto;
    }

    /**
     * @OA\Get(
     *     path="/destaques",
     *     description="Retorna uma lista de produtos em destaque",
     *     summary="Retorna uma lista de produtos em destaque",
     *     tags={"produtos"}, 
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
    public function destaques()
    {
        $produtos = new ProdutoRepository($this->produto);

        return response()->json([
            'catalogo' => $produtos->getResult()
            ->with('categoria', 'marca')
            ->where('inativo', false)
            ->where('destaque', true)
            ->orderBy('nome')->paginate(10)
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/pesquisa/{where}",
     *     description="Retorna uma lista de produtos baseados no termo pesquisado",
     *     summary="Retorna uma lista de produtos baseados no termo pesquisado",
     *     tags={"produtos"}, 
     *     @OA\Parameter(
     *       description="Opções de busca -> busca=shampoo; busca=todos; marca=123; categoria=456",
     *       in="path",
     *       name="where",
     *       required=true,
     *       example="busca=todos",
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
    public function pesquisa($where){
        try {

            $exp = explode('=', urldecode($where));
            if($exp[0] == 'busca'){

                $andWhere = explode(';', $exp[1]);

                if($andWhere[0] == 'todos' || $andWhere[0] == ''){

                    $produtos = DB::connection('pgsql')
                    ->table('produtos')
                    ->select(DB::raw('produtos.*, marcas.nome AS nome_marca, categorias.nome AS nome_categoria'))
                    ->leftJoin('marcas', 'marcas.id', '=', 'produtos.marca_id')
                    ->leftJoin('categorias', 'categorias.id', '=', 'produtos.categoria_id')
                    ->where('produtos.inativo', false);

                } else {

                    $produtos = DB::connection('pgsql')
                    ->table('produtos')
                    ->select(DB::raw('produtos.*, marcas.nome AS nome_marca, categorias.nome AS nome_categoria'))
                    ->leftJoin('marcas', 'marcas.id', '=', 'produtos.marca_id')
                    ->leftJoin('categorias', 'categorias.id', '=', 'produtos.categoria_id')
                    ->where('produtos.inativo', false)
                    ->where('produtos.nome', 'ilike', '%'.$andWhere[0].'%');

                    if(isset($andWhere[1])){

                        $produtos = $produtos
                        ->where('marca_id', '=', $andWhere[1]);

                    } else {

                        $produtos = $produtos
                        ->orWhere('marcas.nome', 'ilike', '%'.$andWhere[0].'%')->where('produtos.inativo', false)
                        ->orWhere('categorias.nome', 'ilike', '%'.$andWhere[0].'%')->where('produtos.inativo', false);

                    }

                    if(isset($andWhere[2])){

                        $produtos = $produtos
                        ->where('categoria_id', '=', $andWhere[2]);

                    }
                }

            }

            if($exp[0] == 'categoria'){

                $andWhere = explode(';', $exp[1]);

                $produtos = DB::connection('pgsql')
                ->table('produtos')
                ->select(DB::raw('produtos.*, marcas.nome AS nome_marca, categorias.nome AS nome_categoria'))
                ->leftJoin('marcas', 'marcas.id', '=', 'produtos.marca_id')
                ->leftJoin('categorias', 'categorias.id', '=', 'produtos.categoria_id')
                ->where('produtos.inativo', false)
                ->where('produtos.categoria_id', '=', $andWhere[0]);

                if(isset($andWhere[1])){
                    $produtos = $produtos->where('marca_id', '=', $andWhere[1]);
                }

            }

            if($exp[0] == 'marca'){

                $andWhere = explode(';', $exp[1]);

                $produtos = DB::connection('pgsql')
                ->table('produtos')
                ->select(DB::raw('produtos.*, marcas.nome AS nome_marca, categorias.nome AS nome_categoria'))
                ->leftJoin('marcas', 'marcas.id', '=', 'produtos.marca_id')
                ->leftJoin('categorias', 'categorias.id', '=', 'produtos.categoria_id')
                ->where('produtos.inativo', false)
                ->where('produtos.marca_id', '=', $andWhere[0]);

                if(isset($andWhere[1])){
                    $produtos = $produtos->where('categoria_id', '=', $andWhere[1]);
                }

            }

            return response()->json([
                'catalogo' => $produtos->get()
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/produtos/{id}",
     *     description="Retorna um produto baseado no ID informado",
     *     summary="Retorna um produto baseado no ID informado",
     *     tags={"produtos"}, 
     *     @OA\Parameter(
     *       description="ID do produto",
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

            $produto = $this->produto->findOrFail($id);
            return response()->json([
                'produto' =>  $produto
            ], 200);

        } catch (\Exception $e) {

            $message = new ApiMessages($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
