<?php

declare (strict_types = 1);

namespace App\Ivb\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Exception;

use DateTime;




require_once(app_path() . '\ivb\app\class.komentar.php');



//komentari
class KomentarController extends Controller
{
    
    //komentari za ponudu
    public static function GetKomentariByIdPonuda(int $id_pon)
    {

        $x = null;

        $id_pon = (int)$_POST['id_pon'];


        try {
            $x = Komentar::getByIdPon($id_pon);
        } catch (Exception $e) {
            $x["error"] = $e->getMessage();
        }

        return  response()->json($x, 200);

    }

    //insert
    public static function InsertKomentar(Request $request){

        $auth_user = false;

        $result = 0;

        //da li je korisnik ulogovan
        if (Session::get('user')) {
            //da li je admin
            $auth_user = Session::get('user')['user_type_name'] == "Admin" || $auth_user = Session::get('user')['user_type_name'] == "StandardUser";
        }

        if (!$auth_user) {
            $result['error']="not_auth";
            return  response()->json($result, 200);
        }
        

        //insert
        try {

            $k = new komentar;

            //neophodno zbog upisa
            $k->id_kom = -1;

            $k->id_pon = (int)$request->input('id_pon');

            $k->user_id = (int)$request->input('user_id');

            $k->dat_kom = new DateTime($request->input('dat_kom'));

            $k->komentar = $request->input('komentar');

            $k->proc =(int) $request->input('proc');

            $result = $k->Insert();

        } catch (Exception $e) {

            $x["error"] = $e->getMessage();
        }

        $x['written'] = ($result > 0);

        return  response()->json($x, 200);

    }

}
 