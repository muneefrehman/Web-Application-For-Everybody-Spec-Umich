<?php

// Flash messages
function flashMessages() {
    if ( isset($_SESSION['success']) ) {
        echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }   

    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
}

// Validation for profiles
function validateProfile() {
    if ( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0
            || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0 ) {
                return "All fields are required";
            }
    
    if ( strpos($_POST['email'], '@') === false) {
        return "Email address must contain @";
    }
    return true;
}

// Look through the POST data and return true or error message
function validatePos() {
    for ( $i=1; $i<=9; $i++ ) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}

// Validating education entries
function validateEdu() {
    for ( $i=1; $i<=9; $i++ ) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue; 
        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];

        if ( strlen($edu_year) == 0 || strlen($edu_school) == 0) {
            return "All fields are required";
        }

        if ( ! is_numeric($edu_year) ) {
            return "Education year must be numeric";
        }
    }
}

// Loading positions
function loadPos($pdo, $profile_id) {
    $stmt = $pdo->prepare("SELECT * FROM position 
                            WHERE profile_id = :profile_id ORDER BY rank");
    $stmt->execute(array(
        ":profile_id" => $profile_id));
    $positions = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $positions[] = $row;
    }       
    return $positions;                     
}

// Loading education
function loadEdu($pdo, $profile_id) {
    $stmt = $pdo->prepare("SELECT year, name FROM education JOIN institution
                            ON education.institution_id = institution.institution_id
                                WHERE profile_id = :profile_id ORDER BY rank");
    $stmt->execute(array(
        ":profile_id" => $profile_id));
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $educations;                                
}

// Inserting positions entries
function insertPositions($pdo, $profile_id) {
    $rank = 1;
    for ( $i=1; $i<=9; $i++ ) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare("INSERT INTO position (profile_id, rank, year, description)
                                VALUES (:profile_id, :rank, :year, :desc)");
        $stmt->execute(array(
            ":profile_id" => $profile_id,
            ":rank" => $rank,
            ":year" => $year,
            ":desc" => $desc));
            
            $rank++;
    }
}

// Inserting Education entries
function insertEducations($pdo, $profile_id) {
    $rank = 1;
    for ( $i=1; $i<=9; $i++ ) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];

        // Lookup the school if it is there
        $institution_id = false;
        $stmt = $pdo->prepare("SELECT institution_id FROM institution WHERE name = :name");
        $stmt->execute(array(
            ":name" => $edu_school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row !== false ) $institution_id = $row['institution_id'];
        
        // If there was no institution, insert it
        if ( $institution_id === false ) {
            $stmt = $pdo->prepare("INSERT INTO institution (name) VALUES (:name)");
            $stmt->execute(array(
                ":name" => $edu_school));
            $institution_id = $pdo->lastInsertId();    
        }

        $stmt = $pdo->prepare("INSERT INTO education (profile_id, rank, year, institution_id)
                                VALUES (:profile_id, :rank, :year, :iid)");
        $stmt->execute(array(
            ":profile_id" => $profile_id,
            ":rank" => $rank,
            ":year" => $edu_year,
            ":iid" => $institution_id));
            
            $rank++;
    }
}