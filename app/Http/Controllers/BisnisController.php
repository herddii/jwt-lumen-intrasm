<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Bisnis;

class BisnisController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $model=Bisnis::select(
            [
                'id',
                'bisnis_name',
                'deskripsi',
                'omset',
                'modal',
                'logo',
                'featured_image',
                'lat',
                'lng'
            ]
        );

        if($request->has('q')){
            $model=$model->where('bisnis_name','like','%'.$request->input('q').'%');
        }

        $model=$model->paginate(25);
        
        return response()->json($model);
    }

    public function store(Request $request)
    {
        $rules=[
            'name'=>'required',
            'deskripsi'=>'required',
            'omset'=>'required',
            'modal'=>'required'
            // 'logo'=>'required',
            // 'featured_image'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model= new Bisnis;
            $model->bisnis_name=$request->input('name');
            $model->deskripsi = $request->input('deskripsi');
            $model->omset = $request->input('omset');
            $model->modal = $request->input('modal');
            
            $model->save();

            if($request->has('category')){
                $category=$request->input('category');

                foreach($category as $key=>$val){
                    $model->category()->attach([$model->id => ['category_id'=>$val]]);
                }
            }

            $data=array(
                'success'=>true,
                'pesan'=>'Data has been save',
                'errors'=>array()
            );
        }

        return $data;
    }

    public function show(Request $request,$id)
    {
        $model=Bisnis::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $rules=[
            'name'=>'required',
            'deskripsi'=>'required',
            'omset'=>'required',
            'modal'=>'required'
            // 'logo'=>'required',
            // 'featured_image'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model= Bisnis::find($id);
            $model->bisnis_name=$request->input('name');
            $model->deskripsi = $request->input('deskripsi');
            $model->omset = $request->input('omset');
            $model->modal = $request->input('modal');
            
            $model->save();

            if($request->has('category')){
                $category=$request->input('category');

                $link=array();
                foreach($category as $key=>$val){
                    $link[]=$val;
                }

                $model->category()->sync($link);
            }

            $data=array(
                'success'=>true,
                'pesan'=>'Data has been save',
                'errors'=>array()
            );
        }

        return $data;
    }

    public function destroy($id)
    {
        $model=Bisnis::find($id);

        $del=$model->delete();

        if($del){
            $data=array(
                'success'=>true,
                'message'=>'Data deleted'
            );
        }else{
            $data=array(
                'success'=>false,
                'message'=>'Data failed to deleted'
            );
        }

        return response()->json($data);
    }

    public function list_bisnis(Request $request)
    {
        $model=Bisnis::select(
            [
                'id',
                'bisnis_name',
                'deskripsi',
                'omset',
                'modal',
                'logo',
                'featured_image',
                'lat',
                'lng'
            ]
        );

        if($request->has('q')){
            $model=$model->where('bisnis_name','like','%'.$request->input('q').'%');
        }

        return $model->get();
    }
}
