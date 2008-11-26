<?php
    require_once("config.php");
    require_once("models.php");

    function log_message_to_file($message){
        global $logfile;
        $sink = fopen($logfile, 'a');
        fwrite($sink, $message."\n");
        fclose($sink);
    }

    log_message_to_file("Attempted use.");
    session_start();
    $_SESSION['counter']++;
?>
<html>
<head>
    <title>Just testing</title>
    <link rel="stylesheet" type="text/css" href="tvphp.css" />
</head>
<body>
    <div id="sidebar">
        <p><a href="">Register</a> or <a href="">Login</a></p>
        <p><?php echo $_SESSION['counter'] ?></p>
        <ul>
    <?php
            foreach(get_all_channels() as $channelData){
                echo "            <li><a href='".$channelData['URL']."'>".$channelData["ChannelName"]."</a></li>\n";
            }
        ?>
        </ul>
    </div>
</body>
</html>
