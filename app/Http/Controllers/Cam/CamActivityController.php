<?php

namespace App\Http\Controllers\Cam;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;

class CamActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function list_activity_calendar(Request $request)
    {
        $userget = user($request->bearerToken());
        $posisi= $userget->POSITION;
        $userid = $userget->USER_ID;
        
        $var=\App\Models\Cam\Cam::with(
            [
                'Cam_activity',
                'Cam_partner',
                'Cam_partner.Name_partner'
            ]
        )->selectRaw('id_cam, date_format(start_date, "%Y-%m-%d")as start_date, date_format(end_date, "%Y-%m-%d")as end_date, brand_variant')
        ->whereNull('deleted_at');
        if($posisi=="AM"){
            $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                $q->where('id_am', $userid)
                ->orWhere('user_id', $userid);
            });
        }elseif($posisi=="SGM"){
            $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                $q->where('id_sgm', $userid)
                ->orWhere('user_id', $userid);
            });
        }elseif($posisi=="SM"){
            $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                $q->where('id_sm', $userid)
                ->orWhere('user_id', $userid);
            });
        }else{
            $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                $q->where('id_gm', $userid)
                ->orWhere('user_id', $userid);
            });
        }
        $var=$var->get();

        return $var;
    }

    public function list_tasklist(Request $request)
    {
        try {
            // return $request->all();
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $tanggal = $request->get('tanggal');

            $search=$request->input('cari_activity');

            $var=\App\Models\Cam\Cam::with(
                [
                    'Cam_brand',
                    'Cam_brand.Brand',
                    'Agencypintu',
                    'Advertiser',
                    'Cam_partner',
                    'user_am',
                    'user_sgm',
                    'user_sm',
                    'user_gm',
                    'Cam_partner.Name_partner',
                    'Cam_activity',
                    'Cam_activity.Cam_cost',
                    'Cam_activity.Cam_typeactivity',
                    'Cam_activity.Nama_task',
                    'Cam_client.Name_userclient_account.Edit_namaclient'
                ]
            )
            ->select('cam.*')
            ->whereHas('Cam_activity', function($q)use($search){
                $q->where('subject', 'like', '%'.$search.'%');
            })
            ->whereNull('deleted_at');

            if($posisi=="AM"){
                $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                    $q->where('id_am', $userid)
                    ->orWhere('user_id', $userid);
                });
            }elseif($posisi=="SGM"){
                $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                    $q->where('id_sgm', $userid)
                    ->orWhere('user_id', $userid);
                });
            }elseif($posisi=="SM"){
                $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                    $q->where('id_sm', $userid)
                    ->orWhere('user_id', $userid);
                });
            }else{
                $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                    $q->where('id_gm', $userid)
                    ->orWhere('user_id', $userid);
                });
            }
        // return $request->tanggal;
            if(!$request->exists($tanggal)){
                $var=$var->whereHas('Cam_activity', function($q) use ($tanggal){
                    $q->where('start_date','like','%'.$tanggal.'%');
                });
            }
            $var=$var->orderBy('start_date', 'ASC')->paginate(5);
            return response($var);
        } catch(\Exception $e){
            return response($e->getMessage());
        }            
    }

    public function list_am_for(Request $request)
    {
        try {
               $userget = user($request->bearerToken());
               $posisi= $userget->POSITION;
               $userid = $userget->USER_ID;

               $var = \DB::connection('cam')->table('tbl_am as a')->select('*')->leftJoin('tbl_user as b','b.USER_ID','a.ID_AM')->where('a.id_am','like','%'.$userid.'%')->get();
               $kerabat = \DB::connection('cam')->table('tbl_am as a')->select('*')->leftJoin('tbl_user as b','b.USER_ID','a.ID_AM')->leftJoin('tbl_bu as c','c.ID_BU','b.ID_BU');

                if($posisi == 'SGM'){
                $kerabat = $kerabat->where('ID_SGM',$userid);
                } else if($posisi == 'SM'){
                    $kerabat = $kerabat->where('ID_SM',$userid);
                } else if($posisi == 'AM'){
                    $kerabat = $kerabat->where('ID_SGM',$userid);
                } else if($posisi == 'GM'){
                    $kerabat = $kerabat->where('ID_SM',$userid);
                }

            $kerabat = $kerabat->get();


            $setVar = \DB::connection('cam')->table('cam_cost as a')->select('*')->leftJoin('tbl_user as b','b.USER_ID','a.cost_by')->leftJoin('tbl_bu as c','c.ID_BU','b.ID_BU')->whereIn('a.cost_by',function($query) use ($posisi, $var, $userid){
                $query->select('ab.id_am')->from('tbl_am as ab');    
                if($posisi == 'SGM'){
                    $query->where('ab.ID_SGM',$userid);
                } else if($posisi == 'SM'){
                    $query->where('ab.ID_SM',$userid);
                } else if($posisi == 'AM'){
                    $query->where('ab.ID_SGM',$userid);
                } else if($posisi == 'GM'){
                    $query->where('ab.ID_SM',$userid);
                }   
            })->get();

            $gabungan = array(
                'me' => $var, 
                'kerabat' => $kerabat,
                'historykerabat' => $setVar);

            return response($gabungan, 200);
        } catch(\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }  
    }

    public function detail_list_tasklist(Request $request, $id_activity, $id_cam)
    {
        try{
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $var=\App\Models\Cam\Cam::with(
                [
                    'Cam_activity' => function($ff) use($id_activity){
                        $ff->where('id_activity',$id_activity);
                    },
                    'Cam_activity.Cam_cost',
                    'Cam_activity.Cam_cost.Nama_cost',
                    'Cam_activity.Cam_file',
                    'Cam_activity.Cam_file.File_type_saleskit',
                    'Cam_activity.Cam_file.File_type_sam',
                    'Cam_partner',
                    'Cam_partner.Name_partner',
                    'Cam_client',
                    'Cam_client.Name_userclient_account',              
                    'Cam_client.Name_userclient_account.Edit_namaclient',              
                    'Agencypintu',
                    'Advertiser',
                    'Cam_brand',
                    'Cam_brand.Brand',
                    'User',
                    'Commen',
                    'Commen.Name_komen'
                ]
            )
            ->whereNull('deleted_at');
            if($posisi=="AM"){
                $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                    $q->where('id_am', $userid)
                    ->orWhere('user_id', $userid);
                });
            }elseif($posisi=="SGM"){
                $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                    $q->where('id_sgm', 'adelia.fortiena@mncgroup.com')
                    ->orWhere('user_id', 'adelia.fortiena@mncgroup.com');
                });
            }elseif($posisi=="SM"){
                $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                    $q->where('id_sm', $userid)
                    ->orWhere('user_id', $userid);
                });
            }else{
                $var=$var->whereHas('Cam_partner', function($q) use ($userid){
                    $q->where('id_gm', $userid)
                    ->orWhere('user_id', $userid);
                });
            }

            $var=$var->where('id_cam', $id_cam)
            ->select(
                [
                    'id_cam',
                    'id_am',
                    'id_sgm',
                    'id_sm',
                    'id_cam_typeactivity',
                    'id_agcy',
                    'id_brand',
                    'id_adv',
                    'type',
                    'start_date',
                    'end_date',
                    'insert_user',
                ]
            )->get();

            return response($var, 200);
        } catch(\Exception $e){
            return response(array('data'=>'Error at Backend')); 
        }  
    }

    // Buat Di Form Add Activity
    public function list_am(Request $request)
    {
        try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $idbu = $userget->ID_BU;
            $var=\DB::table('db_d_target_account as a')->selectRaw('distinct i.id_am_run as id, upper(j.USER_NAME)as text')
            ->leftJoin('db_m_product as c','c.id_produk','a.id_produk')
            ->leftJoin('db_m_advertiser as e','e.id_adv','c.id_adv')
            ->leftJoin('db_m_agencypintu as f','f.id_agcyptu','a.id_agcyptu_run')
            ->leftJoin('db_m_brand as h','h.id_brand','c.id_brand')
            ->leftJoin('db_d_target_account_am as i','i.id_targetaccount','a.id_targetaccount')
            ->leftJoin('tbl_user as j','j.USER_ID','i.id_am_run')
            ->whereNull('a.deleted_at')
            ->where('j.active',1)
            ->where('j.USER_ID', 'not like', '%vacant%')
            ->where('j.USER_ID', 'not like', '%none%')
            ->where('j.POSITION', 'AM')
            ->where('i.id_bu', $idbu)
            ->orderBy('j.USER_NAME')->get();

            return response($var,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }  
    }

    public function list_partner(Request $request)
    {
        try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $idbu = $userget->ID_BU;
            $var=\App\User::selectRaw('USER_ID as id, UPPER(USER_NAME)as text')
            ->where('id_bu', $idbu)
            ->where('active',1)
            ->where('USER_ID', 'not like', '%vacant%')
            ->where('USER_ID', 'not like', '%none%')
            ->whereIn('position',['SGM', 'AM'])
            ->orderBy('user_name');

            return $var->get();
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function plafond(Request $request)
    {
        try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $idbu = $userget->ID_BU;
            $var=\App\Models\Cam\Cam_plafont_entertaiment::selectRaw('*')->where('id_bu',$idbu)->where('position',$posisi)->get();

            return response($var,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function get_reimburse(Request $request){
        try{
            $tanggal = $request->get('tanggal');
            $month = date('n',strtotime($tanggal));
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $idbu = $userget->ID_BU;
            $email = $userget->USER_ID;
            $var = \DB::connection('cam')->table('cam_cost as a')->select('*')->leftJoin('cam_activity as b','b.id_activity','a.id_activity')->where('cost_by','like','%'.$email.'%')->where(\DB::raw('month(b.start_date)'),'like','%'.$month.'%')->paginate(4);

            return response($var,200);
        } catch (\Exception $e){
           return response(array('data'=>'Error at Backend'));
       }
    }

    public function get_report_daily(Request $request)
    {
        try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $idbu = $userget->ID_BU;
            $user = $userget->USER_ID;
            $tanggal = $request->get('tanggal');
            $month = date('n',strtotime($tanggal));
            $var = \DB::connection('cam')->table('cam_cost as a')->select('*')
            ->leftJoin('cam_activity as b','b.id_activity','a.id_activity')
            ->leftJoin('cam_client as c','c.id_cam','b.id_cam')
            ->leftJoin('tbl_userclient_account as d','d.id_client_account','c.id_client_account')
            ->leftJoin('tbl_userclient as e','e.id_client','d.id_client')
            ->where('cost_by','like','%'.$user.'%')->where(\DB::raw('month(b.start_date)'),'like','%'.$month.'%')
            ->groupBy('a.id_cam_cost')->get();

            return response($var,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }  
    }

    public function get_report_monthly(Request $request)
    {
        try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $idbu = $userget->ID_BU;
            $user = $userget->USER_ID;
            $var = \DB::connection('cam')->table('cam_cost as a')->selectRaw('a.title, 
                a.cost_description, 
                a.cost_by, 
                a.nota, 
                sum(a.cost) as total, 
                month(b.start_date) as bulan,
                round((sum(a.cost)/d.limit)*100,3) as percent')
            ->leftJoin('cam_activity as b','b.id_activity','a.id_activity')
            ->leftJoin('tbl_user as c','c.USER_ID','a.cost_by')
            ->leftJoin('cam_plafont_entertaiment as d',function($q){
                $q->on('d.id_bu','c.ID_BU');
                $q->on('d.position','c.POSITION');
            }) 
            ->where('cost_by','like','%'.$user.'%')
            ->whereNull('a.deleted_at')
            ->groupBy(\DB::raw('MONTH(b.start_date)'))->get();

            return response($var,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }   
    }

    public function list_type_activity(Request $request)
    {
        try{
            $var=\App\Models\Cam\Cam_typeactivity::selectRaw('id_cam_typeactivity as id, UPPER(name_activity) as text')
            ->whereNull('deleted_at');

            if($request->has('q')){
                $var=$var->where('name_activity', 'like', '%'.$request->input('q').'%');
            }

            return response($var->get(),200);
        } catch(\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }   
    }

    public function list_agencypintu(Request $request)
    {
        try{
           $userget = user($request->bearerToken());
           $idam = $request->get('id_am');
           $idbu = $userget->ID_BU;
           $var=\DB::connection('cam')->table('db_d_target_account as a')->selectRaw('distinct f.id_agcyptu as id, f.nama_agencypintu as text')
           ->leftJoin('db_m_product as c','c.id_produk','a.id_produk')
           ->leftJoin('db_m_advertiser as e','e.id_adv','c.id_adv')
           ->leftJoin('db_m_agencypintu as f','f.id_agcyptu','a.id_agcyptu_run')
           ->leftJoin('db_m_brand as h','h.id_brand','c.id_brand')
           ->leftJoin('db_d_target_account_am as i','i.id_targetaccount','a.id_targetaccount')
           ->whereNull('a.deleted_at')
           ->where('i.id_bu', $idbu)
           ->where('i.id_am_run',$idam)
           ->orderBy('f.nama_agencypintu')
           ->get();
           return response($var,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function list_advertiser(Request $request)
    {
        try {
            $userget = user($request->bearerToken());
            $idam = $request->get('id_am');
            $idbu = $userget->ID_BU;
            $var=\DB::connection('cam')->table('db_d_target_account as a')->selectRaw('distinct e.id_adv as id, e.nama_adv as text')
            ->leftJoin('db_m_product as c','c.id_produk','a.id_produk')
            ->leftJoin('db_m_advertiser as e','e.id_adv','c.id_adv')
            ->leftJoin('db_m_agencypintu as f','f.id_agcyptu','a.id_agcyptu_run')
            ->leftJoin('db_m_brand as h','h.id_brand','c.id_brand')
            ->leftJoin('db_d_target_account_am as i','i.id_targetaccount','a.id_targetaccount')
            ->whereNull('a.deleted_at')
            ->where('i.id_bu', $idbu)
            ->where('i.id_am_run',$request->get('id_am'))
            ->where('a.id_agcyptu',$request->get('id_agencypintu'))
            ->orderBy('e.nama_adv')->get();
            return response($var,200);
        } catch(\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function list_brand(Request $request)
    {   
        try {
            $userget = user($request->bearerToken());
            $idam = $request->get('id_am');
            $id_adv = $request->get('id_adv');
            $id_agencypintu = $request->get('id_agencypintu');
            $idbu = $userget->ID_BU;
            $var=\DB::connection('cam')->table('db_d_target_account as a')->selectRaw('distinct h.id_brand as id, h.nama_brand as text')
            ->leftJoin('db_m_product as c','c.id_produk','a.id_produk')
            ->leftJoin('db_m_advertiser as e','e.id_adv','c.id_adv')
            ->leftJoin('db_m_agencypintu as f','f.id_agcyptu','a.id_agcyptu_run')
            ->leftJoin('db_m_brand as h','h.id_brand','c.id_brand')
            ->leftJoin('db_d_target_account_am as i','i.id_targetaccount','a.id_targetaccount')
            ->whereNull('a.deleted_at')
            ->where('i.id_am_run',$idam)
            ->where('e.id_adv',$id_adv)
            ->where('a.id_agcyptu',$id_agencypintu)
            ->where('i.id_bu', $idbu)
            ->orderBy('h.nama_brand')->get();

            return response($var,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function list_client(Request $request)
    {
        try {
            $var=\DB::connection('cam')->table('tbl_userclient as a')->selectRaw('UPPER(CONCAT(a.firstname, " ", IF(a.lastname is null, "", a.lastname))) as text, b.id_client_account')
            ->leftJoin('tbl_userclient_account as b', 'b.id_client', 'a.id_client')
            ->where('b.active', 1)
            ->whereNull('b.deleted_at')
            ->whereNull('a.deleted_at')
            ->orderBy('firstname')->get();
            return response($var,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function list_nama_program(Request $request)
    {
        try {

            $userget = user($request->bearerToken());
            $idbu = $userget->ID_BU;

            $var=\DB::table('tbl_program as a')->select('a.id_program', 'b.id_program_periode', 'a.program_name')
            ->leftJoin('tbl_program_periode as b', 'b.id_program', 'a.id_program')
            ->whereNull('a.deleted_at')
            ->where('a.id_bu',$idbu)
            ->where('a.id_channel',$idbu)
            ->groupBy('a.id_program')
            ->orderBy('a.updated_at', 'desc')->get();

            return response($var, 200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function list_nama_salestools(Request $request)
    {
        try {
            
            $userget = user($request->bearerToken());
            $idbu = $userget->ID_BU;

            $salestools = $request->get('id_master_filetype');
            $var=\DB::table('tbl_salestools as a')->selectRaw('a.id_salestools, a.id_master_filetype, a.title')
            ->where('a.id_bu',$idbu)
            ->where('a.mediakit', 1)
            ->whereRaw('CURDATE() BETWEEN a.content_start_date AND a.content_end_date')
            ->where('a.id_master_filetype', $salestools)->get();

            return response($var, 200);

        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function list_nama_samconcept(Request $request)
    {  
        try {
            $userget = user($request->bearerToken());
            $idbu = $userget->ID_BU;

            $var=\DB::table('sam as a')->selectRaw('distinct a.id_sam, a.id_brand, upper(a.brand_variant)as brand_variant, a.budget, c.singkatan')
            ->leftJoin('sam_status as b', 'b.id_req_type', 'a.id_req_type')
            ->leftJoin('sam_request_type as c', 'c.id_req_type', 'a.id_req_type')
            ->where('a.id_bu', $idbu)
            ->whereNull('a.deleted_at')
            ->where('b.dept_status', "MKT")
            ->whereIn('b.id_req_type', [04, 10, 11, 12, 13])
            ->orderBy('a.created_at', 'DESC')
            ->get();

            return response($var, 200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function list_nama_sampaket(Request $request)
    {
        try {

            $userget = user($request->bearerToken());
            $idbu = $userget->ID_BU;

            $var=\DB::table('sam as a')->selectRaw('distinct a.id_sam, a.id_brand, upper(a.brand_variant)as brand_variant, a.budget, c.singkatan')
            ->leftJoin('sam_status as b', 'b.id_req_type', 'a.id_req_type')
            ->leftJoin('sam_request_type as c', 'c.id_req_type', 'a.id_req_type')
            ->where('a.id_bu', $idbu)
            ->whereNull('a.deleted_at')
            ->where('b.dept_status', "MKT")
            ->whereIn('b.id_req_type', [02, 03])
            ->orderBy('a.created_at', 'DESC')
            ->get();

            return response($var, 200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
        
    }

    public function tampilfiletasklistmodal(Request $request)
    {
        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;

        $form = $request->form;
        $id_activity=$form['id_activity'];
        $type_module=$form['type_module'];
        $select_module=$form['id'];

        if($type_module=="SALESKIT_SO"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_master_filetype, a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->where('a.id_master_filetype', 2)
            ->where('a.mediakit', 1)
            ->whereIn('a.id_program_periode', [$select_module])
            ->OrwhereIn('a.id_program_periode', [$select_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();
        }else if($type_module=="SALESKIT_R"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_master_filetype, a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->where('a.id_master_filetype', 1)
            ->where('a.mediakit', 1)
            ->whereIn('a.id_program_periode', [$select_module])
            ->OrwhereIn('a.id_program_periode', [$select_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();
        }else if($type_module=="SALESKIT_P"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_master_filetype, a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->leftJoin('tbl_program_periode as c', 'c.id_program_periode', 'a.id_program_periode')
            ->leftJoin('tbl_program as d', 'd.id_program', 'c.id_program')
            ->where('a.mediakit', 1)
            ->whereIn('a.id_program_periode', [$select_module])
            ->OrwhereIn('a.id_program_periode', [$select_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();
        }else{
            $ambilfile=\DB::table('sam_file as a')->selectRaw('c.id_req_type as id_master_filetype, a.title as content_title, 
            a.id_sam as id_program_periode, a.id_sam_file as id_content, a.nama_file as content_file_download, 
            a.id_filetype, a.link_folder as folder')
            ->leftJoin('sam as b', 'b.id_sam', 'a.id_sam')
            ->leftJoin('sam_request_type as c', 'c.id_req_type', 'b.id_req_type')
            ->whereIn('a.id_sam', [$select_module])
            ->OrwhereIn('a.id_sam', [$select_module])
            ->where('a.id_bu', $idbu)
            ->where('b.id_bu', $idbu)
            ->get();
        }

        return response($ambilfile,200);
    }

    public function tampilfiletasklist(Request $request)
    {
        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;

        $form = $request->form;
        $id_activity=$form['id_activity'];
        $type_module=$form['type_module'];
        $select_module=$form['id'];

                // Awal
        if($type_module=="SALESKIT_SO"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_master_filetype, a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype, c.id_activity')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->leftJoin('cam_file as c', function($j)use($id_activity){
                $j->on('c.id_file', 'a.id_content')
                ->where('c.id_activity', $id_activity);
            })
            ->where('a.id_master_filetype', 2)
            ->where('a.mediakit', 1)
            ->whereIn('a.id_program_periode', [$select_module])
            ->OrwhereIn('a.id_program_periode', [$select_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();
        }else if($type_module=="SALESKIT_R"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_master_filetype, a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype, c.id_activity')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->leftJoin('cam_file as c', function($j)use($id_activity){
                $j->on('c.id_file', 'a.id_content')
                ->where('c.id_activity', $id_activity);
            })
            ->where('a.id_master_filetype', 1)
            ->where('a.mediakit', 1)
            ->whereIn('a.id_program_periode', [$select_module])
            ->OrwhereIn('a.id_program_periode', [$select_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();
        }else if($type_module=="SALESKIT_P"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_master_filetype, a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype, e.id_activity')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->leftJoin('tbl_program_periode as c', 'c.id_program_periode', 'a.id_program_periode')
            ->leftJoin('tbl_program as d', 'd.id_program', 'c.id_program')
            ->leftJoin('cam_file as e', function($j)use($id_activity){
                $j->on('e.id_file', 'a.id_content')
                ->where('e.id_activity', $id_activity);
            })
            ->where('a.mediakit', 1)
            ->whereIn('a.id_program_periode', [$select_module])
            ->OrwhereIn('a.id_program_periode', [$select_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();
        }else{
            $ambilfile=\DB::table('sam_file as a')->selectRaw('a.title as content_title, a.id_sam as id_program_periode, a.id_sam_file as id_content, a.nama_file as content_file_download, a.id_filetype, a.link_folder as folder, b.id_activity')
            ->leftJoin('cam_file as b', function($j)use($id_activity){
                $j->on('b.id_file', 'a.id_sam_file')
                ->where('b.id_activity', $id_activity);
            })
            ->whereIn('a.id_sam', [$select_module])
            ->OrwhereIn('a.id_sam', [$select_module])
            ->get();
        }
                // Akhir
        return response($ambilfile,200);
    }

    public function saveTask(Request $request)
    {

        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;
        $posisi= $userget->POSITION;

        $formtask = $request->form;  
        $userid = $userget->USER_ID;
        $id_cam = $formtask['id_cam'];
        $status_tasklist = $formtask['activity'];
        $part=$formtask['partner'];
        
        $client=$formtask['client'];
        $typeadd = $formtask['type_act'];
        $rata2=array();
        $idclient=array();

        // foreach ($cost as $key => $value) {
        //     return $value;
        // }
        
        $idsgm=\App\Models\Intrasm\Am::select('ID_SGM', 'ID_SM', 'ID_GM');
        if($posisi=="AM"){
            $idsgm=$idsgm->where('ID_AM', $userid);
        }elseif($posisi=="SGM"){
            $idsgm=$idsgm->where('ID_SGM', $userid);
        }elseif($posisi=="SM"){
            $idsgm=$idsgm->where('ID_SM', $userid);
        }else{
            $idsgm=$idsgm->where('ID_GM', $userid);
        }
        $idsgm=$idsgm->get();
        // return $idsgm;
        foreach($idsgm as $c){
            $namasgm=$c['ID_SGM'];
            $namasm=$c['ID_SM'];
            $namagm=$c['ID_GM'];
        }


        // if($id_cam){
            $status_cam_activity=\App\Models\Cam\Cam::select('type')
            ->where('id_cam', $id_cam)
            ->get();
        // End ambil status type

        // Di looping biar dapat type nya aja
            foreach($status_cam_activity as $val2){
                $type_tasklist=$val2->type;
            }
        // End

        // Select id cam buat bikin tasklist report, jika dia sudah buat activity Plan
            $id_activity=\App\Models\Cam\Cam::select('cam.*')
            ->where('id_cam', $id_cam)
            ->get();
        // }


        if(count($id_activity)>0){
        // Kondisi Report
            // Update tabel CAM
                $update_cam=\App\Models\Cam\Cam::where('id_cam', $id_cam)
                ->update(
                    [
                        'id_cam_typeactivity'=>$formtask['type_act'],
                        'id_agcy'=>$formtask['agencypintu'],
                        'id_brand'=>$formtask['brand'],
                        'brand_variant'=>$formtask['variant'],
                        'id_adv'=>$formtask['advertiser'],
                        'type'=>"REPORT",
                        'start_date'=>$formtask['startdate'],
                        'end_date'=>$formtask['enddate'],
                        'update_user'=>$userid
                    ]
                );
            // End update tabel CAM

            // Update tabel Cam Brand
                $update_cam_brand=\App\Models\Cam\Cam_brand::where('id_cam', $id_cam)
                ->update(
                    [
                        'id_brand'=>$formtask['brand']
                    ]
                );
            // End Update Cam Brand

            // Buat dapetin id_ref_activity
                $id_ref_activity=\App\Models\Cam\Cam_activity::select('id_activity')
                ->leftJoin('cam as b','b.id_cam','=','cam_activity.id_cam')
                ->where('cam_activity.id_cam', $id_cam)
                ->whereNull('b.deleted_at')
                ->get();
            // End dapetin id refactivity

            // Update tabel Cam Activity
                if(count($id_ref_activity)>0){
                    $ref_activity=$id_ref_activity[0]['id_activity'];
                }else{
                    $ref_activity=0;
                }

                $insert_cam_activity2=new \App\Models\Cam\Cam_activity;
                $insert_cam_activity2->id_ref_activity=$ref_activity;
                $insert_cam_activity2->id_cam=$id_cam;
                if($status_tasklist=="REPORT"){
                    $insert_cam_activity2->status="ACTUAL";
                }else{
                    $insert_cam_activity2->status="PLAN";
                }
                $insert_cam_activity2->id_cam_typeactivity=$formtask['type_act'];
                $insert_cam_activity2->subject=$formtask['subject'];
                $insert_cam_activity2->location=$formtask['location'];
                $insert_cam_activity2->description=$formtask['desc'];
                $insert_cam_activity2->start_date=$formtask['startdate'];
                $insert_cam_activity2->end_date=$formtask['enddate'];
                $insert_cam_activity2->potency_revenue=$formtask['potency'];
                $insert_cam_activity2->insert_user=$userid;
                $insert_cam_activity2->update_user=$userid;
                $insert_cam_activity2->save();
            // End update tabel Cam Actvity

            // Update Cam Partner
                
                // Delete dlu yang di cam_partner berdasarkan id_cam
                    $delete_cam_partner=\App\Models\Cam\Cam_partner::where('id_cam', $id_cam)->delete();
                // End delete

                // Kalo sudah di delete kesini
                    foreach($part as $val){
                        $insert_partner=new \App\Models\Cam\Cam_partner;
                        $insert_partner->id_cam=$id_cam;
                        $insert_partner->user_id=$val['id'];
                        $insert_partner->keterangan="HADIR";
                        $insert_partner->save();
                    }
                // End
            // End Update Cam Partner

            // Buat Update Cam Client
                // Delete dlu yang di cam_client berdasarkan id_cam
                    $delete_cam_client=\App\Models\Cam\Cam_client::where('id_cam', $id_cam)->delete();
                // End delete

                // Kalo sudah di delete kesini
                    foreach ($client as $key=>$simpanclient) {
                        $insertclient=new \App\Models\Cam\Cam_client;
                        $insertclient->id_cam=$id_cam;
                        $insertclient->id_client_account=$simpanclient['id_client_account'];
                        if($status_tasklist=="plan"){
                            $insertclient->id_status="COST";
                        }else{
                            $insertclient->id_status="REPORTING";
                        }
                        $insertclient->insert_user=$userid;
                        $insertclient->update_user=$userid;
                        $insertclient->save();
                    }
                // End
            // End Update Cam Client
        // End Kondisi buat report
        }else{
        // Kondisi Jika Insert Data
            $cost=$formtask['hargaEnt'];
        $name_file2=$formtask['fileChoose'];
            // insert ke tabel CAM                    
                $insert_cam=new \App\Models\Cam\Cam;
                // Kondisi buat kalo yg login posisinya AM maka field id_am isinya email dia, kalo bukan sesuai field PICAM
                    if(\Auth::User()->POSITION=="AM"){
                        $insert_cam->id_am=$userid;
                    }else{
                        $insert_cam->id_am=$formtask['picam'];
                    }
                // End Kondisi
                $insert_cam = new \App\Models\Cam\Cam;
                $insert_cam->id_am = $formtask['picam'];
                $insert_cam->id_sgm = $namasgm;
                $insert_cam->id_sm = $namasm;
                $insert_cam->id_gm = $namagm;
                $insert_cam->pic_am = $formtask['picam'];
                $insert_cam->id_cam_typeactivity = $formtask['type_act'];
                $insert_cam->id_agcy = $formtask['agencypintu'];
                $insert_cam->id_brand = $formtask['brand'];
                $insert_cam->brand_variant = $formtask['variant'];
                $insert_cam->id_adv = $formtask['advertiser'];
                $insert_cam->type = $formtask['activity'];
                $insert_cam->save();
                
                if($status_tasklist=="plan"){
                    $insert_cam->type="PLAN";
                }else{
                    $insert_cam->type="REPORT";
                }

                $insert_cam->start_date=$formtask['startdate'];
                $insert_cam->end_date=$formtask['enddate'];
                $insert_cam->insert_user=$userid;
                $insert_cam->update_user=$userid;
                $insert_cam->save();
            // End Insert Tabel CAM

            // Ambil id cam
                $getidcam=$insert_cam->id_cam;
            // End ambil id cam

            // Insert ke tabel Cam Brand
                $insert_cam_brand=new \App\Models\Cam\Cam_brand;
                $insert_cam_brand->id_cam=$getidcam;
                $insert_cam_brand->id_brand=$formtask['brand'];
                $insert_cam_brand->save();
            // End insert ke tabel cam

            // Buat dapetin id_ref_activity
                $id_ref_activity=\App\Models\Cam\Cam_activity::select('id_activity')
                ->leftJoin('cam as b','b.id_cam','=','cam_activity.id_cam')
                ->where('cam_activity.id_cam', $getidcam)
                ->whereNull('b.deleted_at')
                ->get();
            // End id ref activity

            // Buat kasih id ref activity
                if(count($id_ref_activity)>0){
                    $ref_activity=$id_ref_activity[0]['id_activity'];
                }else{
                    $ref_activity=0;
                }
            // End id ref activity

            // Insert ke tabel Cam Activity
                $insert_cam_activity=new \App\Models\Cam\Cam_activity;
                $insert_cam_activity->id_ref_activity=$ref_activity;
                $insert_cam_activity->id_cam=$getidcam;
                if($status_tasklist=="plan"){
                    $insert_cam_activity->status="PLAN";
                }else{
                    $insert_cam_activity->status="ACTUAL";
                }
                $insert_cam_activity->id_cam_typeactivity=$formtask['type_act'];
                $insert_cam_activity->subject=$formtask['subject'];
                $insert_cam_activity->location=$formtask['location'];
                $insert_cam_activity->description=$formtask['desc'];
                $insert_cam_activity->start_date=$formtask['startdate'];
                $insert_cam_activity->end_date=$formtask['enddate'];
                $insert_cam_activity->potency_revenue=$formtask['potency'];
                $insert_cam_activity->insert_user=$userid;
                $insert_cam_activity->update_user=$userid;
                $insert_cam_activity->save();
            // End insert cam activity

            // Buat dapetin id activity
                $getidactivity=$insert_cam_activity->id_activity;
            // End id activity

            // Insert ke tabel Cam partner
                foreach($part as $val){
                    $insert_partner=new \App\Models\Cam\Cam_partner;
                    $insert_partner->id_cam=$getidcam;
                    $insert_partner->user_id=$val['id'];
                    if($status_tasklist=="REPORT"){
                        $insert_partner->keterangan="HADIR";
                    }
                    $insert_partner->save();
                }
            // End insert Cam Partner

            // return count($cost);
            // Jika ada cost/entertaimen yg diberikan maka jalankan script ini
                if(count($cost) > 0){
                    foreach($cost as $k=>$v){
                            // return $k;
                        $insert_cam_cost=new \App\Models\Cam\Cam_cost;
                        $insert_cam_cost->id_activity=$getidactivity;
                        $insert_cam_cost->cost=$v;
                        $insert_cam_cost->title=$formtask['titleEnt'][$k];
                        $insert_cam_cost->cost_description=$formtask['descEnt'][$k];
                        $insert_cam_cost->cost_by=$formtask['partnerEnt'][$k]['id'];
                        $insert_cam_cost->insert_user=$userid;
                        $insert_cam_cost->update_user=$userid;
                        //ini untuk save image
                        $folderName = \Auth::user()->USER_NAME;
                        $folderName = str_replace(" ","_",$folderName);
                        $safeName = $folderName.'-CAM-ACTIVITY-'.str_random(10).'.'.'png';
                        if(isset($formtask['fileEnt'][$k])){
                            $img = Image::make($formtask['fileEnt'][$k]);
                            $img->resize(600, null, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save('D:cam_activity_api/public/image/'.$safeName);
                        }
                        // end save image
                        $insert_cam_cost->nota = $safeName;
                        $insert_cam_cost->save();

                        $idcamcost[]=$insert_cam_cost->id_cam_cost;
                    }
                }
                // }
            // End cost/entertaiment

            // Insert ke tabel cam file
                // jika ada file yang dibawa maka
                if(count($name_file2) > 0){
                    foreach($name_file2 as $index=>$val){
                        $insert_cam_file=new \App\Models\Cam\Cam_file;
                        $insert_cam_file->id_activity=$getidactivity;
                        if($val['id_master_filetype'] == 1){
                            $moduling = 'RATECARD';
                        } else if($val['id_master_filetype'] == 2){
                            $moduling = 'SPECIAL OFFER';
                        } else if($val['id_master_filetype'] == 8){
                            $moduling = 'PROGRAM';
                        } else if($val['id_master_filetype'] == 02 || $val['id_master_filetype'] == 03){
                            $moduling = 'PAKET';
                        } else {
                            $moduling = 'CONCEPT';
                        }
                        $insert_cam_file->module=$moduling;
                        $insert_cam_file->id_module=$val['id_program_periode'];
                        $insert_cam_file->id_file=$val['id_content'];
                        $insert_cam_file->name_file=$val['content_file_download'];
                        $insert_cam_file->insert_user=$userid;
                        $insert_cam_file->update_user=$userid;
                        $insert_cam_file->save();
                    }
                }
                // Jika tidak
                    // Tidak save ke tabel cam file
                // end
            // End file

            // Buat nyari rata2 dari costnya
                if(count($cost) > 0){
                    forEach($client as $clients){
                        forEach($idcamcost as $idcosts){
                            $idcost[]=$idcosts;
                        }
                        forEach($cost as $isi){
                            $rata2[]=$isi/count($client);
                            $idclient[]=$clients['id_client_account'];
                        }
                    }
                }
            // End buat nyari rata2 dari costnya

            // Insert ke tabel cost average
                foreach($rata2 as $key1=>$val1){
                    $insert_average_cost=new \App\Models\Cam\Cam_averagecost;
                    $insert_average_cost->id_activity=$getidactivity;
                    $insert_average_cost->id_cam_cost=$idcost[$key1];
                    $insert_average_cost->id_client_account=$idclient[$key1];
                    $insert_average_cost->cost_average=$val1;
                    $insert_average_cost->insert_user=$userid;
                    $insert_average_cost->update_user=$userid;
                    $insert_average_cost->save();
                }
            // End insert ke tabel cost average

            // Insert ke tabel Cam Client
                foreach ($client as $key=>$simpanclient) {
                    $insertclient=new \App\Models\Cam\Cam_client;
                    $insertclient->id_cam=$getidcam;
                    $insertclient->id_client_account=$simpanclient['id_client_account'];
                    if($typeadd=="activity"){
                        $insertclient->id_status="COST";
                    }else{
                        $insertclient->id_status="REPORTING";
                    }
                    $insertclient->insert_user=$userid;
                    $insertclient->update_user=$userid;
                    $insertclient->save();
                }
            // End Insert
        // End Kondisi Insert Data

            return response(['status' => 'Data Success Di Update'], 200);
        }
    }


    public function editTask(Request $request)
    {
        $id_cam=$request->input('id_cam');
        $id_activity=$request->input('id_activity');

        $tasklist=\App\Models\Cam\Cam_activity::with(
            [
                'Cam_edit.User_picam',
                'Cam_typeactivity_edit',
                'Cam_edit',
                'Cam_edit.Agencypintu',
                'Cam_edit.Advertiser',
                'Cam_partner_edit', 
                'Cam_partner_edit.Name_partner', 
                'Cam_client_edit',
                'Cam_client_edit.Edit_idclientaccount',
                'Cam_client_edit.Edit_idclientaccount.Edit_namaclient',
                'Cam_brand_edit',
                'Cam_brand_edit.Brand'
            ]
        )
        ->where('id_cam', $id_cam)
        ->where('id_activity', $id_activity)
        ->get();

        $partner=\App\Models\Cam\Cam_partner::where('id_cam', $id_cam)
        ->get();

        $client=\App\Models\Cam\Cam_client::where('id_cam', $id_cam)
        ->get();

        return $data=[
            'tasklist'=>$tasklist,
            'partner'=>$partner,
            'client'=>$client
        ];
    }

    public function updateTask(Request $request)
    {
        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;
        $userid = $userget->USER_ID;

        $gambar=\App\Models\Cam\Cam_cost::select('nota')
        ->where('id_activity', 6)
        ->where('id_cam_cost', 3)
        ->get();

        foreach($gambar as $val_gambar){
            $gambar_lama=$val_gambar->nota;
        }

        $cost=$request->input('cost_save');
        $cost_replace=str_replace(',','',$cost);
        if($request->hasFile('cost_nota_save')){
            if (!is_dir('img/nota/')) {
                mkdir('img/nota/', 0777, TRUE);
            }

            $file=$request->file('cost_nota_save');
            $filename=str_random(5).'-'.$file->getClientOriginalName();
            $destinationPath='img/nota/';
            $file->move($destinationPath,$filename);
        }else{
            $filename=$gambar_lama;
        }

        $var=\App\Models\Cam\Cam_cost::where('id_activity',$id_activity)
        ->where('id_cam_cost', $id_cam_cost)
        ->update([
            'cost'=>$cost_replace,
            'title'=>$request->input('title_save'),
            'cost_description'=>$request->input('cost_description_save'),
            'cost_by'=>$request->input('cost_by_save'),
            'nota'=>$filename,
            'update_user'=>$userid,
            'updated_at'=>Carbon::now()
        ]);
    }

    public function deleteTask(Request $request)
    {
        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;
        $userid = $userget->USER_ID;
        // return $request->all();        
        $formtask = $request->form;

        // $id_cam=$formtask->id_cam;
        foreach ($formtask as $key => $value) {
        $var=\App\Models\Cam\Cam::find($value['id_cam']);
        $var->update_user=$userid;
        $var->deleted_at=Carbon::now();
        $var->save();
        }
        

        return response(['data'=> 'Data Berhasil Di Delete'], 200);
    }

    public function insert_komen(Request $request)
    {

        $formtask = $request->form;

        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;
        $userid = $userget->USER_ID;

        if($formtask['comment']!=''){
            $var=new \App\Models\Cam\Cam_comment;
            $var->id_cam=$formtask['id_cam'];
            $var->user_id=$userid;
            $var->comment=$formtask['comment'];
            $var->save();

            return response(['data'=> 'Komentar Berhasil Di Kirim'], 200);
        }else{
            return response(['data'=> 'Komentar Harus Diisi'], 200);
        } 
    }

    public function insert_cost_after_save(Request $request)
    {
        // return $request->all();
        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;
        $userid = $userget->USER_ID;
        $username = $userget->USER_NAME;
        $formtask=$request->form;
        $cost=$formtask['cost'];

        if(isset($formtask['id_cam_cost'])){
            $id_cam_cost=$formtask['id_cam_cost'];
            $gambar=\App\Models\Cam\Cam_cost::select('nota')
            ->where('id_cam_cost', $id_cam_cost)
            ->get();
            if($formtask['fileEnt']){
                $folderName = $username;
                $folderName = str_replace(" ","_",$folderName);
                $safeName = $folderName.'-CAM-ACTIVITY-'.str_random(10).'.'.'png';
                $img = Image::make($formtask['fileEnt'][0]);
                $img->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save('D:cam_activity_api/public/image/'.$safeName);
            }
            else {
                $safeName = $gambar[0]->nota;
            }
            $var=\App\Models\Cam\Cam_cost::where('id_cam_cost', $id_cam_cost)
            ->update([
                'cost'=>$cost[0],
                'title'=>$formtask['titleEnt'][0],
                'cost_description'=>$formtask['descEnt'][0],
                'cost_by'=>$formtask['partnerEnt'][0]['id'],
                'nota'=>$safeName,
                'update_user'=>$userid,
                'updated_at'=>Carbon::now()
            ]);
        }else{
            foreach($cost as $k=>$v){
                $insert_cam_cost=new \App\Models\Cam\Cam_cost;
                $insert_cam_cost->id_activity=$formtask['id_activity'];
                $insert_cam_cost->cost=$v;
                $insert_cam_cost->title=$formtask['titleEnt'][$k];
                $insert_cam_cost->cost_description=$formtask['descEnt'][$k];
                $insert_cam_cost->cost_by=$formtask['partnerEnt'][$k]['id'];
                $insert_cam_cost->insert_user=$userid;
                $insert_cam_cost->update_user=$userid;
                    //ini untuk save image
                $folderName = \Auth::user()->USER_NAME;
                $folderName = str_replace(" ","_",$folderName);
                $safeName = $folderName.'-CAM-ACTIVITY-'.str_random(10).'.'.'png';
                $img = Image::make($formtask['fileEnt'][$k]);
                $img->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save('D:cam_activity_api/public/image/'.$safeName);
                    // end save image
                $insert_cam_cost->nota = $safeName;
                $insert_cam_cost->save();
            }
        }
        return response(['data'=> 'Cost Berhasil Di Input'], 200);
    }

    public function insert_file_after_save(Request $request)
    {
        $formtask=$request->form;

        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;
        $userid = $userget->USER_ID;
        $username = $userget->USER_NAME;
        
        if(isset($formtask['id_master_filetype'])){
            if($formtask['id_master_filetype'] == 1){
                $moduling = 'RATECARD';
            } else if($formtask['id_master_filetype'] == 2){
                $moduling = 'SPECIAL OFFER';
            } else if($formtask['id_master_filetype'] == 8){
                $moduling = 'PROGRAM';
            } else if($formtask['id_master_filetype'] == 02 || $formtask['id_master_filetype'] == 03){
                $moduling = 'PAKET';
            } else {
                $moduling = 'CONCEPT';
            }
        }

        if(isset($formtask['fornew'])){
            $name_file2 = $formtask['fileChoose'];
            foreach($name_file2 as $index=>$val){
                $insert_cam_file=new \App\Models\Cam\Cam_file;
                $insert_cam_file->id_activity=$formtask['id_activity'];
                if($val['id_master_filetype'] == 1){
                    $moduling = 'RATECARD';
                } else if($val['id_master_filetype'] == 2){
                    $moduling = 'SPECIAL OFFER';
                } else if($val['id_master_filetype'] == 8){
                    $moduling = 'PROGRAM';
                } else if($val['id_master_filetype'] == 02 || $val['id_master_filetype'] == 03){
                    $moduling = 'PAKET';
                } else {
                    $moduling = 'CONCEPT';
                }
                $insert_cam_file->module=$moduling;
                $insert_cam_file->id_module=$val['id_program_periode'];
                $insert_cam_file->id_file=$val['id_content'];
                $insert_cam_file->name_file=$val['content_file_download'];
                $insert_cam_file->insert_user=$userid;
                $insert_cam_file->update_user=$userid;
                $insert_cam_file->update_user=$userid;
                $insert_cam_file->save();
            }
        } else{
            if(!isset($formtask['module'])){
                $var=\App\Models\Cam\Cam_file::where('id_activity', $formtask['id_activity'])
                ->where('id_module', $formtask['id_module'])
                ->where('id_file', $formtask['id_file'])
                ->where('module', $moduling)
                ->whereNull('deleted_at')
                ->first();
            }else{
                $var=\App\Models\Cam\Cam_file::where('id_activity', $formtask['id_activity'])
                ->where('id_module', $formtask['id_module'])
                ->where('id_file', $formtask['id_file'])
                ->where('module', $formtask['module'])
                ->whereNull('deleted_at')
                ->first();
            }

            if(!empty($var)){
                $updatenyafile=\App\Models\Cam\Cam_file::find($var->id_cam_file);
                $updatenyafile->update_user=$userid;
                $updatefile=$updatenyafile->delete();
            }
        }

        return response(['data'=> 'File Berhasil Di Update'], 200);
    }

    public function hapus_entertaiment(Request $request)
    {
        
        $userget = user($request->bearerToken());
        $idbu = $userget->ID_BU;
        $userid = $userget->USER_ID;
        $username = $userget->USER_NAME;

        $id_activity=$request->id_activity;
        $id_cam_cost=$request->id_cam_cost;
        
        $var=\App\Models\Cam\Cam_cost::where('id_activity',$id_activity)
        ->where('id_cam_cost', $id_cam_cost)
        ->update([
            'update_user'=>$userid,
            'deleted_at'=>Carbon::now()
        ]);


        return response(['data'=> 'File Berhasil Di Hapus'], 200);
    }

    public function get_file(Request $request)
    {
        $formtask=$request->form;
        $id_cam_file=$formtask['id_cam_file'];
        $ambildata=\App\Models\Cam\Cam_file::select('module', 'id_file', 'name_file', 'id_module')->find($id_cam_file);
        $id_file=$ambildata->id_file;
        $name_file=$ambildata->name_file;
        $id_module=$ambildata->id_module;
        $type_module=$ambildata->module;

        if($type_module=="PROGRAM"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->leftJoin('tbl_program_periode as c', 'c.id_program_periode', 'a.id_program_periode')
            ->leftJoin('tbl_program as d', 'd.id_program', 'c.id_program')
            ->where('a.mediakit', 1)
            ->where('a.id_content', $id_file)
            ->whereIn('a.id_program_periode', [$id_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();

        }else if($type_module=="RATECARD"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->where('a.id_master_filetype', 1)
            ->where('a.mediakit', 1)
            ->whereIn('a.id_program_periode', [$id_module])
            ->OrwhereIn('a.id_program_periode', [$id_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();
        }else if($type_module=="SPECIAL OFFER"){
            $ambilfile=\DB::table('tbl_content as a')->selectRaw('a.id_program_periode, a.id_content, a.content_title, a.content_file_download, a.updated_at, b.folder, a.id_filetype')
            ->leftJoin('tbl_filetype as b', 'b.id_filetype', 'a.id_filetype')
            ->where('a.id_master_filetype', 2)
            ->where('a.mediakit', 1)
            ->whereIn('a.id_program_periode', [$id_module])
            ->OrwhereIn('a.id_program_periode', [$id_module])
            ->orderBy('a.updated_at', 'desc')
            ->get();
        }else{
            $ambilfile=\DB::table('sam_file as a')->selectRaw('a.title as content_title, a.id_sam as id_program_periode, a.id_sam_file as id_content, a.nama_file as content_file_download, a.id_filetype, a.link_folder as folder')
            ->whereIn('a.id_sam', [$id_module])
            ->OrwhereIn('a.id_sam', [$id_module])
            ->get();
        }

        $get_extension = substr(strrchr($name_file,'.'),1);

        if($type_module=="PROGRAM" || $type_module=="RATECARD" || $type_module=="SPECIAL OFFER"){
            if($get_extension=="jpg"){
                $html="http://sm.mncgroup.com/datafile/".$ambilfile[0]->folder."/".$ambilfile[0]->id_filetype."/".$ambilfile[0]->content_file_download."";
            }elseif($get_extension=="pdf" || $get_extension=="xlsx" || $get_extension=="pptx" || $get_extension=="ppt" || $get_extension=="xls"){
                $html="http://sm.mncgroup.com/datafile/".$ambilfile[0]->folder."/".$ambilfile[0]->id_filetype."/".$ambilfile[0]->content_file_download."";
            }else{
                $html='FORMAT TIDAK DITEMUKAN';
            }
        }else{
            $html="http://sm.mncgroup.com/uploads/local/".$query->id_filetype."/".$query->content_file_download."";
        }

        return response(['image' => $html], 200);
    }
    // End Buat Di Form

    // list client ulangtahun
    public function list_birthday_client(Request $request)
    {
        try {
            $var=\DB::table('tbl_userclient as a')->selectRaw('a.firstname, a.lastname, a.birth_date, 
                b.id_client_account, a.id_client, 
                IF(b.type_company="AGC", c.nama_agencypintu, d.nama_adv)as company, 
                date_format(a.birth_date, "%M")as bulan, 
                date_format(a.birth_date, "%d")as tanggal')
            ->leftJoin('tbl_userclient_account as b', 'b.id_client', 'a.id_client')
            ->leftJoin('db_m_agencypintu as c', 'c.id_agcyptu', 'b.id_company')
            ->leftJoin('db_m_advertiser as d', 'd.id_adv', 'b.id_company')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->where('a.active', 1)
            ->where('b.active', 1)
            ->whereRaw('date_format(a.birth_date, "%m-%d")=date_format(now(),"%m-%d")')
            ->get();

            return response($var,200);
        }catch(\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }
    // end list client ulangtahun

    // list clienthadnling
    public function list_client_handling(Request $request)
    {
        $userget = user($request->bearerToken());
        $userid = $userget->USER_ID; 
        $idbu = $userget->ID_BU;

        try{
            $var=\DB::table('tbl_userclient_picsgm as a')->selectRaw('a.id, a.id_client_account, a.id_sgm, a.id_bu, b.type_company, b.id_company, e.firstname, e.lastname, e.gender, e.birth_date, b.type_company, b.email, b.position, IF(b.type_company="AGC", c.nama_agencypintu, d.nama_adv) AS company')
            ->leftJoin('tbl_userclient_account as b', 'b.id_client_account', 'a.id_client_account')
            ->leftJoin('db_m_agencypintu as c', 'c.id_agcyptu', 'b.id_company')
            ->leftJoin('db_m_advertiser as d', 'd.id_adv', 'b.id_company')
            ->leftJoin('tbl_userclient as e', 'e.id_client', 'b.id_client')
            ->where('a.active', 1)
            ->where('a.id_bu', $idbu)
            ->where('a.id_sgm', $userid)
            ->whereNull('a.deleted_at')
            ->where('b.active', 1)
            ->whereNull('b.deleted_at')
            ->get();

            return response($var, 200);
        }catch(\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }
    // end Clienthandling

    // list client not handling
    public function list_client_not_handling(Request $request)
    {
        $userget = user($request->bearerToken());
        $userid = $userget->USER_ID;
        $idbu = $userget->ID_BU;
        $isi_id_client_account_agc=array();
        $isi_id_client_account_adv=array();

        try{

            $id_client_agc=\DB::table('tbl_userclient_picsgm as a')->selectRaw('a.id, a.id_client_account, a.id_sgm, a.id_bu, b.type_company, b.id_company,
                IF(b.type_company="AGC", c.nama_agencypintu, d.nama_adv) AS company')
            ->leftJoin('tbl_userclient_account as b', 'b.id_client_account', 'a.id_client_account')
            ->leftJoin('db_m_agencypintu as c', 'c.id_agcyptu', 'b.id_company')
            ->leftJoin('db_m_advertiser as d', 'd.id_adv', 'b.id_company')
            ->where('a.active', 1)
            ->where('a.id_bu', $idbu)
            ->where('a.id_sgm', $userid)
            ->whereNull('a.deleted_at')
            ->where('b.active', 1)
            ->whereNull('b.deleted_at')
            ->where('b.type_company', "AGC")
            ->get();

            forEach($id_client_agc as $val){
                array_push($isi_id_client_account_agc, $val->id_client_account);
            }

            $id_client_adv=\DB::table('tbl_userclient_picsgm as a')->selectRaw('a.id, a.id_client_account, a.id_sgm, a.id_bu, b.type_company, b.id_company,
                IF(b.type_company="AGC", c.nama_agencypintu, d.nama_adv) AS company')
            ->leftJoin('tbl_userclient_account as b', 'b.id_client_account', 'a.id_client_account')
            ->leftJoin('db_m_agencypintu as c', 'c.id_agcyptu', 'b.id_company')
            ->leftJoin('db_m_advertiser as d', 'd.id_adv', 'b.id_company')
            ->where('a.active', 1)
            ->where('a.id_bu', $idbu)
            ->where('a.id_sgm', $userid)
            ->whereNull('a.deleted_at')
            ->where('b.active', 1)
            ->whereNull('b.deleted_at')
            ->where('b.type_company', "ADV")
            ->get();

            forEach($id_client_adv as $val1){
                array_push($isi_id_client_account_adv, $val1->id_client_account);
            }

            $id_client_account_notIn = array_merge($isi_id_client_account_agc, $isi_id_client_account_adv);

            $var=\DB::table('tbl_userclient as a')->selectRaw('a.id_client, b.id_client_account, a.firstname, 
            a.lastname, a.gender, a.birth_date,
            b.email, b.type_company, b.position,
            IF(b.type_company="AGC", c.nama_agencypintu, d.nama_adv) AS company')
            ->leftJoin('tbl_userclient_account as b', 'b.id_client', 'a.id_client')
            ->leftJoin('db_m_agencypintu as c', 'c.id_agcyptu', 'b.id_company')
            ->leftJoin('db_m_advertiser as d', 'd.id_adv', 'b.id_company')
            ->where('a.active', 1)
            ->whereNull('a.deleted_at')
            ->where('b.active', 1)
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.id_client_account', $id_client_account_notIn)
            ->whereNotIn('b.id_client_account', [1])
            ->orderBy('a.firstname')
            ->get();
            return response($var, 200);
        }catch(\Exception $e){
            return response(array('data'=>'Error at Beckend'));
        }
    }
    // end list client not handling

    // list company agc yg di handle dia
    public function list_companyAgency_handling(Request $request)
    {
        try{

            $userget = user($request->bearerToken());
            $userid = $userget->USER_ID; 
            $position = $userget->POSITION;

            $var=\DB::table('db_d_target_account_am as a')->selectRaw('a.id_targetaccount, a.id_am_run, a.id_bu, c.ID_SGM, c.ID_SM, c.ID_GM, b.id_agcyptu_run, d.nama_agencypintu')
            ->leftJoin('db_d_target_account as b', 'b.id_targetaccount', 'a.id_targetaccount')
            ->leftJoin('tbl_am as c', 'c.ID_AM', 'a.id_am_run')
            ->leftJoin('db_m_agencypintu as d', 'd.id_agcyptu', 'b.id_agcyptu_run')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at');
            if($position=="GM"){
                $var=$var->where('c.ID_GM', $userid);
            }else if($position=="SM"){
                $var=$var->where('c.ID_SM', $userid);
            }else if($position=="SGM"){
                $var=$var->where('c.ID_SGM', $userid);
            }else{
                $var=$var->where('c.ID_AM', $userid);
            }
            $var=$var->groupBy('b.id_agcyptu_run')
            ->orderBy('d.nama_agencypintu')
            ->get();

            return response(array($var), 200); 
        }catch(\Exception $e){
            return response(array('data'=>'Error at Beckend'));
        }
    }
    // end list company agc yang di handle

    // list company adv yg di handle dia
    public function list_companyAdv_handling(Request $request)
    {
        try{

            $userget = user($request->bearerToken());
            $userid = $userget->USER_ID; 
            $position = $userget->POSITION;

            $var=\DB::table('db_d_target_account_am as a')->selectRaw('a.id_targetaccount, a.id_am_run, a.id_bu, c.ID_SGM, c.ID_SM, c.ID_GM, e.id_adv, e.nama_adv')
            ->leftJoin('db_d_target_account as b', 'b.id_targetaccount', 'a.id_targetaccount')
            ->leftJoin('tbl_am as c', 'c.ID_AM', 'a.id_am_run')
            ->leftJoin('db_m_product as d', 'd.id_produk', 'b.id_produk')
            ->leftJoin('db_m_advertiser as e', 'e.id_adv', 'd.id_adv')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at');
            if($position=="GM"){
                $var=$var->where('c.ID_GM', $userid);
            }else if($position=="SM"){
                $var=$var->where('c.ID_SM', $userid);
            }else if($position=="SGM"){
                $var=$var->where('c.ID_SGM', $userid);
            }else{
                $var=$var->where('c.ID_AM', $userid);
            }
            $var=$var->groupBy('e.id_adv')
            ->orderBy('e.nama_adv')
            ->get();

            return response(array($var), 200); 
        }catch(\Exception $e){
            return response(array('data'=>'Error at Beckend'));
        }
    }
    // end list company adv yang di handle

    // List Summary report cost
    public function list_summary_cost(Request $request){
        try{

            $userget = user($request->bearerToken());
            $userid = $userget->USER_ID; 
            $position = $userget->POSITION;

            if($position=="GM"){
                $var=\DB::table('tbl_am as a')->selectRaw('ID_SM as idnya')
                ->where('a.ID_GM', $userid)
                ->where('a.active', 1)
                ->get();
            }else if($position=="SM"){
                $var=\DB::table('tbl_am as a')->selectRaw('ID_SGM as idnya')
                ->where('a.ID_SM', $userid)
                ->where('a.active', 1)
                ->get();
            }else if($position=="SGM"){
                $var=\DB::table('tbl_am as a')->selectRaw('ID_AM as idnya')
                ->where('a.ID_SGM', $userid)
                ->where('a.active', 1)
                ->get();
            }else{
                $var=\DB::table('tbl_am as a')->selectRaw('ID_AM as idnya')
                ->where('a.ID_AM', $userid)
                ->where('a.active', 1)
                ->get();
            }

            $datanya=array();

            forEach($var as $data){
                array_push($datanya, $data->idnya);
            }

            $cost=\DB::table('cam_cost as a')->selectRaw('b.id_activity, c.id_cam, a.cost_by, SUM(a.cost) AS jumlah, a.created_at')
            ->leftJoin('cam_activity as b', 'b.id_activity', 'a.id_activity')
            ->leftJoin('cam as c', 'c.id_cam', 'b.id_cam')
            ->whereIn('a.cost_by', $datanya)
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNull('c.deleted_at')
            ->whereRaw('DATE_FORMAT(a.created_at, "%Y-%m")=date_format(now(),"%Y-%m")')
            ->groupBy('a.cost_by')
            ->get();
        }catch(\exception $e){
            return response(array('data'=>'Error at Beckend'));
        }
    }
    // End List Summary report cost
}