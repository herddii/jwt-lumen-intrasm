<?php
use Firebase\JWT\JWT;

function user($value){
	$token = $value;
    $users = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
   	return $users;
}

function autoNumberSam($reqtype){
    $dateNow    = date('YmdHis');
    $ym         = date('mY');

    $count    = \DB::select("select * from sam a
                    where DATE_FORMAT(a.created_at,'%m%Y')=DATE_FORMAT(NOW(),'%m%Y')");

    $n=count($count)+1;
    if ($n<10){
        $n="000".$n;
    } else if ($n<100){
        $n="00".$n;
    } else if ($n<1000){
        $n="0".$n;
    } else {
        $n=$n;
    }

    $id_sam  = "0".$reqtype."".$ym."0".$n;

    return $id_sam;
}

function deadlineCreative($date){
    $deadline   = strtotime ('+5 day' ,strtotime ($date));
    $deadline   = date('YmdHis',$deadline);
    return $deadline;
}

function deadlineSlscrtv($date){
    $deadline   = strtotime ('+14 day' ,strtotime ($date));
    $deadline   = date('YmdHis',$deadline);
    return $deadline;
}

function deadlineApprove($date){
    $weekDay = date('w', strtotime($date));
    switch($weekDay){
        case '0'://minngu
            $deadline   = strtotime ('+2 day' ,strtotime ($date));
            //$deadline = date('Ymd',strtotime($deadline))."000000";
            $deadline   = date('Ymd',$deadline)."000000";
        break;
        case '6'://sabtu
            $deadline   = strtotime ('+3 day' ,strtotime ($date));
            $deadline   = date('Ymd',$deadline)."000000";
            //$deadline = date('Ymd',strtotime($deadline))."000000";
        break;
        case '5'://jumat
            $deadline   = strtotime ('+3 day' ,strtotime ($date));
            $deadline   = date('YmdHis',$deadline);
        break;
        default:
            $deadline   = strtotime ('+1 day' ,strtotime ($date));
            $deadline   = date('YmdHis',$deadline);
    }
    return $deadline;
}

function getIdStatus($reqType,$namaStatus){
    $status=\App\Models\Sam\Status::where('id_req_type',$reqType)
        ->where('nama_status',$namaStatus)
        ->first();

    return $status->id_status;
}

function getIdFiletype($reqType,$status){
    $file=\App\Models\Sam\Filerequest::where('id_req_type',$reqType)
        ->where('id_status',$status)
        ->first();

    $id=1;

    if($file != null){
        $id=$file->id_filetype;
    }

    return $id;
}

function autoNumberFile($fileName,$reqType){
    $count=\DB::select("select * from sam_file a
        where DATE_FORMAT(a.created_at,'%Y%m')=DATE_FORMAT(NOW(),'%Y%m')");

    $n=count($count)+1;
    if($n<10){
        $n="000".$n;
    }else if ($n<100){
        $n="00".$n;
    }else if($n<1000){
        $n="0".$n;
    }else{
        $n=$n;
    }

    $idFile=$fileName."".$reqType."0".date('Ym')."".$n;

    return $idFile;
}

function autoNumberSamMobile($reqtype){
    $dateNow    = date('YmdHis');
    $ym         = date('mY');

    $count    = \DB::select("select * from mobile_sam a
                    where DATE_FORMAT(a.created_at,'%m%Y')=DATE_FORMAT(NOW(),'%m%Y')");

    $n=count($count)+1;
    if ($n<10){
        $n="000".$n;
    } else if ($n<100){
        $n="00".$n;
    } else if ($n<1000){
        $n="0".$n;
    } else {
        $n=$n;
    }

    $id_sam  = $reqtype."".$ym."0".$n."-1";

    return $id_sam;
}

function lastIdActivityMobile(){
    $idActivity=1;
    $ac   = \App\Models\Mobile\Activity::select(DB::raw('max(id_activity) as MAX'))->first();
    if($ac !=null){
        $idActivity = $ac->MAX+1;
    }

    return $idActivity;
}