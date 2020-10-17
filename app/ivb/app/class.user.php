<?php
declare (strict_types = 1);

namespace App\Ivb\App;

use Exception;

require_once('class.user_dal.php');


//const Guest = 0;
//const StandardUser = 1;
//const Admin = 2;



//korisnik
class IVB_User
{
	//primarni ključ 
	private $id;

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $value)
	{
		$this->id = $value;
	}


	//korisničko ime
	private $username;

	public function getUserName(): string
	{
		return $this->username;
	}

	public function setUserName(string $value)
	{
		$this->username = isset($value) ? $value : '';
	}


	//email
	private $email;


	public function getEmail(): string
	{
		return $this->email;
	}


	public function setEmail(string $value)
	{
		$this->email = $value;
	}


	//p	assword      
	private $pwdhash;


	public function getPasswordHash(): string
	{

		return $this->pwdhash;
	}


	public function setPasswordHash(string $value)
	{
		$this->pwdhash = $value;
	}


	//tip korisnika 
	private $user_type;

	public function getUserType(): int
	{
		return $this->user_type;
	}


	public function setUserType(int $value)
	{
		//ako je vrednost u opsegu
		if ($value == 0 || $value == 1 || $value == 2) {
			//postavi
			$this->user_type = $value;
		} else {
			throw new Exception("Nepoznat tip korisnika...!");
		}
	}

	//naziv tipa korisnika
	public function getUserTypeName()
	{

		//izlaz
		$type_name = "";

		switch ($this->user_type) {
			case 2:
				//admin
				$type_name = "Admin";
				break;

			case 1:
				//običan korisnik
				$type_name = "StandardUser";
				break;

			case 0:
				//običan korisnik
				$type_name = "Guest";
				break;

			default:
				break;
		}

		return $type_name;
	}


	//password salt      
	private $pwdsalt;


	public function getPasswordSalt(): string
	{

		return $this->pwdsalt;
	}

	public function setPasswordSalt(string $value)
	{
		$this->pwdsalt = $value;
	}


	//ctor
	public function __construct()
	{
		//default id
		$this->id = -1;

		//default user type - standardni korisnik (bez admin prava)
		$this->user_type = 1;
	}


	//kreira instancu objekta korisnik za dati email ukoliko korisnik postoji, inače vraća null
	public static function getUserByEmail(string $email): ?IVB_User
	{

		$user = null;

		//ako parametar nije definisan vrati grešku
		//if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		//return null;
		//}

		//pokušaj da učitaš podatke o korisniku sa datim e-mailom
		try {
			$userdata = User_DAL::getUserDataByEmail($email);
		} catch (Exception $e) {
			return null;
		}

		//ako korisnik postoji kreiraj instancu
		if (!empty($userdata)) {
			$user = self::CreateFromRow($userdata);
		}

		//vrati rezultat
		return $user;
	}




	//lista svih korisnika
	public static function getUsers(): array
	{
		$users = array();

		//lista svih id-jeva
		$users_ids = User_DAL::getUsersIds();

		try {

			foreach ($users_ids as $key => $value) {

				$user = self::getUserById((int)$value['user_id']);

				array_push($users, $user->ToRow());
			}
		} catch (Exception $e) {
			throw $e;
		}

		//vrati rezultat
		return $users;
	}


	//kreira instancu objekta korisnik za dati id korisnika
	public static function getUserById(int $user_id): IVB_User
	{
		$user = null;

		//pokušaj da učitaš podatke o korisniku sa datim id-jem
		try {
			$userdata = User_DAL::getUserDataById($user_id);
		} catch (Exception $e) {
			throw $e;
		}

		//ako korisnik postoji kreiraj instancu
		if (!empty($userdata)) {
			$user = IVB_User::CreateFromRow($userdata);
		}

		//vrati rezultat
		return $user;
	}


	//Instancira korisnika iz reda db(niza)
	private static function CreateFromRow(array $row): IVB_User
	{

		if (!empty($row)) {
			$user = new IVB_User;

			$user->setId((int)$row['user_id']);
			$user->setUserName($row['username']);
			$user->setEmail($row['email']);
			$user->setPasswordHash($row['pwdhash']);
			$user->setUserType((int)$row['user_type']);
			$user->setPasswordSalt($row['pwdsalt']);
		}
		return $user;
	}


	//napravi niz (red)
	public function ToRow(): array
	{
		$row = array();

		$row['user_id'] = $this->getId();
		$row['username'] = $this->getUserName();
		$row['email'] = $this->getEmail();
		$row['pwdhash'] = $this->getPasswordHash();
		$row['user_type'] = $this->getUserType();
		$row['user_type_name'] = $this->getUserTypeName();
		$row['pwdsalt'] = $this->getPasswordSalt();

		return $row;
	}


	//upis u bazu
	public function InsertUser(): int
	{
		//proveri da li je id=-1
		if ($this->id != -1) {
			throw new Exception('Id novog korisnika mora biti -1 da bi se izvršio upis novog reda u bazu!');
		}

		//proveri da li je validan email   
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			throw new Exception('Nevalidan email..!');
		}

		//proveri da li postoji username 
		if (!isset($this->username)) {
			throw new Exception('Nevalidno korisničko ime..!');
		}

		//proveri da li postoji password 
		if (!isset($this->pwdhash)) {
			throw new Exception('Password nije definisan..!');
		}

		//proveri da li postoji tip korisnika
		if (!isset($this->user_type)) {
			throw new Exception('Tip korisnika nije definisan..!');
		}

		//proveri da li postoji password salt
		if (!isset($this->pwdsalt)) {
			throw new Exception('Nevalidan password salt..!');
		}

		//upiši
		try {
			//upiši korisnika i odmah postavi novi id
			$this->setId(User_DAL::insertUserData($this));
		} catch (Exception $e) {
			throw $e;
		}

		//vraća id novog sloga
		return $this->getId();
	}

	//izmena podataka korisnika
	public function UpdateUser(): int
	{
		$result = 0;

		//proveri da li je id<>-1
		//da se ne bi dogodio pokušaj izmene umesto upisa sloga novog nepostojećeg sloga
		if ($this->id == -1) {
			throw new Exception('Id instance objekta mora biti različit od -1 da bi se izvršio upis u bazu!');
		}

		//proveri da li je validan email   
		//if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
		//throw new Exception('Nevalidan email..!');
		//}

		//proveri da li postoji username 
		if (!isset($this->username)) {
			throw new Exception('Nevalidno korisničko ime..!');
		}

		//proveri da li postoji password 
		if (!isset($this->pwdhash)) {
			throw new Exception('Password nije definisan..!');
		}

		//proveri da li postoji tip korisnika
		if (!isset($this->user_type)) {
			throw new Exception('Tip korisnika nije definisan..!');
		}

		//proveri da li postoji password salt
		if (!isset($this->pwdsalt)) {
			throw new Exception('Nevalidan password salt..!');
		}

		//upiši
		try {
			//upiši korisnika i odmah postavi novi id
			$result = User_DAL::updateUserData($this);
		} catch (Exception $e) {
			throw $e;
		}

		return $result;
	}

	//briše korisnika i vraća broj obrisanih redova
	public static function DeleteUser(int $user_id): int
	{
		$result = 0;

		//proveri da li je id<>-1
		if ($user_id == -1) {
			throw new Exception('Id naloga mora biti različit od -1 da bi se izvršilo brisanje sloga!');
		}


		//briši
		try {
			//obriši korisnika
			$result = User_DAL::deleteUserData($user_id);
		} catch (Exception $e) {
			throw $e;
		}

		return $result;
	}
}
 