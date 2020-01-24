<?php
namespace App\Http\Controllers\Saleskit;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User;
use App\Models\Saleskit\Programperiode; 
use App\Models\Saleskit\Sector; 
use App\Models\Saleskit\Contentnew; 
use Intervention\Image\ImageManagerStatic as Image;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class YoutubeController extends Controller  
{

    public function get_menu_fta(Request $request){
        try {
            $fta = \DB::table('benefits as a')
            ->selectRaw('a.id_benefit, a.nama_benefit, a.benefit_cover, a.img')
            ->leftJoin('benefit_typespot as b','b.id_benefit','a.id_benefit')
            ->where('b.fta',1)
            ->groupBy('a.id_benefit')->get();

            return response($fta,200);
        } catch(\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function get_menu_paytv(Request $request){
        try {
         $paytv = \DB::table('benefits as a')
         ->selectRaw('a.id_benefit, a.nama_benefit, a.benefit_cover, a.img')
         ->leftJoin('benefit_typespot as b','b.id_benefit','a.id_benefit')
         ->where('b.paytv',1)
         ->groupBy('a.id_benefit')->get();

         return response($paytv,200);
        } catch (\Exception $e){
         return response(array('data'=>'Error at Backend'));
        }
    }


    public function get_typespotbaru(Request $request,$id_benefit,$tvtype){
        try {
            $g = user($request->bearerToken());

            if($tvtype != 0){
                $usedfor = ' and c.id_benefit = '.$tvtype.'';
            } else {
                $usedfor = '';
            }

            if($id_benefit === 'FTA'){
                $forwhat = ' and c.fta = 1 ';
            } else if ($id_benefit === 'PAY'){
                $forwhat = ' and c.paytv = 1 ';
            }

            $content = \DB::select('select * from tbl_content a
                left join tbl_filetype b on b.id_filetype = a.id_filetype
                left join benefit_typespot c on c.id_typespot = b.id_typespot
                where a.deleted_at is null
                '.$usedfor.' 
                and a.id_master_filetype = 3
                and a.mediakit = 1
                and b.type = "typespot"
                '.$forwhat.'
                group by c.id_typespot');
            return response($content,200);

        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        } 
    }

    public function get_video(Request $request, $id_typespot, $id_benefit){
        try {
            $g = user($request->bearerToken());

            $content = \DB::table('tbl_content as a')->selectRaw('a.id_content,b.id_typespot, a.content_file_download, a.content_title, a.update_user, a.updated_at, c.id_benefit,a.id_filetype,b.ext_file,d.nama_benefit,c.nama as nama_typespot,b.filetype_name, c.deskripsi as typespot_desc, d.description as benefit_desc, e.BU_SHORT_NAME')
            ->leftJoin('tbl_filetype as b','b.id_filetype','a.id_filetype')
            ->leftJoin('benefit_typespot as c','c.id_typespot','b.id_typespot')
            ->leftJoin('benefits as d','d.id_benefit','c.id_benefit')
            ->leftJoin('tbl_bu as e','e.id_bu','a.id_bu')
            ->where('b.id_master_filetype',3)
            ->whereNull('a.deleted_at')
            ->where('a.mediakit',1);

            if($g->ID_BU = 11){
                $content = $content->whereIn('a.id_bu',[1,2,3,8,11]);
                
            } else if ($g->ID_BU = 10) {
                $content = $content->whereIn('a.id_bu',[1,2,3,8,10]);
            } else {
                $content = $content->where('a.id_bu',$g->ID_BU);
            }

            if($id_typespot != 0 ){
                $content=$content->where('b.id_typespot',$id_typespot);

            } else if ($id_typespot == 0) {
                $content=$content->where('b.id_typespot','LIKE','%%');
            }

            if($id_benefit != 0){
                $content = $content->where('c.id_benefit',$id_benefit);
            } else if ($id_benefit == 0){
                $content = $content->where('c.id_benefit','like','%%');
            }


            $content=$content->orderBy('a.updated_at','desc')->paginate(30);

            return response($content,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }   


    public function get_video_benefit2_tanpabu(Request $request, $id_typespot, $id_benefit,$idBu,$tvtype){
        try {
            $g = user($request->bearerToken());

            $content = \DB::table('tbl_content as a')->selectRaw('a.id_bu, a.id_content,b.id_typespot, a.content_file_download, a.content_title, a.update_user, a.updated_at, c.id_benefit,a.id_filetype,b.ext_file,d.nama_benefit,c.nama as nama_typespot,b.filetype_name, c.deskripsi as typespot_desc, d.description as benefit_desc, e.BU_SHORT_NAME')
            ->leftJoin('tbl_filetype as b','b.id_filetype','a.id_filetype')
            ->leftJoin('benefit_typespot as c','c.id_typespot','b.id_typespot')
            ->leftJoin('benefits as d','d.id_benefit','b.id_typespot')
            ->leftJoin('tbl_bu as e','e.id_bu','a.id_bu')
            ->leftJoin('db_m_channel as f','f.id_channel','a.id_channel')
            ->where('b.id_master_filetype',3)
            ->whereNull('a.deleted_at')
            ->where('a.mediakit',1)
            ->where('b.type','benefit');

            if($id_benefit != 0){
                $content = $content->where('a.id_benefit',$id_benefit);
            } else if ($id_benefit == 0){
                $content = $content->where('a.id_benefit','like','%%');
            }

            if($idBu != 11){
                $content = $content->where('a.id_bu',$idBu);
            }

            if($tvtype === 'FTA'){
                $content = $content->where('c.fta',1)->where('f.type','TV');
            } else if($tvtype === 'PAY'){
                $content = $content->where('c.paytv',1)->where('f.type','<>','TV');
            }


            $content=$content->orderBy('a.updated_at','desc')->paginate(30);

            return response($content,200);
        } catch (\Exception $e){
           return response(array('data'=>'Error at Backend'));
       }
        
    } 


    public function get_video_typespot_tanpabu(Request $request, $id_typespot, $id_benefit, $idBu,$tvtype){
        try {
            $g = user($request->bearerToken());
            $content = \DB::table('tbl_content as a')->selectRaw('a.id_bu, a.id_content,b.id_typespot, a.content_file_download, a.content_title, a.update_user, a.updated_at, c.id_benefit,a.id_filetype,b.ext_file,d.nama_benefit,c.nama as nama_typespot,b.filetype_name, c.deskripsi as typespot_desc, d.description as benefit_desc, e.BU_SHORT_NAME')
            ->leftJoin('tbl_filetype as b','b.id_filetype','a.id_filetype')
            ->leftJoin('benefit_typespot as c','c.id_typespot','b.id_typespot')
            ->leftJoin('benefits as d','d.id_benefit','c.id_benefit')
            ->leftJoin('tbl_bu as e','e.id_bu','a.id_bu')
            ->leftJoin('db_m_channel as f','f.id_channel','a.id_channel')
            ->where('b.id_master_filetype',3)
            ->whereNull('a.deleted_at')
            ->where('a.mediakit',1)
            ->where('b.type','typespot');

            if($id_typespot != 0 ){
                $content=$content->where('b.id_typespot',$id_typespot);

            } else if ($id_typespot == 0) {
                $content=$content->where('b.id_typespot','LIKE','%%');
            }

            if($id_benefit != 0){
                $content = $content->where('c.id_benefit',$id_benefit);
            } else if ($id_benefit == 0){
                $content = $content->where('c.id_benefit','like','%%');
            }

            if($idBu != 11){
                $content = $content->where('a.id_bu',$idBu);
            }

            if($tvtype === 'FTA'){
                $content = $content->where('c.fta',1)->where('f.type','TV');
            } else if($tvtype === 'PAY'){
                $content = $content->where('c.paytv',1)->where('f.type','<>','TV');
            }


            $content=$content->orderBy('a.updated_at','desc')->paginate(30);

            return response($content,200);
        } catch (\Exception $e){
           return response(array('data'=>'Error at Backend'));
       }
    }   



    public function filter_youtube(Request $request){
        $sector = $request->sector;
        $genre = $request->genre;
        $bulan = $request->bulan;

        if(empty($sector)){
            $sector='SELECT a.id_sector FROM db_m_sector as a';
        } else {
            $sector=join(",",$sector);
        }


        if(empty($genre)){
            $genre='SELECT a.id_genre FROM tbl_program_genre as a WHERE a.active=1';
        } else {
            $genre=join(",",$genre);
        }

        // return $genre;

        if(empty($bulan)){
            $bulan='SELECT monthname(a.updated_at) as bulan from tbl_content as a WHERE a.id_master_filetype=3';
        } else {
            $bulan="'".ucfirst(join(",",$bulan))."'";
        }

        // return $bulan;

        $content=\DB::select('select a.id_content,b.id_typespot, a.content_file_download, a.content_title, a.update_user, a.updated_at, c.id_benefit, a.updated_at from tbl_content as a left join tbl_filetype as b on b.id_filetype=a.id_filetype left join benefit_typespot as c on c.id_typespot=b.id_typespot where a.id_master_filetype=3 and a.id_sector IN ('.$sector.') and a.id_genre IN ('.$genre.') and monthname(a.updated_at) IN ('.$bulan.') and a.content_title is not null');

        return response($content, 200);
    }

    public function filter_video_benefit2_tanpabu(Request $request){
        try {
            $g = user($request->bearerToken());
            $id_typespot = $request->get('id_typespot');
            $id_benefit = $request->get('id_benefit');
            $idBu = $request->get('id_bu');
            $carivideobenefit = $request->get('carivideobenefit');
            $content = \DB::table('tbl_content as a')->selectRaw('a.id_bu, a.id_content,b.id_typespot, a.content_file_download, a.content_title, a.update_user, a.updated_at, c.id_benefit,a.id_filetype,b.ext_file,d.nama_benefit,c.nama as nama_typespot,b.filetype_name, c.deskripsi as typespot_desc, d.description as benefit_desc, e.BU_SHORT_NAME')
            ->leftJoin('tbl_filetype as b','b.id_filetype','a.id_filetype')
            ->leftJoin('benefit_typespot as c','c.id_typespot','b.id_typespot')
            ->leftJoin('benefits as d','d.id_benefit','b.id_typespot')
            ->leftJoin('tbl_bu as e','e.id_bu','a.id_bu')
            ->where('b.id_master_filetype',3)
            ->whereNull('a.deleted_at')
            ->where('a.mediakit',1)
            ->where('b.type','benefit');


            if($id_benefit != 0){
                $content = $content->where('a.id_benefit',$id_benefit);
            } else if ($id_benefit == 0){
                $content = $content->where('a.id_benefit','like','%%');
            }

            if($idBu != 11){
                $content = $content->where('a.id_bu',$idBu);
            }

            if($carivideobenefit){
                $content = $content->where('a.content_title','like','%'.$carivideobenefit.'%');
            }


            $content=$content->orderBy('a.updated_at','desc')->paginate(30);

            return response($content,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        } 
    }

    public function filter_video_typespot_tanpabu(Request $request){
        try {
            $g = user($request->bearerToken());
            $id_typespot = $request->get('id_typespot');
            $id_benefit = $request->get('id_benefit');
            $idBu = $request->get('id_bu');
            $carivideobenefit = $request->get('carivideobenefit');


            $content = \DB::table('tbl_content as a')->selectRaw('a.id_bu, a.id_content,b.id_typespot, a.content_file_download, a.content_title, a.update_user, a.updated_at, c.id_benefit,a.id_filetype,b.ext_file,d.nama_benefit,c.nama as nama_typespot,b.filetype_name, c.deskripsi as typespot_desc, d.description as benefit_desc, e.BU_SHORT_NAME')
            ->leftJoin('tbl_filetype as b','b.id_filetype','a.id_filetype')
            ->leftJoin('benefit_typespot as c','c.id_typespot','b.id_typespot')
            ->leftJoin('benefits as d','d.id_benefit','c.id_benefit')
            ->leftJoin('tbl_bu as e','e.id_bu','a.id_bu')
            ->where('b.id_master_filetype',3)
            ->whereNull('a.deleted_at')
            ->where('a.mediakit',1)
            ->where('b.type','typespot');

            if($id_typespot != 0 ){
                $content=$content->where('b.id_typespot',$id_typespot);

            } else if ($id_typespot == 0) {
                $content=$content->where('b.id_typespot','LIKE','%%');
            }

            if($id_benefit != 0){
                $content = $content->where('c.id_benefit',$id_benefit);
            } else if ($id_benefit == 0){
                $content = $content->where('c.id_benefit','like','%%');
            }

            if($idBu != 11){
                $content = $content->where('a.id_bu',$idBu);
            }

            if($carivideobenefit){
                $content = $content->where('a.content_title','like','%'.$carivideobenefit.'%');
            }


            $content=$content->orderBy('a.updated_at','desc')->paginate(30);

            return response($content,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }   

    public function filter_typespotbaru_tanpabu(Request $request){
        try {
            $id_benefit = $request->get('id_benefit');
            $idBu = $request->get('id_bu');
            $g = user($request->bearerToken());
            $carivideobenefit = $request->get('carivideobenefit');
            $content = \DB::table('tbl_content as a')
            ->selectRaw('c.id_typespot, c.id_benefit, c.nama, c.img, a.id_bu')
            ->leftJoin('tbl_filetype as b','b.id_filetype','a.id_filetype')
            ->leftJoin('benefit_typespot as c','c.id_typespot','b.id_typespot')
            ->where('b.id_master_filetype',3)
            ->whereNull('a.deleted_at')
            ->where('a.mediakit',1);
            if($idBu != 11){
                $content = $content->where('id_bu',$idBu);
            }
            if($carivideobenefit){
                $content = $content->where('a.content_title','like','%'.$carivideobenefit.'%');
            }
            if($id_benefit !=0){
                $content=$content->where('c.id_benefit',$id_benefit);
            } 
            $content=$content->groupBy('c.id_typespot')
            ->orderBy('c.id_typespot','ASC')->get();
            
            return response($content,200); 
        } catch( \Exception $e){
            return response(array('data'=>'Error at Backend'));
        }  
    }   
}