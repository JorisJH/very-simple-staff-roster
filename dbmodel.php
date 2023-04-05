<?php
include("dbconnect.php");
class Day{
  //Date of the day as timestamp
  public $date;
  public $bvNoonL;
  public $afNoonL;

  function __construct($date, $bvNoonL, $afNoonL) {
    $this->setDate($date);
    $this->setBvNoonL($bvNoonL);
    $this->setAfNoonL($afNoonL);

   }
  function setDate($date){
    $this->date = $date;
  }
  function getDate(){
    return $this->date;
  }

  function setBvNoonL($bvNoonL){
     $this->bvNoonL = $bvNoonL;
  }
  function getBvNoonL(){
    return $this->bvNoonL;
  }

  function setAfNoonL($afNoonL){
    $this->afNoonL = $afNoonL;
  }
  function getAfNoonL(){
    return $this->afNoonL;
  }


  function getDayName(){
    $date = date_create($this->getDate());
    $dayName = Language::getWord(date_format($date,"l"));
    $dateString = date_format($date,"d.m.y");
    return $dayName."<br>".$dateString;
  }

}

class Month{
  public $dayL;

  public $extra;

  function __construct($dayL,$extra){
    $this->setDayL($dayL);

    $this->extra = $extra;
  }

  function setDayL($dayL){
    $this->dayL = $dayL;
  }
  function getDayL(){
    return $this->dayL;
  }

  function getMonthName(){
    $date =  date_create($this->dayL[0]->getDate());
    $monthNumber = (int) date_format($date,"m");
    $year = date_format($date,"Y");
    $monthName = ucfirst(Language::getMonth($monthNumber));
    return $monthName." ".$year;
  }

  function setExtra($extra){
    $this->extra = $extra;
  }
  function getExtra(){
    return $this->extra;
  }
}

class DBModel{
  public $db;
  function __construct($db){
    $this->db = $db;
  }
  function getDay($date){
    $db = $this->db;
    $stmt = $db->prepare("SELECT name FROM bevor_noon WHERE date = ?");
    $stmt->bind_param("s",$date);
    $stmt->execute();
    $result = $stmt->get_result();
    $bvNoonL = array();
    while($row = mysqli_fetch_object($result)){
      $bvNoonL[] = $row->name;
    }

    $stmt = $db->prepare("SELECT name FROM after_noon WHERE date = ?");
    $stmt->bind_param("s",$date);
    $stmt->execute();
    $result = $stmt->get_result();
    $afNoonL = array();
    while($row = mysqli_fetch_object($result)){
      $afNoonL[] = $row->name;
    }
    $day = new Day($date, $bvNoonL, $afNoonL);
    return $day;
  }
  function getMonth($month,$year){
    $db = $this->db;
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $result = $db->query("SELECT * FROM day WHERE date BETWEEN '$year-$month-1' AND '$year-$month-$daysInMonth'");
    $dayL = array();
    while($row = mysqli_fetch_object($result)){
      $dayL[] =  $this->getDay($row->date);

    }
    $extra = $this->getExtra($month,$year);
    if(is_null($extra)){
      $extra = "";
    }
    return new Month($dayL,$extra);

  }
  function getAllMonthL(){
    $db = $this->db;
    $result = $db->query("SELECT month(date) as month, year(date) as year FROM day GROUP BY month,year ORDER BY year,month ASC;");
    $allMonthL = array();
    while($row = mysqli_fetch_object($result)){
      $allMonthL[] = $this->getMonth($row->month,$row->year);

    }
    return $allMonthL;
  }
  function deleteMonth($month,$year){
    $db = $this->db;
  	$daysInMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
  	$startDate = "$year-$month-1";
  	$endDate = "$year-$month-$daysInMonth";
  	$db->query("DELETE FROM day WHERE date BETWEEN '$startDate' AND '$endDate'");
  	$db->query("DELETE FROM bevor_noon WHERE date BETWEEN '$startDate' AND '$endDate'");
  	$db->query("DELETE FROM after_noon WHERE date BETWEEN '$startDate' AND '$endDate'");
  }

  function dayLToNumberL($tageE){
  	$tage = array("Error","montag","dienstag","mittwoch","donnerstag","freitag","samstag","sonntag");
  	$tageEN = array();
  	for($x=0;$x<count($tageE);$x++){
  		for($i=0;$i<count($tage);$i++){
  			if($tage[$i]==$tageE[$x]){
  					$tageEN[] = $i;
  					}
  				}
  			}
  	return $tageEN;
  	}
  function createMonthDB($month,$year,$dayL ){
      $db = $this->db;
      $stmt = $db->prepare("INSERT INTO day values(?)");

      $dayNumberL = $this->dayLtoNumberL($dayL);
      $daysInMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
      $dateL = array() ;
      for($x=1;$x<=$daysInMonth;$x++){
      		$timestamp = mktime(0,0,0,$month,$x,$year);
      		$tagZ = date("w",$timestamp);
      		if(in_array($tagZ,$dayNumberL)){
      			//$dateL[] = date("Y-m-d",$timestamp);
            $date = date("Y-m-d",$timestamp);
            $stmt->bind_param("s",$date);
            $stmt->execute();
      			}
      		}
    }

  function insertNameDB($date,$name,$isBvNoon){
      $db = $this->db;
      if($isBvNoon){
        $stmt = $db->prepare("INSERT INTO bevor_noon values(?,?)");
      }else{
        $stmt = $db->prepare("INSERT INTO after_noon values(?,?)");
      }
      $stmt->bind_param("ss",$date,$name);
      $stmt->execute();
    }

  function removeFromDB($date,$name,$isBvNoon){
      $db = $this->db;
      if($isBvNoon){
        $stmt = $db->prepare("DELETE FROM bevor_noon WHERE date=? AND name=?");
      }else{
        $stmt = $db->prepare("DELETE FROM after_noon WHERE date=? AND name=?");
      }
      $stmt->bind_param("ss",$date,$name);
      $stmt->execute();
    }

  function addExtra($month,$year,$text){
    $stmt = $this->db->prepare("REPLACE INTO extra VALUES (?,?,?)");
    $stmt->bind_param("iis",$month,$year,$text);
    $stmt->execute();
  }
  function getExtra($month,$year){
    $stmt = $this->db->prepare("SELECT text from extra WHERE year = ? and month = ?");
    $stmt->bind_param("ii",$year,$month);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = mysqli_fetch_object($result)){
      return $row->text;
    }
    return NULL;

  }
  function removeExtra($month,$year){
    $stmt = $this->db->prepare("DELETE FROM extra WHERE year = ? AND month = ?");
    $stmt->bind_param("ii",$year,$month);
    $stmt->execute();
  }
}

$dbModel = new DBModel($db);
?>
