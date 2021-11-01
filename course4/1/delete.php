<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION['name']) ) {
    die("ACCESS DENIED");
}

if ( isset($_POST['cancel']) ){
    header("Location: index.php");
    return;
}

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $stmt = $pdo->prepare("DELETE FROM profile WHERE profile_id = :xyz ");
    $stmt->execute(array(
        ":xyz" => $_POST['profile_id']));
    $_SESSION['success'] = "Record Deleted";
    header("Location: index.php");
    return;    
}

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name FROM profile WHERE profile_id = :zip");
$stmt->execute(array(
    ":zip" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false) {
    $_SESSION = "Bad value for profile_id";
    header("Location: index.php");
    return;
}    

?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Syed Muneef ur Rehman</title>
</head>  
<body>  
    <h1>Deleting Profile</h1>
    <p>First Name: <?= htmlentities($row['first_name']) ?></p>
    <p>Last Name: <?= htmlentities($row['last_name']) ?></p>

    <form method="post">
    <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
    <input type="submit" value="Delete" name="delete">
    <input type="submit" value="Cancel" name="cancel">
    </form>
</body>
</html>    
