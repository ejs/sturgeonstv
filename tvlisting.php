<?php
    require_once("config.php");
    require_once("models.php");

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
    <ol>
<?php
        foreach(get_all_channels() as $channelData){
            echo "        <li><a href='".$channelData['URL']."'>".$channelData["ChannelName"]."</a></li>\n";
        }
    ?>
    </ol>
</body>
</html>
