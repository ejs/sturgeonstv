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

    if (validate($user_name, $password)){
        header('Location: http://localhost/tvlisting.php') ;
    }
    else{
        include("loginscreen.php");
    }
?>
