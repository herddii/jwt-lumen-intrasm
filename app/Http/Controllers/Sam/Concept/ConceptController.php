<?php

namespace App\Http\Controllers\Sam\Concept;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Intrasm\Am;

class ConceptController extends Controller {


	public function request_type(Request $request, $modul){

		try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;

            if($modul=='PAKET'){
                $var=\App\Models\Sam\Requesttype::where('modul',$modul)
                    ->select('id_req_type','singkatan','nama','modul')
                    ->whereIn('id_req_type',['01','02'])
                    ->get();
            }elseif($modul=='CONCEPT'){
                if($idbu==5){
                    $var=\App\Models\Sam\Requesttype::where('modul',$modul)
                        ->select('id_req_type','singkatan','nama','modul')
                        ->whereIn('id_req_type',['4','10','11'])
                        ->get();
                }else{
                    $var=\App\Models\Sam\Requesttype::where('modul',$modul)
                        ->select('id_req_type','singkatan','nama','modul')
                        ->whereIn('id_req_type',['04','10'])
                        ->get();
                }
                
            }elseif($modul == "DATA"){
                $var=\App\Models\Sam\Requesttype::where('modul',$modul)
                    ->select('id_req_type','singkatan','nama','modul')
                    ->get();
            }
            

            return response($var,200);
        } catch (\Exception $e){
            return response($e->getMessage());
        }  
	}

	public function benefit(Request $request, $id){

		try {

			$userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;

			$var = \DB::select('select section, c.section_name, data1.id_benefit , data1.nama_benefit
                from (
                select a.id_benefit, a.nama_benefit,
                if(b.id_section is null, (select id_section from sam_benefit_section
                where  id_req_type='.$id.' and id_bu='.$idbu.' and id_benefit=0), b.id_section) as section
                from benefits a
                left join sam_benefit_section b on b.id_benefit=a.id_benefit and b.id_req_type='.$id.' and b.id_bu='.$idbu.') as data1
                left join tbl_user_section c on c.id_section=data1.section
                left join sam_benefit_section d on d.ID_SECTION = c.ID_SECTION
                left join benefits e on e.id_benefit = d.id_benefit
                where d.id_req_type='.$id.' 
                order by section_name');

            return response($var,200);
		} catch (Exception $e) {
			return response($e->getMessage());
		}
	}

	public function list_program(Request $request){
		try {

			$userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;

            $formtask= $request->get('form');

			if($formtask['to_bu']){
            	$bu=$formtask['to_bu'];
	        }else{
	            $bu=$idbu;
	        }

        	$var=\App\Models\Saleskit\Programperiode::leftJoin('tbl_program','tbl_program_periode.id_program','=','tbl_program.id_program')
                ->where('tbl_program.id_bu',$bu)
                ->whereNull('tbl_program.deleted_at')
                ->select(
                    'tbl_program.id_program',
                    'tbl_program_periode.id_program_periode as id',
                    \DB::raw("upper(tbl_program.program_name) as text")
                )->orderBy('tbl_program.program_name');

	        if($request->get('search')){
	            $var=$var->where('program_name','like','%'.$request->get('search').'%');
	        }

	        if($request->has('page_limit')){
	            $var=$var->paginate($request->input('page_limit'));
	        }else{
	            $var=$var->paginate(10);
	        }

            return response($var,200);
		} catch (Exception $e) {
			return response($e->getMessage());
		}
	}

	public function list_brand(Request $request){
		try {
			$userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;

            $formtask= $request->get('form');

			$brand=\DB::table('db_m_brand as a')
                ->select('a.id_brand','a.id_brand as id','nama_brand as text')
                ->leftJoin('db_m_product as b','b.id_brand','=','a.id_brand')
                ->leftJoin('db_d_target_account as c','c.id_produk','=','b.id_produk');

            if($request->has('q')){
                $brand=$brand->where('a.nama_brand','like','%'.$request->input('q').'%');
            }

            if(isset($formtask['advertiser'])){
                $brand=$brand->where('b.id_adv',$formtask['advertiser']);
            }

            if(isset($formtask['agency'])){
                $brand=$brand->where('c.id_agcyptu_run',$formtask['agency']);
            }

            if(isset($formtask['am'])){
                $brand=$brand->leftJoin('db_d_target_account_am as d','d.id_targetaccount','=','c.id_targetaccount')
                    ->where('d.id_am_run',$formtask['am'])
                    ->where('d.id_bu',$idbu);
            }

            if($request->has('page_limit')){
                $pagelimit=$request->input('page_limit');
            }else{
                $pagelimit=10;
            }

            $brand=$brand->groupBy('a.id_brand')->paginate($pagelimit);

            if(count($brand)>0){
                return $brand;      
            }else{
                $br=\DB::table('db_m_brand as a')
                    ->select('a.id_brand','a.id_brand as id','nama_brand as text')
                    ->leftJoin('db_m_product as b','b.id_brand','=','a.id_brand');

                if($request->has('q')){
                    $br=$br->where('a.nama_brand','like','%'.$request->input('q').'%');
                }

                $br=$br->groupBy('a.id_brand')->paginate(10);      

                return $br;
            }

            return response($brand,200);
		} catch (Exception $e) {
			return response($e->getMessage());
		}
	}

	public function list_advertiser(Request $request){

		try {

			$userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;

            $formtask= $request->get('form');

			$advertiser=\App\Models\Intrasm\Advertiser::select('db_m_advertiser.id_adv','db_m_advertiser.id_adv as id','nama_adv as text')
            	->leftJoin('db_m_product as b','b.id_adv','=','db_m_advertiser.id_adv')
            	->leftJoin('db_d_target_account as c','c.id_produk','=','b.id_produk')
            	->where('db_m_advertiser.filter',1);

            if($request->has('q')){
                $advertiser=$advertiser->where('nama_adv','like','%'.$request->input('q').'%');
            }

            if(isset($formtask['advertiser'])){
                $advertiser=$advertiser->where('db_m_advertiser.id_adv',$formtask['advertiser']);
            }

            if(isset($formtask['brand'])){
                $advertiser=$advertiser->where('b.id_brand',$formtask['brand']);
            }

            if(isset($formtask['agency'])){
                $advertiser=$advertiser->where('c.id_agcyptu_run',$formtask['agency']);
            }

            if(isset($formtask['am'])){
                $advertiser=$advertiser->leftJoin('db_d_target_account_am as d','d.id_targetaccount','=','c.id_targetaccount')
                    ->where('d.id_am_run',$formtask['am'])
                    ->where('d.id_bu',$idbu);
            }

            if($request->has('page_limit')){
                $pagelimit=$request->input('page_limit');
            }else{
                $pagelimit=10;
            }

            $advertiser=$advertiser->groupBy('db_m_advertiser.id_adv')->paginate($pagelimit);

            if(count($advertiser)>0){
                return $advertiser;
            }else{
                $adv=\App\Models\Intrasm\Advertiser::select('db_m_advertiser.id_adv','db_m_advertiser.id_adv as id','nama_adv as text')
                    ->leftJoin('db_m_product as b','b.id_adv','=','db_m_advertiser.id_adv')
                    ->where('db_m_advertiser.filter',1);

                if($request->has('q')){
                    $adv=$adv->where('nama_adv','like','%'.$request->input('q').'%');
                }

                $adv=$adv->groupBy('db_m_advertiser.id_adv')->paginate(10);

                return $adv;
            }
		} catch (Exception $e) {
			
		}
	}

	public function list_agency(Request $request){
		try {
			$userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;

            $formtask= $request->get('form');

			$agency=\DB::table('db_m_agencypintu as a')
                ->leftJoin('db_d_target_account as b','b.id_agcyptu','=','a.id_agcyptu')
                ->leftJoin('db_m_product as c','c.id_produk','=','b.id_produk')
                ->where('a.filter',1);


            if($request->has('q')){
                $agency=$agency->where('nama_agencypintu','like','%'.$request->input('q').'%');
            }

            if(isset($formtask['advertiser'])){
                $agency=$agency->where('c.id_adv','=',$formtask['advertiser']);
            }

            if(isset($formtask['brand'])){
                $agency=$agency->where('c.id_brand',$formtask['brand']);
            }

            if(isset($formtask['agency'])){
                $agency=$agency->where('b.id_agcyptu_run',$formtask['agency']);
            }

            if(isset($formtask['am'])){
                $agency=$agency->leftJoin('db_d_target_account_am as d','d.id_targetaccount','=','b.id_targetaccount')
                    ->where('d.id_am_run',$formtask['am'])
                    ->where('d.id_bu',$idbu);
            }
            $agency=$agency->select('a.id_agcyptu','a.id_agcyptu as id','a.nama_agencypintu as text','a.id_agcy')
                ->groupBy('a.id_agcyptu');

            $agency=$agency->paginate(10);      

            if(count($agency)>0){
                return $agency;
            }else{
                $agc=\DB::table('db_m_agencypintu as a')
                    ->leftJoin('db_d_target_account as b','b.id_agcyptu','=','a.id_agcyptu')
                    ->leftJoin('db_m_product as c','c.id_produk','=','b.id_produk')
                    ->where('a.filter',1);


                if($request->has('q')){
                    $agc=$agc->where('nama_agencypintu','like','%'.$request->input('q').'%');
                }

                $agc=$agc->select('a.id_agcyptu','a.id_agcyptu as id','a.nama_agencypintu as text','a.id_agcy')
                    ->groupBy('a.id_agcyptu');

                $agc=$agc->paginate(10);      

                return $agc;
            }
			
		} catch (Exception $e) {
			return response($e->getMessage());
		}
	}

	public function list_am(Request $request){
		try {
			$userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $formtask= $request->get('form');
			$depName    = \App\Models\Intrasm\Userdepartment::find($idDepart)->DEPT_NAME;

        	//cek tabel user yang tidak ada di tabel am
        	$am=\DB::table('tbl_am as e')
        	    ->leftJoin('tbl_user as a','a.user_id','e.id_am')
        	    ->leftJoin('db_d_target_account_am as b','b.id_am_run','=','e.id_am')
        	    ->leftJoin('db_d_target_account as c','c.id_targetaccount','=','b.id_targetaccount')
        	    ->where('a.id_bu',$idbu)
        	    ->where('a.active',1)
        	    ->select('e.id_am','e.id_sgm','e.id_sm',\DB::raw("e.id_am as id"),\DB::raw('UPPER(a.user_name) as text'));

	        switch ($idbu) {
	            case '1':
	            case '3':
	            case '5':
	            case '8':
	            case '10':
	                    $am=$am->where('position','AM');
	                    if($request->has('page_limit')){
	                        $pagelimit=$request->input('page_limit');
	                    }else{
	                        $pagelimit=10;
	                    }
	                break;
	            case '2':
	                    switch($depName){
	                        case 'SLS':
	                            $am=$am->where('position','AM');
	                            break;
	                        case 'MKT':
	                            $am=$am->where('position','AM')
	                                ->orWhere('a.id_section',14);
	                            break;
	                        default:
	                            $am=$am->where('position','AM')
	                                ->orWhere('a.id_section',14);
	                            break;
	                    }

	                    $pagelimit=500;
	                break;
	            default:
	                    
	                break;
	        }

	        switch($depName){
	            case 'SLS':
	                switch($posisi){
	                    case 'AM':
	                        $am    = $am->where('e.id_am',$userid);
	                    break;
	                    case 'SGM':
	                        $am    = $am->where('e.id_sgm',$userid);
	                    break;
	                    case 'SM':
	                        $am    = $am->where('e.id_sm',$userid);
	                    break;
	                    default:
	                        $kondisi="";
	                }
	            break;
	            case'MKT':
	                $kondisi="";
	        }

	        if($request->input('q') && $request->input('q')!=null){
	            $am=$am->where('a.user_name','like','%'.$request->input('q').'%');
	        }

	        if(isset($formtask['advertiser'])){
	            $am=$am->leftJoin('db_m_product as d','d.id_produk','=','c.id_produk')
	                ->where('d.id_adv',$formtask['advertiser']);
	        }

	        if(isset($formtask['agency'])){
	            $am=$am->where('c.id_agcyptu_run',$formtask['agency']);
	        }

	        if(isset($formtask['am'])){
	            $am=$am->where('b.id_am_run',$formtask['am']);
	        }

	        if(isset($formtask['ntc'])){
	            $am=$am->where('b.ntc',$formtask['ntc']);
	        }

	        if($request->has('page_limit')){
	            $pagelimit=$request->input('page_limit');
	        }else{
	            $pagelimit=25;
	        }
	        $am=$am->groupBy('user_id')->paginate($pagelimit);  

	        if(count($am)>0){
	            return $am;
	        }else{
	            $allAm=\DB::table('tbl_am as e')
	                ->leftJoin('tbl_user as a','a.user_id','e.id_am')
	                ->leftJoin('db_d_target_account_am as b','b.id_am_run','=','e.id_am')
	                ->leftJoin('db_d_target_account as c','c.id_targetaccount','=','b.id_targetaccount')
	                ->where('a.id_bu',$idbu)
	                ->where('a.active',1)
	                ->where('a.position','AM')
	                ->groupBy('a.user_id')
	                ->select('e.id_am','e.id_sgm','e.id_sm',\DB::raw("e.id_am as id"),\DB::raw('UPPER(a.user_name) as text'));

	            if($request->input('q') && $request->input('q')!=null){
	                $allAm=$allAm->where('a.user_name','like','%'.$request->input('q').'%');
	            }

	            $allAm=$allAm->paginate(100);

	            return $allAm;
	        }
				
		} catch (Exception $e) {
			return response($e->getMessage());
		}
	}

	public function list_client(Request $request){
		try {
			$cariclient="";
        	$reqAdv="";
        	$reqAgc="";
        	$posisi="";

        	$userget = user($request->bearerToken());
            $position= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;

            $formtask= $request->get('form');

	        if($request->has('q')){
	            $cariclient="where lower(data.text) like '%".$request->input('q')."%' or data.text like '%".$request->input('q')."%'";
	        }

	        if($request->has('page_limit')){
	            $limit=$request->input('page_limit');
	        }else{
	            $limit=3;
	        }

	        if($formtask['type']){
	            if($position=='AM'){
	                $posisi.= "and c.id_bu='".$idbu."' and d.ID_AM ='".$userid."'"; 
	            }elseif($position=='SGM'){
	                $posisi.= " and c.id_bu='".$idbu."' and d.ID_SGM ='".$userid."'"; 
	            }elseif($position=='SM'){
	                $posisi.= "and c.id_bu='".$idbu."' and d.ID_SM ='".$userid."'"; 
	            }
	        }
        
	        if($formtask['advertiser']){
	            $reqAdv.="and e.id_adv =".$formtask['advertiser'];
	        }

	        if($formtask['agency']){
	            $reqAgc.="and f.id_agcyptu =".$formtask['agency'];
	        }

	        $client=\DB::select("select data.text, data.type_company, data.id_client_account, data.id_client as id from (select concat(a.firstname,' ',a.lastname,' - ',b.type_company) text,
	            b.type_company, b.id_company, b.id_client_account,a.firstname,
	            a.id_client,e.nama_adv
	             from tbl_userclient a
	            left join tbl_userclient_account b on b.id_client = a.id_client
	            left join tbl_userclient_picsgm c on c.id_client_account = b.id_client_account
	            left join tbl_am d on d.ID_SGM = c.id_sgm
	            left join db_m_advertiser e on e.id_adv = b.id_company
	            left join db_m_agencypintu f on f.id_agcyptu = b.id_company
	            left join tbl_userclient_level g on g.id_client_level = b.id_client_level
	            where a.deleted_at is null and b.active=1 and b.deleted_at is null and c.active=1
	            $posisi
	            and b.type_company='ADV' $reqAdv 
	            union all
	            select concat(a.firstname,' ',a.lastname,' - ',b.type_company) text,
	            b.type_company, b.id_company, b.id_client_account,a.firstname,
	            a.id_client,f.nama_agencypintu
	             from tbl_userclient a
	            left join tbl_userclient_account b on b.id_client = a.id_client
	            left join tbl_userclient_picsgm c on c.id_client_account = b.id_client_account
	            left join tbl_am d on d.ID_SGM = c.id_sgm
	            left join db_m_advertiser e on e.id_adv = b.id_company
	            left join db_m_agencypintu f on f.id_agcyptu = b.id_company
	            left join tbl_userclient_level g on g.id_client_level = b.id_client_level
	            where a.deleted_at is null and b.active=1 and b.deleted_at is null and c.active=1
	            $posisi
	            and b.type_company='AGC' $reqAgc 
	            ) as data $cariclient group by data.id_client_account  order by data.firstname asc limit $limit");

	        return $client;

		} catch (Exception $e) {
			return response($e->getMessage());
		}
	}

	public function show_parameter(Request $request,$id){
		try {

			$userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;

			$param=\App\Models\Sam\Samform::where('id_req_type',$id)
		    	->with('parameter')
		    	->get();

            return response($param,200);
		} catch (Exception $e) {
			return response($e->getMessage());
		}
	}

	public function save_request_concept(Request $request){

        try {

            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $formtask     =$request->get('form');

            $brand        =$formtask['brand'];
            $brandvariant =$formtask['brandvariant'];
            $advertiser   =$formtask['advertiser'];
            $agency       =$formtask['agency'];
            $am           =$formtask['am'];
            $benefit      =$formtask['benefit'];
            $present      =date("YmdHis",strtotime($formtask['present']));
            $req_type     =$formtask['requesttype'];
            $budget       =str_replace(",", "", $formtask['budget']);
            $nilai        =str_replace(",", "", $formtask['nilai']);
            $parameter    =$formtask['parameter'];
            $desc         =$formtask['desc'];
            $idsam        =autoNumberSamMobile($req_type);
            $idactivity   =lastIdActivityMobile();
            $bu           =$idbu;
            $dateNow      =date('YmdHis');

            if($req_type==04 or $req_type==10 or $req_type==11){
                if ($idbu==1 or $idbu==5) {
                    $status = getIdStatus($req_type,'REQUEST');
                } else if($idbu==2){
                    if($posisi=='SGM' or $posisi=='SM'){
                        $status = getIdStatus($req_type,'REQUEST');
                    }else{
                        $status = getIdStatus($req_type,'DRAFT REQUEST');
                    }
                } else {
                    $status = getIdStatus($req_type,'DRAFT REQUEST');
                }
            } 

            $present  =date("YmdHis",strtotime($present));

            if($idbu==1 or $idbu==5){
                $deadline =deadlineCreative($dateNow);
            }elseif($idbu==2){
                if($posisi=='SGM' or $posisi=='SM'){
                    $deadline =deadlineCreative($dateNow);
                }else{
                    $deadline =deadlineApprove($dateNow);
                }
            }else{
                $deadline =deadlineApprove($dateNow);
            }

            $sam=new \App\Models\Mobile\Sam;
            $sam->id_sam        =$idsam;
            $sam->id_req_type   =$req_type;
            $sam->id_status     =$status;
            $sam->start_periode =date('Y-m-d',strtotime($deadline));
            $sam->end_periode   =date('Y-m-d',strtotime($deadline));
            $sam->id_brand      =$brand;
            $sam->brand_variant =$brandvariant;
            $sam->id_apu        =$agency;
            $sam->id_advg       =$advertiser;
            $sam->budget        =$budget;
            $sam->nett          =$budget;
            $sam->pic_am        =$am;
            $sam->deadline      =$present;
            $sam->deadline_mkt  =$deadline;
            $sam->id_bu         =$idbu;
            $sam->insert_user   =$userid;
            $sam->update_user   =$userid;
            $sam->dibaca        ='N';
            $sam->active        ='1';

            switch ($posisi) {
                case 'AM':
                    $amd=\DB::table('tbl_am as a')
                    ->where('a.id_am',$userid)
                    ->select('id_am','id_sgm','id_sm')
                    ->get();

                    if(count($amd)>0){
                        foreach($amd as $row){
                            $vsm   =stripos($row->id_sm,"vacant");
                            $vsgm  =stripos($row->id_sgm,"vacant");

                            if($vsm !== FALSE){
                                $sam->approved_sm = $row->id_sm;
                            }
                            if($vsgm !== FALSE){
                                $sam->approved_sgm = $row->id_sgm;
                            }
                        }
                    }

                break;
                case 'SGM':
                    $sgm=\DB::table('tbl_am as a')
                    ->where('a.id_sgm',$userid)
                    ->select('id_am','id_sgm','id_sm')
                    ->get();

                    if(count($sgm)>0){
                        foreach($sgm as $row){
                            $vsm   =stripos($row->id_sm,"vacant");
                            if($vsm !== FALSE){
                                $sam->approved_sm = $row->id_sm;
                            }
                        }
                    }

                    $sam->approved_sgm = $userid;
                   
                break;
                case 'SM':
                    $sm=\DB::table('tbl_am as a')
                    ->where('a.id_sm',$userid)
                    ->select('id_am','id_sgm','id_sm')
                    ->get();

                    if(count($sm)>0){
                        foreach($sm as $row){
                            $vsgm  =stripos($row->id_sgm,"vacant");
                            if($vsgm !== FALSE){
                                $sam->approved_sm = $row->id_sm;
                            }
                        }
                    }
                    $sam->approved_sm = $userid;
                    
                break;
                default:
                break;
            }

            $simpan =$sam->save();

            $sm=\App\Models\Mobile\Sam::find($idsam);

            if($simpan){
                if(isset($formtask['benefit'])){
                    $benefit=$formtask['benefit'];
                    if(count($benefit)>0){
                        $section="";
                        $nos=0;
                        foreach($benefit as $key=>$val){
                            if($val!="false"){

                                if($nos==0){

                                $section=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                ->where('id_bu',$idbu)
                                ->where('id_benefit',$val)
                                ->first();


                                if($section != null){
                                    $picsection= $section->id_section;
                                }else{
                                    $sections=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                    ->where('id_bu',$idbu)
                                    ->where('id_benefit',0)
                                    ->first();
                                    $picsection= $sections->id_section;
                                }
                                    $s=\App\Models\Mobile\Sam::find($idsam);
                                    $s->pic_section=$picsection;
                                    $s->save();
                                }
                                
                                $nos++;            
                                $ben              =new \App\Models\Mobile\Detailbenefit;
                                // $ben->id_sm      =$sm->id_sm;
                                $ben->id_sam      =$idsam;
                                $ben->id_benefit  =$val;
                                $ben->budget      =$nilai[$val];
                                $ben->id_status   =$status;
                                $ben->insert_user =$userid;
                                $ben->save();
                            }
                        }
                    }
                }

                if(isset($formtask['parameter'])){
                    $parameter=$formtask['parameter'];

                    foreach($parameter as $key=>$val){
                        $param               =new \App\Models\Mobile\Parameterdetail;
                        $param->id_sam       =$idsam;
                        // $param->id_sm        =$sm->id_sm;
                        $param->id_parameter =$key;
                        $param->value        =$val;
                        $param->save();
                    }
                }

                if(isset($formtask['program'])){
                    $pecProg=explode(",", $formtask['program']);
                    if(count($pecProg)>0){
                        foreach($pecProg as $row){
                            $newProgram=new \App\Models\Mobile\Samprogram;
                            $newProgram->id_sam=$idsam;
                            // $newProgram->id_sm=$sm->id_sm;
                            $newProgram->id_program_periode=$row;
                            $newProgram->insert_user=$userid;
                            $newProgram->save();       
                        }
                    }
                }
                
                $attachfile=0;
                $timeWork=0;

                $ac                 =new \App\Models\Mobile\Activity;
                $ac->id_activity    =$idactivity;
                $ac->id_status      =$status;
                $ac->id_sam         =$idsam;
                $ac->id_attach_file =$attachfile;
                $ac->description    =$desc;
                $ac->insert_user    =$userid;
                $ac->time_to_work   =$timeWork;
                $ac->deadline       =$deadline;
                $ac->id_bu          =$idbu;
                $ac->save();

                $app=\App\Models\Mobile\Sam::find($idsam);
                $approved_sgm=$app->approved_sgm; 
                $approved_sm=$app->approved_sm;

                switch ($idbu) {
                    case '1':
                        $st=\App\Models\Mobile\Sam::find($idsam);
                        $st->type      ='REQUEST';
                        $st->save();

                    break;
                    case '2':
                        $st=\App\Models\Mobile\Sam::find($idsam);

                        if($approved_sgm != null or $approved_sm != null){
                            $st->type ='REQUEST';
                        }else{
                            $st->type ='DRAFT REQUEST';
                        }
                    $st->save();

                    break;
                    default:
                        $st=\App\Models\Mobile\Sam::find($idsam);
                        $st->type      ='DRAFT REQUEST';
                        $st->save();
                    break;
                }

                if($request->hasFile('file')){

                    $idFileType=getIdFiletype($req_type,$status);
                    $file=$request->file('file');

                    foreach($file as $key=>$val){
                        if(!is_dir('uploads/mobile/'.$idFileType)){
                            mkdir('uploads/mobile/'.$idFileType, 0777, TRUE);
                        }

                        $folder="uploads/mobile/".$idFileType."/";
                        $filename=$val->getClientOriginalName();
                        $destinationPath="uploads/mobile/".$idFileType."/";
                        $val->move($destinationPath,$filename);

                        $idFile="REG".$key."".$dateNow;

                        $f=new \App\Models\Mobile\File;
                        $f->id_sam_file=$idFile;
                        $f->title=$filename;
                        $f->id_activity=$idactivity;
                        $f->nama_file=addslashes($filename);
                        $f->insert_user=$userid;
                        $f->id_filetype=$idFileType;
                        $f->id_bu=$idbu;
                        $f->save();

                        $attachfile=1;
                    }

                    $act=\App\Models\Mobile\Activity::find($idactivity);
                    $act->id_attach_file=$attachfile;
                    $act->save();
                }

                if($request->has('cc')){
                    $listCc=$request->input('cc');

                    $pecah=explode(",", $listCc);

                    if(count($pecah)>0){
                        foreach($pecah as $row){
                            $row=trim($row);
                        }
                    }
                }

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data berhasil disimpan',
                    'pesanEmail'=>'',
                    'error'=>''
                    );
            }else{
                $data=array(
                    'success'=>false,
                    'pesan'=>'Gagal menyimpan sam',
                    'pesanEmail'=>'',
                    'error'=>''
                    );
            }
        
        } catch (Exception $e) {
            return response($e->getMessage());
        }
           
    }

    public function detail_brief(Request $request,$idsam){
        
        try {

            $sam=\App\Models\Sam\Sam::with(
                'detailbenefit',
                'pic_creative',
                'samprogram',
                'samprogram.program',
                'brand',
                'advertiser',
                'agency',
                'am',
                'activity',
                'parameters',
                'activity.file',
                'approveSgm',
                'approveSm',
                'approveMc'
            )
            ->select(
                'id_sam',
                'id_req_type',
                'created_at',
                'end_periode',
                'pic_section',
                'id_brand',
                'id_advg',
                'id_apu',
                'brand_variant',
                'budget',
                'pic_am',
                'deadline_mkt',
                'deadline',
                'approved_sgm',
                'approved_sm',
                'approved_mc')->find($idsam);  
        
            return $sam;
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function get_approve(Request $request,$idsam){
        try {
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $formtask     =$request->get('form');

            $dateNow=date('YmdHis');

            //ambil data2 yang ada di local
            $sam          =\App\Models\Sam\Sam::find($idsam);
            $idsam        = $sam->id_sam;
            $req_type     = $sam->id_req_type;
            $approved_sgm = $sam->approved_sgm;
            $approved_sm  = $sam->approved_sm;
            $approved_mc  = $sam->approved_mc;
            $periode      = $sam->deadline;
            
            //klasifikasi berdasarkan type
            $type =isset($formtask['type']);
            
            if($type=="approve"){
                $deadlineapp =deadlineApprove($dateNow);
                $deadpresent = date("YmdHis",strtotime($formtask['present']));
                $present     = deadlineCreative($dateNow);

                $budget       =str_replace(",", "", $formtask['budget']);
                $brandvariant =$formtask['brandvariant'];

                $sm                =new \App\Models\Mobile\Sam;
                $sm->id_sam        =$idsam;
                $sm->id_req_type   =$req_type;
                $sm->brand_variant =$brandvariant;
                $sm->start_periode =date('Y-m-d',strtotime($periode));
                $sm->end_periode   =date('Y-m-d',strtotime($periode));
                $sm->approved_sm   =$formtask['sm_approve'];
                $sm->approved_sgm  =$formtask['sgm_approve'];
                $sm->approved_mc   =$formtask['mc_approve'];

                switch ($idbu) {
                    case '1':
                    case '5':
                        if ($req_type==04) {
                            $sm->type   ='REQUEST';
                            $sm->id_status = 82;
                        }else {
                            $sm->type   ='REQUEST';
                            $sm->id_status = 93;
                        }
                        $sm->deadline      =$present;
                        $sm->deadline_mkt  =$present;
                    break;
                    case '3':
                        if ($req_type==04) {
                            if ($formtask['sm_approve']!= null and $formtask['sgm_approve'] != null) {
                                $sm->type   ='REQUEST';
                                $sm->id_status = 82;
                                $sm->deadline      =$present;
                                $sm->deadline_mkt  =$present;
                            } else {
                                $sm->type      ='APPROVED';
                                $sm->id_status = 21;
                                $sm->deadline      =$deadlineapp;
                                $sm->deadline_mkt  =$deadpresent;
                            }
                        } else {
                            if ($formtask['sm_approve']!= null and $formtask['sgm_approve']!= null) {
                                $sm->type   ='REQUEST';
                                $sm->id_status = 93;
                                $sm->deadline      =$present;
                                $sm->deadline_mkt  =$present;
                            } else {
                                $sm->type      ='APPROVED';
                                $sm->id_status = 140;
                                $sm->deadline      =$deadlineapp;
                                $sm->deadline_mkt  =$deadpresent;
                            }
                        }
                    break;
                    case '2':
                        if ($req_type==04) {
                           
                            if($posisi =='SGM' or $posisi =='SM'){
                                $sm->type   ='REQUEST';
                                $sm->id_status = 82;
                                $sm->deadline      =$present;
                                $sm->deadline_mkt  =$present;
                            } else{
                                $sm->type      ='APPROVED';
                                $sm->id_status = 21;
                                $sm->deadline      =$deadlineapp;
                                $sm->deadline_mkt  =$deadpresent;
                            }

                        } else {
                            if($posisi =='SGM' or $posisi =='SM'){
                                $sm->type   ='REQUEST';
                                $sm->id_status = 93;
                                $sm->deadline      =$present;
                                $sm->deadline_mkt  =$present;
                            } else{
                                $sm->type      ='APPROVED';
                                $sm->id_status = 140;
                                $sm->deadline      =$deadlineapp;
                                $sm->deadline_mkt  =$deadpresent;
                            }
                        }
                    break;
                    case '8':
                       if ($req_type==04) {
                            if ($formtask['sm_approve']!= null or $formtask['sgm_approve']!= null) {
                                $sm->type   ='REQUEST';
                                $sm->id_status = 82;
                                $sm->deadline      =$present;
                                $sm->deadline_mkt  =$present;
                            } else {
                                $sm->type      ='APPROVED';
                                $sm->id_status = 21;
                                $sm->deadline      =$deadlineapp;
                                $sm->deadline_mkt  =$deadpresent;
                            }
                        } else {
                            if ($formtask['sm_approve']!= null or $formtask['sgm_approve']!= null) {
                                $sm->type   ='REQUEST';
                                $sm->id_status = 93;
                                $sm->deadline      =$present;
                                $sm->deadline_mkt  =$present;
                            } else {
                                $sm->type      ='APPROVED';
                                $sm->id_status = 140;
                                $sm->deadline      =$deadlineapp;
                                $sm->deadline_mkt  =$deadpresent;
                            }
                        }
                    break;
                    default:
                        # code...
                    break;
                }

                switch ($posisi) {
                    case 'SGM':
                        $sm->approved_sgm = $userid;
                    break;
                    case 'SM':
                        $sm->approved_sm = $userid;
                    break;
                    default:
                        $sm->approved_mc = $userid;
                    break;
                }
                

                if(isset($formtask['brand'])){
                    $sm->id_brand=$formtask['brand'];
                }

                if(isset($formtask['advertiser'])){
                    $sm->id_advg=$formtask['advertiser'];
                }

                if(isset($formtask['agency'])){
                    $sm->id_apu=$formtask['agency'];
                }

                if(isset($formtask['am'])){
                    $sm->pic_am=$formtask['am'];
                }

                $sm->budget        =$budget;
                $sm->nett          =$budget;
                $sm->approved_by   =$userid;
                $sm->approved_date =date('Y-m-d H:i:s');
                $sm->insert_user   =$userid;
                $sm->update_user   =$userid;
                $sm->id_bu         =$idbu;
                $sm->dibaca        ='N';
                $sm->active        ='1';

                if(isset($formtask['benefit'])){
                    $benefit=$formtask['benefit'];
                    $nilai  =str_replace(",", "", $formtask['nilai']);

                    if(count($benefit)>0){
                        $section="";
                        $nos=0;
                        foreach($benefit as $key=>$val){
                            if($val!="false"){

                                if($nos==0){
                                    $section=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                    ->where('id_bu',$idbu)
                                    ->where('id_benefit',$val)
                                    ->first();

                                    if($section != null){
                                        $picsection= $section->id_section;
                                    }else{
                                        $sections=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                        ->where('id_bu',$idbu)
                                        ->where('id_benefit',0)
                                        ->first();
                                        $picsection= $sections->id_section;
                                    }
                                        $sm->pic_section=$picsection;
                                }
                                
                                $nos++;            
                                $ben              =new \App\Models\Mobile\Detailbenefit;
                                $ben->id_sam      =$idsam;
                                $ben->id_status   =$sm->id_status;
                                $ben->id_benefit  =$val;
                                $ben->budget      =$nilai[$val];

                                $ben->insert_user =$userid;
                                $ben->save();
                            }
                        }
                    }
                }

                $simpan =$sm->save();

                $idactivity  =lastIdActivityMobile();
                $description =$formtask['desc'];

                if(isset($formtask['parameter'])){
                    $parameter=$formtask['parameter'];

                    foreach($parameter as $key=>$val){
                        $param               =new \App\Models\Mobile\Parameterdetail;
                        $param->id_sam       =$idsam;
                        $param->id_parameter =$key;
                        $param->value        =$val;
                        $param->save();
                    }
                }
        
                if(isset($formtask['program'])){
                    $program=$formtask['program'];
                    foreach($program as $key=>$val){
                        \DB::table('mobile_sam_program')
                            ->insert(
                                [
                                    'id_sam'             =>$idsam,
                                    // 'id_sm'             =>$idactivity,
                                    'id_program_periode' =>$val,
                                    'update_user'        =>$userid,
                                    'updated_at'         =>date('Y-m-d H:i:s')
                                ]
                            );
                    }
                }

                $timeWork   =0;

                $newActivity            =new \App\Models\Mobile\Activity;
                $newActivity->id_sam    =$idsam;
                $newActivity->id_status =$sm->id_status;

                if($sm->id_status==21 or $sm->id_status==140 ){
                    $newActivity->deadline    =$deadlineapp;
                } else{
                    $newActivity->deadline    =$present;
                }
                $newActivity->id_sam       =$idsam;
                $newActivity->id_activity  =$idactivity;
                $newActivity->description  =$description;
                $newActivity->time_to_work =$timeWork;
                $newActivity->insert_user  =$userid;
                $newActivity->update_user  =$userid;
                $newActivity->id_bu        =$idbu;
                $newActivity->save();

            }else if($type=="notapprove"){
                
                $present      =date("YmdHis",strtotime($formtask['present']));
                $budget       =str_replace(",", "", $formtask['budget']);
                $brandvariant =$formtask['brandvariant'];

                $sm                =new \App\Models\Mobile\Sam;
                $sm->type          ='REQUEST';
                $sm->id_sam        =$idsam;
                $sm->id_req_type   =$req_type;
                $sm->id_status     = 83;
                $sm->brand_variant =$brandvariant;
                $sm->start_periode =date('Y-m-d',strtotime($present));
                $sm->end_periode   =date('Y-m-d',strtotime($present));
                $sm->deadline      =$present;
                $sm->deadline_mkt  =$present;
                
                if(isset($formtask['brand'])){
                    $sm->id_brand=$formtask['brand'];
                } 

                if(isset($formtask['advertiser'])){
                    $sm->id_advg=$formtask['advertiser'];
                }

                if(isset($formtask['agency'])){
                    $sm->id_apu=$formtask['agency'];
                }

                if(isset($formtask['am'])){
                    $sm->pic_am=$formtask['am'];
                }

                $sm->budget        =$budget;
                $sm->nett          =$budget;
                $sm->approved_by   =$userid;
                $sm->approved_date =date('Y-m-d H:i:s');
                $sm->insert_user   =$userid;
                $sm->update_user   =$userid;
                $sm->id_bu         =$idbu;
                $sm->dibaca        ='N';
                $sm->active        ='1';
                $simpan            =$sm->save();

                $id_status   = $formtask['idstatus'];
                $idactivity  =lastIdActivityMobile();
                $status      =$id_status;
                $description =$formtask['desc'];

        
                if(isset($formtask['parameter'])){
                    $parameter=$formtask['parameter'];

                    foreach($parameter as $key=>$val){
                        $param               =new \App\Models\Mobile\Parameterdetail;
                        $param->id_sam       =$idsam;
                        // $param->id_sm        =$idactivity;
                        $param->id_parameter =$key;
                        $param->value        =$val;
                        $param->save();
                    }
                }
        

                if(isset($formtask['program'])){
                    $program=$formtask['program'];

                    foreach($program as $key=>$val){
                        \DB::table('mobile_sam_program')
                            ->insert(
                                [
                                    'id_sam'             =>$idsam,
                                    // 'id_sm'             =>$idactivity,
                                    'id_program_periode' =>$val,
                                    'update_user'        =>$userid,
                                    'updated_at'         =>date('Y-m-d H:i:s')
                                ]
                            );
                    }
                }

                $timeWork   =0;

                $newActivity              =new \App\Models\Mobile\Activity;
                $newActivity->id_sam      =$idsam;
                $newActivity->id_activity=$idactivity;
                $newActivity->id_status   =82;
                $newActivity->description =$description;
                $newActivity->time_to_work =$timeWork;
                $newActivity->deadline    =$present;
                $newActivity->insert_user =$userid;
                $newActivity->update_user =$userid;
                $newActivity->id_bu       =$idbu;
                $newActivity->save();

                

                if(isset($formtask['file'])){
                    $email['lampirans']=array();
                    $lampirans=array();
                    $files=$formtask['file'];

                    $idFileType="1";                    

                    foreach($files as $key=>$val){
                        if(!is_dir('uploads/mobile/'.$idFileType)){
                            mkdir('uploads/mobile/'.$idFileType,0777, TRUE);
                        }

                        $idFile="REV".$key."0".$dateNow;
                        $folder="uploads/mobile/".$idFileType."/";
                        $filename=trim(str_replace(" ","_",$val->getClientOriginalName()));
                        $destinationPath="uploads/mobile/".$idFileType."/";
                        $val->move($destinationPath,$filename);

                        $lampirans[]=$destinationPath."/".$filename;

                        $newFile=new \App\Models\Mobile\File;
                        $newFile->id_sam_file=$idFile;
                        $newFile->id_activity=$idactivity;
                        //$newFile->id_status=$request->input('status');
                        $newFile->title=$filename;
                        $newFile->nama_file=$filename;
                        $newFile->insert_user=$userid;
                        $newFile->id_filetype=$idFileType;
                        $newFile->id_bu=$idbu;
                        $newFile->save();
                    }

                    $newActivity->id_attach_file='1';

                    $email['lampirans']=$lampirans;
                }


                if(isset($formtask['benefit'])){

                    $benefit=$formtask['benefit'];
                    $nilai  =str_replace(",", "", $formtask['nilai']);

                    if(count($benefit)>0){
                        $section="";
                        $nos=0;
                        foreach($benefit as $key=>$val){
                            if($val!="false"){

                                if($nos==0){
                                    $section=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                    ->where('id_bu',$idbu)
                                    ->where('id_benefit',$val)
                                    ->first();

                                    if($section != null){
                                        $picsection= $section->id_section;
                                    }else{
                                        $sections=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                        ->where('id_bu',$idbu)
                                        ->where('id_benefit',0)
                                        ->first();
                                        $picsection= $sections->id_section;
                                    }

                                    $s=\App\Models\Mobile\Sam::find($idsam);
                                    $s->pic_section=$picsection;
                                    $s->save();
                                }
                                
                                $nos++;            
                                $ben              =new \App\Models\Mobile\Detailbenefit;
                                $ben->id_sam      =$idsam;
                                // $ben->id_sm       =$idactivity;
                                $ben->id_status   =82;
                                $ben->id_benefit  =$val;
                                $ben->budget      =$nilai[$val];

                                $ben->insert_user =$userid;
                                $ben->save();
                            }
                        }
                    }
                }


            }else{
                echo "eror";
            }

            if($request->hasFile('file')){
                $email['lampirans']=array();
                $lampirans=array();
                $files=$request->file('file');
                $idFileType="1";                    

                foreach($files as $key=>$val){
                    if(!is_dir('uploads/mobile/'.$idFileType)){
                        mkdir('uploads/mobile/'.$idFileType,0777, TRUE);
                    }

                    $idFile="REV".$key."0".$dateNow;
                    $folder="uploads/mobile/".$idFileType."/";
                    $filename=trim(str_replace(" ","_",$val->getClientOriginalName()));
                    $destinationPath="uploads/mobile/".$idFileType."/";
                    $val->move($destinationPath,$filename);

                    $lampirans[]=$destinationPath."/".$filename;

                    $newFile=new \App\Models\Mobile\File;
                    $newFile->id_sam_file=$idFile;
                    $newFile->id_activity=$idactivity;
                    $newFile->title=$filename;
                    $newFile->nama_file=$filename;
                    $newFile->insert_user=$userid;
                    $newFile->id_filetype=$idFileType;
                    $newFile->id_bu=$idbu;
                    $newFile->save();
                }

                $newActivity->id_attach_file='1';
                $email['lampirans']=$lampirans;

            } else {
                $email['lampirans']=array();
                $lampirans=array(); 
                if(count($sam->activity)>0){
                    foreach($sam->activity as $row){
                        if(count($row->file)>0){
                            foreach($row->file as $key){
                                $destinationPath="uploads/local/".$key->id_filetype;
                                $lampirans[]=$destinationPath."/".$key->nama_file;
                            }
                        }
                    }
                    $email['lampirans']=$lampirans;
                }
            }

            
            $data=array(
                'success' =>true,
                'pesan'   =>'Berhasil',
                'error'   =>''
            );


            return $data;
        } catch (Exception $e) {
            
        }
    }

    public function sam_by_id(Request $request, $id){
        try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $user     =\Auth::user();
            $depName  =\App\Models\Userdepartment::find($idDepart)->DEPT_NAME;
            $sam=\App\Models\Sam\Sam::with('brand',
                'agency',
                'advertiser',
                'section',
                'picsam',
                'am',
                'tblam',
                'samprogram',
                'samprogram.program',
                'request',
                'subrequest',
                'status',
                'statusprogress',
                'parameters',
                'activity',
                'activity.status',
                'activity.user',
                'activity.file',
                'activity.user.departement',
                'insertByUser',
                'benefit')
            ->find($id);

            $periode=periode($sam->start_periode,$sam->end_periode);

            $sam->periode=$periode;

            return $sam;
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function detail_parameter(Request $request,$id){

        try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $sam=\App\Models\Sam\Sam::with('parameters')
            ->where('id_bu',$idbu)
            ->where('id_sam','=',$id)
            ->first();

            return $sam;
        } catch (Exception $e) {
            return response($e->getMessage());
            
        }
        
    }

    public function activity_by_id(Request $request, $id){
        try {
            $activity=\App\Models\Sam\Activity::with('file','file.parameters','user')
                ->find($id);
            return $activity;
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function update_sam(Request $request,$id){
        try {
            $userget = user($request->bearerToken());
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $formtask     =$request->get('form');

            $sam=\App\Models\Sam\Sam::find($id);
            $req_type = $sam->id_req_type;
            $status = $sam->id_status;

            if(isset($formtask['brand'])){
                $sam->id_brand=$formtask['brand'];
            }

            if(isset($formtask['advertiser'])){
                $sam->id_advg=$formtask['advertiser'];
            }

            if(isset($formtask['agency'])){
                $sam->id_apu=$formtask['agency'];
            }

            if(isset($formtask['am'])){
                $sam->pic_am=$formtask['am'];
            }
           
            if(isset($formtask['budget'])){
                $sam->budget=str_replace(",", "", $formtask['budget']);
            }

            if(isset($formtask['start_periode'])){
                $sam->start_periode=isset($formtask['start_periode']);
            }

            if(isset($formtask['end_periode'])){
                $sam->end_periode=isset($formtask['end_periode']);
            }

            if(isset($formtask['program'])){

                $program=$formtask['program'];
                \DB::table('sam_program')
                    ->where('id_sam',$id)
                    ->delete();

                foreach($program as $key=>$val){
                    \DB::table('sam_program')
                        ->insert(
                            [
                                'id_sam'             =>$id,
                                'id_program_periode' =>$val,
                                'update_user'        =>$userid,
                                'updated_at'         =>date('Y-m-d H:i:s')
                            ]
                        );
                }
            }

            if(isset($formtask['benefit'])){
                $benefit =$formtask['benefit'];
                $nilai   =str_replace(",", "", $formtask['nilai']);
                $cost    =str_replace(",", "", $formtask['cost']);
                $budget_mkt    =str_replace(",", "", $formtask['budget_mkt']);

                \DB::table('sam_detail_benefit')
                        ->where('id_sam',$id)
                        ->delete();

                if(count($benefit)>0){
                    $section="";
                    $nos=0;
                    foreach($benefit as $key=>$val){
                        if($val!="false"){
                            if($nos==0){
                                $section=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                    ->where('id_bu',$idbu)
                                    ->where('id_benefit',$val)
                                    ->first();

                                if($section != null){
                                    $picsection= $section->id_section;
                                }else{
                                    $sections=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                        ->where('id_bu',$idbu)
                                        ->where('id_benefit',0)
                                        ->first();
                                        $picsection= $sections->id_section;
                                }
                                $s=Sam::find($id);
                                $s->pic_section=$picsection;
                                $s->save();
                            }
                                
                            $nos++;            
                            $ben              =new \App\Models\Sam\Detailbenefit;
                            $ben->id_sam      =$id;
                            $ben->id_benefit  =$val;
                            $ben->budget      =$nilai[$val];
                            $ben->budget_mkt  =$budget_mkt[$val];
                            $ben->cost        =$cost[$val];
                            $ben->id_status   =$status;
                            $ben->insert_user =$userid;
                            $ben->save();
                        }
                    }
                }
            }

            if(isset($formtask['parameter'])){ 
                $parameter=$request->input('parameter');
                foreach($parameter as $key=>$val){
                    \App\Models\Sam\Parameterdetail::where('id_sam',$id)
                        ->where('id_parameter',$key)
                        ->update(
                            [
                                'value'=>$val
                            ]
                        );
                }
            }

            $simpan=$sam->save();

            if($simpan){
                $data=array(
                    'success' =>true,
                    'pesan'   =>'Data berhasil diupdate',
                    'error'   =>''
                );
            }else{
                $data=array(
                    'success' =>false,
                    'pesan'   =>'Data gagal disimpan',
                    'error'   =>''
                );
            }

            return $data;
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function status_progress_upload_concept(Request $request,$req){
        try {

            if(isset($formtask['idsam'])){
                $sam=\App\Models\Sam\Sam::find($formtask['idsam']);

                $data=array();
                $status=\App\Models\Sam\Status::where('id_req_type',$req)
                    ->where('dept_status','MKT')
                    ->where('relasi_status',2)
                    ->where('stat','PROGRESS')
                    ->orWhere('type_status',6)
                    ->where('id_req_type',$req)
                    ->orderBy('relasi_status')
                    ->select('id_status','nama_status')
                    ->get();

                $ada="N";
                foreach($status as $row){
                    if($row->id_status==$sam->id_status_progress){
                        $ada="Y";
                    }else{
                        if( $row->id_status==$sam->id_status){
                            $ada='Y';
                        } else {
                            $ada="N";
                        }
                    }
                    $data[]=array(
                        'id_status'=>$row->id_status,
                        'nama_status'=>$row->nama_status,
                        'ada'=>$ada
                    );
                }
                return $data;
            }
            $status=\App\Models\Sam\Status::where('id_req_type',$req)
                    ->where('dept_status','MKT')
                    ->where('relasi_status',1)
                    ->where('stat','PROGRESS')
                    ->orWhere('type_status',6)
                    ->where('id_req_type',$req)
                    ->orderBy('relasi_status')
                    ->select('id_status','nama_status')
                    ->get();

            return $status;
                
        } catch (Exception $e) {
            return response($e->getMessage());
        }    
    }

    public function update_status_progress(Request $request, $id){
        try {
            
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $formtask     =$request->get('form');

            $dateNow=date('YmdHis');
            $status=$formtask['status'];
            $idactivity=lastIdActivity();
            $idrefactivity=refIdActivity($id,'SLS');

            $email            =array();
            $email['attach']  =array();
            $email['desc']    ="";
            $email['id_file'] ="";
            $cc=array();
            $to=array();
            
            $sam          =\App\Models\Sam\Sam::find($id);
            $sekarang     =$sam->updated_at;
            $brandvariant = $sam->brand_variant;
            
            switch ($status) {
                case '85':
                case '98':
                case '105':
                    //non aktif
                    $sam->id_status          =$status;
                    $sam->id_status_progress =null;
                    $sam->update_active      =$userid;
                    $sam->active             ='0';
                break;
                case '87':
                case '99':
                case '106':
                    //aktif
                    $sam->id_status          =$status;
                    $sam->id_status_progress =null;
                    $sam->update_active      =$userid;
                    $sam->active             ='1';
                break;
                case '86':
                case '139':
                case '143':
                    // reject
                    $sam->id_status          =$status;
                    $sam->id_status_progress =null;

                    if(isset($formtask['concept'])){
                        $email['id_file']=$formtask['concept'];
                    }
                break;
                case '136':
                case '141':
                    //revisi hold
                    $sam->id_status_progress =$status;
                    $sam->deadline           =deadlineCreative($dateNow);
                    $sam->deadline_mkt       =deadlineCreative($dateNow);
                break;
                case '37':
                case '97':
                case '145':
                    $sam->id_status_progress=$status;
                break;
                default:
                    $sam->id_status_progress=$status;
                break;
            }

            $sam->update_progress_user =$userid;
            $sam->update_user          =$userid;
            $sam->updated_at           =$sekarang;
            $simpan                    =$sam->save();

            if($simpan){
                $newActivity         =new \App\Models\Sam\Activity;
                $newActivity->id_activity=$idactivity;
                $newActivity->id_ref_activity=$idrefactivity;
                $newActivity->id_sam =$id;

                if(isset($formtask['desc'])){
                    $newActivity->id_status   =$status;
                    $newActivity->description =$formtask['desc'];
                    $email['desc']            =$formtask['desc'];
                }else{
                    $newActivity->id_status   =$status;
                }

                $newActivity->insert_user  =$userid;
                $newActivity->time_to_work =0;
                $newActivity->id_bu        =$idbu;
                $newActivity->save();

                $stat=\App\Models\Sam\Status::find($formtask['status']);

                $email['status2'] ="UPDATE";
                $email['id_bu']   =$idbu;
                $email['id_status_progress']=$sam->id_status_progress;

                /* konfigurasi email */
                switch ($sam->id_req_type) {
                    case '4':
                        $reqtypename="FC";

                        $programs="";
                        $i=0;
                        $len = count($sam->samprogram);
                        if(count($sam->samprogram)>0){
                            foreach($sam->samprogram as $row){
                            $prog=\App\Models\Portal\Programperiode::select('id_program_periode','id_program')
                                                ->with('program')->find($row->id_program_periode);
                                if ($i == 0 && $len>1) {
                                    $programs.=$prog->program->program_name." - ";
                                } else if ($i == $len - 1) {
                                    $programs.="".$prog->program->program_name;
                                }else{
                                    $programs.=$prog->program->program_name;
                                }
                                $i++;
                            }
                        }

                        $email['subject']="SAM-".$stat->nama_status." ".$reqtypename." ".$programs." ".$brandvariant;
                    break;
                    case '10':
                        $reqtypename="TVDAY";

                        $programs="";
                        $i=0;
                        $len = count($sam->samprogram);
                        if(count($sam->samprogram)>0){
                            foreach($sam->samprogram as $row){
                            $prog=\App\Models\Portal\Programperiode::select('id_program_periode','id_program')
                                                ->with('program')->find($row->id_program_periode);
                                if ($i == 0 && $len>1) {
                                    $programs.=$prog->program->program_name." - ";
                                } else if ($i == $len - 1) {
                                    $programs.="".$prog->program->program_name;
                                }else{
                                    $programs.=$prog->program->program_name;
                                }
                                $i++;
                            }
                        }

                        $email['subject']="SAM-".$stat->nama_status." ".$reqtypename." ".$programs." ".$brandvariant;
                    break;
                    case '11':
                        $reqtypename="AMPLIFY";

                        $programs="";
                        $i=0;
                        $len = count($sam->samprogram);
                        if(count($sam->samprogram)>0){
                            foreach($sam->samprogram as $row){
                            $prog=\App\Models\Portal\Programperiode::select('id_program_periode','id_program')
                                                ->with('program')->find($row->id_program_periode);
                                if ($i == 0 && $len>1) {
                                    $programs.=$prog->program->program_name." - ";
                                } else if ($i == $len - 1) {
                                    $programs.="".$prog->program->program_name;
                                }else{
                                    $programs.=$prog->program->program_name;
                                }
                                $i++;
                            }
                        }

                        $email['subject']="SAM-".$stat->nama_status." ".$reqtypename." ".$brandvariant;
                    break;
                    default:
                        # code...
                        $email['subject']="";
                    break;
                }

                $email['statusList']=\App\Models\Sam\Status::where('id_req_type',$sam->id_req_type)
                        ->where('stat','STATUS')
                        ->where('relasi_status',3)
                        ->where('type_status','<>',7)
                        ->get();

                $req_type=$sam->id_req_type;
                $email['idsam']=$sam->id_sam;
                $email['idreqtype']=$sam->id_req_type;

                
                $toSales=\DB::table('tbl_am as a')
                    ->where('a.id_am',$sam->pic_am)
                    ->select('id_am','id_sgm','id_sm','id_gm')
                    ->get();

                if(count($toSales)>0){
                    foreach($toSales as $row){
                        if(!strstr($row->id_gm,'vacant') && $row->id_gm!=""){
                            array_push($to, $row->id_gm);
                        }

                        if(!strstr($row->id_sm,'vacant') && $row->id_sm!=""){
                            array_push($to,$row->id_sm);
                        }

                        if(!strstr($row->id_sgm,'vacant') && $row->id_sgm!=""){
                            array_push($to,$row->id_sgm);
                        }

                        if(!strstr($row->id_am,'vacant') && $row->id_am!=""){
                            array_push($to,$row->id_am);
                        }
                    }
                }

                $ccBenefit=\App\Models\Sam\Sam::leftJoin('sam_detail_benefit as b','b.id_sam','=','sam.id_sam')
                        ->leftJoin('sam_benefit_section as c','c.id_benefit','=','b.id_benefit') 
                        ->leftJoin('tbl_user as d','d.ID_SECTION','=','c.id_section') 
                        ->where('sam.id_sam',$sam->id_sam)
                        ->where('c.id_bu',$idbu)
                        ->where('d.active',1)
                        ->where('d.true_email','=','Y')
                        ->whereNotNull('c.id_section')
                        ->select('b.id_benefit','c.id_section','d.user_id')
                        ->groupBy('d.user_id')
                        ->get();

                        foreach ($ccBenefit as $key => $value) {
                            array_push($cc,$value->user_id);
                        }

                /*--------------email by type request---------*/ 
                
                $ccMkt=\App\Models\Sam\Picsection::leftJoin('tbl_user as b','b.id_section','=','sam_benefit_section.id_section')
                        ->where('id_req_type',$sam->id_req_type)
                        ->where('sam_benefit_section.id_bu',$idbu)
                        ->where('b.active',1)
                        ->where('b.true_email','=','Y')
                        ->where('id_benefit',0)
                        ->orderBy('b.position','asc')
                        ->select('b.user_id','b.id_section','b.id_bu')
                        ->get();    
                

                foreach ($ccMkt as $key => $value) {
                    array_push($cc,$value->user_id);
                }
                /*--------------email by type request---------*/ 
                if($idbu == 1) {
                    array_push($cc,'firdauzi.cece@mncgroup.com');
                    array_push($cc,'ivan.faisal@mncgroup.com');
                    array_push($cc,'zulfan.usuluddin@mncgroup.com');
                    array_push($cc,'yeni.susanti@mncgroup.com');
                    array_push($cc,'annisa.fildzah@mncgroup.com');
                }
                array_push($cc,'mujib.nashikha@mncgroup.com','jamal.apriadi@mncgroup.com','jaenudin.fawwaz@mncgroup.com');

                // array_push($cc,$userid);
                $email['to']=$to;
                $email['cc']=$cc;
                $email['from']=$userid;
                $email['fromname']=ucwords(strtolower($userget->USER_NAME));


                $pesanEmail="";
                \Mail::send('sam.concept.email.update_status_progress',$email,function($mail) use($email){
                    $mail->from($email['from'],$email['fromname']);
                    $mail->to($email['to'])->cc($email['cc']);

                    $mail->subject($email['subject']);
                });

                if(count(\Mail::failures())>0){
                    $pesanEmail="Email Not Send";
                    $pesanEmail.="<ul>";
                    foreach(Mail::failures as $email_address){
                        $pesanEmail.="<li>".$email_address."</li>";
                    }
                    $pesanEmail="</ul>";
                }else{
                    $pesanEmail="Email berhasil dikirim";
                }

                /* end konfigurasi email */
                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil terkirim',
                    'error'=>'',
                    'pesanEmail'=>$pesanEmail
                );

            } else {
                $data=array(
                    'success' =>false,
                    'pesan'   =>'Data tidak terupdate',
                    'error'   =>''
                );
            }

            return $data;
        
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function update_status_progress_status(Request $request,$req){
        try {
            
            $status=\App\Models\Sam\Status::where('id_req_type',$req)
            ->where('relasi_status',3)
            ->where('type_status',11)
            ->orderBy('relasi_status')
            ->get();

            return $status;
        } catch (Exception $e) {
            return response($e->getMessage());
        }
       
    }

    public function update_request_mobile(Request $request,$id){
        try {
            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $formtask     =$request->get('form');

            $dateNow=date('YmdHis');
            $email=array();
            $cc=array();

            $sam=\App\Models\Sam\Sam::findOrFail($id);

            $req_type = $sam->id_req_type;
            $status    =$formtask['status'];


            $newSam=new \App\Models\Mobile\Sam;
            $newSam->id_sam=$id;
            $newSam->id_req_type=$sam->id_req_type;
            $newSam->type="REQUEST";
            $newSam->id_status=$formtask['status'];
            $newSam->deadline=deadlineCreative($dateNow);
            $newSam->deadline_mkt=deadlineCreative($dateNow);
            $newSam->dibaca="N";
            $newSam->id_brand =$formtask['brand'];
            $newSam->brand_variant =$formtask['brandvariant'];
            $newSam->id_advg =$formtask['advertiser'];
            $newSam->id_apu =$formtask['agency'];
            $newSam->budget =$formtask['budget'];
            $newSam->approved_by=$userid;
            $newSam->insert_user=$userid;
            $newSam->update_user=$userid;
            $newSam->pic_am=$sam->pic_am;
            $newSam->id_bu=$idbu;

            $simpan=$newSam->save();

            if($simpan){

                $idactivity=lastIdActivityMobile();
                $idFileType="1";                    

                $activity=\App\Models\Sam\Activity::leftJoin('sam_status as b','b.id_status','=','sam_activity.id_status')
                    ->select('sam_activity.id_sam','sam_activity.created_at',\DB::raw("TIMESTAMPDIFF(SECOND,sam_activity.created_at,NOW()) as timeWork"))
                    ->orderBy('created_at','desc')
                    ->where('b.dept_status','MKT')
                    ->where('b.id_status',[82,93])
                    ->where('sam_activity.id_sam',$id)
                    ->groupBy('id_sam')
                    ->first();

                if($activity != null){
                    $timeWork=$activity->timeWork;
                }else{
                    $timeWork=0;
                }

                $newActivity=new \App\Models\Mobile\Activity;
                $newActivity->id_activity=$idactivity;
                $newActivity->id_sam=$id;

                $email['lampirans']=array();
                
                if(isset($formtask['file'])){
                    $email['lampirans']=array();
                    $lampirans=array();
                    $files=$formtask['file'];
                    foreach($files as $key=>$val){
                        if(!is_dir('uploads/mobile/'.$idFileType)){
                            mkdir('uploads/mobile/'.$idFileType,0777, TRUE);
                        }

                        $idFile="UPDATE".$key."0".$dateNow;
                        $folder="uploads/mobile/".$idFileType."/";
                        $filename=trim(str_replace(" ","_",$val->getClientOriginalName()));
                        $destinationPath="uploads/mobile/".$idFileType."/";
                        $val->move($destinationPath,$filename);

                        $lampirans[]=$destinationPath."/".$filename;

                        $newFile=new \App\Models\Mobile\File;
                        $newFile->id_sam_file=$idFile;
                        $newFile->id_activity=$idactivity;
                        //$newFile->id_status=$request->input('status');
                        $newFile->title=$filename;
                        $newFile->nama_file=$filename;
                        $newFile->insert_user=$userid ;
                        $newFile->id_filetype=$idFileType;
                        $newFile->id_bu=$idbu;
                        $newFile->save();
                    }

                    $newActivity->id_attach_file='1';
                    $newActivity->id_sam_file =$idFile;
                    $email['lampirans']=$lampirans;

                } else {
                    $email['lampirans']=array();
                    $lampirans=array(); 
                    if(count($sam->activity)>0){
                        foreach($sam->activity as $row){
                            if(count($row->file)>0){
                                foreach($row->file as $key){
                                    $destinationPath="uploads/local/".$key->id_filetype;
                                    $lampirans[]=$destinationPath."/".$key->nama_file;
                                }
                            }
                        }
                        $email['lampirans']=$lampirans;
                    }
                }

                $newActivity->description=nl2br($formtask['desc']);
                $newActivity->id_status=$formtask['status'];
                $newActivity->insert_user=$userid ;
                $newActivity->time_to_work=$timeWork;
                $newActivity->deadline=deadlineMkt($dateNow);
                $newActivity->id_bu=$idbu;

                if($request->has('nodeal')){
                    $newActivity->id_nodeal=$formtask['nodeal'];
                }

                $saveActivity=$newActivity->save();

                if(isset($formtask['cc'])){
                    $listCc=$formtask['cc'];
                    $pecah=explode(",", $listCc);

                    if(count($pecah)>0){
                        foreach($pecah as $row){
                            $row=trim($row);
                            $newCc=new \App\Models\Mobile\Cc;
                            $newCc->id_sam=$id;
                            $newCc->email=$row;
                            $newCc->save();
                        }
                    }
                }
                    
                if(isset($formtask['benefit'])){
                    $benefit =$formtask['benefit'];
                    $nilai   =$formtask['nilai'];
                    if(count($benefit)>0){
                        $section="";
                        $nos=0;
                        foreach($benefit as $key=>$val){
                                if($val!="false"){

                                    if($nos==0){
                                        $section=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                            ->where('id_bu',$idbu)
                                            ->where('id_benefit',$val)
                                            ->first();

                                        if($section != null){
                                            $picsection= $section->id_section;
                                        }else{
                                            $sections=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                            ->where('id_bu',$idbu)
                                            ->where('id_benefit',0)
                                            ->first();
                                            $picsection= $sections->id_section;
                                        }
                                        $s=\App\Models\Mobile\Sam::find($id);
                                        $s->pic_section=$picsection;
                                        $s->save();
                                    }
                                    
                                    $nos++;            
                                    $ben              =new \App\Models\Mobile\Detailbenefit;
                                    // $ben->id_sm       =$idactivity;
                                    $ben->id_sam      =$id;
                                    $ben->id_benefit  =$val;
                                    $ben->budget      =$nilai[$val];

                                    $ben->id_status   =$status;
                                    $ben->insert_user =$userid ;
                                    $ben->save();
                                }
                        }
                    }
                }

                if(isset($formtask['parameter'])){
                    $parameter=$formtask['parameter'];
                    foreach($parameter as $key=>$val){
                        $param               =new \App\Models\Mobile\Parameterdetail;
                        $param->id_sam       =$id;
                        $param->id_parameter =$key;
                        $param->value        =$val;
                        $param->save();
                    }
                }

                if(isset($formtask['program'])){
                    $tes = $formtask['program'];
                    foreach($tes as $row){
                        $newProgram=new \App\Models\Mobile\Samprogram;
                        $newProgram->id_sam=$id;
                        $newProgram->id_program_periode=$row;
                        $newProgram->insert_user=$userid ;
                        $newProgram->save();     
                    }  
                }

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil terkirim',
                    'error'=>''
                );
            }else{
                $data=array(
                    'success'=>false,
                    'pesan'=>'Data gagal terkirim',
                    'error'=>''
                );
            }

                return $data;

        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function update_status_mobile(Request $request, $id){
       try {

            $posisi= $userget->POSITION;
            $userid = $userget->USER_ID;
            $idbu = $userget->ID_BU;
            $idDepart= $userget->ID_DEPARTMENT;

            $formtask     =$request->get('form');

            $dateNow=date('YmdHis');
            $email=array();
            $cc=array();

            $sam=\App\Models\Sam\Sam::findOrFail($id);
            $newSam= new \App\Models\Mobile\Sam;
            $budget=str_replace(",", "", $formtask['budget']);

            $newSam->id_sam=$id;
            $newSam->id_req_type=$sam->id_req_type;
            $newSam->type="UPDATE";
            $newSam->id_status=$formtask['status'];
            $newSam->deadline=deadlineCreative($dateNow);
            $newSam->deadline_mkt=deadlineCreative($dateNow);
            $newSam->dibaca="N";
            $newSam->id_brand =$formtask['brand'];
            $newSam->brand_variant =$formtask['brandvariant'];
            $newSam->id_advg =$formtask['advertiser'];
            $newSam->id_apu =$formtask['agency'];
            $newSam->budget =$budget;
            $newSam->nett =$budget;
            $newSam->approved_by   =$userid ;
            $newSam->insert_user   =$userid ;
            $newSam->update_user   =$userid ;
            $newSam->pic_am=$sam->pic_am;
            $newSam->id_bu=$idbu;

            if(isset($formtask['brand'])){
                $newSam->id_brand=$formtask['brand'];
            } 

            $simpan=$newSam->save();

            if($simpan){
                $idactivity=lastIdActivityMobile();
                $idFileType="6";                    

                $activity=\App\Models\Sam\Activity::leftJoin('sam_status as b','b.id_status','=','sam_activity.id_status')
                    ->select('sam_activity.id_sam','sam_activity.created_at',\DB::raw("TIMESTAMPDIFF(SECOND,sam_activity.created_at,NOW()) as timeWork"))
                    ->orderBy('created_at','desc')
                    ->where('b.dept_status','MKT')
                    ->whereIn('b.id_status',[23,24,88,95,96])
                    ->where('sam_activity.id_sam',$id)
                    ->groupBy('id_sam')
                    ->first();

                if($activity != null){
                    $timeWork=$activity->timeWork;
                }else{
                    $timeWork=0;
                }

                $newActivity=new \App\Models\Mobile\Activity;
                $newActivity->id_activity=$idactivity;
                $newActivity->id_sam=$id;

                $email['lampirans']=array();
                    
                if($request->hasFile('file')){
                    $email['lampirans']=array();
                    $lampirans=array();
                    $files=$request->file('file');
                    foreach($files as $key=>$val){
                        if(!is_dir('uploads/mobile/'.$idFileType)){
                            mkdir('uploads/mobile/'.$idFileType,0777, TRUE);
                        }

                        $idFile="UPDATE".$key."0".$dateNow;
                        $folder="uploads/mobile/".$idFileType."/";
                        $filename=trim(str_replace(" ","_",$val->getClientOriginalName()));
                        $destinationPath="uploads/mobile/".$idFileType."/";
                        $val->move($destinationPath,$filename);

                        $lampirans[]=$destinationPath."/".$filename;

                        $newFile=new \App\Models\Mobile\File;
                        $newFile->id_sam_file=$idFile;
                        $newFile->id_activity=$idactivity;
                        $newFile->title=$filename;
                        $newFile->nama_file=$filename;
                        $newFile->insert_user=$userid ;
                        $newFile->id_filetype=$idFileType;
                        $newFile->id_bu=$idbu;
                        $newFile->save();
                    }
                    $newActivity->id_attach_file='1';
                    $email['lampirans']=$lampirans;
                }

                $newActivity->description=nl2br($formtask['desc']);
                $newActivity->id_status=$formtask['status'];
                $newActivity->insert_user=$userid ;
                $newActivity->update_user=$userid ;
                $newActivity->time_to_work=$timeWork;
                $newActivity->deadline=deadlineMkt($dateNow);
                $newActivity->id_bu=$idbu;

                if($request->has('nodeal')){
                    $newActivity->id_nodeal=$formtask['nodeal'];
                }

                $saveActivity=$newActivity->save();

                if(isset($formtask['cc'])){
                    $listCc=$formtask['cc'];
                    $pecah=explode(",", $listCc);

                    if(count($pecah)>0){
                        foreach($pecah as $row){
                            $row=trim($row);
                            $newCc=new \App\Models\Mobile\Cc;
                            $newCc->id_sam=$id;
                            $newCc->email=$row;
                            $newCc->save();
                        }
                    }
                }

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil terkirim',
                    'error'=>''
                );
            }else{
                $data=array(
                    'success'=>false,
                    'pesan'=>'Data gagal terkirim',
                    'error'=>''
                );
            }

            return $data;
       } catch (Exception $e) {
            return response($e->getMessage());
       }
    }

}