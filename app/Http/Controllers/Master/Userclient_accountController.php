<?php
namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Intrasm\Userclient_account;
// use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache; 

class Userclient_accountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $model=Userclient_account::whereNull('deleted_at')
        ->where('active', 1);
        if($request->has('q')){
            $model=$model->where('email','like','%'.$request->input('q').'%');
        }
        $model=$model->paginate(25);
        // Cache::store('redis')->put('storing', $model);
        // if (Cache::has('storing')) {
        // return Cache::get('storing');
        // }
        // return Redis::remember($model);
        return response()->json($model);
    }
    public function show(Request $request,$id)
    {
        $model=Userclient_account::find($id);
        return $model;
    }
    public function destroy($id)
    {
        $model=Userclient_account::find($id);
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