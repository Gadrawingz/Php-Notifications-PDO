<?php
class DatabaseConnectivity {

  public    $host="localhost";
  protected $dbase= "database";
  private   $user="root";
  private   $pass="";
  public    $con;
  
  public function connection(){

    try{
    $dsn= "mysql:host=$this->host; dbname=$this->dbase";
    $this->con = new PDO($dsn, $this->user, $this->pass );
    return $this->conn;

  }catch(PDOException $err ){
    echo "OOPS! ERROR OCCURED".$err->getMessage();

  }
  }
}  


class Notifs extends DatabaseConnectivity{

  public function __construct() {
    $obj = new DatabaseConnectivity;
    $this->conx= $obj->connection();
  }
  
  public function viewNotifications($loggedInUser, $nstart, $nlimit) {
    $sql= "SELECT * FROM notifications WHERE user_id!='$loggedInUser'  ORDER BY notif_id DESC LIMIT $nstart, $nlimit";
    $stmt=$this->conn->prepare($sql);
    $stmt->execute();
    return $stmt;
  }


  public function countNotificationsPerUser($user) {
    $query ="SELECT COUNT(*) FROM notifications WHERE notif_status='unread' AND notif_to = '$user' ";
    $stnum = $this->conn->query($query)->fetchColumn();
    return $stnum;
  }
  
?>
