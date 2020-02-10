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

	        if($request->input('q')){
	            $var=$var->where('program_name','like','%'.$request->input('q').'%');
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

	public function save_request(Request $request){
		$userget = user($request->bearerToken());
        $posisi= $userget->POSITION;
        $userid = $userget->USER_ID;
        $idbu = $userget->ID_BU;

        $formtask 	  =$request->get('form');
        $request_by   =$formtask['request_by'];
		$brand        =$formtask['brand'];
		$brandvariant =$formtask['brandvariant'];
		$advertiser   =$formtask['advertiser'];
		$agency       =$formtask['agency'];
		$am           =$formtask['am'];
		$idclient     =$formtask['client'];
		$clientname   =$formtask['clientName'];
		$benefit      =$formtask['benefit'];
		$present      =date("YmdHis",strtotime($formtask['present'))];
		$req_type     =$formtask['requesttype'];
		$budget       =str_replace(",", "", $formtask['budget')];
		$nilai    	  =str_replace(",", "", $formtask['nilai')];
		$parameter    =$formtask['parameter'];
		$desc         =$formtask['desc'];
		$idsam        =autoNumberSam($req_type);
		$idactivity   =lastIdActivity();
		$bu           =$idbu;
		$dateNow      =date('YmdHis');

		// set status request 
		if($req_type==04 or $req_type==10 or $req_type==11 ){
			if ($idbu==1 or $idbu==5) {
				$status = getIdStatus($req_type,'REQUEST');
			}else if($idbu==2){
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

		// set deadline 
		if($idbu==1 or $idbu==5) {
			$deadline =deadlineCreative($dateNow);
		}else if($idbu==2){
			if($posisi=='SGM' or $posisi=='SM'){
				$deadline =deadlineCreative($dateNow);
			}else{
				$deadline =deadlineApprove($dateNow);
			}
		} else {
			$deadline =deadlineApprove($dateNow);
		}

		$sam                =new \App\Models\Sam\Sam;
		$sam->id_sam        =$idsam;
		$sam->id_req_type   =$req_type;
		$sam->request_by    =$request_by;
		$sam->id_status     =$status;
		$sam->start_periode =date('Y-m-d',strtotime($deadline));
		$sam->end_periode   =date('Y-m-d',strtotime($deadline));
		$sam->id_brand      =$brand;
		$sam->brand_variant =$brandvariant;
		$sam->id_apu        =$agency;
		$sam->id_advg       =$advertiser;
		$sam->id_client     =$idclient;
		$sam->client_name   =$clientname;
		$sam->nett        	=$budget;
		$sam->budget        =$budget;
		$sam->pic_am        =$am;
		$sam->deadline      =$deadline;
		$sam->deadline_mkt  =$present;
		$sam->active        ='1';
		$sam->id_bu         =$idbu;
		$sam->insert_user   =$userid;
		$sam->update_user   =$userid;

		switch ($posisi) {
            case 'AM':
            	$amd=\DB::table('tbl_am as a')
	                ->where('a.id_am',$userid)
	                ->select('id_am','id_sgm','id_sm')
	                ->get();

                if(count($amd)>0){
                    foreach($amd as $row){
						$sam->id_am  =$row->id_am;
						$sam->id_sgm =$row->id_sgm;
						$sam->id_sm  =$row->id_sm;
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
                    	$sam->id_am  =$row->id_am;
						$sam->id_sgm =$row->id_sgm;
						$sam->id_sm  =$row->id_sm;
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
                    	$sam->id_am  =$row->id_am;
						$sam->id_sgm =$row->id_sgm;
						$sam->id_sm  =$row->id_sm;
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

		$simpan = $sam->save();

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
                                    
                                $s=Sam::find($idsam);
                                $s->pic_section=$picsection;
                                $s->save();
	                        }
	                            
	                        $nos++;            
	                        $ben              =new \App\Models\Sam\Detailbenefit;
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

            if(isset($formtask['parameter']){
				$parameter=$formtask['parameter'];
				foreach($parameter as $key=>$val){
					$param               =new \App\Models\Sam\Parameterdetail;
					$param->id_sam       =$idsam;
					$param->id_parameter =$key;
					$param->value        =$val;
					$param->save();
				}
			}

			if(isset($formtask['program']){
                $pecProg=explode(",", isset($formtask['program']));
                if(count($pecProg)>0){
                    foreach($pecProg as $row){
						$newProgram                     =new \App\Models\Sam\Samprogram;
						$newProgram->id_sam             =$idsam;
						$newProgram->id_program_periode =$row;
						$newProgram->insert_user        =$userid;
						$newProgram->save();       
                    }
                }
            }

            $attachfile =0;
			$timeWork   =0;
				
			$ac                 =new \App\Models\Sam\Activity;
			$ac->id_activity    =$idactivity;
			$ac->id_status      =$status;
			$ac->id_sam         =$idsam;
			$ac->id_attach_file =$attachfile;
			$ac->description    =$desc;
			$ac->insert_user    =$userid;
			$ac->update_user    =$userid;
			$ac->time_to_work   =$timeWork;
			$ac->deadline       =$deadline;
			$ac->id_bu          =$idbu;
			$ac->save();

			$email['lampirans'] =array();
			$lampirans          =array();

			if(isset($formtask['file'])){
				$idFileType=getIdFiletype($req_type,$status);
				$file=$formtask['file'];

				foreach($file as $key=>$val){
					if(!is_dir('uploads/local/'.$idFileType)){
						mkdir('uploads/local/'.$idFileType, 0777, TRUE);
					}

					$folder          ="uploads/local/".$idFileType."/";
					$filename        =$val->getClientOriginalName();
					$destinationPath ="uploads/local/".$idFileType."/";
					$val->move($destinationPath,$filename);

					$lampirans[]=$destinationPath."/".$filename;

					$idFile="REQ".$key."".$dateNow;

					$f              =new \App\Models\Sam\File;
					$f->id_sam_file =$idFile;
					$f->id_activity =$idactivity;
					$f->id_sam      =$idsam;
					$f->title       =addslashes($filename);
					$f->nama_file   =addslashes($filename);
					$f->insert_user =$userid;
					$f->id_filetype =$idFileType;
					$f->id_bu       =$idbu;
					$f->save();

					$attachfile=1;
				}

				$act                 =\App\Models\Sam\Activity::find($idactivity);
				$act->id_attach_file =$attachfile;
				$act->save();
                $email['lampirans']=$lampirans;
			}

			$email['id_bu']=$idbu;

			switch ($req_type) {
                case '4':
                    if($idbu==1 or $idbu==5){
		            	$namastatus="REQUEST";
		            }elseif($idbu==2){
		            	if($posisi=='SGM' or $posisi=='SM'){
		            		$namastatus="REQUEST";
		            	}else{
		            		$namastatus="DRAFT REQUEST";
		            	}
		            }else{
		            	$namastatus="DRAFT REQUEST";
		            }

                    $reqtypename="FC";

                    $programs="";
                    if(isset($formtask['program'])){
                        $pecahProg=explode(",", $formtask['program']);
                        $i=0;
                        $len = count($pecahProg);
                        if(count($pecahProg)>0){
                            foreach($pecahProg as $row){
                                $prog=\App\Models\Saleskit\Programperiode::select('id_program_periode','id_program')
                                        ->with('program')->find($row);
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
                    }

                    $email['subject']="SAM-".$namastatus." ".$reqtypename." ".$programs." ".$brandvariant;
                break;
                case '10':
                    if($idbu==1 or $idbu==5){
		            	$namastatus="REQUEST";
		            }elseif($idbu==2){
		            	if($posisi=='SGM' or $posisi=='SM'){
		            		$namastatus="REQUEST";
		            	}else{
		            		$namastatus="DRAFT REQUEST";
		            	}
		            }else{
		            	$namastatus="DRAFT REQUEST";
		            }
		            	
                    $reqtypename="TVDAY";

                   	$programs="";
                    if(isset($formtask['program'])){
                        $pecahProg=explode(",", $formtask['program']);
                        $i=0;
                        $len = count($pecahProg);
                        if(count($pecahProg)>0){
                            foreach($pecahProg as $row){
                                $prog=\App\Models\Saleskit\Programperiode::select('id_program_periode','id_program')
                                    ->with('program')->find($row);
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
                    }

                    $email['subject']="SAM-".$namastatus." ".$reqtypename." ".$programs." ".$brandvariant;
                break;
                case '11':
		            $namastatus="REQUEST";
                    $reqtypename="AMPLIFY";

                    $programs="";
                    if(isset($formtask['program'])){
                        $pecahProg=explode(",", $formtask['program']);
                        $i=0;
                        $len = count($pecahProg);
                        if(count($pecahProg)>0){
                            foreach($pecahProg as $row){
                                $prog=\App\Models\Saleskit\Programperiode::select('id_program_periode','id_program')
                                            ->with('program')->find($row);
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
                    }

                    $email['subject']="SAM-".$namastatus." ".$reqtypename." ".$brandvariant;
                break;

                default:
                    # code...
                break;
            }

            $email['desc']    =$desc;
			$email['idsam']   =$idsam;
			$email['reqtype'] =$req_type;

			/*------------------Validasi untuk Approval--------------------------*/
     		$st           =\App\Models\Sam\Sam::find($idsam);
			$approved_sgm =$st->approved_sgm; 
			$approved_sm  =$st->approved_sm; 

            switch ($idbu) {
               	case '2':
                	if($req_type==04){
	               		if($approved_sgm !=null){
							$st->id_status=82;
						}
					}else{
						if($approved_sgm !=null){
							$st->id_status=93;
						}
					}
				break;
                case '3':
                case '8':
                	if($req_type==04){
	                	if($approved_sgm !=null and $approved_sm !=null){
							$st->id_status=82;
						}
					}else{
						if($approved_sgm !=null and $approved_sm !=null){
							$st->id_status=93;
						}
					}
                break;
                default:
                break;
            }

			$st->save();
			/*------------------End Validasi untuk Approval----------------------*/

			if(isset($formtask['cc'])){
				$listCc =$formtask['cc'];
				$pecah  =explode(",", $listCc);

                if(count($pecah)>0){
                    foreach($pecah as $row){
                        $row=trim($row);
                        array_push($cc,$row);
                    }
                }
            }

            if($idbu == 3) {
                array_push($cc,'ratih.dewi@mncgroup.com');
            	array_push($cc,'rhezika.fibrian@mncgroup.com');
            	array_push($cc,'galih.thanta@mncgroup.com');
            }

            if($idbu == 1) {
                array_push($cc,'firdauzi.cece@mncgroup.com');
            	array_push($cc,'ivan.faisal@mncgroup.com');
            	array_push($cc,'zulfan.usuluddin@mncgroup.com');
            	array_push($cc,'yeni.susanti@mncgroup.com');
            	array_push($cc,'annisa.fildzah@mncgroup.com');
            }

            if($idbu == 5){
               	array_push($cc,'sandy.anugerah@mncgroup.com');
               	array_push($cc,'iwan.abdurahman@mncgroup.com');
               	array_push($cc,'Akhmad.Gunanto@mncgroup.com');

                $planner=\App\Models\User::where('id_section',45)
			        ->where('active',1)
			        ->where('id_bu',5)
			        ->select('user_id')
			        ->get();

			    foreach($planner as $pln){
			        array_push($cc, $pln->user_id);
			    }
            }

            array_push($cc,'mujib.nashikha@mncgroup.com','jamal.apriadi@mncgroup.com','jaenudin.fawwaz@mncgroup.com');
                
	        array_push($cc,$userid);

	        /*-----------------------------------DRAFT--------------------------------*/
                if($st->id_status==21 or $st->id_status==140){
                	
                	$email['status2']="REQUEST";
					$email['list']=\App\Models\Sam\Status::where('id_req_type',$sam->id_req_type)
                        ->where('stat','STATUS')
                        ->where('relasi_status',1)
                        ->where('type_status',0)
                        ->get();

                	$toAtasan=\DB::table('tbl_am as a')
                        ->where('a.id_am',$am)
                        ->select('id_am','id_sgm','id_sm','id_gm')
                        ->get();

	           		if(count($toAtasan)>0){
	                    foreach($toAtasan as $row){
	                        if(!strstr($row->id_gm,'vacant') && $row->id_gm!=""){
	                            array_push($to, $row->id_gm);
	                        }
	                        if(!strstr($row->id_sm,'vacant') && $row->id_sm!=""){
	                            array_push($to,$row->id_sm);
	                        }
	                        if(!strstr($row->id_sgm,'vacant') && $row->id_sgm!=""){
	                            array_push($to,$row->id_sgm);
	                        }
	                    }
	                }

	                $email['to']=$to;
	                $email['cc']=$cc;
	                $email['from']=\Auth::user()->USER_ID;
                    $email['fromname']=ucwords(strtolower(\Auth::user()->USER_NAME));

	                \Mail::send('sam.concept.email.detail_brief',$email,function($mail) use($email){
			            $mail->from($email['from'],$email['fromname']);
			            $mail->to($email['to'])->cc($email['cc']);

			            if(count($email['lampirans'])>0){
			                foreach($email['lampirans'] as $key=>$val){
			                    $mail->attach($val);
			                }
			            }
			            $mail->subject($email['subject']);
			        });

                } else {
                /*-----------------------------------REQUEST--------------------------------*/
					$email['req_type'] =$req_type;
                	
                	$ccAtasan=\DB::table('tbl_am as a')
                        ->where('a.id_am',$sam->pic_am)
                        ->select('id_am','id_sgm','id_sm','id_gm')
                        ->get();

            		if(count($ccAtasan)>0){
		               	foreach($ccAtasan as $row){
		                    if(!strstr($row->id_gm,'vacant') && $row->id_gm!=""){
		                       	array_push($cc, $row->id_gm);
		                    }

		                    if(!strstr($row->id_sm,'vacant') && $row->id_sm!=""){
		                        array_push($cc,$row->id_sm);
		                    }

		                    if(!strstr($row->id_sgm,'vacant') && $row->id_sgm!=""){
		                        array_push($cc,$row->id_sgm);
		                    }
               			}
            		}
            		
                	if($request->has('benefit')){
                		$benefits=$request->input('benefit');
                		foreach($benefits as $key=>$val){
                    			$section=\App\Models\Sam\Sambenefitsection::where('id_req_type',$req_type)
                                    ->where('id_bu',\Auth::user()->ID_BU)
                                    ->where('id_benefit',$val)
                                    ->first();
                                
			                if($section != null){
			                    $userSection=\App\Models\User::where('id_section',$section->id_section)
			                                ->where('active',1)
			                                ->where('id_bu',\Auth::user()->ID_BU)
			                                ->select('user_id','user_name')
			                                ->get();
			                    foreach($userSection as $up){
			                        array_push($to, $up->user_id);
			                    }
			                }
                		}
            		}

            		/*--------------email by type request---------*/ 
            		
	                $ccMkt=\App\Models\Sam\Picsection::leftJoin('tbl_user as b','b.id_section','=','sam_benefit_section.id_section')
		                ->where('id_req_type',$sam->id_req_type)
		                ->where('sam_benefit_section.id_bu',\Auth::user()->ID_BU)
		                ->where('b.active',1)
		                ->where('id_benefit',0)
		                ->where('b.true_email','=','Y')
		                ->orderBy('b.position','asc')
		                ->select('b.user_id','b.id_section','b.id_bu')
		                ->get();
					

	                foreach ($ccMkt as $key => $value) {
                        array_push($to,$value->user_id);
                    }
            		/*--------------email by type request---------*/ 

                    $email['to']=$to;
	               	$email['cc']=$cc;
	                $email['from']=\Auth::user()->USER_ID;
                    $email['fromname']=ucwords(strtolower(\Auth::user()->USER_NAME));

                    //info hutang
                    $email['agcCek']=\App\Models\Dashboard\Agencypintu::find($agency);
                    $email['advCek']=\App\Models\Dashboard\Advertiser::find($advertiser);
                    $email['brandCek']=\App\Models\Dashboard\Brand::find($brand);

                	\Mail::send('sam.concept.email.request_concept',$email,function($mail) use($email){
			            $mail->from($email['from'],$email['fromname']);
			            $mail->to($email['to'])->cc($email['cc']);

			            if(count($email['lampirans'])>0){
			                foreach($email['lampirans'] as $key=>$val){
			                    $mail->attach($val);
			                }
			            }

			            $mail->subject($email['subject']);
			        });
                }






		}


	}

}