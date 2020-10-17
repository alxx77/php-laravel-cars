<?php
declare(strict_types=1);

namespace App\Ivb\App;

require_once(app_path() . '\ivb\app\class.dbconnect.php');

use PDO;
use PDOStatement;
use DateTime;
use Exception;


//DAL klasa za korisnika
class Ponuda_DAL
{

		
	//ponuda po id-ju
	public static function getPonudaDataById(PDO $trans, int $ponuda_id):array
	{
		$query="CALL GetPonuda(:id_pon)";
		$stmt = $trans->prepare($query);
		$stmt->bindValue(':id_pon', $ponuda_id, PDO::PARAM_INT);
		$stmt->execute();
		//vraća niz sa 1 redom
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}


	//redni broj stranice sa datim id-jem ponude
	public static function getPageById(int $id_pon,int $page_size,int $id_brand,int $active_only):int
	{
		$trans=Connection::GetConnection();
		$query="CALL GetPageByIdPon(:id_pon,:page_size,:id_brand,:active_only,@a);";
		$stmt = $trans->prepare($query);
		$stmt->bindValue(':id_pon', $id_pon, PDO::PARAM_INT);
		$stmt->bindValue(':page_size', $page_size, PDO::PARAM_INT);
		$stmt->bindValue(':id_brand', $id_brand, PDO::PARAM_INT);
		$stmt->bindValue(':active_only', $active_only, PDO::PARAM_INT);
		$stmt->execute();

		//pre uzimanja rezultata mora se prvo završiti prethodni upit
		$stmt->closeCursor();

		$output = $trans->query("select @a as page_nr;")->fetch(PDO::FETCH_ASSOC);

		//rezultat

		return (int) $output['page_nr'];
	}
	
	//lista id-jeva aktivnih ponuda
	public static function getActivePonudaIdLista():array
	{
	    $trans=Connection::GetConnection();
		$query="select id_pon from ponude where dat_zatv is null order by id_pon asc";
		$stmt = $trans->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	//broj ponuda
	public static function getPonudaCount(int $id_brand,int $active_only):int
	{
		//broj slogova
		$c=0;
		
	    $trans=Connection::GetConnection();

		//aktivne samo
		if((int) $active_only==1){
			$query="select count(id_pon) as c from ponude where (dat_unos is not null) and (dat_pon is not null) and (id_brand=:id_brand  or :id_brand=0) and (dat_zatv is null)";
		} else {
			//sve
			$query="select count(id_pon) as c from ponude where (dat_unos is not null) and (id_brand=:id_brand  or :id_brand=0) and (dat_zatv is null)";
		}

		
		$stmt = $trans->prepare($query);
		$stmt->bindValue(':id_brand', $id_brand, PDO::PARAM_INT);
		$stmt->execute();
		$row= $stmt->fetch(PDO::FETCH_ASSOC);
		
		if($row)
		{
			if(count($row)>0)
			{
				$c=(int) $row['c'];
			}
		}
		
		return $c;
	}


	//vraća listu id-jeva za traženu stranicu ponuda
	public static function getPonudaPage(int $index_start,int $page_size,int $id_brand,int $active_only):array
	{
		
		$trans=Connection::GetConnection();
		
		//id-jevi tražene stranice
		if((int) $active_only==1){
			//samo aktivne ponude
			$query="select id_pon from ponude where (dat_unos is not null) and (dat_pon is not null) and (dat_zatv is null) and (id_brand=:id_brand  or :id_brand=0) order by id_pon asc limit :index_start,:page_size";
		} else {
			//sve ponude
			$query="select id_pon from ponude where (dat_unos is not null) and (dat_pon is not null)  and (id_brand=:id_brand  or :id_brand=0) order by id_pon asc limit :index_start,:page_size";
		}

		
		$stmt = $trans->prepare($query);
		
		$stmt = $trans->prepare($query);		
		$stmt->bindValue(':index_start', $index_start, PDO::PARAM_INT);
		$stmt->bindValue(':page_size', $page_size, PDO::PARAM_INT);
		$stmt->bindValue(':id_brand', $id_brand, PDO::PARAM_INT);
		$stmt->execute();

		//vraća više redova
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(!($row))
		{
			$row=array();
		}

		return $row;
	
	}
	
	//obriši ponudu
	public static function deletePonudaById(PDO $trans,int $id_pon):bool
	{
		$result=false;
		
		$query=" delete from ponude where (ponude.id_pon=:id_pon) and (dat_zatv is null) and (tip_zatv is null)";
	    $stmt = $trans->prepare($query);
		$stmt->bindValue(':id_pon', $id_pon, PDO::PARAM_INT);
		$stmt->execute();
		
		//broj redova koji je izmenjen, ako je 1 onda je brisanje uspešno obavljeno
		$row_count =(int) $stmt->rowCount();
		
		if($row_count==1)
		{
			$result=true;
		}
		
		return $result;
	}

	
	
	//upis nove ponude
	public static function insertPonudaData(PDO $trans,array $row):int
	{
		$last_id=0;
		
		try
		{
			//ne prosleđuju se
			//id ponude
			//broj ponude - generiše se automatski za unos
			//datum unosa - generiše se automatski
			
			//datum zatvaranja ponude - menja se iz separatne klase
			//tip zatvaranja ponude -//-
			
			$query="CALL InsertPonuda(
					 
					 :id_brand
					,:id_model
					,:tip
					,:vat_ddct
					,:neto_cena_val
					,:pdv_cena_val
					,:bruto_cena_val
					,:opis
					,:dat_pon
					,:god_proizv
					,:real_bruto_cena_val
					,:a000_karoserija
					,:a001_broj_vrata
					,:a002_tip_goriva
					,:a003_polovno
					,:a004_ccm
					,:a005_kw
					,:a006_km
					
					)";
			
			//pripremi upit		
			$stmt = $trans->prepare($query);

			//veži parametre
			self::BindParams($stmt,$row);
			
			//izvrši upit
			$stmt->execute();
		
			//id insertovanog unosa
			$row= $stmt->fetch(PDO::FETCH_ASSOC);
			
			$last_id=(int) $row['id'];
			
		}
		
		catch
		(Exception $e)
		{
			throw $e;
		}
	
		//vrati id
		return $last_id;
	}
	
