<?php
declare(strict_types=1);

namespace App\Ivb\App;

use PDO;
use Exception;

require_once(app_path().'\ivb\app\class.dbconnect.php');

class Model
{
    private $id_model;
    private $id_brand;
    private $model_nr;
    private $model;
    private $tip_vozila;

    public function get_Id_model(){ return $this->id_model; } public function set_Id_model(int $value) { $this->id_model=$value; }
    public function get_Id_brand(){ return $this->id_brand; } public function set_Id_brand(int $value) { $this->id_brand=$value; }
	public function get_Model_nr(){ return $this->model_nr; } public function set_Model_nr(string $value) { $this->model_nr=$value; }
	public function get_Model(){ return $this->model; } public function set_Model(string $value) { $this->model=$value; }
    public function get_Tip_vozila(){ return $this->tip_vozila; } public function set_Tip_vozila(string $value) { $this->tip_vozila=$value; }

    //vraća instancu klase
    public static function getModelById(int $id_model):?Model
    {
        $model = null;

		try
		{
            $trans=Connection::GetConnection();

			$data = self::GetModelDataById($trans,$id_model);

            $trans=null;

			//instanciraj
			$model=new Model;

            $model->set_Id_model((int) $data['id_model']); 
			$model->set_Id_brand((int) $data['id_brand']); 
			$model->set_Model_nr($data['model_nr']); 
			$model->set_Model($data['model']); 
            $model->set_Tip_vozila($data['tip_vozila']); 

		}
		catch(Exception $e)
		{
			throw $e;
		}

		
		// vrati rezultat
		return $model;

    }

    //upiši instancu
    public function insertModel():int
    {
        $result=0;

		try
		{
            $trans=Connection::GetConnection();

			$result = self::insertModelData($trans,$this);

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
    public static function GetModelDataById(PDO $trans,int $id_model)
    {
		$stmt = $trans->prepare("SELECT id_model,id_brand,model,model_nr,tip_vozila FROM models
                                 where (models.id_model=:id_model)");
		$stmt->bindValue(':id_model', $id_model, PDO::PARAM_INT);
		$stmt->execute();
		$result=$stmt->fetch(PDO::FETCH_ASSOC);
		if(!($result))
		{
			$result= array();
		}
		return  $result;
    }


    //vraća celu modele za brend i vrstu vozila
    public static function GetAllModelsForBrandAndType(PDO $trans,int $id_brand,string $tip_vozila)
    {
		$stmt = $trans->prepare("SELECT id_model,id_brand,model,model_nr,tip_vozila FROM models
                                 where ((models.id_brand=:id_brand) and (models.tip_vozila=:tip_vozila)) order by id_model asc");
        $stmt->bindValue(':id_brand', $id_brand, PDO::PARAM_INT);
        $stmt->bindValue(':tip_vozila', $tip_vozila, PDO::PARAM_STR);                         
		$stmt->execute();
		$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		if(!($result))
		{
			$result= array();
		}
		return  $result;
    }

    //insert reda u bazu
    public static function insertModelData(PDO $trans, Model $model):int
    {
        //id mora biti -1 da bi se upisao novi red
        if($model->get_Id_model()!=-1)
        {
            throw new Exception("Id mora biti -1 pri insertu");
        }

		$stmt = $trans->prepare("CALL InsertModel (:id_brand,:model,:tip_vozila)");

        $stmt->bindValue(':id_brand', $model->get_Id_brand(), PDO::PARAM_INT);
        $stmt->bindValue(':model', $model->get_Model(), PDO::PARAM_STR);
        $stmt->bindValue(':tip_vozila', $model->get_Tip_vozila(), PDO::PARAM_STR);

		$stmt->execute();

        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        //upiši vrednosti
        $model->set_Id_model((int) $result['id_model']);
        $model->set_Model_nr($result['model_nr']);

        return $model->get_Id_model();
    }



}




?>