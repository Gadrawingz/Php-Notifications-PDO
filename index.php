<?php
session_start();
if(!isset($_SESSION['User']) || !isset($_SESSION['UserId'])) {
  echo "<script>window.location='./../'</script>"; // redir u to index shit
}

include ('../account/acc_query.php');
$hobj= new AccountQuery;

include_once('../comments/comqueries.php');
$comobj = new TcommentsQueries;

include_once('nqueries.php');
$nobj= new NotificationsQuery;


if(isset($_REQUEST['/'])) {
  echo "<script>window.location='../notifications/'</script>"; // redir u to index shit
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>My Notifications - Donnekt</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../stylesh/main_menus.css">
<link rel="stylesheet" type="text/css" href="../stylesh/large_styles.css">
<link rel="stylesheet" type="text/css" href="../stylesh/magicss.css">
<?php include ('../scriptz/dtmetags.php'); ?>

</style>
</head>
<body>

<div class="wholepage">
<!-- This is my fuckin header section -->

<?php include('../scriptz/dtheader.php'); ?>

<!--Header section endin-->
<div class="divcontainer"><!--divcontainer div starts-->
<div class="leftside_contents garticlez" ><!--leftside div starts-->

  <div class="main_display"><!--main_display(timeline) starts-->
    <h1>My Notifications</h1><br>
  <div class="main_tbox task_boxized" style="height: auto; min-height: 400px;">

<?php

$npglimit = 30;  //Number of entries to show in a page.
$page = '';      
if (isset($_GET["page"])) {  
  $page= $_GET["page"];  }  
  else {  $page=1;  }
$pagestartfrom = ($page-1) * $npglimit; 

$getnotifs = $nobj->viewNotifications($_SESSION['UserId'], $pagestartfrom, $npglimit);
while ($notifrow = $getnotifs->FETCH(PDO::FETCH_ASSOC)) {

$getuserd = $hobj->getUserDetails($notifrow['user_id']);
$userrow = $getuserd->FETCH(PDO::FETCH_ASSOC); 



  // I wanna get only u_data for someone who posted a post with that id
  $getx = $nobj->getComments4SingleUserByStuffId($notifrow['stuff_id']);
  $crow = $getx->FETCH(PDO::FETCH_ASSOC);

// wanna get row per user in replies table
$getg = $nobj->getReplies4SingleUserByStuffId($notifrow['stuff_id']);
$reprow = $getg->FETCH(PDO::FETCH_ASSOC);

  // I wanna get only u_data for someone who posted a post with that id
  $gett = $nobj->getPosts4SingleUserByStuffId($notifrow['stuff_id']);
  $trow = $gett->FETCH(PDO::FETCH_ASSOC);

// For using it for friendship case
$to_user_followed_friend = $hobj->getUserDetails($notifrow['stuff_id']);
$to_urow = $to_user_followed_friend->FETCH(PDO::FETCH_ASSOC);



  /*
  ahangaha izajya izimarking as seen igendeye kuri stuff id ishobora kuba 
  (1)tpost_id, (2)tcom_id cg (3)followed user!
  */
  if(isset($_GET['_notifseen'])) {
  $nobj->markNotifsAsSeenByClicking($_SESSION['UserId']);
}


/*  if($trow['user_idref']==$_SESSION['UserId'] || $crow['user_idr']== $_SESSION['UserId'] || $to_urow['user_id']== $_SESSION['UserId']) {*/
  if($notifrow['notif_to']==$_SESSION['UserId'] ) {

    if($notifrow['notif_status']=='seen') { ?>
      <script type="text/javascript">
      let notif_anchor = document.getElementsByClassName("notif_anchor");
      notif_anchor.style.background="brown";
      notif_anchor.style.border="none";
      notif_anchor.style.color="blue";
      </script>
    <?php } ?>

<!--  End Commentin shit! -->
    <?php if($notifrow['notif_message']=='CommentOnly' && $notifrow['notif_type']=='Comment' && $trow['user_idref']==$_SESSION['UserId']) { ?>
      
      <?php /*echo $trow['user_idref']."CDM";*/ ?>
      <a class="notif_anchor notiflink" href="../comments/?comd&tpid=<?php echo $trow['tpost_id'];?>&don_notif_status=unread&com_only">
        <div class="notifcontent don-notifbox">
        <?php 
        // For getting username by considering user_idr on tcomments or user_idref on tposts
        $utcstmt = $hobj->getUserDetails($trow['user_idref']);
        $uuurow = $utcstmt->FETCH(PDO::FETCH_ASSOC);
        ?>

          &#9997; <?php echo $userrow['username'];?> commented to your topic (<?php echo $notifrow['stuff_id']." - ".$nobj->limitNotifStringToDisplay($trow['tpost_content'])." (".$uuurow['username'];?>) &bull; <?php echo date('j F, g:i',strtotime($notifrow['action_date']));?>
        </div>
      </a>
    <?php } ?>
