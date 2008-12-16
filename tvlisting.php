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

    function layout_shows($start, $end, $message)
    {
        global $client;
        echo "        <tr class='Info'>\n";
        echo "            <td colspan='3' style='text-align: center'>".$message."</td>\n";
        echo "        </tr>\n";
        foreach($client->getShows($start, $end) as $showinfo){
            echo "            <tr class='Show'>\n";
            echo "                <td>".strftime("%H:%M", $showinfo["Start Time"])." - ".strftime("%H:%M", $showinfo["End Time"])."</td>\n";
            echo "                <td>".$showinfo["Channel Name"]."</td>\n";
            echo "                <td>".$showinfo["Show Name"]."</td>\n";
            echo "            </tr>\n";

        }
    }
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
                echo '<a href="register.php">Register</a>';
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
    <div id="main">
        <div id="infobar">
            <ul>
                <li><a href=''>Contact</a></li>
                <li><a href=''>About</a></li>
                <li><a href=''>FAQ</a></li>
            </ul>
        </div>
        <div id="body">
            <table id="ShowInformation" align="center" cellspacing="20"><?php
                $a = "'".date("Y-m-d H:i:s", time())."' < endtime ";
                $b = "starttime < '".date("Y-m-d H:i:s", time())."' ";
                layout_shows($a, $b, "Shows currently on");
                $a = "'".date("Y-m-d H:i:s", time())."' < starttime ";
                $b = "starttime < '".date("Y-m-d H:i:s", time()+(2*60*60))."' ";
                layout_shows($a, $b, "Shows on soon");
                $a = "'".date("Y-m-d H:i:s", time()+(2*60*60))."' < starttime ";
                $b = "starttime < '".date("Y-m-d H:i:s", time()+(24*60*60))."' ";
                layout_shows($a, $b, "Shows on Later");
            ?>
            </table>
        </div>
    </div>
</body>
</html>
