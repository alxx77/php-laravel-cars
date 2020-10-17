<?php
declare (strict_types = 1);

namespace App\Ivb\App;
require_once(app_path() . '\ivb\app\class.dbconnect.php');

use DateTime;
use PDO;
use Exception;



class komentar
{
    public $id_kom;
    public $id_pon;
    public $user_id;
    public $username;
    public $dat_kom;
    public $komentar;
    public $proc;


    //učitaj sve komentare za ponudu
    public static function getByIdPon(int $id_pon): array
    {

        $result = array();

        $trans = Connection::GetConnection();

        $query = "select * from komentari left join users on komentari.user_id=users.user_id where id_pon=:id_pon order by dat_kom asc";
        $stmt = $trans->prepare($query);
        $stmt->bindValue(':id_pon', $id_pon, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //prođi kroz redove
        foreach ($rows as $key => $value) {

            //novi komentar
            $komentar = new komentar();

            //postavi vrednosti
            $komentar->id_kom = (int)$value["id_kom"];
            $komentar->id_pon = (int)$value["id_pon"];
            $komentar->user_id = (int)$value["user_id"];
            $komentar->username = $value["username"];
            $komentar->dat_kom = new DateTime($value["dat_kom"]);
            $komentar->komentar = $value["komentar"];
            $komentar->proc = (bool)$value["proc"];

            //stavi u izlazni niz
            array_push($result, $komentar);
        }

        return $result;
    }

    //upiši ponudu
    public function Insert(): int
    {
        if ($this->id_kom != -1) {
            throw new Exception("Id pri upisu komentara mora biti -1");
        }
        if (!is_integer($this->id_pon)) {
            throw new Exception("Id ponude nije definisan");
        }
        if (!is_integer($this->user_id)) {
            throw new Exception("Id korisnika nije definisan");
        }

        if (!($this->dat_kom instanceof DateTime)) {
            throw new Exception("Datum komentara nije definisan...!");
        }

        if (!is_integer($this->proc)) {
            throw new Exception("Status komentara nije definisan");
        }


        try {
            $conn = Connection::GetConnection();
            $stmt = $conn->prepare("INSERT INTO komentari(id_pon,user_id,dat_kom,komentar,proc) VALUES(:id_pon,:user_id,:dat_kom,:komentar,:proc)");
            $stmt->bindValue(':id_pon', $this->id_pon, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_STR);
            $stmt->bindValue(':dat_kom', $this->dat_kom->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(':komentar', $this->komentar, PDO::PARAM_STR);
            $stmt->bindValue(':proc', $this->proc, PDO::PARAM_INT);

            $stmt->execute();

            //id insertovanog unosa
            $last_id = (int)$conn->lastInsertId();

            $conn = null;
        } catch (Exception $e) {
            throw $e;
        }

        return $last_id;
    }
}
 