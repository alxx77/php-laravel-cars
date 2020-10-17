<?php
declare (strict_types = 1);

namespace App\Ivb\App;
use Exception;

require_once (app_path().'\ivb\app\class.dbconnect.php');

use PDO;
use DateTime;


class Page
{
    public $id_page;
    public $rootx;
    public $pathx;
    public $level;
    public $node_type;
    public $idx;
    public $page_data;
    public $descr;
    public $dat_page;



    //učitaj sve podatke
    public static function getPageInfoByRoot(string $rootx): array
    {

        $result = array();

        $trans = Connection::GetConnection();

        $query = "select id_page,rootx,pathx,node_type,level,idx,descr,dat_page from cms_pages where rootx=:rootx order by `level`,idx asc";
        $stmt = $trans->prepare($query);
        $stmt->bindValue(':rootx', $rootx, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public static function getPageById(int $id_page): array
    {

        $trans = Connection::GetConnection();

        $query = "select id_page,rootx,pathx,`level`,node_type,level,idx,page_data,descr,dat_page from cms_pages where id_page=:id_page";
        $stmt = $trans->prepare($query);
        $stmt->bindValue(':id_page', $id_page, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }


    public static function getPageObjectById(int $id_page): ?Page
    {

        $trans = Connection::GetConnection();

        $query = "select id_page,rootx,pathx,`level`,node_type,level,idx,page_data,descr,dat_page from cms_pages where id_page=:id_page";
        $stmt = $trans->prepare($query);
        $stmt->bindValue(':id_page', $id_page, PDO::PARAM_INT);
        $stmt->execute();
        $value = $stmt->fetch(PDO::FETCH_ASSOC);

        //novi
        $page = new Page();

        //postavi vrednosti
        $page->id_page = (int)$value["id_page"];
        $page->rootx = $value["rootx"];
        $page->pathx = $value["pathx"];
        $page->level = $value["level"];
        $page->node_type = $value["node_type"];
        $page->idx = $value["idx"];
        $page->page_data= $value["page_data"];
        $page->dat_page = new DateTime($value["dat_page"]);
        $page->descr = $value["descr"];

        return $page;
    }




    //upiši instancu (bez podataka o samoj stranici)
    public function Insert(): int
    {
        if ($this->id_page != -1) {
            throw new Exception("Id pri upisu komentara mora biti -1");
        }

        if (!($this->dat_page instanceof DateTime)) {
            throw new Exception("Datum upisa nije definisan...!");
        }

        $conn = Connection::GetConnection();

        //postavi level
        $arr=explode("/",$this->pathx);

        $this->level=count($arr)-1;

        $last_id=0;

        try {
            
            $stmt = $conn->prepare("call insertcmspage(:rootx,:pathx,:node_type,:level,:idx,:descr,:dat_page)");
            $stmt->bindValue(':rootx', $this->rootx, PDO::PARAM_STR);
            $stmt->bindValue(':pathx', $this->pathx, PDO::PARAM_STR);
            $stmt->bindValue(':level', $this->level, PDO::PARAM_INT);
            $stmt->bindValue(':node_type', $this->node_type, PDO::PARAM_INT);
            $stmt->bindValue(':idx', $this->idx, PDO::PARAM_INT);
            $stmt->bindValue(':descr', $this->descr, PDO::PARAM_STR);
            $stmt->bindValue(':dat_page',  $this->dat_page->format('Y-m-d H:i:s'), PDO::PARAM_STR);

            $stmt->execute();

            //id insertovanog unosa
			$row= $stmt->fetch(PDO::FETCH_ASSOC);
           
            //id insertovanog unosa
            $last_id=(int) $row['id'];

           
        } catch (Exception $e) {

            throw $e;
        }

        return $last_id;
    }




    //upiši nove podatke stranice
    public function UpdatePageData(): int
    {

        $conn = Connection::GetConnection();


        $stmt = $conn->prepare("update cms_pages set page_data=:page_data where id_page=:id_page");
        $stmt->bindValue(':id_page', $this->id_page, PDO::PARAM_INT);
        $stmt->bindValue(':page_data', $this->page_data, PDO::PARAM_STR);
        $stmt->execute();

        //id insertovanog unosa
        $last_id = (int)$stmt->rowCount();

        return $last_id;
    }

    public function DeletePage(): int
    {

        $conn = Connection::GetConnection();


        $stmt = $conn->prepare("delete from cms_pages where id_page=:id_page");
        $stmt->bindValue(':id_page', $this->id_page, PDO::PARAM_INT);
        $stmt->execute();

        //id insertovanog unosa
        $last_id = (int)$stmt->rowCount();

        return $last_id;
    }




}
 