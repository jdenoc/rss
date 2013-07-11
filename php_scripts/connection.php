<?php
/**
 * Author: Denis O'Connor
 * Last Modified: 13-OCT-2012
 */
class pdo_connection{

    private $db;
    private $host="localhost"; 				// Host name
    private $username="jdenocco_root"; 		// Mysql username
    private $password="root_pass"; 			// Mysql password
    private $debug;

    public function __construct($db_name, $debug=false){
        $this->db = new PDO(
            "mysql:host=$this->host;dbname=$db_name",
            $this->username,
            $this->password
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->debug = $debug;
    }

    public function exec($stmt){
        if($this->debug)    echo $stmt."\r\n";
        return $this->db->exec($stmt);
    }


    public function getAllRows($stmt, $bind=array()){
        $query = $this->db->prepare($stmt);
        foreach($bind as $key=>$item){
            $query->bindValue(':'.$key, $item, PDO::PARAM_STR);
        }
        $query->execute();

        if($this->debug){
            echo $stmt."\r\n";
            $query->debugDumpParams();
        }
        return $query->fetchAll();
    }

    /**
     * @param $stmt string      SQL statement
     * @param array $bind       Array of values to bind to SQL statement
     * @return string           The value result from the SQL statement | FALSE if nothing found
     */
    public function getValue($stmt, $bind=array()){
        $query = $this->db->prepare($stmt);
        /*** bind the paramaters ***/
        foreach($bind as $key=>$item){
            $query->bindParam(':'.$key, $item, PDO::PARAM_STR);
        }
        $query->execute();

        if($this->debug){
            echo $stmt."\r\n";
            $query->debugDumpParams();
        }
        return $query->fetchColumn();
    }

    public function getAllValues($stmt, $bind=array()){
        $query = $this->db->prepare($stmt);
        $array = array();
        /*** bind the paramaters ***/
        foreach($bind as $key=>$item){
            $query->bindParam(':'.$key, $item, PDO::PARAM_STR);
        }
        $query->execute();
        $rows = $query->rowCount();
        for($i=0; $i<$rows; $i++){
            $array[] = $query->fetchColumn();
        }

        if($this->debug){
            echo $stmt."\r\n";
            $query->debugDumpParams();
        }
        return $array;
    }

    public function getRow($stmt, $bind=array()){
        $query = $this->db->prepare($stmt);

        /*** bind the paramaters ***/
        foreach($bind as $key=>$item){
            $query->bindValue(':'.$key, $item, PDO::PARAM_STR);
        }
        $query->execute();

        if($this->debug){
            echo $stmt."\r\n";
            $query->debugDumpParams();
        }
        return $query->fetch();
    }

    public function insert($tbl_name, $array=array()){
        $values = '';
        foreach($array as $key=>$value){
            $value = (get_magic_quotes_gpc())? $value : addslashes($value);
            $values .= " $key='$value',";
        }
        $values = substr($values, 0, strlen($values)-1);
        $stmt = "INSERT INTO $tbl_name SET $values";

        if($this->debug)    echo $stmt."\r\n";
        return $this->db->exec($stmt);
    }

    public function update($tbl_name, $array=array(), $whereString, $whereArray=array()){
        $values = '';
        foreach($array as $key=>$value){
            $value = (get_magic_quotes_gpc())? stripslashes($value) : $value;
            $values .= " $key='$value',";
        }
        $values = substr($values, 0, strlen($values)-1);

        $stmt = "UPDATE ".$tbl_name." SET ".$values." WHERE ".$whereString;
        $query = $this->db->prepare($stmt);

        /*** bind the paramaters ***/
        foreach($whereArray as $key=>$item){
            $query->bindValue(':'.$key, $item, PDO::PARAM_STR);
        }
        $result = $query->execute();

        if($this->debug){
            echo $stmt."\r\n";
            $query->debugDumpParams();
        }
        return $result;
    }

    public function delete($tbl_name, $whereString, $whereArray=array()){
        $stmt = "DELETE FROM $tbl_name WHERE $whereString";
        $query = $this->db->prepare($stmt);

        /*** bind the paramaters ***/
        foreach($whereArray as $key=>$item){
            $query->bindValue(':'.$key, $item, PDO::PARAM_STR);
        }
        $result = $query->execute();

        if($this->debug){
            echo $stmt."\r\n";
            $query->debugDumpParams();
        }
        return $result;
    }

    public function closeConnection(){
        $this->db = null;
        $this->host = null;
        $this->username = null;
        $this->password = null;
        $this->debug = null;
    }
}