<?php
include("dbmodel.php");

$month = $_POST["month"];
$year = $_POST["year"];
$text = $_POST["text"];

$dbModel->addExtra($month,$year,$text);
echo('<head>
	<meta http-equiv="refresh" content="2; URL=index.php">
	</head>');
echo("Sie werden automatisch weitergeleitet...<br>");
echo('Ansonsten <a href="index.php">hier</a> klicken')
?>
