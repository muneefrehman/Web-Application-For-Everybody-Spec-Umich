<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION['name']) ) {
    die("ACCESS DENIED");
  }

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
        && isset($_POST['headline']) && isset($_POST['summary']) ) {
            if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 
                    || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
                        $_SESSION['error'] = "All fields are required";
                        header("Location: edit.php?profile_id=".urlencode($_POST['profile_id']));
                        return;
                    }else if ( strpos($_POST['email'],"@") == FALSE) {
                        $_SESSION['error'] = "Email address must contain @";
                        header("Location: edit.php?profile_id=".urlencode($_POST['profile_id']));
                        return;
                    }else {
                        $stmt = $pdo->prepare("UPDATE profile SET first_name = :fn, last_name = :ln, 
                                    email = :em, headline = :hd, summary = :sm WHERE profile_id = :profile_id");
                        $stmt->execute(array(
                            ":profile_id" => $_POST['profile_id'],
                            ":fn" => $_POST['first_name'],
                            ":ln" => $_POST['last_name'],
                            ":em" => $_POST['email'],
                            ":hd" => $_POST['headline'],
                            ":sm" => $_POST['summary'])
                        );
                        $_SESSION['success'] = "Record Updated";
                        header("Location: index.php");
                        return;            
                    }
        }  



// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hd = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
?>


<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Syed Muneef ur Rehman</title>
</head>
<body>    
    <h1>Editing Profile for <?php echo($_SESSION['name']) ?> </h1>
    <form method="post">
    <p>
        First Name:
        <input type="text" name="first_name" size="60" value="<?= $fn ?>">
    </p>
    <p>
        Last Name:
        <input type="text" name="last_name" size="60" value="<?= $ln ?>">
    </p>
    <p>
        Email:
        <input type="text" name="email" size="30" value="<?= $em ?>">
    </p>
    <p>
        Headline:
        <input type="text" name="headline" size="80" value="<?= $hd ?>">
    </p>
    <p>
        Summary:</br>
        <textarea name="summary" rows="8" cols="80"><?= $sm ?></textarea>
    </p>    
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
    <p><input type="submit" value="Save"/>
    <a href="index.php">Cancel</a></p>
    </form>
</body>
</html>