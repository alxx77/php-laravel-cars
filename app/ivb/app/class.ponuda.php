<?php
declare(strict_types=1);


namespace App\Ivb\App;

use DateTime;
use Exception;

require_once(app_path() . '\ivb\app\class.dbconnect.php');
require_once(app_path() . '\ivb\app\class.ponuda_dal.php');

use PDO;

class Ponuda 
{
    //priv. prom
	private $id_pon;
	private $brpon;
	private $id_brand;
	private $id_model;
	private $tip;
	private $vat_ddct;
	private $neto_cena_val;
	private $pdv_cena_val;
	private $bruto_cena_val;
	private $opis;
	private $dat_unos;
	private $dat_pon;
	private $god_proizv;
	private $dat_zatv;
	private $tip_zatv;
	private $real_bruto_cena_val;
	private $a000_karoserija;
	private $a001_broj_vrata;
	private $a002_tip_goriva;
	private $a003_polovno;
	private $a004_ccm;
	private $a005_kw;
	private $a006_km;
		
	//dodatno
	public $brand='';
	public $model='';
	public $tip_vozila='';
	public $tip_vozila_kod='';
	public $a000_karoserija_naziv='';
	public $a002_tip_goriva_naziv='';
	
	
	
	//public props
	public function get_Id_pon(){ return $this->id_pon; } public function set_Id_pon(int $value) { $this->id_pon=$value; }
	public function get_Brpon(){ return $this->brpon; } public function set_Brpon(string $value) { $this->brpon=$value; }
	public function get_Id_brand(){ return $this->id_brand; } public function set_Id_brand(int $value) { $this->id_brand=$value; }
	public function get_Id_model(){ return $this->id_model; } public function set_Id_model(int $value) { $this->id_model=$value; }
	public function get_Tip(){ return $this->tip; } public function set_Tip($value) { $this->tip=$value; }
	public function get_Vat_ddct(){ return $this->vat_ddct; } public function set_Vat_ddct(bool $value) { $this->vat_ddct=$value; }
	public function get_Neto_cena_val(){ return $this->neto_cena_val; } public function set_Neto_cena_val(float $value) { $this->neto_cena_val=$value; }
	public function get_Pdv_cena_val(){ return $this->pdv_cena_val; } public function set_Pdv_cena_val(float $value) { $this->pdv_cena_val=$value; }
	public function get_Bruto_cena_val(){ return $this->bruto_cena_val; } public function set_Bruto_cena_val(float $value) { $this->bruto_cena_val=$value; }
	public function get_Opis(){ return $this->opis; } public function set_Opis($value) { $this->opis=$value; }
	public function get_Dat_unos(){ return $this->dat_unos; } public function set_Dat_unos(DateTime $value) { $this->dat_unos=$value; }
	public function get_Dat_pon(){ return $this->dat_pon; } public function set_Dat_pon(DateTime $value) { $this->dat_pon=$value; }
	public function get_God_prozv(){ return $this->god_proizv; } public function set_God_proizv(int $value) { $this->god_proizv=$value; }
	public function get_Dat_zatv(){ return $this->dat_zatv; } public function set_Dat_zatv(DateTime $value) { $this->dat_zatv=$value; }
	public function get_Tip_zatv(){ return $this->tip_zatv; } public function set_Tip_zatv(string $value) { $this->tip_zatv=$value; }
	public function get_Real_bruto_cena_val(){ return $this->real_bruto_cena_val; } public function set_Real_bruto_cena_val(float $value) { $this->real_bruto_cena_val=$value; }
	
	public function get_A000_karoserija(){ return $this->a000_karoserija; } public function set_A000_karoserija(string $value) { $this->a000_karoserija=$value; }
	public function get_A001_broj_vrata(){ return $this->a001_broj_vrata; } public function set_A001_broj_vrata(string $value) { $this->a001_broj_vrata=$value; }
	public function get_A002_tip_goriva(){ return $this->a002_tip_goriva; } public function set_A002_tip_goriva(string $value) { $this->a002_tip_goriva=$value; }
	public function get_A003_polovno(){ return $this->a003_polovno; } public function set_A003_polovno(string $value) { $this->a003_polovno=$value; }
	public function get_A004_ccm(){ return $this->a004_ccm; } public function set_A004_ccm(float $value) { $this->a004_ccm=$value; }
	public function get_A005_kw(){ return $this->a005_kw; } public function set_A005_kw(float $value) { $this->a005_kw=$value; }
	public function get_A006_km(){ return $this->a006_km; } public function set_A006_km(float $value) { $this->a006_km=$value; }
	
	
	//učitava ponudu
	public static function getById(PDO $db, int $id_pon):Ponuda
	{

		$ponuda=new Ponuda();

		try
		{
			$userdata = Ponuda_DAL::getPonudaDataById($db,$id_pon);
		}
		catch(Exception $e)
		{
			throw $e;
		}

		if (isset($userdata))
		{
			try
			{
				$ponuda = self::CreateFromRow($userdata);
			}
			catch(Exception $e)
			{
				throw $e;
			}
		}
		
		// vrati rezultat
		return $ponuda;
	}
	
