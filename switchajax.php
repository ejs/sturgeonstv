<?php
    require_once("config.php");
    require_once("models.php");

    session_start();
    $client = load_user();
    $client->update_channel();
?>
<html></html>
