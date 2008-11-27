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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
                if ($channelData['default?'] == 1){
                    echo "            <li class='active'>";
                }
                else{
                    echo "            <li class='inactive'>";
                }
                echo "                <a href='".$channelData['URL']."'>".$channelData["ChannelName"]."</a>\n";
                echo "            </li>\n";
            }
        ?>
        </ul>
    </div>
    <div id="infobar">
        <ul>
            <li><a href=''>Contact</a></li>
            <li><a href=''>About</a></li>
            <li><a href=''>FAQ</a></li>
        </ul>
    </div>
</body>
</html>
