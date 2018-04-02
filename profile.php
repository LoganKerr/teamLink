<?php
    session_start();
    ob_start();
    header('Content-Type: text/html; charset=utf-8');
    
    require_once("config/config.php");
    
    // if user is not signed in
    if (!isset($_SESSION['user_id']))
    {
        header("Location: index.php");
    }
    
    $user_id = $_SESSION['user_id'];
    
    // profile form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $error = array();
        // validate data -----------------------
        // escape data
        $department = $_POST['department'];
        $major = $_POST['major'];
        $interests = $_POST['interests'];
        
        
        if (!empty($department))
        {
            // TODO: validate department (same as signup)
        }
        
        if (!empty($major))
        {
            // TODO: validate majors (same as signup)
        }
        
        if (!empty($interests))
        {
            // TODO: validate interests (same as signup)
        }
        
        if (count($error) == 0)
        {
            //$query = "UPDATE `students` SET `major`='".$major."', `interests`='".$interests."' WHERE `id`=(SELECT `student_id` FROM `users` WHERE `id`=$user_id);";
            $stmt = $conn->prepare("UPDATE `students` SET `major`=?, `interests`=? WHERE `id`=(SELECT `student_id` FROM `users` WHERE `id`=?);");
            $stmt->bind_param("ssi", $major, $interests, $user_id);
            
            //$query2 = "UPDATE `faculty` SET `department`='".$department."' WHERE `id`=(SELECT `faculty_id` FROM `users` WHERE `id`=$user_id);";
            $stmt2 = $conn->prepare("UPDATE `faculty` SET `department`=? WHERE `id`=(SELECT `faculty_id` FROM `users` WHERE `id`=?);");
            $stmt2->bind_param("si", $department, $user_id);
            
            //if (mysqli_query($conn, $stmt))
            if ($stmt->execute())
            {
                if ($stmt2->execute())
                {
                    echo "<p><strong>Changes have been saved.</strong></p>";
                }
                else
                {
                    echo "<strong>Changes could not be saved: ".$stmt2->error."</strong>";
                }
            } else {
                echo "<strong>Changes could not be saved: ".$stmt->error."</strong>";
            }
        }
    }
    
    $stmt = $conn->prepare("SELECT `student_id`, `faculty_id`, `major`, `interests`, `department` FROM `users` LEFT JOIN `faculty` ON `users`.`faculty_id`=`faculty`.`id` LEFT JOIN `students` ON `users`.`student_id`=`students`.`id` WHERE `users`.`id`=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows == 0)
    {
        die("User not found.");
    }
    $row = $res->fetch_assoc();
?>
<?php include "resources/templates/header.php"; ?>
<?php include "resources/templates/navbar.php"; ?>
<body>
    <div id="signup-panel" class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Change your information</div>   
            <div class="panel-body">
                <form method="post" action="/profile.php">
                    <?php if (isset($row['faculty_id'])) { ?>
                    <p><label class="form-label">Department:</label><input class="textbox" name="department" type="text" value='<?php if (isset($department)) { echo htmlentities($department, ENT_QUOTES); } else { echo htmlentities($row['department'], ENT_QUOTES); } ?>'/>
                    <?php echo(isset($error['department']))?$error['department']:""; ?></p>
                    <?php } ?>
                    <?php if (isset($row['student_id'])) { ?>
                    <p><label class="form-label">Major:</label><input class="textbox" name="major" type="text" value='<?php if (isset($major)) { echo htmlentities($major, ENT_QUOTES); } else { echo htmlentities($row['major'], ENT_QUOTES); } ?>'/>
                    <?php echo(isset($error['major']))?$error['major']:""; ?></p>
                    <p><label class="form-label">Interests:</label><textarea name="interests" rows="4" cols="50" ><?php if (isset($interests)) { echo htmlentities($interests, ENT_QUOTES); } else { echo htmlentities($row['interests'], ENT_QUOTES); } ?></textarea>
                    <?php echo(isset($error['interests']))?$error['interests']:""; ?></p>
                    <?php } ?>
                    <div class="submit-button"><input class="btn btn-primary btn-block" type="submit" value="Submit" /></div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
