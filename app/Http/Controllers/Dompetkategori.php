<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Dompetkategori;

class DompetkategoriController extends Controller
{
    public function index(Request $request)
    {
        $model=Dompetkategori::all();

        return $model;
    }

    public function store(Request $request)
    {
        $rules=[
            'name'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validation error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model= new Dompetkategori;
            $model->dompet_kategori_name=$request->input('name');

            $model->save();

            $data=array(
                'success'=>true,
                'message'=>"Data has been saved",
                'errors'=>array()
            );
        }

        return $data;
    }

    public function show($id)
    {
        $model= Dompetkategori::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $rules=[
            'name'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validation error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model= Dompetkategori::find($id);
            $model->dompet_kategori_name=$request->input('name');

            $model->save();

            $data=array(
                'success'=>true,
                'message'=>"Data has been updated",
                'errors'=>array()
            );
        }

        return $data;
    }

    public function destroy($id)
    {
        $model= Dompetkategori::find($id);

        $del=$model->delete();

        if($del){
            $data=array(
                'success'=>true,
                'message'=>'Data has been deleted',
                'errors'=>array()
            );
        }else{
            $data=array(
                'success'=>false,
                'message'=>'Data failed to deleted',
                'errors'=>array()
            );
        }

        return $data;
    }
}