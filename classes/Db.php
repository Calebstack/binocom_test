<?php

    require_once "Config.php";

    class Db{
        private $dbhost = DB_HOST;
        private $dbuser = DB_USER;
        private $dbpass = DB_PASS;
        private $dbname = DB_NAME;

        protected function connect(){
            $dsn= "mysql:host=$this->dbhost;dbname=$this->dbname;";
            $option = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];
            try{
                $pdo = new PDO($dsn,$this->dbuser,$this->dbpass,$option);
                return $pdo;
            }catch(PDOException $e){
                //echo $e->getMessage();
                return false;
            }
        }
    }

?>