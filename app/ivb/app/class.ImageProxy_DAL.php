<?php
declare(strict_types=1);

namespace App\Ivb\App;

use PDO;
use Exception;

require_once(app_path() . '\ivb\app\class.dbconnect.php');
	
    class ImageProxy_DAL 
    {
    
	//slika
	public static function getImageDataByImageId(int $image_id):array 
	{

		$trans=Connection::GetConnection();

		try
		{
					
			$query="
			SELECT `slike`.`image_id`,
			`slike`.`id_pon`,
			`slike`.`img_num`,
			`slike`.`image_type`,
			`slike`.`image`,
			`slike`.`image_height`,
			`slike`.`image_width`,
			`slike`.`image_thumb`,
			`slike`.`thumb_height`,
			`slike`.`thumb_width`,
			`slike`.`image_name`,
			`slike`.`dat_ent`
			FROM `slike`
				 WHERE (image_id=:image_id)
             		";
			
			$stmt = $trans->prepare($query);
			
			//broj ponude nije moguće promeniti
			$stmt->bindValue(':image_id',$image_id,PDO::PARAM_INT);
			
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		
		catch(Exception $e)
		{
			throw $e;
		}
		
		if(!($row))
			{
				$row=array();
			}
			
		return $row;
		
	}
  
	//informativni podaci o svim slikama ponude
	public static function GetImagesInfoByIdPon(int $id_pon):array
	{
			
			$trans=Connection::GetConnection();
			$query="SELECT `slike`.`image_id`,
					`slike`.`id_pon`,
					`slike`.`img_num`,
					`slike`.`image_type`,
					`slike`.`image_height`,
					`slike`.`image_width`,
					`slike`.`thumb_height`,
					`slike`.`thumb_width`,
					`slike`.`image_name`,
					`slike`.`dat_ent`
					from slike
					where (id_pon=:id_pon)
					order by img_num asc;
					";
			
			$stmt = $trans->prepare($query);		
			$stmt->bindValue(':id_pon', $id_pon, PDO::PARAM_INT);
			$stmt->execute();
			//može biti više redova
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if(!($row))
			{
				$row=array();
			}
			
			return $row;
	}
			
	
		
	
    //informativni podaci o slici
	public static function getImageInfo(int $image_id):array
	{
		
	    $trans=Connection::GetConnection();
		$query="SELECT `slike`.`image_id`,
				`slike`.`id_pon`,
				`slike`.`img_num`,
				`slike`.`image_type`,
				`slike`.`image_height`,
				`slike`.`image_width`,
				`slike`.`thumb_height`,
				`slike`.`thumb_width`,
				`slike`.`image_name`,
				`slike`.`dat_ent`
				from slike
				where (image_id=:image_id)
				";
		
		$stmt = $trans->prepare($query);		
		$stmt->bindValue(':image_id', $image_id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if(!($row))
		{
			$row=array();
		}
		
		return $row;
	}

	//id podrazumevane slike (trebalo bi da to bude slika id_num==0, ako ne onda prva sa sledećim najmanjim brojem)
	public static function getDefaultImageId(int $id_pon):int
	{

		$id=-1;
			
		$trans=Connection::GetConnection();
			$query="SELECT `slike`.`image_id`
					from slike
					where (id_pon=:id_pon)
					order by img_num asc
					limit 1
					";
			
			$stmt = $trans->prepare($query);		
			$stmt->bindValue(':id_pon', $id_pon, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if($row)
			{
				if(count($row)>0)
				{
					$id=(int) $row['image_id'];
				}
			}
			
			return $id;
	}
    
    
    //upis slike
    public static function Write(PDO $trans,ImageProxy $imp)
    {




        //upit za upis
        //uvek prvo obriši sliku sa tim brojem
        //u slučaj da već postoji
        $stmt = $trans->prepare("
                                INSERT INTO slike (id_pon, img_num, image_type, image, image_height, image_width, image_thumb, thumb_height, thumb_width, image_name, dat_ent)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,NOW());
        ");
        
        //id ponuda
        $stmt->bindValue(1,$imp->get_Id_pon(), PDO::PARAM_INT);
        
        //broj slike
        $stmt->bindValue(2, $imp->get_Image_number(), PDO::PARAM_INT);
        
        //mime tip
        $stmt->bindValue(3, $imp->get_Image_type(),PDO::PARAM_STR);
		
	    //podaci 
        $stmt->bindValue(4, fopen($imp->get_Imgfp(), 'rb'), PDO::PARAM_LOB);
		
        //visina
        $stmt->bindValue(5, $imp->get_Image_height(), PDO::PARAM_INT);
        
        //širina
        $stmt->bindValue(6, $imp->get_Image_width(),  PDO::PARAM_INT);
        
        //thumbnail
        $stmt->bindValue(7, $imp->get_Image_thumb(),  PDO::PARAM_LOB);
        
        //širina i visina
        $stmt->bindValue(8, $imp->get_Thumb_height(), PDO::PARAM_INT);
        $stmt->bindValue(9, $imp->get_Thumb_width(),  PDO::PARAM_INT);
        
        //ime slike
		$stmt->bindValue(10, $imp->Get_Image_name(),PDO::PARAM_STR);
		
		try {
			        //izvrši
					$stmt->execute();
		} catch (\Throwable $th) {
			//throw $th;
		}
    

    } 

	//briši sve slike date ponude
	public static function DeleteByPonudaId(PDO $trans, int $id_pon):int
	{
		$row_count=0;
		
		try
		{
								
			$query="DELETE FROM `slike` WHERE (id_pon=:id_pon)";
			
			$stmt = $trans->prepare($query);
			
			$stmt->bindParam(':id_pon', $id_pon,PDO::PARAM_INT);
			
			//izvrši upit
			$stmt->execute();
			
			$row_count =(int) $stmt->rowCount();
					
		}
		
		catch(Exception $e)
		{
			throw $e;
		}
		
		return $row_count;
		
	}
    
    }
    
    ?>