<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $model=Category::select(
            [
                'id',
                'category_name'
            ]
        );

        if($request->has('q')){
            $model=$model->where('category_name','like','%'.$request->input('q').'%');
        }

        $model=$model->paginate(25);
        
        return response()->json($model);
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
                'pesan'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model= new Category;
            $model->category_name=$request->input('name');
            $model->save();

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
        $model=Category::find($id);

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
                'pesan'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model= Category::find($id);
            $model->category_name=$request->input('name');
            $model->save();

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
        $model=Category::find($id);

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

    public function list_category(Request $request)
    {
        $model=Category::select(
            [
                'id',
                'category_name'
            ]
        );

        if($request->has('q')){
            $model=$model->where('category_name','like','%'.$request->input('q').'%');
        }

        $model=$model->get();

        return $model;
    }
}
