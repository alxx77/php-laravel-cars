<?php
declare(strict_types=1);

namespace App\Ivb\App;

use PDO;
use PDOException;


//connection factory
class Connection
{
	
	public static $dbhost ='127.0.0.1';
	public static $dbhostport='3306';
	public static $dbname='ivbsol_prod';
	public static $dbuser = 'ivbsol_admin';
	public static $dbpass = '!!!Honda100';

	//vraća konekcioni objekt
	public static function GetConnection():PDO
	{
		$conn = NULL;

		//konekcija na bazu
		try
		{
			//konekcija
			
			$conn = new PDO('mysql:host='.Connection::$dbhost.
							'; port='.Connection::$dbhostport.
							'; dbname='.Connection::$dbname.
							'; charset=utf8;'
							,Connection::$dbuser
							,Connection::$dbpass
							,array(PDO::MYSQL_ATTR_FOUND_ROWS => true) //+ opcija da uvek za update,delete operacije vraća broj uparenih slogova
						);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		
		//vrati rezultat
		return $conn;
	}
}

?>