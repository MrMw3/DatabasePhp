<?php

class Database
{
    //Decleare Variables

    protected $db_type;
    protected $host;
    protected $db_name;
    protected $dsn;
    protected $username;
    protected $password;
    protected $options;

    //Constructor

    public function __construct($db_type, $host, $db_name, $username, $password){
        // Dsn connect string
        $this -> dsn = "$db_type:host=$host;dbname=$db_name";

        // Username & Password of database
        $this -> username = $username;
        $this -> password = $password;

        // Set pdo option
        $this->options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
        ];
    }

    // Connect to database and return object for execute and etc.
    private function Connect(){
        $database = new PDO($this->dsn, $this->username, $this->password);
        return $database;
    }


    // Select All Values From Database
    final public function selectAll($table_name, $where = 'true', $fetch='fetchAll'){
        $connection = $this->Connect();
        $result = $connection->prepare("SELECT * FROM $table_name WHERE $where");
        $result -> execute();

        // IF More than 1 data exists use FetchAll else Use Fetch

        if($result->rowCount() >= 1){
            
            if($fetch == 'fetchAll'){
                return $result->fetchAll(PDO::FETCH_ASSOC);
            }

            else{
                return $result->fetch(PDO::FETCH_ASSOC);
            }

        }

        // IF DATA NOT EXIST RETURN FALSE

        else{
            return false;
        }

    }

    // Insert Data To Database
    final public function insert($table_name, $columns, $values){

        try{

            // Join Columns together whit format: column_name=?  To bind later
            $column_names = "";
            foreach($columns as $key=>$column){
                if($key+1 < count($columns)){
                    $column_names .= "$column=?, ";
                }
                
                else{
                    $column_names .= "$column=? ";
                }
            }


            // Connect To Databse and Prepare it
            $connection = $this->Connect();
            $result = $connection->prepare("INSERT INTO $table_name SET $column_names");
            

            // Bind Values to Columns
            foreach($values as $key=>$value){
                $result->bindValue($key+1, $value);
            }

            // Execute and return Response
            return $result->execute();
            

        }

        catch(PDOException $ex){
            return $ex->getMessage();
        }
    }

    // Update Data in Database
    final public function update($table_name, $columns, $values, $where){
        try{

            // Join Columns together whit format: column_name=?  To bind later
            $column_names = "";
            foreach($columns as $key=>$column){
                if($key+1 < count($columns)){
                    $column_names .= "$column=?, ";
                }
                
                else{
                    $column_names .= "$column=? ";
                }
            }


            // Connect To Databse and Prepare it
            $connection = $this->Connect();
            $result = $connection->prepare("UPDATE $table_name SET $column_names WHERE $where");

            // Bind Values to Columns
            foreach($values as $key=>$value){
                $result->bindValue($key+1, $value);
            }


            // Execute and return Response
            return $result->execute();
            

        }

        catch(PDOException $ex){
            return $ex->getMessage();
        }
    }

    // Delete Data form Database
    final public function delete($table_name, $where){
        try{

            // Connect to database
            $connection = $this->Connect();
            $result = $connection->prepare("DELETE FROM $table_name WHERE $where");

            // Execute and return
            return $result->execute();
        }

        catch(PDOException $ex){
            return $ex->getMessage();
        }
    }
}

?>