	//upisuje ponudu
	//upiši se u bazu (novi slog)
	public function insertPonuda(PDO $db):int 
	{
		//novi id unosa
		$id=-1;
		
	
		//provera sopstvenog id-ja 
		//ojekt se sme upisati u bazu samo ako mu je id=-1
		if($this->get_Id_pon()!=-1)
		{
			//vrati grešku, negde postoji greška u logici, ne sme se upisati ponovo već postojeći objekt
			throw new Exception();
		}
		
		//kreiraj niz sa vrednostima polja
		$row=$this->ToRow();
		
		//proveri da li vrednosti imaju smisla
		
		
		try
		{
			$id=Ponuda_DAL::insertPonudaData($db,$row);

		}
		catch(Exception $e)
		{
			throw $e;
		}
		
		//ako je slog upisan $id će biti različit od -1
		if($id==-1)
		{
			//vrati grešku, negde postoji greška u logici, ako je novi slog uspešno insertovan
			//novi id ne moće ostati -1, mora biti pozitivan
			throw new Exception();
		}
		
		//postavi novi sopstveni id
		$this->set_Id_pon($id);
		
		return $id;
	}
	
    //upisuje ponudu
	//u bazu (izmena postojećeg sloga)
	public function updatePonuda(PDO $db):int 
	{
		
		//broj redova koji je izmenjen
		$rowcount=0;
		
		//provera sopstvenog id-ja 
		//objekt se sme editovati samo ako je id >0
		if(!($this->get_Id_pon()>0))
		{
			//vrati grešku, negde postoji greška u logici, ne može se editovati objekt koji nije upisan u bazu
			throw new Exception();
		}
		
		//kreiraj niz sa vrednostima polja
		$row=$this->ToRow();
		
		
		try
		{
			$rowcount=Ponuda_DAL::updatePonudaData($db,$row);
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
		//vrati true
		return $rowcount;
	}
	
	
    //kreiranje reda iz objekta
	public function ToRow():array
	{
		//novi red
		$row=array();
		
		$null_date=new DateTime('1000-01-01 00:00:00');
		
		
		$row['id_pon']=$this->get_Id_pon();
		$row['brpon']=$this->get_Brpon();
		$row['id_brand']=$this->get_Id_brand();
		$row['id_model']=$this->get_Id_model();
		$row['tip']=$this->get_Tip()=='' ? null :$this->get_Tip();
		$row['vat_ddct']=$this->get_Vat_ddct();
		$row['neto_cena_val']=$this->get_Neto_cena_val();
		$row['pdv_cena_val']=$this->get_Pdv_cena_val();
		$row['bruto_cena_val']=$this->get_Bruto_cena_val();
		$row['opis']=$this->get_Opis()=='' ? null : $this->get_Opis();
		$row['dat_unos']=$this->get_Dat_unos() == $null_date ? null : $this->get_Dat_unos();
		$row['dat_pon']=$this->get_Dat_pon() == $null_date ? null : $this->get_Dat_pon();
		$row['god_proizv']=$this->get_God_prozv();
		$row['dat_zatv']=$this->get_Dat_zatv() == $null_date ? null : $this->get_Dat_zatv();
		$row['tip_zatv']=$this->get_Tip_zatv() == '' ? null : $this->get_Tip_zatv();
		$row['real_bruto_cena_val']=$this->get_Real_bruto_cena_val();
		$row['a000_karoserija']=$this->get_A000_karoserija() == '' ? null : $this->get_A000_karoserija() ;
		$row['a001_broj_vrata']=$this->get_A001_broj_vrata() == '' ? null : $this->get_A001_broj_vrata();
		$row['a002_tip_goriva']=$this->get_A002_tip_goriva() == '' ? null : $this->get_A002_tip_goriva();
		$row['a003_polovno']=$this->get_A003_polovno() == '' ? null : $this->get_A003_polovno();
		$row['a004_ccm']=$this->get_A004_ccm();
		$row['a005_kw']=$this->get_A005_kw();
		$row['a006_km']=$this->get_A006_km();
		
		
		//ne koristi se pri upisu
		$row['brand']=$this->brand;
		$row['model']=$this->model;
		$row['tip_vozila']=$this->tip_vozila;
		$row['tip_vozila_kod']=$this->tip_vozila_kod;
		$row['a000_karoserija_naziv']=$this->a000_karoserija_naziv;
		$row['a002_tip_goriva_naziv']=$this->a002_tip_goriva_naziv;
		
		
		return $row;
	}
	
    //kreiranje objekta iz reda
    private static function CreateFromRow(array $row):Ponuda
	{
		$ponuda=new Ponuda;
		
		
		$ponuda->set_Id_pon((int) $row['id_pon']);
		$ponuda->set_Brpon($row['brpon']);
		$ponuda->set_Id_brand((int) $row['id_brand']);
		$ponuda->set_Id_model((int) $row['id_model']);
		$ponuda->set_Tip($row['tip'] ?? '');
		$ponuda->set_Vat_ddct((bool) $row['vat_ddct']);
		$ponuda->set_Neto_cena_val(floatval($row['neto_cena_val']));
		$ponuda->set_Pdv_cena_val(floatval($row['pdv_cena_val']));
		$ponuda->set_Bruto_cena_val(floatval($row['bruto_cena_val']));
		$ponuda->set_Opis($row['opis'] ?? '');

		if (is_null($row['dat_unos'])) {
			$ponuda->set_Dat_unos(new DateTime('1000-01-01 00:00:00'));
		} else {
			$ponuda->set_Dat_unos(new DateTime($row['dat_unos']));
		}

		if (is_null($row['dat_pon'])) {
			$ponuda->set_Dat_pon(new DateTime('1000-01-01 00:00:00'));
		} else {
			$ponuda->set_Dat_pon(new DateTime($row['dat_pon']));
		}

		$ponuda->set_God_proizv((int) $row['god_proizv']);

		if (is_null($row['dat_zatv'])) {
			$ponuda->set_Dat_zatv(new DateTime('1000-01-01 00:00:00'));
		} else {
			$ponuda->set_Dat_zatv(new DateTime($row['dat_zatv']));
		}

		$ponuda->set_Tip_zatv($row['tip_zatv'] ?? '');
		$ponuda->set_Real_bruto_cena_val(floatval($row['real_bruto_cena_val']));
		$ponuda->set_A000_karoserija($row['a000_karoserija'] ?? '');
		$ponuda->set_A001_broj_vrata($row['a001_broj_vrata'] ?? '');
		$ponuda->set_A002_tip_goriva($row['a002_tip_goriva'] ?? '');
		$ponuda->set_A003_polovno($row['a003_polovno'] ?? '');
		$ponuda->set_A004_ccm(floatval($row['a004_ccm'] ?? 0));
		$ponuda->set_A005_kw(floatval($row['a005_kw'] ?? 0));
		$ponuda->set_A006_km(floatval($row['a006_km'] ?? 0));
		
		
		//dodatna polja
		$ponuda->brand=$row['brand'];
		$ponuda->model=$row['model'];
		$ponuda->tip_vozila=$row['tip_vozila'];
		$ponuda->tip_vozila_kod=$row['tip_vozila_kod'];
		$ponuda->a000_karoserija_naziv=$row['a000_karoserija_naziv'];
		$ponuda->a002_tip_goriva_naziv=$row['a002_tip_goriva_naziv'];

		return $ponuda;
	
	}
     
    

    
	//obriši ponudu
    public static function deletePonuda(PDO $db, int $id_pon):bool
	{
		$result = false;

		try
		{
			$result = Ponuda_DAL::deletePonudaById($db,$id_pon);
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
		// vrati rezultat
		return $result;
	}
	
	//vraća broj ponuda
	public static function GetPonudaCount(int $id_brand,int $active_only):int
	{
		return Ponuda_DAL::getPonudaCount($id_brand,$active_only);
	}
	
	//vraća stranicu sa id-jevima ponuda
	public static function GetPonudaPage(int $index_start,int $page_size,int $id_brand,int $active_only):array
	{
		return Ponuda_DAL::getPonudaPage($index_start,$page_size,$id_brand,$active_only);
	}
	
	//stranica na kojoj se nalazi data ponuda
	public static function GetPageById(int $id_pon,int $page_size,int $id_brand,int $active_only):int
	{
		return  Ponuda_DAL::getPageById($id_pon,$page_size,$id_brand,$active_only);
	}
	
	
       
} 

?>