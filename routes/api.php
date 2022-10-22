<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('Api')->group(function(){

    // Rotas de cadastro de usuário
    Route::post('cadastro',[App\Http\Controllers\Api\UserController::class, 'store']);

    // Rotas de autenticação de usuário
    Route::post('login-usuario',[App\Http\Controllers\Api\Auth\LoginJwtController::class, 'login_usuario']);
    Route::post('logout-usuario',[App\Http\Controllers\Api\Auth\LoginJwtController::class, 'logout_usuario']);

    // Rotas de autenticação de administrador
    Route::post('login-admin',[App\Http\Controllers\Api\Auth\LoginJwtController::class, 'login_administrador']);
    Route::post('logout-admin',[App\Http\Controllers\Api\Auth\LoginJwtController::class, 'logout_admin']);

    // Rota de pesquisa de produtos
    Route::get('pesquisa/{where}',[App\Http\Controllers\Api\BuscaProdutoController::class, 'pesquisa']);
    Route::get('destaques',[App\Http\Controllers\Api\BuscaProdutoController::class, 'destaques']);
    Route::get('produtos/{id}',[App\Http\Controllers\Api\BuscaProdutoController::class, 'show']);

    // Rotas de pesquisa de marcas
    Route::get('marcas',[App\Http\Controllers\Api\MarcaController::class, 'index']);
    Route::get('principais-marcas',[App\Http\Controllers\Api\MarcaController::class, 'principais_marcas']);
    Route::get('marcas-categoria/{id}',[App\Http\Controllers\Api\MarcaController::class, 'marca_categoria']);

    // Rotas de pesquisa de categorias
    Route::get('categorias',[App\Http\Controllers\Api\CategoriaController::class, 'index']);
    Route::get('categorias-marca/{id}',[App\Http\Controllers\Api\CategoriaController::class, 'categorias_marca']);
    Route::get('principais',[App\Http\Controllers\Api\CategoriaController::class, 'principais']);

    // ADMIN
    Route::group(['middleware' => ['assign.guard:admin','jwt.auth']],function () {

        // Rotas de produtos
        Route::post('produtos',[App\Http\Controllers\Api\ProdutoController::class, 'store']);
        Route::put('produtos/{id}',[App\Http\Controllers\Api\ProdutoController::class, 'update']);

        // Rotas de marcas
        Route::get('marcas/{id}',[App\Http\Controllers\Api\MarcaController::class, 'show']);
        Route::post('marcas',[App\Http\Controllers\Api\MarcaController::class, 'store']);
        Route::put('marcas/{id}',[App\Http\Controllers\Api\MarcaController::class, 'update']);

        // Rotas de categorias (categorias)
        Route::get('categorias/{id}',[App\Http\Controllers\Api\CategoriaController::class, 'show']);
        Route::post('categorias',[App\Http\Controllers\Api\CategoriaController::class, 'store']);
        Route::put('categorias/{id}',[App\Http\Controllers\Api\CategoriaController::class, 'update']);

        //Rotas de administrador
        Route::get('admins',[App\Http\Controllers\Api\AdminController::class, 'index']);
        Route::get('admins/{id}',[App\Http\Controllers\Api\AdminController::class, 'show']);
        Route::post('admins',[App\Http\Controllers\Api\AdminController::class, 'store']);
        Route::put('admins/{id}',[App\Http\Controllers\Api\AdminController::class, 'update']);

        // Rota de listagem de usuários
        Route::get('users', 'UserController@index')->name('users');
        Route::get('users/{id}', 'UserController@show')->name('users');
        Route::put('users/{id}', 'UserController@update')->name('users');

        // Rotas de controle de pedidos
        Route::get('pedidos/{id}',[App\Http\Controllers\Api\PedidoController::class, 'show']);
        Route::get('concluidos',[App\Http\Controllers\Api\PedidoController::class, 'concluidos']);
        Route::get('cancelados',[App\Http\Controllers\Api\PedidoController::class, 'cancelados']);
        Route::get('pedidos-completo',[App\Http\Controllers\Api\PedidoController::class, 'pedidos_completo']);
        Route::put('pedido-update/{id}',[App\Http\Controllers\Api\PedidoController::class, 'pedido_update']);

    });

    // USUÁRIOS
    Route::group(['middleware' => ['assign.guard:api','jwt.auth']],function () {

        // Rotas de usuário
        Route::get('perfil',[App\Http\Controllers\Api\UserController::class, 'perfil']);
        Route::put('edita-perfil',[App\Http\Controllers\Api\UserController::class, 'edita_perfil']);

        // Rotas de pedidos
        Route::get('carrinho',[App\Http\Controllers\Api\PedidoController::class, 'carrinho']);
        Route::get('pedidos',[App\Http\Controllers\Api\PedidoController::class, 'index']);
        Route::post('pedidos',[App\Http\Controllers\Api\PedidoController::class, 'store']);
        Route::put('pedidos/{id}',[App\Http\Controllers\Api\PedidoController::class, 'update']);

        // Rotas de itens
        Route::get('item-pedidos/{id}',[App\Http\Controllers\Api\ItemPedidoController::class, 'show']);
        Route::post('item-pedidos',[App\Http\Controllers\Api\ItemPedidoController::class, 'store']);
        Route::put('item-pedidos/{id}',[App\Http\Controllers\Api\ItemPedidoController::class, 'update']);
        Route::delete('item-pedidos/{id}',[App\Http\Controllers\Api\ItemPedidoController::class, 'destroy']);

        // Rotas de favoritos
        Route::get('favoritos-usuario',[App\Http\Controllers\Api\FavoritoController::class, 'user_favorite']);
        Route::delete('favoritos/{produto_id}',[App\Http\Controllers\Api\FavoritoController::class, 'destroy']);
        Route::post('favoritos',[App\Http\Controllers\Api\FavoritoController::class, 'store']);
    });
});
