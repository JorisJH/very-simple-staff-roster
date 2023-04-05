<?php
include("dbmodel.php");


$year = $_POST["year"];
$month = $_POST["month"];



$dbModel->deleteMonth($month,$year);

echo('<head>
	<meta http-equiv="refresh" content="2; URL=index.php">
	</head>');
echo("Sie werden automatisch weitergeleitet...<br>");
echo('Ansonsten <a href="index.php">hier</a> klicken')
?>
