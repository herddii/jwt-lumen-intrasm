<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $model=Role::paginate(25);

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
            $model=new Role;
            $model->name=$request->input('name');
            $model->guard_name=$request->input('guard');

            $model->save();

            $data=array(
                'success'=>true,
                'message'=>'Data has been save',
                'errors'=>array()
            );
        }

        return $data;
    }

    public function list_role(Request $request)
    {
        return Role::all();
    }

    public function show($id){
        $model=Role::find($id);
        
        return $model;
    }

    public function destroy($id){

    }
}