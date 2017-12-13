<?php

if($argc < 2){
    echo ' No arguments passed'.PHP_EOL; die;
}
$config = getopt('p:u:s:d::');

$servername = $config['s'];
$username = $config['u'];
$password = $config['p'];
$database = $config['d'] ?? '';
$charset = 'utf8mb4';
$driver = 'mysql';

// Create connection
$dsn = "$driver:host=$servername;dbname=$database;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $username, $password, $opt);
//$conn = new mysqli($servername, $username, $password);

// Check connection
if (!$pdo) {
    die("Connection failed: ");
}
echo "Connected successfully".PHP_EOL;

$sql = 'select * from information_schema.columns';
$sql .= $database? " where columns.table_schema = :database;":  ';';
echo $sql.PHP_EOL;
$stmt = $pdo->prepare($sql);
$stmt->execute(['database' => $database]);
$result = $stmt->fetchAll();


$tables =[];
if($result){

    foreach($result as $row ){
        $tables[] = $row;
    }
}else{
    echo '0 results found'.PHP_EOL;
}



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
    $line = "* ".$tables[$x]['COLUMN_NAME']." (".$tables[$x]['COLUMN_TYPE'].")\n";
    fwrite($file, $line);
}
fclose($file);
