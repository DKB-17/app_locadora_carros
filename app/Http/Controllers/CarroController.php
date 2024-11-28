<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Http\Requests\StoreCarroRequest;
use App\Http\Requests\UpdateCarroRequest;
use App\Repositories\CarroRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CarroController extends Controller
{
    public function __construct(Carro $carro){
        $this->carro = $carro;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {

        $carroRepository = new CarroRepository($this->carro);

        if ($request->has('atributos_modelo')) {
            $atributos_modelo = 'modelo:id,' . $request->atributos_modelo;
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        } else {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if ($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $carroRepository->selectAtributos($request->atributos);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCarroRequest  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        $request->validate($this->carro->rules());

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);
        return response()->json($carro,201);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'],404); // json
        }
        return response()->json($carro,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Carro $carro
     * @return void
     */
    public function edit(Carro $carro)
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
        $carro = $this->carro->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Impossivel realiza a atualizacao. O recurso solicitado não existe'],404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();

            foreach($carro->rules() as $input => $regra){
                // coletar apenas as regras   aplicadas aos parametros parciais da requisicao PATCH
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas);
        }else{
            $request->validate($carro->rules());
        }

        // preencher o objeto $marca com os dados do request
        $carro->fill($request->all());
        $carro->save();

        return response()->json($carro,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Carro $carro
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);
        if($carro === null){
            return response()->json(['erro' => 'Impossivel realiza a exclusao. O recurso solicitado não existe'],404);
        }

        $carro->delete();
        return response()->json(['msg' => "O carro foi removida com sucesso"],200);
    }
}
