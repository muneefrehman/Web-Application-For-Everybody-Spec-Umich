<?php
require_once "pdo.php";
session_start();
require_once "util.php";

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

$profile_id = htmlentities($row['profile_id']);

// Load positions
$positions = loadPos($pdo, $profile_id);

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
        <p>Headline:<br> <?= htmlentities($row['headline']) ?></p>
        <p>Summary:<br> <?= htmlentities($row['summary']) ?></p>

        <?php
        if ( $positions !== array() ) {
            echo ("<p>Positions:"."\n");
            echo ("<ul>");
            foreach ($positions as $position) {
                echo ("<li>".$position['year'].": ".$position['description']."</li>");
            }
            echo ("</ul>");
        }
        
        ?>
        <a href="index.php">Done</a>
    </body>
</html>