<?php
require_once "pdo.php";
session_start();
require_once "util.php";
require_once "head.php";


if ( ! isset($_SESSION['name']) ) {
    die("ACCESS DENIED");
  }

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
        && isset($_POST['headline']) && isset($_POST['summary']) ) {
            
            // Validating profile entries
            $msg = validateProfile();
            if ( is_string($msg) ) {
                $_SESSION['error'] = $msg;
                header("Location: edit.php?profile_id=".$_POST['profile_id']);
                return;
            }

            // Validating position entries
            $msg = validatePos();
            if ( is_string($msg) ) {
                $_SESSION['error'] = $msg;
                header("Location: edit.php?profile_id=".$_POST['profile_id']);
                return;
            }

            // Validating education entries
            $msg = validateEdu();
            if ( is_string($msg) ) {
                $_SESSION['error'] = $msg;
                header("Location: edit.php?profile_id=".$_POST['profile_id']);
                return;
            }

            // Updating profile entries
            $stmt = $pdo->prepare("UPDATE profile SET first_name = :fn, last_name = :ln, email = :em,
                                headline = :hd, summary = :sm WHERE profile_id = :profile_id");
            $stmt->execute(array(
                ":profile_id" => $_POST['profile_id'],
                ":fn" => $_POST['first_name'],
                ":ln" => $_POST['last_name'],
                ":em" => $_POST['email'],
                ":hd" => $_POST['headline'],
                ":sm" => $_POST['summary']));                    

            // Clearing out old position entries
            $stmt = $pdo->prepare("DELETE FROM position WHERE profile_id = :profile_id");
            $stmt->execute(array(
                ":profile_id" => $_POST['profile_id'])); 
                
            // Inserting updated position entries
            insertPositions($pdo, $_POST['profile_id']);

            // Clearing out old education entries
            $stmt = $pdo->prepare("DELETE FROM education WHERE profile_id = :profile_id");
            $stmt->execute(array(
                ":profile_id" => $_POST['profile_id']));

            // Inserting updated education entries
            insertEducations($pdo,$_POST['profile_id']);

             $_SESSION['success'] = "Profile updated";
             header("Location: index.php");
             return;
        }  


// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz AND user_id = :user_id");
$stmt->execute(array(":xyz" => $_GET['profile_id'],
                     ":user_id" => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hd = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

// Load up the position and education rows
$positions = loadPos($pdo, $profile_id);
$schools = loadEdu($pdo, $profile_id)

?>


<!DOCTYPE html>
<html>
<head>
    <title>Syed Muneef ur Rehman</title>
</head>
<body>    
    <h1>Editing Profile for <?php echo($_SESSION['name']) ?> </h1>
    <?php flashMessages(); ?>
    <form method="post" action="edit.php">
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>">    
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
    
    <?php

    $countEdu = 0;
    echo ('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
    echo ('<div id="education_fields">'."\n");
    if ( count($schools) > 0 ) {
        foreach ( $schools as $school ) {
            $countEdu++;
            echo ('<div id="education'.$countEdu.'">');
            echo ('<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.$school['year'].'" />
              <input type="button" value="-" onclick="$(\'#education'.$countEdu.'\').remove();return false;"></p>
              <p>School: <input type="text" size="80" name="edu_school'.$countEdu.'" class="school"
              value="'.htmlentities($school['name']).'" />');
            echo ("\n</div>\n");  
        }
    }
    echo ("</div></p>\n");

    
    $pos = 0;
    echo ('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
    echo ('<div id="position_fields">'."\n");
    foreach ($positions as $position) {
        $pos++;
        echo ('<div id="position'.$pos.'">'."\n");
        echo ('<p>Year: <input type="text" name="year'.$pos.'"');
        echo (' value="'.$position['year'].'" />'."\n");
        echo ('<input type="button" value="-" ');
        echo ('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
        echo ("</p>\n");
        echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
        echo (htmlentities($position['description'])."\n");
        echo ("\n</textarea>\n</div>\n");
    }
    echo ("</div></p>\n");
    ?>


    <p><input type="submit" value="Save"/>
    <a href="index.php">Cancel</a></p>
    </form>
</body>

<script>
        countEdu = <?= $countEdu ?>

        $(document).ready(function() {
            $('#addEdu').click(function(event) {
                event.preventDefault();
                if ( countEdu >= 9 ) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
            countEdu++;
            window.console && console.log("Adding education "+countEdu);
            $('#education_fields').append(
                '<div id="education'+countEdu+'"> \
                <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
                <input type="button" value="-" \
                    onclick="$(\'#education'+countEdu+'\').remove();return false;"></p> \
                <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" /></p>\
                </div>'    
            )    
            })
        })


        countPos = <?= $pos ?>;

        $(document).ready(function() {
            window.console && console.log("Document ready called");
            $('#addPos').click(function(event) {
                event.preventDefault();
                if (countPos >= 9) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                window.console && console.log("Adding position "+countPos);
                $('#position_fields').append(
                    '<div id="position'+countPos+'"> \
                    <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                    <input type="button" value="-" \
                        onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                    <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                    </div>'
                )
            })
           
            $('.school').autocomplete({
                source: "school.php"
            })
        })
</script>
</html>



