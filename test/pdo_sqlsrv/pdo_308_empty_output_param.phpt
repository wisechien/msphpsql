--TEST--
GitHub issue #308 - empty string set to output parameter on stored procedure 
--DESCRIPTION--
Verifies GitHub issue 308 is fixed, empty string returned as output parameter will remain an empty string.
--SKIPIF--
--FILE--
<?php
require_once("pdo_tools.inc");

// Connect 
require_once("autonomous_setup.php");

$dbName = "tempdb";

$conn = new PDO("sqlsrv:server=$serverName;Database=$dbName", $username, $password);   
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );   

$procName = GetTempProcName();

$sql = "CREATE PROCEDURE $procName @TEST VARCHAR(200)='' OUTPUT
AS BEGIN
SET NOCOUNT ON;
SET @TEST='';
SELECT HELLO_WORLD_COLUMN='THIS IS A COLUMN IN A SINGLE DATASET';
END";
$stmt = $conn->exec($sql);

$sql = "EXEC $procName @Test = :Test";
$stmt = $conn->prepare($sql);
$out = '';
$stmt->bindParam(':Test', $out, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 200);
$stmt->execute();

$result = $stmt->fetchAll();
$stmt->closeCursor();

echo "OUT value: ";
var_dump($out);

// Free the statement and connection resources. 
$stmt = null;
$conn = null;

print "Done";
?> 
--EXPECT--
OUT value: string(0) ""
Done