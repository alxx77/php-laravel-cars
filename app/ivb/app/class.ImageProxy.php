<?php
declare (strict_types = 1);

namespace App\Ivb\App;

use Illuminate\Support\Facades\Input;

use PDO;
use Symfony\Component\HttpFoundation\FileBag;
use Illuminate\Http\UploadedFile;
use Exception;

require_once(app_path() . '\ivb\app\class.dbconnect.php');
require_once(app_path() . '\ivb\app\class.ImageProxy_DAL.php');


//slika
class ImageProxy
{
    //id slike
    private $image_id;

    //id ponude
    private $id_pon;

    //redni broj slike
    private $img_number;

    //mime tip
    private $image_type;

    //file stream
    private $imgfp;

    //širina slike
    private $image_width;

    //visina slike
    private $image_height;

    //mala slika
    private $image_thumb;

    //širina
    private $thumb_width;

    //visina
    private $thumb_height;

    //naziv slike
    private $image_name;

    //max. veličina
    private $maxsize;
    //***************


    public function get_Image_id(): int
    {
        return $this->image_id;
    }

    public function set_Image_id(int $id)
    {
        $this->image_id = $id;
    }

    public function get_Id_pon(): int
    {
        return $this->id_pon;
    }

    public function get_Image_number(): int
    {
        return $this->img_number;
    }

    public function get_Image_type()
    {
        return $this->image_type;
    }



    public function get_Imgfp()
    {
        return $this->imgfp;
    }

    public function get_Image_width(): int
    {
        return $this->image_width;
    }
    public function get_Image_height(): int
    {
        return $this->image_height;
    }

    public function get_Image_thumb()
    {
        return $this->image_thumb;
    }
    public function get_Thumb_width()
    {
        return $this->thumb_width;
    }
    public function get_Thumb_height()
    {
        return $this->thumb_height;
    }

    public function get_Image_name(): string
    {
        return $this->image_name;
    }



    //kreira instancu objekta
    public static function getImageProxy(UploadedFile $file, int $id_pon,$file_name): ImageProxy
    {

        //redni broj slike
        $img_nr = (int)substr($file_name, 4, 1);


        //napravi nov objekt
        $ip = new ImageProxy();

        //odmah proveri veličinu
        if ($file->getSize() > 1000000) {
            throw new Exception("Veličina slike prelazi maksimalnu dozvoljenu veličinu");
        }

        //detaljne informacije o slici
        $size = getimagesize($file->getRealPath());


        try {

            //id će biti poznat nakon inserta

            //id ponude
            $ip->id_pon = $id_pon;

            //sačuvaj i broj slike
            $ip->img_number = $img_nr;

            //mime tip
            $ip->image_type = $size['mime'];

            //data stream
            $ip->imgfp = $file->getRealPath();

            //širina slike
            $ip->image_width = $size[0];

            //visina slike
            $ip->image_height = $size[1];

            //naziv slike
            $ip->image_name = $file->getClientOriginalName();


        } catch (Exception $e) {
            throw $e;
        }

        return $ip;
    }


    //upis slike   
    public function WriteImage(PDO $trans)
    {
        ImageProxy_DAL::Write($trans, $this);
    }


    //briši sve slike ponude
    public static function DeleteByPonudaId(PDO $trans, int $id_pon): int
    {
        return ImageProxy_DAL::DeleteByPonudaId($trans, $id_pon);
    }


    //informativni podaci o svim slikama date ponude
    public static function GetImagesInfoByIdPon(int $id_pon): array
    {
        return  ImageProxy_DAL::GetImagesInfoByIdPon($id_pon);
    }


    //vraća naslovnu (podrazumevanu sliku za ponudu)
    public static function GetDefaultImageId(int $id_pon): int
    {
        return ImageProxy_DAL::getDefaultImageId($id_pon);
    }

    //info o slici
    public static function getImageDataByImageId(int $image_id): array
    {
        return ImageProxy_DAL::getImageDataByImageId($image_id);
    }

    //info o slici
    public static function getImageInfo(int $image_id): array
    {
        return ImageProxy_DAL::getImageInfo($image_id);
    }


}
 