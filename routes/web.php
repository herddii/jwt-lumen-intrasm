<?php
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post(
    'auth/login', 
    [
       'uses' => 'AuthController@authenticate'
    ]
);

    $router->get('auth/me', 'AuthController@me');
$router->group(['prefix' => 'api' , 'middleware' => 'jwt.auth'], function() use ($router) {
    

    $router->group(['prefix'=>'master'],function() use($router){
        $router->get('advertiser',['as'=>'advertiser','uses'=>'Master\AdvertiserController@index']);
        $router->get('advertiser/{id}',['as'=>'advertiser.view','uses'=>'Master\AdvertiserController@show']);
        $router->delete('advertiser/{id}',['as'=>'advertiser.delete','uses'=>'Master\AdvertiserController@destroy']);
    
        $router->get('agencypintu',['as'=>'agencypintu','uses'=>'Master\AgencypintuController@index']);
        $router->get('agencypintu/{id}',['as'=>'agencypintu.view','uses'=>'Master\AgencypintuController@show']);
        $router->delete('agencypintu/{id}',['as'=>'agencypintu.delete','uses'=>'Master\AgencypintuController@destroy']);
    
        $router->get('brand',['as'=>'brand','uses'=>'Master\BrandController@index']);
        $router->get('brand/{id}',['as'=>'brand.view','uses'=>'Master\BrandController@show']);
        $router->delete('brand/{id}',['as'=>'brand.delete','uses'=>'Master\BrandController@destroy']);
    
        $router->get('userclient',['as'=>'userclient','uses'=>'Master\UserclientController@index']);
        $router->get('userclient/{id}',['as'=>'userclient.view','uses'=>'Master\UserclientController@show']);
        $router->delete('userclient/{id}',['as'=>'userclient.delete','uses'=>'Master\UserclientController@destroy']);
    
        $router->get('userclient_account',['as'=>'userclient_account','uses'=>'Master\Userclient_accountController@index']);
        $router->get('userclient_account/{id}',['as'=>'userclient_account.view','uses'=>'Master\Userclient_accountController@show']);
        $router->delete('userclient_account/{id}',['as'=>'userclient_account.delete','uses'=>'Master\Userclient_accountController@destroy']);

        $router->get('get_bu',['as'=>'getbu','uses'=>'Master\BuController@index']);
        $router->get('get_bu/{id}',['as'=>'get_bu.view','uses'=>'Master\BuController@show']);
        $router->delete('get_bu/{id}',['as'=>'get_bu.delete','uses'=>'Master\BuController@destroy']);
    });

    $router->group(['prefix'=>'cam_activity'], function() use($router){
        $router->get('plafond', ['as'=>'plafond', 'uses'=>'Cam\CamActivityController@plafond']);
        $router->post('get_reimburse','Cam\CamActivityController@get_reimburse');
        $router->post('get_report_daily','Cam\CamActivityController@get_report_daily');
        $router->get('get_report_monthly','Cam\CamActivityController@get_report_monthly');
        $router->post('get-activity-calendar', ['as'=>'get-activity-calendar', 'uses'=>'Cam\CamActivityController@list_activity_calendar']);
    	$router->post('list-tasklist', ['as'=>'list-tasklist', 'uses'=>'Cam\CamActivityController@list_tasklist']);
    	$router->get('detail-list-tasklist/{id_activity}/{id_cam}', ['as'=>'detail-list-tasklist', 'uses'=>'Cam\CamActivityController@detail_list_tasklist']);
        $router->get('list-partner', ['as'=>'list-partner', 'uses'=>'Cam\CamActivityController@list_partner']);
    	$router->get('list-type-activity', ['as'=>'list-type-activity', 'uses'=>'Cam\CamActivityController@list_type_activity']);
        $router->get('list-am', ['as'=>'list-am', 'uses'=>'Cam\CamActivityController@list_am']);
        $router->post('list-agencypintu', ['as'=>'list-agencypintu', 'uses'=>'Cam\CamActivityController@list_agencypintu']);
        $router->post('list-advertiser', ['as'=>'list-advertiser', 'uses'=>'Cam\CamActivityController@list_advertiser']);
        $router->post('list-brand', ['as'=>'list-brand', 'uses'=>'Cam\CamActivityController@list_brand']);
        $router->get('list-client', ['as'=>'list-client', 'uses'=>'Cam\CamActivityController@list_client']);
        $router->get('list-nama-program', ['as'=>'list-nama-program', 'uses'=>'Cam\CamActivityController@list_nama_program']);
        $router->post('list-nama-salestools', ['as'=>'list-nama-salestools', 'uses'=>'Cam\CamActivityController@list_nama_salestools']);
        $router->get('list-nama-samconcept', ['as'=>'list-nama-samconcept', 'uses'=>'Cam\CamActivityController@list_nama_samconcept']);
        $router->get('list-nama-sampaket', ['as'=>'list-nama-sampaket', 'uses'=>'Cam\CamActivityController@list_nama_sampaket']);
        $router->post('add-file-tasklist', ['as'=>'list-file-add', 'uses'=>'Cam\CamActivityController@tampilfiletasklistmodal']);
        $router->post('edit-file-tasklist', ['as'=>'list-file-edit', 'uses'=>'Cam\CamActivityController@tampilfiletasklist']);
        $router->post('saveTask', ['as'=>'saveTask', 'uses'=>'Cam\CamActivityController@saveTask']);
        $router->post('editTask', ['as'=>'editTask', 'uses'=>'Cam\CamActivityController@editTask']);
        $router->put('updateTask', ['as'=>'updateTask', 'uses'=>'Cam\CamActivityController@updateTask']);
        $router->delete('deleteTask', ['as'=>'deleteTask', 'uses'=>'Cam\CamActivityController@deleteTask']);
        $router->post('insertKomen', ['as'=>'insertKomen', 'uses'=>'Cam\CamActivityController@insert_komen']);
        $router->post('insertCost', ['as'=>'insertCost', 'uses'=>'Cam\CamActivityController@insert_cost_after_save']);
        $router->post('insertFile', ['as'=>'insertFile', 'uses'=>'Cam\CamActivityController@insert_file_after_save']);
        $router->delete('deleteCostEntertaiment', ['as'=>'deleteCostEntertaiment', 'uses'=>'Cam\CamActivityController@hapus_entertaiment']);
        $router->post('getFile', ['as'=>'getFile', 'uses'=>'Cam\CamActivityController@get_file']);
        $router->post('listAm','Cam\CamActivityController@list_am_for');
        $router->get('listBirthdayClient','Cam\CamActivityController@list_birthday_client');
        $router->post('listClienthandling','Cam\CamActivityController@list_client_handling'); /*userid & idbu*/
        $router->post('listNotClienthandling','Cam\CamActivityController@list_client_not_handling'); /*userid & idbu*/
        $router->post('listCompanyAgency','Cam\CamActivityController@list_companyAgency_handling'); /*userid & position*/
        $router->post('listCompanyAdvertiser','Cam\CamActivityController@list_companyAdv_handling'); /*userid & position*/
        $router->post('listclientcompany','Cam\CamActivityController@search_client_company'); /*userid & position*/
        $router->post('listSummaryCost','Cam\CamActivityController@list_summary_cost'); /*userid & position*/
        $router->get('getprofiladvance/{id_client_account}','Cam\CamActivityController@getprofiladvance'); /*userid & position*/
        $router->post('saveProfileAdvance','Cam\CamActivityController@saveProfileAdvance'); /*userid & position*/
        $router->get('listHobby', 'Cam\CamActivityController@listHobby');
        $router->post('saveMyclient', 'Cam\CamActivityController@saveMyclient');
    });

    $router->group(['prefix'=>'saleskit'],function() use($router){
        // Route::get('details', 'Api\LoginController@details');
        Route::get('getIndex/{id_kategori}','Saleskit\GalleryController@getIndex');
        Route::get('getkategori','Saleskit\GalleryController@getkategori');
        Route::get('vers','Saleskit\GalleryController@version');
        Route::get('get-detail-article/{slug}/{id_kategori}','Saleskit\GalleryController@detail_article');
        Route::get('get_gallery_all_program_eloquent_tanpabu/{idGenre}/{idBu}','Saleskit\GalleryController@get_gallery_all_program_eloquent_tanpabu');
        Route::get('get_special_offers_eloquent_tanpabu/{idBu}','Saleskit\GalleryController@get_special_offers_eloquent_tanpabu');
        Route::get('get_rate_card_eloquent2_tanpabu/{idBu}','Saleskit\GalleryController@get_rate_card_eloquent2_tanpabu');
        Route::get('get_performance_tanpabu/{idBu}','Saleskit\GalleryController@get_performance_tanpabu');
        Route::get('get_gallery_all_tanpabu/{idBu}','Saleskit\GalleryController@get_gallery_all_tanpabu');
        Route::get('get_genre_program','Saleskit\GalleryController@get_genre_program');
        Route::post('getImage','Saleskit\GalleryController@get_image');
         //apibaru update nanti yang video
        Route::get('get_video_benefit2_tanpabu/{id_typespot}/{id_benefit}/{idBu}/{tvtype}','Saleskit\YoutubeController@get_video_benefit2_tanpabu');
        Route::get('get_video_typespot_tanpabu/{id_typespot}/{id_benefit}/{idBu}/{tvtype}','Saleskit\YoutubeController@get_video_typespot_tanpabu');
        Route::get('get_typespotbaru_tanpabu/{id_typespot}/{idBu}','Saleskit\YoutubeController@get_typespotbaru_tanpabu');
        Route::get('get_menu_fta','Saleskit\YoutubeController@get_menu_fta');
        Route::get('get_menu_paytv','Saleskit\YoutubeController@get_menu_paytv');
        Route::post('pagehitInsert','Saleskit\PagehitController@pagehitInsert');
        Route::get('get_typespotbaru/{id_typespot}/{tvtype}','Saleskit\YoutubeController@get_typespotbaru');
            //ini untuk filter / search gallery
        Route::post('filter_all','Saleskit\GalleryController@filter_all');
        Route::post('filter_gallery_all_program_eloquent_tanpabu','Saleskit\GalleryController@filter_gallery_all_program_eloquent_tanpabu');
        Route::post('filter_special_offers_eloquent_tanpabu','Saleskit\GalleryController@filter_special_offers_eloquent_tanpabu');
        Route::post('filter_rate_card_eloquent2_tanpabu','Saleskit\GalleryController@filter_rate_card_eloquent2_tanpabu');
        Route::post('filter_youtube','Saleskit\YoutubeController@filter_youtube');
        Route::post('filter_performance_tanpabu','Saleskit\GalleryController@filter_performance_tanpabu');


            //ini untuk filter / search youtube
        Route::post('filter_video_benefit2_tanpabu','Saleskit\YoutubeController@filter_video_benefit2_tanpabu');
        Route::post('filter_video_typespot_tanpabu','Saleskit\YoutubeController@filter_video_typespot_tanpabu');
        Route::post('filter_typespotbaru_tanpabu','Saleskit\YoutubeController@filter_typespotbaru_tanpabu');
    });

    $router->group(['prefix'=>'sam'],function() use($router){
        /*concept*/
        Route::group(['prefix'=>'concept'],function(){
            Route::get('request_type/{modul}','Sam\Concept\ConceptController@request_type');
            Route::get('benefit/{id}','Sam\Concept\ConceptController@benefit');
            Route::get('list_program','Sam\Concept\ConceptController@list_program');
            Route::post('list_brand','Sam\Concept\ConceptController@list_brand');
            Route::post('list_advertiser','Sam\Concept\ConceptController@list_advertiser');
            Route::post('list_agency','Sam\Concept\ConceptController@list_agency');
            Route::post('list_am','Sam\Concept\ConceptController@list_am');
            Route::post('list_client','Sam\Concept\ConceptController@list_client');
            Route::get('show_parameter/{id}','Sam\Concept\ConceptController@show_parameter');

        });

    });
});
