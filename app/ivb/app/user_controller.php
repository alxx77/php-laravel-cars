<?php

declare (strict_types = 1);

namespace App\Ivb\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Exception;
use Illuminate\Support\Facades\Session;


require_once(app_path().'\ivb\app\class.user.php');

class UserController extends Controller
{

    //vraća korisnika
    public static function GethAuthUser()
    {
        //izlaz
        $result = session("user");

        if(!$result){
            $result=array();
        }
 
        //vrati rezultat
        return response()->json($result, 200);
    }


    //vraća korisnika po id-ju
    public static function GetUserById(int $id)
    {
        $result=array();

        try {
              $result=IVB_User::getUserById($id)->ToRow(  );
        } catch (Exception $e) {
            
            $result["error"] = $e->getMessage();
        }

        return response()->json($result, 200);
    }


    //vraća listu id-jeva svih korisnika
    public static function GetUsersIdList()
    {
        $result=array(); 

        try {
              $result=IVB_User::GetUsers();
        } catch   (Exception $e) {
            
            $result["error"] = $e->getMessage();
        }

        return response()->json($result, 200);
    }


    //insert/update korisnika
    public static function UpdateUser(Request $request)
    {

        if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
            return  response()->json(['error'=>'not_auth_error'], 200);
        }


        //ako je brisanje
        if($request->input('delete_user')=='true'){
            return self::DeleteUser((int) $request->input('user_id'));
        }

        //izlaz
        $result = 0;

        //update
        try {
            //ako se briše

                //inače u pitanju je izmena podataka korisnika

                //učitaj korisnika i generiši objekt
                $u = IVB_User::GetUserById((int)$request->input('user_id'));

                //ako je uspešno učitan
                if ($u != null) {

                    //generiši password hash
                    $u->setPasswordHash(md5($request->input('password') . $u->getPasswordSalt()));

                    //postavi tip korisnika
                    $u->setUserType((int)$request->input('user_type'));

                    //postavi email
                    $u->setEmail($request->input('user_email'));

                    //postavi korisničko ime
                    $u->setUserName($request->input('username'));

                    //upiši izmenu
                    $result = $u->UpdateUser();
                } else {
                    //ako user nije uspešno učitan vrati grešku
                    $x["error"]='Korisnik sa datim id-jem ne postoji u bazi...!';
                }
            
        } catch (Exception $e) {
            $x["error"] = $e->getMessage();
        }


        $x['result'] = $result;


        return response()->json($x, 200);
    }

    //brisanje
    public static function DeleteUser(int $user_id){

        $x=array();

        try {
            $x['result'] = IVB_User::DeleteUser($user_id);
        } catch (Exception $e) {
            $x["error"] = $e->getMessage();
        }
            
        return response()->json($x, 200);

    }




    public static function LoginUser(Request $request){
    
        try {

            //pokušaj da nađeš korisnika
            $u = IVB_User::getUserByEmail($request->input('email'));

            if(!$u){
                throw new Exception('Korisnik ne postoji...!');
            }

            //ako korisnik postoji
            //i hash lozinke + salt se slaže sa onim iz baze

            //password hash iz baze (md5 pwd+salt)
            $dbpwdhash=$u->getPasswordHash();

            //password iz GUIa
            $uipwd=$request->input('password');

            //salt 
            $dbpwdsalt=$u->getPasswordSalt();

            //md5 podataka
            $md5hash=md5($uipwd.$dbpwdsalt);

            if (!($u === null) && ($dbpwdhash == $md5hash)) {

                //postavi korisnika u sesijsku promenljivu
                $request->session()->put("user",$u ->ToRow());

                $request->session()->save();

            }
        } catch (Exception $e) {

            return Redirect::route('login_error');
        }

        return Redirect::route("home");
    }


    //registracija novog korisnika
    public static function RegisterUser(Request $request){
    
        $u = new IVB_User();
        $u->setEmail($request->input('email'));
        $u->setUserName($request->input('uname'));

        //Dozvoljeni karatkteri
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $count = strlen($chars);

        // Generisanje 12 random bajtova
        $bytes = random_bytes(12);

        //izlaz
        $new_password = '';

        //podeli niz slučajnih bajtova na pojedinačne karaktere i spoj ih
        foreach (str_split($bytes) as $byte) {
            $new_password .= $chars[ord($byte) % $count];
        }

        //generiši novi salt
        $pwdsalt = $new_password;

        $u->setPasswordSalt($pwdsalt);

        $u->setPasswordHash(md5(trim($request->input('password')) . $pwdsalt));

        //default je standardni korisnik
        try {
            $u->InsertUser();
        } catch (Exception $e) {
            return Redirect::route('user_reg_error');
        }

        return Redirect::route('reg_success');
 
    }

}



 