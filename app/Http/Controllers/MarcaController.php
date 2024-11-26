<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class MarcaController extends Controller
{
    public function __construct(Marca $marca){
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //$marcas = Marca::all();
        $marcas = $this->marca->all();
        return response()->json($marcas,200);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //$marca = Marca::create($request->all());
        $regras = [
            'nome' => 'required|unique:marcas',
            'imagem' => 'required'
        ];
        $feedback = [
            'required' => 'O campo :attribute é obrigatorio',
            'nome.unique' => 'O nome da marca já existe'
        ];

        //stateless
        $request->validate($regras, $feedback);


        $marca = $this->marca->create($request->all());
        return response()->json($marca,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'],404); // json
        }
        return response()->json($marca,200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //$marca->update($request->all());
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Impossivel realiza a atualizacao. O recurso solicitado não existe'],404);
        }
        $marca->update($request->all());
        return response()->json($marca,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //$marca->delete();
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Impossivel realiza a exclusao. O recurso solicitado não existe'],404);
        }
        $marca->delete();
        return response()->json(['msg' => "A marca foi removida com sucesso"],200);
    }
}
