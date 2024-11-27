<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo){
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json($this->modelo->all(),200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);
        return response()->json($modelo,201);

    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {

        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'],404); // json
        }
        return response()->json($modelo,200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Modelo $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {

        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'Impossivel realiza a atualizacao. O recurso solicitado não existe'],404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();

            foreach($modelo->rules() as $input => $regra){
                // coletar apenas as regras   aplicadas aos parametros parciais da requisicao PATCH
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas);
        }else{
            $request->validate($modelo->rules());
        }

        // Remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
        }

        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens/modelos', 'public');

        $modelo->update([
            'marca_id' => $request->marca_id ?? $modelo->marca_id,
            'nome' => $request->nome ?? $modelo->nome,
            'imagem' => $imagem_urn ?? $modelo->image,
            'numero_portas' => $request->numero_portas ?? $modelo->numero_portas,
            'lugares' => $request->lugares ?? $modelo->lugares,
            'air_bag' => $request->air_bag ?? $modelo->air_bag,
            'abs' => $request->abs ?? $modelo->abs
        ]);

        return response()->json($modelo,200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {

        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Impossivel realiza a exclusao. O recurso solicitado não existe'],404);
        }

        // Remove a imagem no storage do disk, pasta public
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json(['msg' => "A marca foi removida com sucesso"],200);

    }
}
