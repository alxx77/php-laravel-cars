<?php
declare(strict_types=1);

namespace App\Ivb\App;
use Exception;

use PDO;

require_once(app_path().'\ivb\app\class.dbconnect.php');


class Brand
{

    private $id_brand;
    private $brand_nr;
    private $brand;

    public function get_Id_brand(){ return $this->id_brand; } public function set_Id_brand(int $value) { $this->id_brand=$value; }
	public function get_Brand_nr(){ return $this->brand_nr; } public function set_Brand_nr(string $value) { $this->brand_nr=$value; }
	public function get_Brand(){ return $this->brand; } public function set_Brand(string $value) { $this->brand=$value; }

    //vraća instancu klase
    public static function getBrandById(int $id_brand)
    {
        $brand = null;

		try
		{
            $trans=Connection::GetConnection();

			$data = self::getBrandDataById($trans,$id_brand);

            $trans=null;

			//instanciraj
			$brand=new Brand;

			$brand->set_Id_brand((int) $data['id_brand']); 
			$brand->set_Brand_nr($data['brand_nr']); 
			$brand->set_Brand($data['brand']); 

		}
		catch(Exception $e)
		{
			throw $e;
		}

		//vrati rezultat
		return $brand;

    }

    //upiši instancu
    public function insertBrand():int
    {
        $result=0;

		try
		{
            $trans=Connection::GetConnection();

			$result = self::insertBrandData($trans,$this);

            $trans=null;

		}
		catch(Exception $e)
		{
			throw $e;
		}

		
		// vrati rezultat - true ako je id>0
		return $result;
    }

    //DA****************************

    //vraća podatke za 1 red
    public static function GetBrandDataById(PDO $trans,int $id_brand):array
    {
		$stmt = $trans->prepare("SELECT id_brand,brand_nr,brand FROM brands where (id_brand=:id_brand)");
		$stmt->bindValue(':id_brand', $id_brand, PDO::PARAM_INT);
		$stmt->execute();
		$result=$stmt->fetch(PDO::FETCH_ASSOC);
		if(!($result))
		{
			$result = array();
		}
		return  $result;
    }


    //vraća celu tabelu
    public static function GetAllBrands(PDO $trans):array
    {
		$stmt = $trans->prepare("SELECT id_brand,brand_nr,brand FROM brands order by brand asc");
		$stmt->execute();
		$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		if(!($result))
		{
			$result= array();
		}
		return  $result;
    }

    //insert reda u bazu
    public static function insertBrandData(PDO $trans, Brand $brand):int
    {
        //id mora biti -1 da bi se upisao novi red
        if($brand->get_Id_brand()!=-1)
        {
            throw new Exception("Id pri insertu treba biti -1");
        }

		$stmt = $trans->prepare("CALL InsertBrand (:brand)");

        $stmt->bindValue(':brand', $brand->get_Brand(), PDO::PARAM_STR);

		$stmt->execute();

        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        //upiši vrednosti
        $brand->set_Id_brand((int) $result['id_brand']);
        $brand->set_Brand_nr($result['brand_nr']);

        return $brand->get_Id_brand();
    }



}




?>