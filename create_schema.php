<?php

if($argc < 2){
    echo ' No arguments passed'.PHP_EOL; die;
}
$config = getopt('p:u:s:d:', ['driver::']);

$servername = $config['s'];
$username = $config['u'];
$password = $config['p'];
$database = $config['d'];
$charset = 'utf8mb4';
$driver = !empty($config['driver'])? $config['driver']:'mysql'; // set driver to mysql if empty

// Create connection
$dsn = "$driver:host=$servername;dbname=$database";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $username, $password, $opt);


// Check connection
if (!$pdo) {
    die("Connection failed: ");
}
echo "Connected successfully".PHP_EOL;

if($driver == 'mysql'){
    $sql = 'select * from information_schema.columns';
    $sql .=  " where columns.table_schema = '$database';";
}elseif ($driver == 'pgsql'){
    $sql = "SELECT *  FROM information_schema.columns where table_catalog = '$database' and table_schema  = 'public'";
}

echo $sql.PHP_EOL;
$stmt = $pdo->query($sql);
//$stmt->execute(['database' => $database]);
$result = $stmt->fetchAll();

//var_dump($result);die();
$tables =[];
if($result){

    foreach($result as $row ){
        $tables[] = array_change_key_case($row, CASE_UPPER);
    }
}else{
    echo '0 results found'.PHP_EOL;
}

$tables = array_change_key_case($tables, CASE_UPPER);

$file = fopen('db.md', 'w');
$count = count($tables);

$db = '';
$table_name = '';
for($x = 0; $x < $count; $x++){
    if($db != $tables[$x]['TABLE_SCHEMA']){
        $db = $tables[$x]['TABLE_SCHEMA'];
        fwrite($file ,"##".$db."\n\n");
    }
    if($table_name != $tables[$x]['TABLE_NAME']){
        $table_name = $tables[$x]['TABLE_NAME'];
        fwrite($file ,"\n###".$table_name."\n\n");
    }
    if($driver == 'mysql'){
        $line = "* ".$tables[$x]['COLUMN_NAME']." (".$tables[$x]['COLUMN_TYPE'].")\n";
    }
    elseif($driver == 'pgsql'){
        $line = "* ".$tables[$x]['COLUMN_NAME']." (".$tables[$x]['DATA_TYPE'].")\n";
    }
    fwrite($file, $line);
}
fclose($file);
