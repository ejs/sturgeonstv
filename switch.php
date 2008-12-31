<?php
    require_once("config.php");
    require_once("models.php");

    session_start();
    $client = load_user();
    $client->update_channel();
    if ($_GET && $_GET["ajax"]);
    else header('Location: tvlisting.php');
?>
