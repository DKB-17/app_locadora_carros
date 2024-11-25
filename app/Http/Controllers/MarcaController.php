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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$marcas = Marca::all();
        $marcas = $this->marca->all();
        return $marcas;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$marca = Marca::create($request->all());
        $marca = $this->marca->create($request->all());
        return $marca;
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return string[]
     */
    public function show($id)
    {
        $marca = $this->marca->find($id);
        if($marca === null){
            return ['erro' => 'Recurso pesquisado não existe'];
        }
        return $marca;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return string[]
     */
    public function update(Request $request, $id)
    {
        //$marca->update($request->all());
        $marca = $this->marca->find($id);
        if($marca === null){
            return ['erro' => 'Impossivel realiza a atualizacao. O recurso solicitado não existe'];
        }
        $marca->update($request->all());
        return $marca;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return string[]
     */
    public function destroy($id)
    {
        //$marca->delete();
        $marca = $this->marca->find($id);
        if($marca === null){
            return ['erro' => 'Impossivel realiza a exclusao. O recurso solicitado não existe'];
        }
        $marca->delete();
        return ['msg' => "A marca foi removida com sucesso"];
    }
}
