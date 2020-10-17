<?php

declare (strict_types = 1);

namespace App\Ivb\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ivb\App\Brand;
use App\Ivb\App\Model;
use App\Ivb\App\Connection;
use Illuminate\Support\Facades\Session;

use PDO;
use Exception;

require_once(app_path() . '\ivb\app\class.user.php');
require_once(app_path() . '\ivb\app\class.Brand.php');
require_once(app_path() . '\ivb\app\class.Model.php');
require_once(app_path() . '\ivb\app\class.dbconnect.php');


//biblioteka funkcija koje učitavaju podatke za različite delove korisničkog interfejsa
class UIHelperController extends Controller
{

    //lista brendova
    public static function getBrandData()
    {
        $trans = Connection::GetConnection();

        try {
            $result = Brand::GetAllBrands($trans);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $trans = null;

        return  response()->json($result, 200);
    }

    //lista modela za dati brend
    //ukoliko je id brenda nevalidan vraća se prazan skup
    public static function getModelData(int $id_brand,string $tip_vozila)
    {

        //učitava i vraća listu brendova iz tabele brands u JSON formatu 
        $trans = Connection::GetConnection();

        try {
            $result = Model::GetAllModelsForBrandAndType($trans, $id_brand, $tip_vozila);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $trans = null;

        return  response()->json($result, 200);
    }

    //lista vrste vozila
    //ukoliko je id brenda nevalidan vraća se prazan skup
    public static function getVrstaVozilaData()
    {
        try {
            //vrste vozila - prefix 100 u kodovima
            $result = UI_helper_DAL::getKodList('100');
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }

    public static function getTipoviKaroserijeData()
    {
        try {
            //vrste vozila - prefix 101 u kodovima
            $result = UI_helper_DAL::getKodList('101');
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }

    public static function getVrsteGorivaData()
    {
        try {
            //vrste goriva - prefix 200 u kodovima
            $result = UI_helper_DAL::getKodList('200');
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }


    //insert
    public static function InsertModel(Request $request)
    {

        $result = 0;
        $x = array();

        //da li je korisnik ulogovan i da li je admin
        if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
            $x['error']='not_auth';
            return  response()->json($x, 200);
        }

        try {
            $m = new Model;

            //neophodno zbog upisa
            $m->set_Id_model(-1);

            $m->set_Id_brand((int)$request->input("id_brand"));

            $m->set_Model($request->input("model"));

            $m->set_Tip_vozila($request->input("tip_vozila"));

            $result = $m->insertModel();
        } catch (Exception $e) {
            $x["error"] = $e->getMessage();
        }

        $x['success'] = ($result > 0);
        $x['id'] = ($result);

        return  response()->json($x, 200);
    }

    public static function InsertBrand(Request $request){

        $x=array();

        //da li je korisnik ulogovan i da li je admin
        if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
            $x['error']='not_auth';
            return  response()->json($x, 200);
        }

        $result=0 ; 

                try{ 
                    $b=new Brand;

                    //neophodno zbog upisa
                    $b->set_Id_brand(-1);

                    $b->set_Brand($request->input("brand"));

                    $result = $b->insertBrand();
                } catch(Exception $e) {
                    $x["error"] = $e->getMessage();
                }

        $x['success'] = ($result > 0);
        $x['id'] = ($result);

        return  response()->json($x, 200);
    }
}




//pristup bazi
class UI_helper_DAL
{

    //kodovi
    public static function getKodList(string $prefix): array
    {
        $result = array();
        $conn = Connection::GetConnection();
        $stmt = $conn->prepare("select id_kod, prefix, kod, naziv FROM kodovi where (prefix=:prefix) order by uxsort asc");
        $stmt->bindValue(':prefix', $prefix, PDO::PARAM_STR);
        $stmt->execute();
        $conn = null;
        $result = $stmt->fetchall(PDO::FETCH_ASSOC);
        if (!($result)) {
                $result = array();
            }

        return $result;
    }

}
 