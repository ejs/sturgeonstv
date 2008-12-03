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
    $client = load_user();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Just testing</title>
    <link rel="stylesheet" type="text/css" href="tvphp.css" />
</head>
<body>
    <div id="sidebar">
        <p><?php
            if ($client->name){
                echo '<a href="">'.$client->name.'</a>';
            }
            else{
                echo '<a href="">Register</a>';
            }
        ?></p>
        <p><?php
            if ($client->name){
                echo '<a href="logout.php">Logout</a>';
            }
            else{
                echo '<a href="login.php">Login</a>';
            }
        ?></p>
        <ul>
<?php
            foreach($client->channels as $channelData){
                if ($channelData['default?'] == 1){
                    echo "            <li class='active'>";
                    echo "\n                <a href='switch.php?to=off&channel=".$channelData["ChannelName"]."'>".$channelData["ChannelName"]."</a>\n";
                }
                else{
                    echo "            <li class='inactive'>";
                    echo "\n                <a href='switch.php?to=on&channel=".$channelData["ChannelName"]."'>".$channelData["ChannelName"]."</a>\n";
                }
                echo "            </li>\n";
            }
        ?>
        </ul>
        <p><?php echo $client->visit_count; ?></p>
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
