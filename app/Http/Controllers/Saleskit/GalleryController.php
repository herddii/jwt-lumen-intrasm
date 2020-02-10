<?php
namespace App\Http\Controllers\Saleskit;
use App\User;
use App\Models\Saleskit\Programperiode; 
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Models\Saleskit\Contentnew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;

class GalleryController extends Controller 
{
    public function getIndex(Request $request,$id_kategori){
        try {
            $isi_portal = \DB::table('portal_berita as b')
            ->leftJoin('portal_bankfoto as c','c.id_portal','b.id_portal')
            ->where('b.id_kategori',$id_kategori)
            // ->where('b.type_video','CITRAPARIWARA') 
            ->whereNull('b.deleted_at')
            ->orderBy('b.created_at','DESC')
            ->groupBy('b.id_portal')
            ->paginate(6);
            return response($isi_portal,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        } 
    }

    public function getkategori(Request $request){
        try {
            $kategori = \DB::table('portal_berita as a')->selectRaw('a.id_kategori,b.nama_kategori')
            ->leftJoin('portal_kategori as b','b.id_kategori','a.id_kategori')
            ->groupBy('a.id_kategori')->get();
            return response($kategori,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }    
    }

    public function get_genre_program(Request $request){
        try {
            $content = \DB::select('select a.id_genre, a.genre_name from tbl_program_genre as a where a.active = 1');
            return response($content,200); 
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function detail_article(Request $request, $id, $id_kategori){
        try {
            $berita=\App\Models\Saleskit\Portalberita::with('portal_tag','kategori','advertiser')
            ->where('slug','=',$id_kategori)->first();

            $market=\App\Models\Saleskit\Portalberita::with('portal_tag','kategori','advertiser','brand')
            ->where('id_kategori',$id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

            return response([
                'berita' => $berita,
                'market' => $market
            ],200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function get_bu(Request $request){
        try {
            $content = \DB::table('tbl_content as a')->selectRaw('a.id_bu, b.BU_SHORT_NAME')
            ->leftJoin('tbl_bu as b','b.ID_BU','a.id_bu')
            ->where('a.id_bu','!=',0)
            ->groupBy('a.id_bu')
            ->get();
            return response($content,200); 
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }   
    }

    public function version(Request $request){
        try {
            $url = 'https://play.google.com/store/apps/details?id=com.saleskit.app';
            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, $url);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 5);   
            $cek = curl_exec($session);
            curl_close($session);
            preg_match('/<span[^>]+class="htlgb"[^>]*>(.*)<\/span>/', $cek, $title);
            $cg = explode('<span class="htlgb">',$title[0]);
            $res = preg_replace("/[^0-9-.]/", "", $cg[8]);
            $sendto = Array('version' => $res);
            return response($sendto,200); 
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }    
    }

    public function get_gallery_all_program_eloquent(Request $request, $idGenre){

        try {

            $g = user($request->bearerToken());
            $skrg=date('Y-m-d');

            $prog=\App\Models\Mediakit\Programperiode::with(
                [
                    'hotspot.filetype','presentation.filetype','paket.filetype',
                    'program.genre','program.bu','program']);
            if($idGenre != 20){
                $prog->whereHas('program.genre',function($x)use($idGenre){
                    $x->where('id_genre',$idGenre);
                });
            }
            if($g->ID_BU != 11){
                $prog->whereHas('program',function($xy)use($g){
                    $xy=$xy->where('tbl_program.id_bu',$g->ID_BU);
                });
            } else {
                $prog->whereHas('program',function($xy)use($g){
                    $xy=$xy->whereIn('tbl_program.id_bu',[1,2,3,8]);
                });
            }
            $prog=$prog->where('content_use','!=',0)
            ->where('tbl_program_periode.mediakit','=',1)
            ->whereNull('deleted_at')
            ->where(\DB::raw("DATE_FORMAT(content_start_date,'%Y-%m-%d')"),'<=',$skrg)
            ->where(\DB::raw("DATE_FORMAT(content_end_date,'%Y-%m-%d')"),'>=',$skrg)
            ->orderBy('updated_at','desc')->paginate(5);

            return response($prog,200);

        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function get_gallery_all_program_eloquent_tanpabu(Request $request, $idGenre, $idBu){
        try {

            $g = user($request->bearerToken());
            $skrg=date('Y-m-d');

            $prog=\App\Models\Saleskit\Programperiode::with(
                [
                    'hotspot.filetype','presentation.filetype','paket.filetype',
                    'program.genre','program.bu','program']);
            if($idGenre != 20){
                $prog->whereHas('program.genre',function($x)use($idGenre){
                    $x->where('id_genre',$idGenre);
                });
            }
            if($idBu != 11){
                $prog->whereHas('program',function($xy)use($idBu){
                    $xy=$xy->where('tbl_program.id_bu',$idBu);
                });
            } else if($idBu = 11){
                $prog->whereHas('program',function($xy)use($idBu){
                    $xy=$xy->whereIn('tbl_program.id_bu',[1,2,3,5,8,10,13,15,16,18,19]);
                });
            }
            $prog=$prog->where('content_use','!=',0)
            ->where('tbl_program_periode.mediakit','=',1)
            ->whereNull('deleted_at')
            ->where(\DB::raw("DATE_FORMAT(content_start_date,'%Y-%m-%d')"),'<=',$skrg)
            ->where(\DB::raw("DATE_FORMAT(content_end_date,'%Y-%m-%d')"),'>=',$skrg)
            ->orderBy('updated_at','desc')->paginate(10);

            return response($prog,200);

        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function get_image(Request $request){
        try {
            $g = user($request->bearerToken());
            $idProgram = $request->idProgram;
            $content = \DB::table('tbl_content as a')->selectRaw('*')
            ->where('id_content',$idProgram)->get();

            return response($content,200); 
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }


    public function get_gallery_all_tanpabu(Request $request, $idBu){
        try {
            $g = user($request->bearerToken());
            $testing = \DB::table('tbl_content as a')->selectRaw('a.id_content,
                a.id_program_periode,
                a.deleted_at,
                a.id_master_filetype, 
                a.content_title, 
                a.content_file_download,
                a.updated_at,
                if(c.title = "PROGRAM",g.program_name,f.title) AS title_name,
                a.created_at,
                b.folder,
                b.id_filetype,
                b.filetype_name,
                b.ext_file,
                a.id_bu,
                c.title,
                a.mediakit,
                d.BU_SHORT_NAME,
                concat(week(a.updated_at), "-", year(a.updated_at)) as weeky,
                concat(monthname(a.updated_at),",",year(a.updated_at)) as bulan,
                concat(round(week(a.updated_at)/12)+1,",",monthname(a.updated_at)," ",year(a.updated_at)) as week')
            ->leftJoin('tbl_filetype as b','b.id_filetype','a.id_filetype')
            ->leftJoin('tbl_master_filetype as c','c.id_master_filetype','a.id_master_filetype')
            ->leftJoin('tbl_program_periode as e','e.id_program_periode','a.id_program_periode')
            ->leftJoin('tbl_salestools as f','f.id_salestools','a.id_program_periode')
            ->leftJoin('tbl_program as g','g.id_program','e.id_program')
            ->leftJoin('tbl_bu as d','d.id_bu','a.id_bu')
            ->where('b.folder','public')
            ->whereIn('a.id_master_filetype',[1,2,8])
            ->where('a.updated_at','!=','0000-00-00 00:00:00')
            ->where('a.mediakit',1)
            ->where('b.ext_file','!=','url')
            ->orderBy('a.updated_at','desc')
            ->whereNull('a.deleted_at')
            ->groupBy('a.id_program_periode');
            if($idBu != 11){
                $testing = $testing->where('a.id_bu',$idBu);
            }
            $testing = $testing->paginate(30);


            return response($testing,200);

        } catch (\Exception $e){
           return response(array('data'=>'Error at Backend'));
       }
    }


    public function get_special_offers_eloquent_tanpabu(Request $request, $idBu){
        try {
            $g = user($request->bearerToken());
            $skrg=date('Y-m-d');
            $special=\App\Models\Saleskit\Salestool::with(
                [   
                    'content.filetype','content.bu'
                ]
            );
            $special->whereHas('content',function($x){
                $x->whereIn('id_filetype',[14,152])
                ->where('id_master_filetype','<>',8);
            });
            if($idBu != 11){
                $special->whereHas('content',function($xy)use($idBu){
                    $xy=$xy->where('tbl_content.id_bu',$idBu);
                });
            }
            $special=$special->where('content_use','!=',0)
            ->where('tbl_salestools.mediakit','=',1)
            ->whereNull('deleted_at')
            ->where('id_master_filetype','=',2)
            ->where(\DB::raw("DATE_FORMAT(content_start_date,'%Y-%m-%d')"),'<=',$skrg)
            ->where(\DB::raw("DATE_FORMAT(content_end_date,'%Y-%m-%d')"),'>=',$skrg);

            $special = $special->orderBy('updated_at','desc')->paginate(5);

            return response($special,200);  
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function get_rate_card_eloquent2_tanpabu(Request $request, $idBu){
        try {
             $g = user($request->bearerToken());
             $skrg=date('Y-m-d');

             $rate=\App\Models\Saleskit\Salestool::with(
                [
                    'ratepdf.filetype',
                    'content'=>function($q){
                        $q->where('id_filetype',85)
                        ->where('mediakit',1)
                        ->where('id_master_filetype','<>',8);
                    },
                    'content.filetype',
                    'ratepdf.bu',
                    'content.bu'

                ]
            )
             ->where('tbl_salestools.mediakit','=',1)
             ->whereNull('deleted_at')
             ->where('content_use','!=',0)
             ->where('id_master_filetype',1);

            if($idBu != 11){
                $rate->whereHas('content',function($xy)use($idBu){
                $xy=$xy->where('tbl_content.id_bu',$idBu);
                });
            }

            $rate = $rate->orderBy('updated_at','desc')->paginate(10);
            return response($rate,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        } 
    }

    public function get_performance_tanpabu(Request $request, $idBu){
        try {
            $g = user($request->bearerToken());
            $skrg = date('Y-m-d');
            $performance = '(select * from (select a.id_master_filetype, a.title, b.content_title,a.content_use, a.description,b.id_filetype, a.id_bu,a.updated_at, b.id_content,  b.content_file_download , concat(monthname(b.updated_at),", ",year(b.updated_at)) as bulan,
            concat(round(week(b.updated_at)/12)+1,", ",monthname(b.updated_at)," ",year(b.updated_at)) as week, c.BU_SHORT_NAME, d.filetype_name
            from tbl_salestools a
            left join tbl_content b on b.id_program_periode=a.id_salestools and b.id_content=a.content_use
            left join tbl_bu c on c.ID_BU = b.id_bu
            left join tbl_filetype d on d.id_filetype = b.id_filetype
            where a.id_master_filetype=21 and a.deleted_at is null and b.content_file_download is not null order by a.updated_at desc)
            as data) as total';

            $testing = \DB::table(\DB::raw($performance))
            ->where('total.id_filetype',21);
            if($idBu != 11){
                $testing = $testing->where('total.id_bu',$idBu);
            }
            $testing = $testing->paginate(10);


            return response($testing,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function filter_all(Request $request){
        try {
            $testing = \DB::table('tbl_content as a')->selectRaw('a.id_content,
                a.id_program_periode,
                a.deleted_at,
                a.id_master_filetype, 
                a.content_title, 
                a.content_file_download,
                a.updated_at,
                if(c.title = "PROGRAM",g.program_name,f.title) AS title_name,
                a.created_at,
                b.folder,
                b.id_filetype,
                b.filetype_name,
                b.ext_file,
                a.id_bu,
                c.title,
                a.mediakit,
                d.BU_SHORT_NAME,
                concat(week(a.updated_at), "-", year(a.updated_at)) as weeky,
                concat(monthname(a.updated_at),",",year(a.updated_at)) as bulan,
                concat(round(week(a.updated_at)/12)+1,",",monthname(a.updated_at)," ",year(a.updated_at)) as week')
            ->leftJoin('tbl_filetype as b','b.id_filetype','a.id_filetype')
            ->leftJoin('tbl_master_filetype as c','c.id_master_filetype','a.id_master_filetype')
            ->leftJoin('tbl_program_periode as e','e.id_program_periode','a.id_program_periode')
            ->leftJoin('tbl_salestools as f','f.id_salestools','a.id_program_periode')
            ->leftJoin('tbl_program as g','g.id_program','e.id_program')
            ->leftJoin('tbl_bu as d','d.id_bu','a.id_bu')
            ->where('b.folder','public')
            ->where('a.updated_at','!=','0000-00-00 00:00:00')
            ->where('a.mediakit',1)
            ->where('a.content_title','like','%'.$request->get('name').'%');
            if($request->get('id_bu') != 11){
                $testing = $testing->where('a.id_bu',$request->get('id_bu'));
            }
            $testing=$testing->orderBy('a.updated_at','desc')
            ->groupBy('a.id_program_periode');

            $testing = $testing->paginate(30);

            return response($testing,200);
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function filter_gallery_all_program_eloquent_tanpabu(Request $request){
        try {
            $g = user($request->bearerToken());
            $skrg=date('Y-m-d');
            $idBu = $request->get('id_bu');
            $idGenre = $request->get('id_genre');
            $cariprogram = $request->get('cariprogram');

            $prog=\App\Models\Saleskit\Programperiode::with(
                [
                    'hotspot.filetype','presentation.filetype','paket.filetype',
                    'program.genre','program.bu','program']);
            if($idGenre != 20){
                $prog->whereHas('program.genre',function($x)use($idGenre){
                    $x->where('id_genre',$idGenre);
                });
            }
            if($idBu != 11){
                $prog->whereHas('program',function($xy)use($idBu){
                    $xy=$xy->where('tbl_program.id_bu',$idBu);
                });  
            }

            if($cariprogram){
                $prog->whereHas('program',function($c)use($cariprogram){
                    $c=$c->where('program_name','like','%'.$cariprogram.'%');
                });
            }
            $prog=$prog->where('content_use','!=',0)
            ->where('tbl_program_periode.mediakit','=',1)
            ->whereNull('deleted_at')
            ->where(\DB::raw("DATE_FORMAT(content_start_date,'%Y-%m-%d')"),'<=',$skrg)
            ->where(\DB::raw("DATE_FORMAT(content_end_date,'%Y-%m-%d')"),'>=',$skrg)
            ->orderBy('updated_at','desc')->paginate(10);


            return response($prog,200);
        } catch (\Exception $e){
           return response(array('data'=>'Error at Backend'));
       }
        
    }

    public function filter_special_offers_eloquent_tanpabu(Request $request){
        try {
            $g = user($request->bearerToken());
            $skrg=date('Y-m-d');
            $idBu = $request->get('id_bu');
            $cari_specialoffers = $request->get('cari_specialoffers');
            $special=\App\Models\Saleskit\Salestool::with(
                [   
                    'content.filetype','content.bu'
                ]
            );
            $special->whereHas('content',function($x){
                $x->whereIn('id_filetype',[14,152])
                ->where('id_master_filetype','<>',8);
            });
            if($idBu != 11){
                $special->whereHas('content',function($xy)use($idBu){
                    $xy=$xy->where('tbl_content.id_bu',$idBu);
                });
            }

            if($cari_specialoffers){
                $special->whereHas('content',function($c)use($cari_specialoffers){
                    $c=$c->where('content_title','like','%'.$cari_specialoffers.'%');
                });
            }
            $special=$special->where('content_use','!=',0)
            ->where('tbl_salestools.mediakit','=',1)
            ->whereNull('deleted_at')
            ->where('id_master_filetype','=',2)
            ->where(\DB::raw("DATE_FORMAT(content_start_date,'%Y-%m-%d')"),'<=',$skrg)
            ->where(\DB::raw("DATE_FORMAT(content_end_date,'%Y-%m-%d')"),'>=',$skrg);

            $special = $special->orderBy('updated_at','desc')->paginate(5);

            return response($special,200); 
        } catch (\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
    }

    public function filter_rate_card_eloquent2_tanpabu(Request $request){
        try {
            $g = user($request->bearerToken());
            $skrg=date('Y-m-d');
            $idBu= $request->get('id_bu');
            $carirate = $request->get('cari_ratecard');

            $rate=\App\Models\Saleskit\Salestool::with(
                [
                    'ratepdf.filetype',
                    'content'=>function($q){
                        $q->where('id_filetype',85)
                        ->where('mediakit',1)
                        ->where('id_master_filetype','<>',8);
                    },
                    'content.filetype',
                    'ratepdf.bu',
                    'content.bu'

                ]
            )
            ->where('tbl_salestools.mediakit','=',1)
            ->whereNull('deleted_at')
            ->where('content_use','!=',0)
            ->where('id_master_filetype',1);

            if($idBu != 11){
                $rate->whereHas('content',function($xy)use($idBu){
                    $xy=$xy->where('tbl_content.id_bu',$idBu);
                });
            }

            if($carirate){
                $rate->whereHas('content',function($c)use($carirate){
                    $c=$c->where('tbl_content.content_title',$carirate);
                });
            }

            $rate = $rate->orderBy('updated_at','desc')->paginate(10);
            return response($rate,200);
        } catch(\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }      
    }


    public function filter_performance_tanpabu(Request $request){
        try {
            $g = user($request->bearerToken());
            $idBu = $request->get('id_bu');
            $skrg = date('Y-m-d');
            $cariperformance = $request->get('cariperformance');
            $performance = '(select * from (select a.id_master_filetype, a.title, b.content_title,a.content_use, a.description,b.id_filetype, a.id_bu,a.updated_at, b.id_content,  b.content_file_download , concat(monthname(b.updated_at),", ",year(b.updated_at)) as bulan,
            concat(round(week(b.updated_at)/12)+1,", ",monthname(b.updated_at)," ",year(b.updated_at)) as week, c.BU_SHORT_NAME, d.filetype_name
            from tbl_salestools a
            left join tbl_content b on b.id_program_periode=a.id_salestools and b.id_content=a.content_use
            left join tbl_bu c on c.ID_BU = b.id_bu
            left join tbl_filetype d on d.id_filetype = b.id_filetype
            where a.id_master_filetype=21 and a.deleted_at is null and b.content_file_download is not null order by a.updated_at desc)
            as data) as total';

            $testing = \DB::table(\DB::raw($performance))
            ->where('total.id_filetype',21);
            if($idBu != 11){
                $testing = $testing->where('total.id_bu',$idBu);
            }
            if($cariperformance){
                $testing=$testing->where('total.content_title','like','%'.$cariperformance.'%');
            }
            $testing = $testing->paginate(10);


            return response($testing,200);
        } catch(\Exception $e){
            return response(array('data'=>'Error at Backend'));
        }
        
    } 
}