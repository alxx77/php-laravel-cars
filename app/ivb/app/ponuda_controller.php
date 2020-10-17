<?php

declare (strict_types = 1);

namespace App\Ivb\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PDO;
use PDOStatement;
use DateTime;
use Illuminate\Support\Facades\Session;
use Exception;


require_once(app_path() . '\ivb\app\class.dbconnect.php');
require_once(app_path() . '\ivb\app\class.ponuda.php');
require_once(app_path() . '\ivb\app\class.ImageProxy.php');

class PonudaController extends Controller
{
    //broj ponuda
    public static function GetPonudaCount(int $id_brand, int $active_only)
    {
        $result = 0;

        try {
            $result = Ponuda::GetPonudaCount($id_brand, $active_only);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return response()->json($result, 200);
    }

    //stranica sa ponudama
    public static function GetPonudaPage(int $index_start, int $page_size, int $id_brand, int $active_only)
    {
        $result = array();

        try {

            $result = Ponuda::GetPonudaPage($index_start, $page_size, $id_brand, $active_only);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }

    //rb. stranice sa datom ponudom 
    public static function GetPageByIdPon(int $id_pon, int $page_size, int $id_brand, int $active_only)
    {
        try {
            $result = Ponuda::GetPageById($id_pon, $page_size, $id_brand, $active_only);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }


    //obriši ponudu
    public static function DeletePonuda(int $id_pon)
    {

        //da li je korisnik ulogovan i da li je admin
        if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
            return  response()->json(['error' => 'not_auth_error'], 200);
        }


        $result = null;
        $result_slike=null;

        $trans = Connection::GetConnection();

        //transakcioni blok
        $trans->beginTransaction();
        try {

            //slike
            $result_slike = ImageProxy::DeleteByPonudaId($trans, $id_pon);

            //pokušaj da obrišeš ponudu
            $result = Ponuda::deletePonuda($trans, $id_pon);

            if($result_slike>0 && $result==true){
            //izvrši
                $trans->commit();
            }else{
                $trans->rollBack();
            }

        } catch (Exception $e) {
            //ako dođe do greške
            $trans->rollBack();
        }

        //rezultat
        $x = ($result == true) ? array('deleted' => true) : array('deleted' => false);

        return  response()->json($x, 200);
    }


    //upiši ponudu
    public static function WritePonuda(Request $request)
    {

        //da li je korisnik ulogovan i da li je admin
        if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
            return  response()->json(['error' => 'not_auth_error'], 200);
        }

        try {
            $x = WritePonudaHelper::WritePonuda($request);
        } catch (Exception $e) {
            $x['error'] = $e->getMessage();
        }

        return  response()->json($x, 200);
    }

    public static function GetPonudaData(int $id_pon)
    {

        $result = null;


        $trans = Connection::GetConnection();

        try {
            $result = Ponuda::getById($trans, $id_pon);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return  response()->json($result, 200);
        }

        $x = $result->ToRow();

        //ne dozvoli prosleđivanje null vrednosti u stringovima

        $x['tip'] = $x['tip'] ?? '';
        $x['opis'] = $x['opis'] ?? '';
        $x['tip_zatv'] = $x['tip_zatv'] ?? '';
        $x['a000_karoserija'] = $x['a000_karoserija'] ?? '';
        $x['a001_broj_vrata'] = $x['a001_broj_vrata'] ?? '';
        $x['a002_tip_goriva'] = $x['a002_tip_goriva'] ?? '';
        $x['a003_polovno'] = $x['a003_polovno'] ?? '';

        return  response()->json($x, 200);
    }

    //pregledi
    public static function UpdateViewStats(int $id_stat_cntr, int $idxx)
    {
        //izlaz
        $result = 0;

        //update
        try {

            $conn = Connection::GetConnection();

            $stmt = $conn->prepare("call IncrementStatsCounter(:id_stat_cntr,:idxx)");
            $stmt->bindValue(':id_stat_cntr', $id_stat_cntr, PDO::PARAM_INT);
            $stmt->bindValue(':idxx', $idxx, PDO::PARAM_INT);
            $stmt->execute();

            $conn = null;
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }

    public static function SetPageSize(Request $request)
    {

        $page_size = (int)$request->input("page_size");

        if ($page_size == 2 || $page_size == 3 || $page_size = 5) {

            $request->session()->pull('page_size');
            $request->session()->put('page_size', $page_size);
            $request->session()->save();
        }

        return $request->session()->get('page_size');
    }

    public static function GetPageSize(Request $request)
    {

        return $request->session()->get('page_size');
    }
}





class WritePonudaHelper
{

    //upis
    public static function WritePonuda(Request $request): array
    {
        $success = false;

        $result = array();

        $rec_no = 0;

        $action = $request->input("action");
        $id_pon = (int)$request->input("id_pon");

        //kreiraj instancu objekta
        $p = self::CreateFromRow($request);


        //proveri da li je insert i id=-1
        //ako nije izađi
        if ($action == 'insert' && $id_pon != -1) {
            throw new Exception('Pri upisu novog sloga id mora biti -1');
        }

        //proveri da li je update i id<>-1
        if ($action == 'update' && $id_pon < 0) {
            throw new Exception('Pri izmeni postojećeg sloga id ne sme biti -1');
        }

        //image proxy
        $ip = null;

        //nov konekcioni objekt
        $trans = Connection::GetConnection();

        //transakcioni blok
        $trans->beginTransaction();
        try {

            //upiši ponudu
            if ($action == 'insert') {
                //nov unos
                $id_pon = $p->insertPonuda($trans);

                if ($id_pon > 0) {
                    $rec_no = 1;
                }
            } else {
                if ($action == 'update') {
                    //postojeći unos
                    //rezultat je broj redova koji su izmenjeni
                    //ako je operacija bila uspešna to treba biti 1
                    $rec_no = $p->updatePonuda($trans);
                } else {
                    throw new Exception('Nedefinisana akcija');
                }
            }

            //slike

            //briši sve slike ponude
            ImageProxy::DeleteByPonudaId($trans, $id_pon);

            $files = $request->allFiles();

            //upiši slike
            foreach ($files as $key => $val) {

                //napravi instancu objekta imageproxy
                $ip = ImageProxy::getImageProxy($val, $id_pon, $key);

                //pokušaj sliku da upišeš ukoliko postoji
                if ($ip != null) {
                    //upiši
                    $ip->WriteImage($trans);
                }
            }

            //izvrši
            $trans->commit();

            //rezultat
            $success = true;
        } catch (Exception $e) {

            //ako dođe do greške
            $trans->rollBack();

            $result['error'] = $e;
        }

        //izlaz

        $result["success"] = $success;

        $result["id_pon"] = $id_pon;

        $result["rec_no"] = $rec_no;



        return $result;
    }




    //kreira novu instancu objekta Ponuda sa podacima iz unosa korisnika
    private static function CreateFromRow(Request $request): Ponuda
    {

        //nova ponuda
        $p = new Ponuda();

        //da bi insert bio moguć
        $p->set_Id_pon((int)$request->input("id_pon"));


        //pokušaj da parsiraš sve parametre
        //počinje se od broja ponude

        //svi polja objekta
        //se trebaju setovati

        //broj ponude se automatski generiše u bazi za nove unose
        //za postojeći br. ponude svejedno se ignoriše pri upisu izmena
        $p->set_Brpon('00000');



        //id brend mora biti int>0
        $id_brand = 0;
        if (self::TryGetPositiveInt($request->input("id_brand"), $id_brand) == true) {
            $p->set_Id_brand($id_brand);
        } else {
            throw new Exception();
        }

        //id model mora biti int>0 
        $id_model = 0;
        if (self::TryGetPositiveInt($request->input("id_model"), $id_model) == true) {
            $p->set_Id_model($id_model);
        } else {
            throw new Exception();
        }

        //tip
        $p->set_Tip($request->input("tip"));



        //godina proizvodnje
        $god_proizv = 0;
        if (self::TryGetPositiveInt($request->input("god_proizv"), $god_proizv) == true) {
            $p->set_God_proizv($god_proizv);
        } else {
            throw new Exception();
        }

        //tip karoserije
        $p->set_A000_karoserija($request->input("a000_karoserija"));

        //tip goriva
        $p->set_A002_tip_goriva($request->input("a002_tip_goriva"));

        //kilometraža
        $a006_km = 0.0;
        if (self::TryGetNonNegativeDecimal($request->input("a006_km"), $a006_km) == true) {
            $p->set_A006_km($a006_km);
        } else {
            throw new Exception();
        }

        //zapremina motora
        $a004_ccm = 0.0;
        if (self::TryGetNonNegativeDecimal($request->input("a004_ccm"), $a004_ccm) == true) {
            $p->set_A004_ccm($a004_ccm);
        } else {
            throw new Exception();
        }

        //snaga motora
        $a005_kw = 0.0;
        if (self::TryGetNonNegativeDecimal($request->input("a005_kw"), $a005_kw) == true) {
            $p->set_A005_kw($a005_kw);
        } else {
            throw new Exception();
        }


        //iznosi se zaokružuju na 2 decimale
        //bruto cena
        $bruto_cena = 0.0;
        if (self::TryGetNonNegativeDecimal($request->input("bruto_cena_val"), $bruto_cena) == true) {
            $p->set_Bruto_cena_val($bruto_cena);
        } else {
            throw new Exception();
        }

        //neto_cena
        $neto_cena = 0.0;
        if (self::TryGetNonNegativeDecimal($request->input("neto_cena_val"), $neto_cena) == true) {
            $p->set_Neto_cena_val($neto_cena);
        } else {
            throw new Exception();
        }

        //pdv
        $pdv = 0.0;
        if (self::TryGetNonNegativeDecimal($request->input("pdv_cena_val"), $pdv) == true) {
            $p->set_Pdv_cena_val($pdv);
        } else {
            throw new Exception();
        }

        //mogućnost korišćenja pdv
        $p->set_Vat_ddct($request->input("vat_ddct") === 'true' ? true : false);

        //opis
        $p->set_Opis($request->input("opis"));

        //datum ponude
        if ((null !== $request->input("dat_pon")) && DateTime::createFromFormat('Y-m-d H:i:s', $request->input("dat_pon"))) {
            $p->set_Dat_pon(new DateTime($request->input("dat_pon")));
        } else {
            $p->set_Dat_pon(new DateTime('1000-01-01 00:00:00'));
        }

        //datum zatvaranja
        if ((null !== $request->input("dat_zatv")) && DateTime::createFromFormat('Y-m-d H:i:s', $request->input("dat_zatv"))) {
            $p->set_Dat_zatv(new DateTime($request->input("dat_zatv")));
        } else {
            $p->set_Dat_zatv(new DateTime('1000-01-01 00:00:00'));
        }


        //vrati ponudu
        return $p;
    }



    //proverava da li prosleđeni tekstualni argument
    //pozitivan int
    private static function TryGetPositiveInt(string $value, int &$int1): bool
    {
        //izlaz
        $x = 0;

        //izlaz 2
        $result = false;

        //ako je broj
        if (ctype_digit($value) == true) {
            //kast u int
            $x = (int)$value;

            //ako je pozitivan
            if ($x > 0) {
                //postavi izlaz
                $int1 = $x;

                //true
                $result = true;
            }
        }

        return $result;
    }

    //proverava da li prosleđeni tekstualni argument
    //decimalni broj veći ili jednak 0
    private static function TryGetNonNegativeDecimal(string $value, float &$dec): bool
    {
        //izlaz
        $x = 0;

        //izlaz 2
        $result = false;

        try {
            $x = round(floatval($value), 2);

            if ($x >= 0) {
                $dec = $x;
                $result = true;
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $result;
    }
}
