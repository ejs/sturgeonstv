<?php
    require_once("config.php");
    require_once("models.php");

    session_start();

    $user_name = $_POST['username'];
    $password = $_POST['password'];

    function validate($username, $password){
        $result = run_sql('SELECT username FROM user WHERE username ="'.escape($username).'" AND password = SHA1("'.escape($password).'")');
        return (mysql_num_rows($result) > 0);
    }

    if (validate($user_name, $password)){
        setcookie("validuser", $user_name, time()+60*60*24*30);
        header('Location: tvlisting.php') ;
    }
    else{
        include("loginscreen.php");
    }
?>
