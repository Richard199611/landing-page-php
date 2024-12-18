<?php
session_start();
mysqli_report(MYSQLI_REPORT_STRICT);  // Enable strict error reporting for MySQLi
// Functions

// Function to create the database and tables if they do not exist
function setupDatabase() {
    $link = mysqli_connect('localhost', 'root', '');

    // Check if the database exists
    $dbCheck = mysqli_query($link, "SHOW DATABASES LIKE 'SRI24'");
    if (mysqli_num_rows($dbCheck) == 0) {
        // Create database if it doesn't exist
        $createDbSql = "CREATE DATABASE SRI24";
        mysqli_query($link, $createDbSql);
        echo "Database 'SRI24' created.<br>";
    }
    mysqli_select_db($link, 'SRI24');  // Select the database

    // Check if the 'user' table exists
    $tableCheck = mysqli_query($link, "SHOW TABLES LIKE 'user'");
    if (mysqli_num_rows($tableCheck) == 0) {
        // Create 'user' table
        $createGameTableSql = "CREATE TABLE user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            UserName VARCHAR(255) NOT NULL,
            EmailAdress VARCHAR(255) NOT NULL,
            BirthDate DATE NOT NULL,
            Pass VARCHAR(255) NOT NULL
        )";
        mysqli_query($link, $createGameTableSql);
    }

    // Check if there are any users, if not, create a new one
    $check_empty_query = "SELECT COUNT(*) AS count FROM `user`";
    $answer = mysqli_query($link, $check_empty_query);
    $row = mysqli_fetch_assoc($answer);
    if ($row['count'] == 0) {
        $insert_users_query = "
        INSERT INTO `user` (UserName, EmailAdress, BirthDate, Pass) VALUES
        ('example', 'example@example', '1996-11-06' , 'admin')
        ";
        mysqli_query($link, $insert_users_query);

    }   

    mysqli_close($link);  // Close the database connection
}

function checkLogIn()
{
    
    if (isset($_POST['submit'])) {
        $link = mysqli_connect('localhost', 'root', '', 'SRI24');
        // Create a query to check if the entered email and password match a record in the database
        $sql = 'SELECT `EmailAdress`, `Pass` FROM `user` WHERE `EmailAdress` = "' . $_POST['email'] . '" AND `Pass` = "' . $_POST['pass'] . '"';
        $answer = mysqli_query($link, $sql);

        // If one matching row is found, log in the user
        if (mysqli_num_rows($answer) == 1) {
            $_SESSION['enter'] = true; // Set session status to logged in
            $_SESSION['email'] = $_POST['email'];
            getAllData();
        } else {
            $_SESSION['enter'] = false;
            echo "Invalid email or password.<br>"; // Display error message for invalid login
        }
            
        mysqli_close($link);  // Close the database connection
    }
}

//Get everything for update values
function getAllData() 
{
    $link = mysqli_connect('localhost', 'root', '', 'SRI24');
    $sql = 'SELECT * FROM `user` WHERE `EmailAdress` = "' . $_POST['email'] . '"';
    $row = mysqli_query($link, $sql);
    $result = mysqli_fetch_assoc($row);
    $_SESSION['user'] = $result['UserName'];
    $_SESSION['pass'] = $result['Pass'];
    $_SESSION['birthday'] = $result['BirthDate'];
}

function register()
{
    if (isset($_POST['register'])) {
        $link = mysqli_connect('localhost', 'root', '', 'SRI24');
        // Create a query to check if the entered email is in the database
        $sql = 'SELECT `EmailAdress` FROM `user` WHERE `EmailAdress` = "' . $_POST['email'] . '"';
        $answer = mysqli_query($link, $sql);

        // If one matching row is found, log in the user
        if (mysqli_num_rows($answer) == 1) {
            echo "This e-mail is already used";
        } 
        else 
        {
            $sql = 'INSERT INTO `user` (UserName, EmailAdress, BirthDate, Pass) VALUES 
            ("'.$_POST['user'] . '","'.$_POST['email'] . '","'.$_POST['birthday'] . '","'.$_POST['pass'] . '")';
        }
        

        mysqli_query($link, $sql);

        mysqli_close($link);  // Close the database connection
    }

}

function updateData()
{
    if (isset($_POST['update'])) {
        $link = mysqli_connect('localhost', 'root', '', 'SRI24');

        $sql = 'UPDATE `user` SET UserName = "'. $_POST['user'] .'", BirthDate = "'. $_POST['birthday'] .'", Pass = "'. $_POST['pass'] .'" WHERE EmailAdress = "'. $_SESSION['email'] .'"';
        
        echo $_POST['pass'];

        mysqli_query($link, $sql);

        
        mysqli_close($link);  // Close the database connection
    }
}

//Functions for visuals
function showLogin()
{
    // Display the login form if not logged in
    echo '<FORM NAME="form1" action="MainPage.php" method="POST">
            <INPUT TYPE="email" name="email" placeholder="E-Mail"><BR>
            <INPUT TYPE="password" name="pass" placeholder="Password"><BR>
            <INPUT TYPE="submit" name="submit" value="ENTER">
            <INPUT TYPE="submit" name="registerpage" value="REGISTER">
          </FORM>';
}

function showRegister()
{
    // Display the register form
    echo '<FORM NAME="form2" action="MainPage.php" method="POST">
            <INPUT TYPE="text" name="user" placeholder="Username"><BR>
            <INPUT TYPE="email" name="email" placeholder="E-mail"><BR>
            <INPUT TYPE="password" name="pass" placeholder="Password"><BR>
            <input type="date" id="birthday" name="birthday"><BR>
            <INPUT TYPE="submit" name="register" value="ENTER">
            <INPUT TYPE="submit" name="login" value="LOGIN">
          </FORM>';
}

function showUpdate()
{
    // Display the register form
    echo '<FORM NAME="form3" action="MainPage.php" method="POST">
            <INPUT TYPE="text" name="user" placeholder="Username" value='. $_SESSION['user'] .'><BR>
            <INPUT TYPE="password" name="pass" placeholder="Password" value= '.$_SESSION['pass'] .'><BR>
            <input type="date" id="birthday" name="birthday" value = '. $_SESSION['birthday'] .'><BR>
            <INPUT TYPE="submit" name="update" value="UPDATE">
            <INPUT TYPE="submit" name="logout" value="LOGOUT">
          </FORM>';
}

//Running code starts here
setupDatabase();

// Handle session initialization and logout logic
if (!isset($_SESSION['enter'])) {
    $_SESSION['enter'] = false; // Set default session status to logged out
}


if(isset($_POST['logout'])){
    $_SESSION['enter'] = false;
    $_SESSION['email'] = "";
}

updateData();

checkLogIn();

register();

if(!isset($_SESSION['enter']) || $_SESSION['enter'] == false)
{
    if(!isset($_POST['registerpage']) || isset($_POST['login']))
    {
        showLogin();
    }
    else showRegister();
}
else showUpdate();


?>