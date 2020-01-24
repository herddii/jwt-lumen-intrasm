<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Cabang;

class CabangController extends Controller
{
    public function index(Request $request)
    {
        $model=Cabang::paginate(25);

        return $model;
    }

    public function store(Request $request)
    {
        $rules=[
            'user'=>'required',
            'nama'=>'required',
            'bisnis'=>'required',
            'deskripsi'=>'required',
            'alamat'=>'required',
            'hp'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model = new Cabang;
            $model->bisnis_id=$request->input('bisnis');
            $model->nama=$request->input('nama');
            $model->deskripsi=$request->input('deskripsi');
            $model->alamat=$request->input('alamat');
            $model->no_hp=$request->input('hp');
            $model->lat=$request->input('lat');
            $model->lng=$request->input('lng');
            $model->save();

            if($request->has('user')){
                $model->user()->sync([$model->id => ['user_id'=>$request->input('user')]]);
            }

            $data=array(
                'success'=>true,
                'message'=>'Data has been save',
                'errors'=>array()
            );
        }

        return $data;
    }

    public function show($id){
        $model=Cabang::with('user')->find($id);

        return $model;
    }

    public function update(Request $request, $id){
        $rules=[
            'user'=>'required',
            'nama'=>'required',
            'bisnis'=>'required',
            'deskripsi'=>'required',
            'alamat'=>'required',
            'hp'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model = Cabang::find($id);
            $model->bisnis_id=$request->input('bisnis');
            $model->nama=$request->input('nama');
            $model->deskripsi=$request->input('deskripsi');
            $model->alamat=$request->input('alamat');
            $model->hp=$request->input('hp');
            $model->lat=$request->input('lat');
            $model->lng=$request->input('lng');
            $model->save();

            if($request->has('user')){
                $model->user()->sync([$model->id => ['user_id'=>$val]]);
            }

            $data=array(
                'success'=>true,
                'message'=>'Data has been save',
                'errors'=>array()
            );
        }

        return $data;
    }

    public function destroy($id){
        $model = Cabang::find($id);

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
                'message'=>'Data failed delete',
                'errors'=>array()
            );
        }

        return $data;
    }

    public function list_cabang_by_user(Request $request){
        $user=\Auth::user(); 

        $model=Cabang::with(
            [
                'user'
            ]
        );

        return $model->get();
    }
}