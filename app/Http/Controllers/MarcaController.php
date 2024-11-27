<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;
use Illuminate\Support\Facades\Storage;

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
        return response()->json($this->marca->with('modelos')->get(),200);
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

        //stateless
        $request->validate($this->marca->rules(), $this->marca->feedback());


        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
        ]);
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
        $marca = $this->marca->with('modelos')->find($id);

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

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();

            foreach($marca->rules() as $input => $regra){
                 // coletar apenas as regras   aplicadas aos parametros parciais da requisicao PATCH
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $marca->feedback());
        }else{
            $request->validate($marca->rules(), $marca->feedback());
        }

        // Remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
        }

        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens', 'public');

        $marca->update([
            'nome' => $request->nome ?? $marca->nome,
            'imagem' => $imagem_urn ?? $marca->image
        ]);

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

        // Remove a imagem no storage do disk, pasta public
        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json(['msg' => "A marca foi removida com sucesso"],200);
    }
}
