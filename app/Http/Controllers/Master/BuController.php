<?php
namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bu;
class BuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $model=Bu::where('active',1);
        // if($request->has('q')){
        //     $model=$model->where('nama_adv','like','%'.$request->input('q').'%');
        // }
        $model=$model->get();
        
        return response($model,200);
    }
    public function show(Request $request,$id)
    {
        $model=Bu::find($id);
        return $model;
    }
    public function destroy($id)
    {
        $model=Bu::find($id);
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
}