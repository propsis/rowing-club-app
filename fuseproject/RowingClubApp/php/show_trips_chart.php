<?php
require_once('/home/scocta5/bin/composer/vendor/autoload.php');

use Zend\Config\Factory;

$userId = $_POST['userId'];

$config = Factory::fromFile('config_rowing_club_db.php', true);

$servername=$config->get("database")->get("servername");
$dbusername=$config->get("database")->get("username");
$dbpassword=$config->get("database")->get("password");
$dbname=$config->get("database")->get("dbname");


try {
  $connection = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt_searchUserTrips = $connection->prepare('SELECT SUM(km) as sum_km, DATE_FORMAT(date,"%m") as fdate FROM Trip JOIN User_trip ON Trip.id=User_trip.trip_id AND User_trip.user_id=:userId AND date >= date_sub(now(), interval 4 month) GROUP BY DATE_FORMAT(date,"%m") ORDER BY fdate ASC');
  $stmt_searchUserTrips->bindParam(':userId', $userId);
  $stmt_searchUserTrips->execute();
  $searchResult = $stmt_searchUserTrips->fetchAll();
  
  $array=json_encode(array('Trips' => $searchResult)); 
  echo $array;
   
} catch (PDOException $e) {
   header('HTTP/1.0 500 Internal Server Error');
}


?>