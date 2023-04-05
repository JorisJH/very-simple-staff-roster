<?php
$db = mysqli_connect('HOSTNAME', 'USERNAME', 'PASSWORD', 'DATABASE');
if(!$db)
{
  exit("Verbindungsfehler: ".mysqli_connect_error());
};
?>
