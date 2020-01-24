<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transaksi;

class TransaksiController extends Controller
{
    public function index(){
        $user=\Auth::user();

        $model=Transaksi::with(
            [
                'cabang'
            ]
        )->where('user_id',$user->id)
            ->paginate(25);

        return $model;
    }

    public function store(Request $request)
    {
        $rules=[
            'cabang'=>'required',
            'tanggal'=>'required',
            'jumlah'=>'required',
            'catatan'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validasi errors',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $model= new Transaksi;
            $model->cabang_id=$request->input('cabang');
            $model->tanggal=date('Y-m-d',strtotime($request->input('tanggal')));
            $model->jumlah = $request->input('jumlah');
            $model->catatan=$request->input('catatan');
            $model->user_id=\Auth::user()->id;
            $model->save();

            $data=array(
                'success'=>true,
                'message'=>'Data has been save',
                'errors'=>array()
            );
        }

        return $data;
    }
}