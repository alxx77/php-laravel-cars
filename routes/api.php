<?php

use Illuminate\Http\Request;
use App\Ivb\App\UserController;
use App\Ivb\App\CMSPageController;
use App\Ivb\App\UIHelperController;
use App\Ivb\App\ImagesController;
use App\Ivb\App\PonudaController;
use Illuminate\Support\Facades\Redirect;
use App\Ivb\App\KomentarController;
use Illuminate\Support\Facades\Route;


require_once(app_path().'\ivb\app\user_controller.php');
require_once(app_path().'\ivb\app\cmspages_controller.php');
require_once(app_path().'\ivb\app\ui_helper_controller.php');
require_once(app_path().'\ivb\app\images_controller.php');
require_once(app_path().'\ivb\app\ponuda_controller.php');
require_once(app_path().'\ivb\app\komentar_controller.php');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//korisnici
//vrati autentifikovanog korisnika
Route::post('/users/get_auth_user', function () {
    return UserController::GethAuthUser();
});


//vrati korisnika po id-ju
Route::post('/users/get_user_by_id', function(Request $request) {

    return UserController::GetUserById((int) $request->input("user_id"));

});

//login
Route::post('/users/login', function(Request $request) {

    return UserController::LoginUser($request);

});

//registracija
Route::post('/users/register', function(Request $request) {

    return UserController::RegisterUser($request);

});


//update
Route::post('/users/update', function(Request $request) {


    return UserController::UpdateUser($request);

});

//lista svih korisnika
Route::post('/users/get_list', function() {

    return UserController::GetUsersIdList();

});



//CMS
Route::post('/cms/get_cms_pages_info_by_root', function(Request $request) {

    return CMSPageController::GetCMSPagesInfoByRoot($request->input("rootx"));

});

Route::post('/cms/get_cms_page_data', function(Request $request) {

    return CMSPageController::GetCMSPageData((int) $request->input("id_page"));

});

Route::post('/cms/insert_cms_page', function(Request $request) 
{



    return CMSPageController::InsertCMSPage($request->input("rootx")
                                            ,$request->input("pathx")
                                            ,(int) $request->input("node_type")
                                            ,(int)  $request->input("idx")
                                            ,$request->input("descr"));


});

Route::post('/cms/update_cms_page_data', function(Request $request) 
{


    return CMSPageController::UpdateCMSPageData((int) $request->input("id_page"),$request->input("editor1"));

});

Route::post('/cms/delete_cms_page', function(Request $request) 
{

    return CMSPageController::DeleteCMSPage((int) $request->input("id_page"));

});



//UI Helper
//lista brendova
Route::post('/ui/get_brand_data', function(Request $request) 
{
    return UIHelperController::getBrandData();
});

//insert novog brenda
Route::post('/ui/insert_brand', function(Request $request) 
{
    return UIHelperController::InsertBrand($request);

});


//lista modela za dati model
Route::post('/ui/get_model_data', function(Request $request) 
{
    $id_brand=(int) $request->input("id_brand");
    $tip_vozila=$request->input("tip_vozila");

    return UIHelperController::getModelData($id_brand,$tip_vozila);

});


//insert novog modela
Route::post('/ui/insert_model', function(Request $request) 
{

    return UIHelperController::InsertModel($request);

});



//vrsta vozila 
Route::post('/ui/get_vrsta_vozila_data', function() 
{
    return UIHelperController::getVrstaVozilaData();

});

//tipvi karoserije
Route::post('/ui/get_tipovi_karoserije_data', function() 
{
    return UIHelperController::getTipoviKaroserijeData();

});

//vrsta goriva
Route::post('/ui/get_vrsta_goriva_data', function(Request $request) 
{
    return UIHelperController::getVrsteGorivaData();

});


//slike
//id default slike
Route::post('/images/get_default_image_id_by_id_pon', function(Request $request) 
{

    return ImagesController::GetDefaultImageIdByIdPon((int) $request->input("id_pon"));

});

//podaci o slici (bez same slike)
Route::post('/images/get_image_data', function(Request $request) 
{
    return ImagesController::GetImageData((int) $request->input("image_id"));
});

//podaci o svim slikama jedne ponude
Route::post('/images/get_images_data_by_id_pon', function(Request $request) 
{
    return ImagesController::GetImagesInfoByIdPon((int) $request->input("id_pon"));

});

//slika
Route::get('/images/get_image/{image_id}', function($image_id) 
{
    $image_id=(int) $image_id;

    $result=ImagesController::GetImage($image_id);

    if(isset($result['image'])){

    //heder
    header("Content-type: " . $result['image_type']);

    //slika
    echo $result['image'];
    }
});


//ponuda
//broj ponuda sa datim parametrima
Route::post('/ponuda/get_ponuda_count', function(Request $request) 
{
    $id_brand=(int) $request->input("id_brand");
    $active_only=(int) $request->input("active_only");

    return PonudaController::GetPonudaCount($id_brand,$active_only);

});

// 1 stranica ponude 
Route::post('/ponuda/get_ponuda_page', function(Request $request) 
{
    $page_size=(int) $request->input("page_size");
    $index_start=(int) $request->input("index_start");
    $id_brand=(int) $request->input("id_brand");
    $active_only=(int) $request->input("active_only");

    return PonudaController::GetPonudaPage($index_start,$page_size,$id_brand,$active_only);

});

//redni broj stranice koja sadrži datu ponudu
Route::post('/ponuda/get_page_by_id_pon', function(Request $request) 
{
    $page_size=(int) $request->input("page_size");
    $id_pon=(int) $request->input("id_pon");
    $id_brand=(int) $request->input("id_brand");
    $active_only=(int) $request->input("active_only");

    return PonudaController::GetPageByIdPon($id_pon,$page_size,$id_brand,$active_only);
});


//brisanje ponude
Route::post('/ponuda/delete_ponuda', function(Request $request) 
{

    $id_pon=(int) $request->input("id_pon");

    return PonudaController::DeletePonuda($id_pon);

});

//upis/izmena ponude
Route::post('/ponuda/upsert_ponuda', function(Request $request) 
{

    return PonudaController::WritePonuda($request);

});

//get ponuda
Route::post('/ponuda/get_ponuda_data', function(Request $request) 
{
    $id_pon=(int) $request->input("id_pon");

    return PonudaController::GetPonudaData($id_pon);

});

//statistika pregleda
Route::post('/update_view_stats', function(Request $request) 
{
    $id_stat_cntr=(int) $request->input("id_stat_cntr");
    $idxx=(int) $request->input("idxx");

    return PonudaController::UpdateViewStats($id_stat_cntr,$idxx);



});


//komentari
//komentar za ponudu
Route::post('/komentar/get_komentari_by_id_pon', function(Request $request) 
{
    $id_pon=(int) $request->input("id_pon");

    return KomentarController::GetKomentariByIdPonuda($id_pon);
});

//insert komentara
Route::post('/komentar/insert_komentar', function(Request $request) 
{

    return KomentarController::InsertKomentar($request);

});

//veličina stranice
Route::post('/set_list_view_page_size', function(Request $request) 
{
    $result=PonudaController::SetPageSize($request);

    return  response()->json(true, 200);

});












