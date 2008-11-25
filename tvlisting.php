<?php
    require("config.php");

    function log_message_to_file($message){
        global $logfile;
        $sink = fopen($logfile, 'a');
        fwrite($sink, $message."\n");
        fclose($sink);
    }

    log_message_to_file("Attempted use.")
?>
<html>
<head>
    <title>Just testing</title>
</head>
<body>
    <h1><?php
    echo $databaseuser;
    ?></h1>
    <h2><?php
        echo $databasename;
    ?></h2>
    <h3><?php
        echo $logfile;
    ?></h3>
</body>
</html>
