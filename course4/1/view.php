<?php
require_once "pdo.php";
session_start();

if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :xyz");
$stmt->execute(array(
    ":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false) {
    $_SESSION['error'] = "Bad value for profile_id";
    header("Location: index.php");
    return;
}    

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Syed Muneef ur Rehman</title>
        <?php require_once "bootstrap.php"; ?>
    </head>

    <body>
        <h1>Profile Information</h1>
        <p>First Name: <?= htmlentities($row['first_name']) ?></p>
        <p>Last Name: <?= htmlentities($row['last_name']) ?></p>
        <p>Email: <?= htmlentities($row['email']) ?></p>
        <p>Headline: <?= htmlentities($row['headline']) ?></p>
        <p>Summary: <?= htmlentities($row['summary']) ?></p>
        <a href="index.php">Done</a>
    </body>
</html>