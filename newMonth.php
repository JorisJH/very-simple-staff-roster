<?php
include("dbmodel.php");

function convert($string){
	$fertig =strtolower(str_replace(" ","",$string));
	return $fertig;
	};


$dayES = $_POST["days"];
$dayL = explode(",",convert($dayES));
$month = $_POST["month"];
$year = $_POST["year"];






$dbModel->createMonthDB($month,$year,$dayL);



echo('<head>
	<meta http-equiv="refresh" content="2; URL=index.php">
	</head>');
echo("Sie werden automatisch weitergeleitet...<br>");
echo('Ansonsten <a href="index.php">hier</a> klicken')











?>
