<?php

session_start();

if (!empty($_POST['color'])) {
    $_SESSION['color'] = $_POST['color'];
}
    
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
   header("Location: index.php");
}

?>

<html>
<head>
<title>Octank Electronics Server1</title>

</head>
<body>
<h4>Server1</h4>
Welcome :  <?php echo $_SESSION['login_user'] ?> 
<br>
<?php
if (!isset($_SESSION['color'])) {
    echo "No favorite color set yet";
} else {
    echo "Current favorite color: " . $_SESSION['color'];
}
?>

<form action="home.php" method="post">
    <label for="color">New Favorite color:</label><br>
    <input type="text" id="color" name="color"><br>
    <input type="submit" value="Change Color">
</form>

<form action="logout.php" method="post">
  <input type ="submit" value="Logout">
</form>

</body>
</html>
