<?php

session_start();

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "mydb";
// Create connection
$conn = new mysqli ( $host, $dbusername, $dbpassword, $dbname );
if ( mysqli_connect_error() ) {
    die( 'Connect Error ('. mysqli_connect_errno() .') '
    . mysqli_connect_error() );
    }


    if ( !isset( $_POST['email'], $_POST['password'] ) ) {
        // Could not get the data that should have been sent.
        exit( 'Please fill both the email and password fields!' );
    }

    if ( $stmt = $conn->prepare( 'SELECT EMPLOYEE_ID, EMPLOYEE_PASSWORD FROM EMPLOYEE WHERE EMPLOYEE_EMAIL = ?' ) ) {
        // Bind parameters ( s = string, i = int, b = blob, etc ), in our case the username is a string so we use "s"
        $stmt->bind_param( 's', $_POST['email'] );
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $stmt->store_result();

        if ( $stmt->num_rows > 0 ) {
            $stmt->bind_result( $id, $password );
            $stmt->fetch();
            // Account exists, now we verify the password.
            // Note: remember to use password_hash in your registration file to store the hashed passwords.
            if ( $_POST['password'] === $password ) {
                // Verification success! User has loggedin!
                // Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['email'];
                $_SESSION['id'] = $id;
                header('Location: welcome.php');
            } else {
                // Incorrect password
                echo 'Incorrect username and/or password!';
            }
        } else {
            // Incorrect username
            echo 'Incorrect username and/or password!';
        }

        $stmt->close();
    }


?>