<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credenciais = $request->all(['email', 'password']);

        //autenticacao (email e senha)
        $token = auth('api')->attempt($credenciais);

        /*
         * Caso exista as credenciais estaja valida o token e retornado
         * Caso não e retornado 'false'
         * */

        if($token){ //usuari autenticado com sucesso
            return response()->json(['token' => $token],200);
        }else{ // erro de usuario ou senha
            return response()->json(['error' => 'Usuário ou senha invalido!'],403);
            // 401 = Unauthorized -> nao autorizado
            // 403 = Forbidden -> proibido (login invalido)
        }

        //retornar um Json Web Token

        return 'login';
    }

    public function logout()
    {
        return 'logout';
    }

    public function refresh()
    {
        $token = auth('api')->refresh(); // cliente encaminhe um jwt valido
        return response()->json(['token'=>$token]);
    }

    public function me()
    {
        return response()->json(auth()->user(),200);
    }
}
