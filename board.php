<html>
<head><title>Message Board</title></head>
<body>


<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
if(isset($_POST['logout']))
{
  $_SESSION = array();
  if ( ini_get( 'session.use_cookies' ) ) {
      $params = session_get_cookie_params();
      setcookie( session_name(), '', ( time() - 42000 ), $params['path'], $params['domain'], $params['secure'], $params['httponly'] );
  }
  session_destroy();
  header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
  header("Pragma: no-cache");
  header("Expires: 0");
  header("Location: /project5/login.php");
  exit;
}
try {
  session_start();
  if($_SESSION['user'])
  {
    print "Welcome user ".$_SESSION['user'];
    print "<form action='board.php' method='POST'>
    <input type='submit' name='logout' value='Logout'/>
    </form>";
    print "<hr/>
    <b>Message Board
    </b><hr/>";
    print "<form action='board.php' method='POST'>
    <textarea rows='3' cols='35' name='msgtext' ></textarea>
    <input type='submit' name='newpost' value='New Post'/>
    </form><hr/>";
  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","12345",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

  if(isset($_POST['newpost']))
  {
    $dbh->beginTransaction();
    $uni=uniqid();
  $dbh->exec('insert into posts values("'.$uni.'",null,"'.$_SESSION['user'].'",NOW(),"'.$_POST['msgtext'].'")')
        or die(print_r($dbh->errorInfo(), true));
  $dbh->commit();
  }

  if(isset($_GET['replyto']))
  {
    $dbh->beginTransaction();
    $uni=uniqid();
  $dbh->exec('insert into posts values("'.$uni.'","'.$_GET['replyto'].'","'.$_SESSION['user'].'",NOW(),"'.$_GET['msgtext1'].'")')
        or die(print_r($dbh->errorInfo(), true));
  $dbh->commit();
  }
  
  // print_r($dbh);
  $stmt = $dbh->prepare('select * from posts order by datetime desc');
  $stmt->execute();
  $stmt1 = $dbh->prepare('select * from users');
  $stmt1->execute();
  print "<div>";
  while ($row = $stmt->fetch()) {
    print "<div><b> Message ID: </b>".$row[0];
    print "<br/><b> Username: </b>".$row[2];
    $stmt1 = $dbh->prepare('select fullname from users where username="'.$row[2].'"');
    $stmt1->execute();
    $r=$stmt1->fetch();
    print "<br/><b> Fullname: </b>".$r[0];    
    print "<br/><b> Time Posted: </b>".$row[3];
    if($row[1])
    print "<br/><b> Reply for Message ID: </b>".$row[1];
    print "<p style='font-size:large'><b> Message: </b>".$row[4]."</p>";
    print "<br/><form action='board.php' method='GET'>
    <textarea rows='3' cols='25' name='msgtext1' ></textarea>
    <input type='submit' name='reply' value='Reply'/>
    <input type='hidden' name='replyto' value='".$row[0]."'/>
    </form>";
    // print " ReplyTo: ".$row[1];
    print "</div><hr/>";

  }
  print "</div>";
}
else
{
  header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
  header("Pragma: no-cache");
  header("Expires: 0");
  header("Location: /project5/login.php");
  exit;
}
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}


?>
</body>
</html>
