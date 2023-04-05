<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//include("dbconnect.php");
include("dbmodel.php");


class Language{
  public static $langDict = array(
    "monday" => "Montag",
    "tuesday" => "Dienstag",
    "wednesday" => "Mittwoch",
    "thursday" => "Donnerstag",
    "friday" => "Freitag",
    "Saturday" => "Samstag",
    "Sunday" => "Sonntag"
  );
  public static $monthL = array("ERROR","januar","februar","maerz","april","mai","juni","juli","august","september","oktober","november","dezember");
  public static function getWord($word){
    return self::$langDict[strtolower($word)];
  }
  public static function getMonth($monthNumber){
    return self::$monthL[$monthNumber];
  }
}

function eintragen($datum,$isAfNoon,$normalVersion){
		if($normalVersion){
			echo ("<form action='add.php?date=$datum&isafternoon=$isAfNoon'  method='post'>");
			echo ('<input name = "nameE">');
			echo ('<input type="submit" value="Eintragen">');
			echo ('</form>');
			}
		else{
			echo("&nbsp;");
			};
		};

function austragen($name,$date,$isAfNoon,$normalVersion){
		if($normalVersion){
			echo( "<form onsubmit='return confirm(&quot;Willst du dich wirklich austragen?&quot;);' action='remove.php?date=$date&isafternoon=$isAfNoon&nameE=$name' method='post'>
					$name
					<input type='submit' value='Austragen'><p>
					</form>");
			}
		else{
			echo("$name");
			};
		};


function echoTable($month,$normalVersion=True){
	echo('<br><font size="4"><center>');							 //ueberschrift
	echo("<font size='4'><b> ".$month->getMonthName()." </b> ".$month->getExtra()); //ueberschrift zusatz hinzufügen
	echo("</font></center><br>");											//
	echo('<table width="548"');												//tabeln anfang und breite
	echo(">");

	//obere spalte
	echo("<tr><td></td><td><center>
		9:20 - 12:00 Uhr
		</center></td><td><center>
		14:45 - 18:00 Uhr
		</center></td>"); //<tr>
  $maxPersons = 3;
  foreach($month->getDayL() as $day ){
    echo("<tr ><td class = 'boldborder' rowspan ='$maxPersons'>");
    echo($day->getDayName());
    echo("</td>");
    $firstL = $day->getBvNoonL();
    $secondL = $day->getAfNoonL();
    $tempL = array($firstL,$secondL);

    for($x=0; $x<$maxPersons; $x++){
      if($x != 0){
        echo("<tr>");
        $tdclass = "''";
      }else{

        $tdclass = "'boldborder'";
      }
      for($i=0;$i<2;$i++){
        $currentL = $tempL[$i];
        if(count($currentL)>$x){
          echo("<td class = $tdclass>");
          austragen($currentL[$x],$day->getDate(),$i,$normalVersion);
          echo("</td>");
        }elseif(count($currentL) == $x){
          $rowSpan = $maxPersons - count($currentL);
          echo("<td class = $tdclass rowspan ='".$rowSpan."'>");
          //echo("test");
          eintragen($day->getDate(),$i,$normalVersion);
          echo("</td>");
        }
      }
    echo("</tr>\n");
    }

  }
  echo("</table>");
}



if(isset($_GET["normalView"]) && $_GET["normalView"]=="0"){
	$isNormalView = False;
	}
else{
	$isNormalView = True;
	};


$monthL = $dbModel->getAllMonthL();
//HTML START
echo('<html><head>');
echo('	  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">');

echo ('<link rel="stylesheet" href="stylesheet.css?version=1" type="text/css">');

echo('</head>
	  <body>');


    foreach ($monthL as $month) {
      echoTable($month,$isNormalView);
    }


    echo("<a href='newMonthForm.php' >Neue Tabelle</a> <a href='deleteMonthF.php' >Tabelle loeschen</a> ");
    echo ("<a href='extraF.php' >&Uuml;berschriften hinzuf&uuml;gen</a> ");
    echo (" <a href='deleteExtraF.php'>&Uuml;berschriften entfernen</a>");
    if($isNormalView){
    	echo (" <a href='index.php?normalView=0'>Druckansicht aktivieren</a>");
    	}
    else{
    	echo (" <a href='index.php?normalView=1'>Druckansicht deaktivieren</a>");
    	};




echo("</body></html>");


?>
