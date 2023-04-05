<?php
include("dbmodel.php");

$name = $_GET["nameE"];

$date = $_GET["date"];
$isAfterNoon = $_GET["isafternoon"];
if($isAfterNoon == 1){
	$bvNoon = False;
}else{
	$bvNoon = True;
}




$dbModel->removeFromDB($date,$name,$bvNoon,$db);
echo('<head>
	<meta http-equiv="refresh" content="2; URL=index.php">
	</head>');
echo("Sie werden automatisch weitergeleitet...<br>");
echo('Ansonsten <a href="index.php">hier</a> klicken')
?>
