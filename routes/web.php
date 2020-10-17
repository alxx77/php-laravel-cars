<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Ivb\App\Page;
use App\Ivb\App\PonudaController;

require_once(app_path() . '\ivb\app\class.CMSPage.php');


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//root

Route::get('/', function (Request $request) {

    $page_size=PonudaController::GetPageSize($request);

    return view('index',['page_size'=>$page_size]);

})->name('home');


//login
Route::get('/login', function () {
    return view('login');
})->name('login');

//logout
Route::get('/logout', function () {

    if (Session::get('user')) {
        Session::pull('user');
        Session::save();
    }

    return Redirect::route('login');
})->name('logout');



//editovanje ponude
Route::get('/edit', function (Request $request) {

    $id_pon=$request->input('id_pon');
    $action=$request->input('action');

    //da li je korisnik ulogovan i da li je admin
    if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
        return Redirect::route("not_auth_error");
    }


    if ($action=="insert" || $action="update") {
        return view('upsert_ponuda', ['id_pon' => $id_pon, 'action' => $request->$action]);
    } else {
        return view('general_error');
    }
    
})->name('edit');


Route::get('/delete', function (Request $request) {

    $id_pon=$request->input('id_pon');

    //da li je korisnik ulogovan i da li je admin
    if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
        return Redirect::route("not_auth_error");
    }

    return view('delete_ponuda', ['id_pon' => $id_pon]);
    
})->name('delete');



//prikaži CMS stranicu
Route::get('/show_cms_page', function (Request $request) {

    $id_page = $request->input('id_page');

    if(!$id_page || $id_page==0){
        return Redirect::route('general_error');
    }
    
    $pd = Page::getPageById((int)$id_page)[0];

    //učitaj podatke o stranici
    if (isset($pd['page_data'])) {
        $data = $pd['page_data'];
    } else {
        $data = '';
    }

    //prikaži - samo sadržaj body elementa se čuva u bazi
    $data = html_entity_decode(htmlspecialchars_decode($data));

    return view('showCMSPage', ['data' => $data]);

})->name('show_cms_page');


//cms editor
Route::get('/cms_editor', function (Request $request) {

    $id_page =(int) $request->input('id_page');

    if(!$id_page || $id_page==0){
        return Redirect::route('general_error');
    }

    return view('editor', ['id_page' => $id_page]);

});




//promena lozinke
Route::get('/reset_password', function () {
    return view('reset_password');
})->name('reset_password');


//mail za promenu lozinke poslat
Route::get('/reset_password_sent', function () {
    return view('reset_password_sent');
})->name('reset_password_sent');


//registracija
Route::get('/register', function () {
    return view('register');
})->name('register');

//obaveštenje
Route::get('/reg_success', function () {
    return view('reg_success');
})->name('reg_success');


//korisnički računi
Route::get('/acct_mgmt', function () {
    //da li je korisnik ulogovan i da li je admin
    if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
        return Redirect::route("not_auth_error");
    }
    return view('acct_mgmt');
})->name('acct_mgmt');

//podešavanja aplikacije
Route::get('/settings', function () {

    //da li je korisnik ulogovan i da li je admin
    if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
        return Redirect::route("not_auth_error");
    }
     return view('settings');
})->name('settings');

//kontakt
Route::get('/contact', function () {
    return view('contact');
})->name('contact');



//greške

//opšta greška u obradi
Route::get('/general_error', function () {
    return view('err_general_error', ['error_message' => 'Greška...!']);
})->name('general_error');

//greška pri logovanju
Route::get('/login_error', function () {
    return view('err_general_error', ['error_message' => 'Greška - korisničko ime ili lozinka nisu ispravni...!']);
})->name('login_error');

//pristup nije dozvoljen
Route::get('/not_auth_error', function () {
    return view('err_general_error', ['error_message' => 'Greška - pristup nije dozvoljen...!']);
})->name('not_auth_error');

//greška u registraciji
Route::get('/user_reg_error', function () {
    return view('err_general_error', ['error_message' => 'Greška pri registraciji korisnika...!']);
})->name('user_reg_error');

Route::get('/user_not_found_error', function () {
    return view('err_general_error', ['error_message' => 'Greška - korisnik ne postoji...!']);
})->name('user_not_found_error');
 