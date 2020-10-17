<?php

declare (strict_types = 1);

namespace App\Ivb\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Exception;

require_once(app_path() . '\ivb\app\class.dbconnect.php');
require_once(app_path() . '\ivb\app\class.ImageProxy.php');


class ImagesController extends Controller
{

    //podrazumevana slika
    public static function GetDefaultImageIdByIdPon(int $id_pon)
    {

        $result = null;

        try {
            $result = ImageProxy::GetDefaultImageId($id_pon);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }

    //podaci za sliku
    public static function GetImageData(int $image_id)
    {

        $result = array();

        try {
            $result = ImageProxy::getImageinfo($image_id);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }

    //samo informacije o slici, bez samih podataka
    public static function GetImagesInfoByIdPon(int $id_pon)
    {

        $result = null;

        //Samo podaci o slikama bez samih slika
        try {
            $result = ImageProxy::GetImagesInfoByIdPon($id_pon);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return  response()->json($result, 200);
    }

    //samo informacije o slici, bez samih podataka
    public static function GetImage(int $image_id):array
    {
        return ImageProxy::getImageDataByImageId($image_id);
    }




}
 