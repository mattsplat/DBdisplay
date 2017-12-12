<?php

if($argc < 2){
    echo ' No arguments passed'.PHP_EOL; die;
}
$config = getopt('p:u:s:d::');

$servername = $config['s'];
$username = $config['u'];
$password = $config['p'];
$database = $config['d'] ?? '';

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully".PHP_EOL;

$sql = 'select * from information_schema.columns';
$sql .= $database? " where columns.table_schema = '$database';":  ';';
echo $sql.PHP_EOL;
$result = $conn->query($sql);

$tables =[];
if($result->num_rows > 0){

    while($row = $result->fetch_assoc() ){
        $tables[] = $row;
    }
}else{
    echo '0 results found'.PHP_EOL;
}

$conn->close();

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
