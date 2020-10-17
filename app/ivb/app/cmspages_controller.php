<?php

declare (strict_types = 1);

namespace App\Ivb\App;

use DateTime;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Exception;

require_once(app_path() . '\ivb\app\class.cmspage.php');

class CMSPageController
{

    public static function GetCMSPagesInfoByRoot(string $rootx)
    {

        $x = null;

        try {
            $x = Page::getPageInfoByRoot($rootx);
        } catch (Exception $e) {
            $x['error'] = $e->getMessage();
        }

        return  response()->json($x, 200);
    }


    public static function GetCMSPageData(int $id_page){

        $x = array();

        try {
            $x = Page::getPageById($id_page);
        } catch (Exception $e) {
            $x['error']=$e->getMessage();
        }

        return  response()->json($x, 200);

    }


    public static function DeleteCMSPage(int $id_page){

        //da li je korisnik ulogovan i da li je admin
        if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
            return  response()->json(['error' => 'not_auth_error'], 200);
        }
    
        $result = 0;

        try {

            $k = Page::getPageObjectById($id_page);

            $result = $k->DeletePage();
        } catch (Exception $e) {
            $x["error"] = $e->getMessage();
        }

        $x['success'] = ($result > 0);

        return  response()->json($x, 200);
    }


    //insert  
    public static function InsertCMSPage(
        string $rootx,
        string $pathx,
        int $node_type,
        int $idx,
        string $descr
    ){

        //da li je korisnik ulogovan i da li je admin
        if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
            return Redirect::route("not_auth_error");
        }

        $result = 0;

        //insert
        try {

            $k = new Page();

            //neophodno zbog upisa
            $k->id_page = -1;

            $k->rootx = $rootx;

            $k->pathx = $pathx;

            $k->node_type = $node_type;

            $k->idx = $idx;

            $k->descr = $descr;

            $k->dat_page = new DateTime();

            $result = $k->Insert();

        } catch (Exception $e) {

            return Redirect::route("general_error");

        }

        return Redirect::route("settings");
    }


    public static function UpdateCMSPageData(int $id_page,string $editor1){

        //da li je korisnik ulogovan i da li je admin
        if (!(Session::get('user') && Session::get('user')['user_type_name'] == "Admin")) {
            return Redirect::route("not_auth_error");
        }


         //izlaz
        $result = 0;


        //update
        try {

            $encoded_html="";

            $html = $editor1;

            $page = Page::getPageObjectById($id_page);

            if ($page) {

                $encoded_html=htmlentities($html);

                $page->page_data = $encoded_html;

            } else {

                return Redirect::route("general_error");
            }

            $result=$page-> UpdatePageData();

        } catch (Exception $e) {

            return Redirect::route("general_error");

        }

        return Redirect::route("settings");

    }


}
 