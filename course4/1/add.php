<?php

session_start();

if ( ! isset($_SESSION['name']) ) {
  die("ACCESS DENIED");
}

if ( isset($_POST['cancel']) ) {
    header("Location: index.php?name=".urlencode($_SESSION['name']));
    return;
    }


 require_once "pdo.php";



// Check to see if we have some POST data, if we do process it
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
     && isset($_POST['headline']) && isset($_POST['summary']) ) {
        if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1
            || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
                $_SESSION['error'] = "All fields are required";
                header("Location: add.php");
                return;
            }else if ( strpos($_POST['email'],"@") == FALSE) {
                $_SESSION['error'] = "Email address must contain @";
                header("Location: add.php");
                return;
            }else {
                $stmt = $pdo->prepare("INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
                                        VALUES (:user_id, :fn, :ln, :em, :hd, :sm)");
                $stmt->execute(array(
                    ':user_id' => $_SESSION['user_id'],
                    ':fn' => $_POST['first_name'],
                    ':ln' => $_POST['last_name'],
                    ':em' => $_POST['email'],
                    ':hd' => $_POST['headline'],
                    ':sm' => $_POST['summary']));
                $_SESSION['success'] = "Profile added";
                header("Location: index.php");
                return;    
            }   
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Syed Muneef ur Rehman</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">   
<h1>Adding Profile for <?php echo($_SESSION['name']) ?> </h1>

<?php
        if ( isset($_SESSION['error']) ) {
          echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
          unset($_SESSION['error']);
        }
      ?>


<form method="post">
<p>
    First Name:
    <input type="text" name="first_name" size="60">
</p>
<p>
    Last Name:
    <input type="text" name="last_name" size="60">
</p>   
<p>
    Email:
    <input type="text" name="email" size="30">
</p>
<p>
    Headline:
    <input type="text" name="headline" size="80">
</p>    
<p>
    Summary:</br>
    <textarea name="summary" rows="8" cols="80"></textarea>
</p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>

</html>