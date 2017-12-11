<html>
<head><title>Login</title></head>
<body>
<b>Login</b><br/><br/>
<form action="login.php" method="GET">
User:<input type="text" name="user"/><br/><br/>
Password:<input type="password" name="password"/><br/>
<input type="submit" value="Login" name="Login"/>
</form>
<br/>
<br/>
<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

try {
  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","12345",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
//   print_r($dbh);

if(isset($_GET['Register']))
{
  $dbh->beginTransaction();
//   $dbh->exec('delete from users where username="smith"');
  $dbh->exec('insert into users values("'.$_GET['userr'].'","' . md5($_GET['passwordr']) . '","'.$_GET['fullname'].'","'.$_GET['email'].'")')
        or die(print_r($dbh->errorInfo(), true));
  $dbh->commit();
  print "<script>alert('Registered..')</script>";
}
if(isset($_GET['Login']))
{
  $stmt = $dbh->prepare('select * from users where username="'.$_GET['user'].'" AND password="'.md5($_GET['password']).'" ');
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if($stmt->rowcount()==0)
  {
    print "<script>alert('Login Failed..Try again..')</script>";
  }
  else
  {
    
    session_start();
    $_SESSION['user']=$row["username"];
    $_SESSION['fullname']=$row["fullname"];
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Location: /project5/board.php");
    exit;
  }
//   print "<pre>";
//   while ($row = $stmt->fetch()) {
//     print_r($row);
//   }
//   print "</pre>";
}


} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
?>
 <br/>
<b>Register as a user</b>
<form action="login.php" method="GET">
User:<input type="text" name="userr"/><br/>
Password:<input type="password" name="passwordr"/><br/>
Fullname:<input type="text" name="fullname"/><br/>
Email:<input type="text" name="email"/><br/>
<input type="submit" value="Register" name="Register"/>
</form> 
</body>
</html>


<!-- References:
https://stackoverflow.com/questions/15090785/redirecting-to-a-new-page-after-successful-login
https://www.tutorialspoint.com/php/php_login_example.htm
http://php.net/manual/en/pdostatement.fetch.php
https://stackoverflow.com/questions/21233375/destroy-session-in-php?noredirect=1&lq=1
https://stackoverflow.com/questions/49547/how-to-control-web-page-caching-across-all-browsers/2068407#2068407 -->