<?php
declare (strict_types = 1);

namespace App\Ivb\App;

require_once('class.dbconnect.php');

use PDO;
use PDOStatement;
use Exception;

//DAL klasa za korisnika
class User_DAL
{

	//red sa podacima o korisniku po emailu
	public static function getUserDataByEmail(string $email) : array
	{
		$conn = Connection::GetConnection();
		$stmt = $conn->prepare("SELECT * FROM users where email=:email");
		$stmt->bindValue(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		$conn = null;
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		//ako ima više od 1 reda, vrati grešku jer to znači da podaci nisu ispravni
		if (count($result) > 1) {
			throw new Exception();
		}
		{
			//oslobodi se spoljnog niza
			if (count($result) == 1) {
				$result = $result[0];
			} else {
				//ako nema redova, vrati prazan niz
				$result = array();
			}
		}
		
		//vrati
		return $result;
	}
	
	
	//red sa podacima o korisniku po id-ju
	public static function getUserDataById(int $user_id) : array
	{
		$conn = Connection::GetConnection();
		$stmt = $conn->prepare("SELECT * FROM users where user_id=:user_id");
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$conn = null;
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!($result)) {
			$result = array();
		}
		return $result;
	}
	
	//id-jevi svih korisnika
	public static function getUsersIds() : array
	{
		$conn = Connection::GetConnection();
		$stmt = $conn->prepare("SELECT user_id FROM users");
		$stmt->execute();
		$conn = null;
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!($result)) {
			$result = array();
		}
		return $result;
	}
	
	//pronađi podatke po emailu
	public static function getUserDataByUserName(string $username) : array
	{
		$conn = Connection::GetConnection();
		$stmt = $conn->prepare("SELECT * FROM users where username=:username");
		$stmt->bindValue(':username', $username, PDO::PARAM_STR);
		$stmt->execute();
		$conn = null;
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!($result)) {
			$result = array();
		}
		return $result;
	}
	
	
	//upis novog korisnika
	public static function insertUserData(IVB_User $user) : int
	{

		try {
			$conn = Connection::GetConnection();
			$stmt = $conn->prepare("INSERT INTO users(username,email,pwdhash,user_type,pwdsalt) VALUES(:username,:email,:pwdhash,:user_type,:pwdsalt)");
			$stmt->bindValue(':username', $user->getUserName(), PDO::PARAM_STR);
			$stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
			$stmt->bindValue(':pwdhash', $user->getPasswordHash(), PDO::PARAM_STR);
			$stmt->bindValue(':user_type', $user->getUserType(), PDO::PARAM_INT);
			$stmt->bindValue(':pwdsalt', $user->getPasswordSalt(), PDO::PARAM_STR);

			$stmt->execute();
			
			//id insertovanog unosa
			$last_id = (int)$conn->lastInsertId();

			$conn = null;

		} catch (Exception $e) {
			throw $e;
		}

		return $last_id;
	}

    //izmena postojećeg korisnika
	public static function updateUserData(IVB_User $user) : int
	{

		try {
			$conn = Connection::GetConnection();

			$stmt = $conn->prepare("update users
									set username=:username
									   ,email=:email
									   ,pwdhash=:pwdhash
									   ,user_type=:user_type
									   ,pwdsalt=:pwdsalt
									   where users.user_id=:user_id
									   ");
			$stmt->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
			$stmt->bindValue(':username', $user->getUserName(), PDO::PARAM_STR);
			$stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
			$stmt->bindValue(':pwdhash', $user->getPasswordHash(), PDO::PARAM_STR);
			$stmt->bindValue(':user_type', $user->getUserType(), PDO::PARAM_INT);
			$stmt->bindValue(':pwdsalt', $user->getPasswordSalt(), PDO::PARAM_STR);

			$stmt->execute();
			
			//broj redova
			$row_count = (int)$stmt->rowCount();

			$conn = null;

		} catch (Exception $e) {
			throw $e;
		}

		return $row_count;
	}

    //brisanje postojećeg korisnika
	public static function deleteUserData(int $user_id) : int
	{

		try {
			$conn = Connection::GetConnection();

			$stmt = $conn->prepare("delete from users
									   where (users.user_id=:user_id)
									   ");
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			
			//broj redova
			$row_count = (int)$stmt->rowCount();

			$conn = null;

		} catch (Exception $e) {
			throw $e;
		}

		return $row_count;
	}



}


?>