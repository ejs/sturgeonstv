<?php
    require_once("config.php");
    require_once("models.php");

    function log_message_to_file($message){
        global $logfile;
        $sink = fopen($logfile, 'a');
        fwrite($sink, $message."\n");
        fclose($sink);
    }

    log_message_to_file("Log in.");
    session_start();

    $user_name = $_POST['username'];
    $password = $_POST['password'];

    function validate($username, $password){
        $query = 'SELECT username FROM user WHERE username ="'.$username.'" AND password = PASSWORD("'.$password.'");';
        $result = mysql_query($query) or die("Sorry dB error");
        return ($result->numRows() > 0);
        # True on valid, false on invalid
    }

    if (validate($user_name, $password)){
        header('Location: http://localhost/tvlisting.php') ;
    }
    else{
        include("loginscreen.php");
    }
?>
