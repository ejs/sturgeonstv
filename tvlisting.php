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

    function layout_shows($start, $end, $message, $minrating=1, $null=1)
    {
        global $client;
        echo "        <tr class='Info'>\n";
        echo "            <td colspan='4' style='text-align: center'>".$message."</td>\n";
        echo "        </tr>\n";
        foreach($client->getShows($start, $end, $minrating, $null) as $showinfo){
            echo "            <tr class='Show'>\n";
            echo "                <td>".strftime("%H:%M", $showinfo["Start Time"])." - ".strftime("%H:%M", $showinfo["End Time"])."</td>\n";
            echo "                <td>".$showinfo["Channel Name"]."</td>\n";
            echo "                <td><a href=\"show.php?name=".$showinfo["Show Name"]."\">".$showinfo["Show Name"]."</a></td>\n";
            echo "                <td class='ratings'>";
            echo '<a href="set.php?show='.$showinfo["Show Name"].'&rating=0"><img src="x.png" /></a>';
            for($a=0 ; $a < $showinfo["Rating"] ; $a = $a + 1){
                echo '<a href="set.php?show='.$showinfo["Show Name"].'&rating='.($a+1).'"><img src="black.png" /></a>';
            }
            for(;$a < 5; $a = $a + 1){
                echo '<a href="set.php?show='.$showinfo["Show Name"].'&rating='.($a+1).'"><img src="white.png" /></a>';
            }
            echo "</td>\n";
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
                <li><a href='mailto:tv.whuffie@spamgourmet.com'>Contact</a></li>
                <li><a href=''>About</a></li>
                <li><a href=''>FAQ</a></li>
            </ul>
        </div>
        <div id="body">
            <table id="ShowInformation" align="center" cellpadding="10"><?php
                $a = "'".date("Y-m-d H:i:s", time())."' < endtime ";
                $b = "starttime < '".date("Y-m-d H:i:s", time())."' ";
                layout_shows($a, $b, "Shows currently on", 2);
                $a = "'".date("Y-m-d H:i:s", time())."' < starttime ";
                $b = "starttime < '".date("Y-m-d H:i:s", time()+(2*60*60))."' ";
                layout_shows($a, $b, "Shows on soon", 3);
                $a = "'".date("Y-m-d H:i:s", time()+(2*60*60))."' < starttime ";
                $tmp = getdate();
                $b = "starttime < '".$tmp['year'].'-'.$tmp['mon'].'-'.$tmp['mday']." 23:59:59' ";
                layout_shows($a, $b, "Shows on Later Today", 4);
                for($c = 1; $c < 7; $c += 1){
                    $tmp = getdate(time()+(($c+1)*24*60*60));
                    $b = "starttime < '".$tmp['year'].'-'.$tmp['mon'].'-'.$tmp['mday']." 00:00:00' ";
                    $tmp = getdate(time()+($c*24*60*60));
                    $a = "'".$tmp['year'].'-'.$tmp['mon'].'-'.$tmp['mday']." 00:00:00' < starttime ";
                    if ($c == 1){
                        layout_shows($a, $b, $tmp['mday'].' '.$tmp['month'].' '.$tmp['year'], 4, 1);
                    }
                    else{
                        layout_shows($a, $b, $tmp['mday'].' '.$tmp['month'].' '.$tmp['year'], 5, 0);
                    }
                }
            ?>
            </table>
        </div>
    </div>
</body>
</html>
