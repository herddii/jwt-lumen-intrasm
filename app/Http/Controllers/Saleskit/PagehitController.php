<?php
namespace App\Http\Controllers\Saleskit;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 


class PagehitController extends Controller 
{
    public function pagehitInsert(Request $request){ 
        try {
            $userid = user($request->bearerToken());
            $form = $request->content;
            \DB::table('access_logs')->insert([
                'user_id'=>$userid->USER_ID,
                'type_apps'=>'APP',
                'response_time' => microtime(true),
                'day' => date('Y-m-d'),
                'hour' => date('H:i:s'),
                'modul'=>$form['modul'],
                'submodul'=>$form['submodul'],
                'title'=>$form['title'],
                'id_title'=>$form['id_title'],
                'id_master_filetype'=>$form['id_master_filetype'],
                'file'=>$form['file'],
                'id_file'=>$form['id_file'],
                'action'=>$form['action'],
                'created_at'=>Date('Y-m-d H:i:s'),
                'updated_at'=>Date('Y-m-d H:i:s')
            ]);

            return response(['isi' => 'success'],200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }
}