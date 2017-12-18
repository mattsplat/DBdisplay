<?php

class DatabaseConnection
{

    public $driver;
    public $database;
    public $server_name;
    public $password;
    public $username;
    public $connection;
    public $query;

    function __construct(array $new) {

        $this->database = $new['database'];
        $this->server_name = $new['server_name'];
        $this->password = $new['password'];
        $this->username = $new['username'];
    }

    public function connect(){



        $dsn = $this->driver.":host=".$this->server_name.";dbname=".$this->database;
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        echo 'Connecting'.PHP_EOL;

        $this->connection = new PDO($dsn, $this->username, $this->password, $opt);

        if (!$this->connection) {
            die("Connection failed: ");
        }
        echo "Connected successfully".PHP_EOL;

        return true;
    }

    public function query(){

        $sql = $this->formatQuery();

        $stmt = $this->connection->query($sql);

        $this->query = $stmt->fetchAll();

    }

    public function writeToFile($filename){
        if($this->query){
            // make sure all table names are capital
            foreach($this->query as $row ){
                $tables[] = array_change_key_case($row, CASE_UPPER);
            }
        }else{
            echo '0 results found'.PHP_EOL;die;
        }

        $file = fopen($filename, 'w');
        $count = count($tables);

        $db = '';
        $table_name = '';
        for($x = 0; $x < $count; $x++){
            // write database name
            if($db != $tables[$x]['TABLE_SCHEMA']){
                $db = $tables[$x]['TABLE_SCHEMA'];
                fwrite($file ,"## ".$db."\n\n");
            }
            // write table name
            if($table_name != $tables[$x]['TABLE_NAME']){
                $table_name = $tables[$x]['TABLE_NAME'];
                fwrite($file ,"\n### ".$table_name."\n\n");
            }
            $line = $this->formatColumn($tables[$x]);

            fwrite($file, $line);
        }
        echo 'File '.$filename.' written successfully';
        fclose($file);
    }
    protected function formatQuery(){
        // child method
    }
    protected function getDriver(){
        //child method
    }
}

class PGsqlConnection extends databaseConnection
{

    public $driver = 'pgsql';

    protected function formatQuery()
    {
        $sql = "SELECT *  FROM information_schema.columns where table_catalog = '".
            $this->database."' and table_schema  = 'public'";
        return $sql;
    }

    protected function formatColumn($column)
    {
        $line = "* " . $column['COLUMN_NAME'] . " (" . $column['DATA_TYPE'] . ")\n";
        return $line;
    }
    protected function getDriver(){
        return $this->driver;
    }

}

class MYsqlConnection extends databaseConnection
{


    public $driver = 'mysql';

    protected function formatQuery()
    {
        $sql = 'select * from information_schema.columns';
        $sql .= " where columns.table_schema = '$this->database';";
        return $sql;
    }

    protected function formatColumn($column)
    {
        $line = "* ".$column['COLUMN_NAME']." (".$column['COLUMN_TYPE'].")\n";
        return $line;
    }
    protected function getDriver(){
        return $this->driver;
    }

}