	//izmena podataka o ponudi
	public static function updatePonudaData(PDO $trans, array $row):int
	{
			//broj ponude - nije moguća izmena nakon što se jednom generiše

		$row_count=0;
		
		try
		{
			
				$query="CALL UpdatePonuda(
					 :id_pon
					,:id_brand
					,:id_model
					,:tip
					,:vat_ddct
					,:neto_cena_val
					,:pdv_cena_val
					,:bruto_cena_val
					,:opis
					,:dat_pon
					,:god_proizv
					,:real_bruto_cena_val
					,:a000_karoserija
					,:a001_broj_vrata
					,:a002_tip_goriva
					,:a003_polovno
					,:a004_ccm
					,:a005_kw
					,:a006_km
					,:dat_zatv
					,:tip_zatv
					
					)";
					
			$stmt = $trans->prepare($query);

			//postavi vrednosti parametara
			$stmt->bindValue(':id_pon',$row['id_pon'],PDO::PARAM_INT);
			
			if (is_null($row['dat_zatv'])) {
				$stmt->bindValue(':dat_zatv',null, PDO::PARAM_NULL);
			} else {
				$stmt->bindValue(':dat_zatv', $row['dat_zatv']->format('Y-m-d H:i:s'), PDO::PARAM_STR);
			}
	
			$stmt->bindValue(':tip_zatv',$row['tip_zatv'],PDO::PARAM_STR);


			//ostali parametri
			self::BindParams($stmt,$row);
			
			//izvrši upit
			$stmt->execute();
			
			//broj redova
			$row_count =(int) $stmt->rowCount();
			
		
		}
		
		catch (Exception $e)
		{
			throw $e;
		}
	
		//vrati broj izmenjenih redova
		return $row_count;
		
	}
	
	//vezuje vrednosti reda sa parametrima u upitu
	private static function BindParams(PDOStatement  $stmt, array $row)
	{
	    $stmt->bindValue(':id_brand',$row['id_brand'],PDO::PARAM_INT);
		$stmt->bindValue(':id_model',$row['id_model'],PDO::PARAM_INT);
		$stmt->bindValue(':tip',$row['tip'],PDO::PARAM_STR);
		$stmt->bindValue(':vat_ddct',$row['vat_ddct'],PDO::PARAM_INT);
		$stmt->bindValue(':neto_cena_val',$row['neto_cena_val'],PDO::PARAM_STR);
		$stmt->bindValue(':pdv_cena_val',$row['pdv_cena_val'],PDO::PARAM_STR);
		$stmt->bindValue(':bruto_cena_val',$row['bruto_cena_val'],PDO::PARAM_STR);
		$stmt->bindValue(':opis',$row['opis'],PDO::PARAM_STR);

		if (is_null($row['dat_pon'])) {
			$stmt->bindValue(':dat_pon',null, PDO::PARAM_NULL);
		} else {
			$stmt->bindValue(':dat_pon', $row['dat_pon']->format('Y-m-d H:i:s'), PDO::PARAM_STR);
		}

		$stmt->bindValue(':god_proizv',$row['god_proizv'],PDO::PARAM_INT);
		$stmt->bindValue(':real_bruto_cena_val',$row['real_bruto_cena_val'],PDO::PARAM_STR);
		$stmt->bindValue(':a000_karoserija',$row['a000_karoserija'],PDO::PARAM_STR);
		$stmt->bindValue(':a001_broj_vrata',$row['a001_broj_vrata'],PDO::PARAM_STR);
		$stmt->bindValue(':a002_tip_goriva',$row['a002_tip_goriva'],PDO::PARAM_STR);
		$stmt->bindValue(':a003_polovno',$row['a003_polovno'],PDO::PARAM_STR);
		$stmt->bindValue(':a004_ccm',$row['a004_ccm'],PDO::PARAM_STR);
		$stmt->bindValue(':a005_kw',$row['a005_kw'],PDO::PARAM_STR);
		$stmt->bindValue(':a006_km',$row['a006_km'],PDO::PARAM_STR);


	}
	

	
}


?>