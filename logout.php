<?php
    require_once("config.php");
    require_once("models.php");

    header('Location: tvlisting.php') ;
    session_start();
    setcookie("validuser", $user_name, time()-60*60*24*30);
    session_destroy();
?>
