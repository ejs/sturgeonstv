<?php
    require_once("config.php");
    require_once("models.php");

    session_start();
    $client = load_user();

    function layout_shows($start, $end, $message, $minrating=1, $null=1)
    {
        global $client;
        $shows = $client->getShows($start, $end, $minrating, $null);
        if ($shows or $null){
            if($null){
                echo "        <tr class='Infoa' onclick='toggle(this);'>\n";
            }
            else{
                echo "        <tr class='Infob'>\n";
            }
            echo "            <td colspan='4' style='text-align: center'>".$message."</td>\n";
            echo "        </tr>\n";
            foreach($client->getShows($start, $end, $minrating, $null) as $showinfo){
                if($showinfo["Rating"] or $null==2)
                    echo "            <tr class='Show'>\n";
                else
                    echo "            <tr class='Show' style='display:none'>\n";
                echo "                <td>".strftime("%H:%M", $showinfo["Start Time"])." - ".strftime("%H:%M", $showinfo["End Time"])."</td>\n";
                echo "                <td>".$showinfo["Channel Name"]."</td>\n";
                echo "                <td><a href=\"show.php?name=".urlencode($showinfo["Show Name"])."\" target='_blank'>".$showinfo["Show Name"]."</a></td>\n";
                echo "                <td class='ratings:".$showinfo["Rating"]."'>";
                echo '<a href="set.php?show='.urlencode($showinfo["Show Name"]).'&rating=0"><img src="x.png" /></a>';
                for($a=0 ; $a < $showinfo["Rating"] ; $a = $a + 1){
                    echo '<a href="set.php?show='.urlencode($showinfo["Show Name"]).'&rating='.($a+1).'"><img src="black.png" /></a>';
                }
                for(;$a < 5; $a = $a + 1){
                    echo '<a href="set.php?show='.urlencode($showinfo["Show Name"]).'&rating='.($a+1).'"><img src="white.png" /></a>';
                }
                echo "</td>\n";
                echo "            </tr>\n";
            }
        }
    }
?>
<?php include("head.php") ?>
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
                    echo "\n                <a href='switch.php?to=off&channel=".urlencode($channelData["ChannelName"])."' onclick='return channelSwitch(\"".$channelData["ChannelName"]."\");'>".$channelData["ChannelName"]."</a>\n";
                }
                else{
                    echo "            <li class='inactive'>";
                    echo "\n                <a href='switch.php?to=on&channel=".urlencode($channelData["ChannelName"])."'>".$channelData["ChannelName"]."</a>\n";
                }
                echo "            </li>\n";
            }
        ?>
        </ul>
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
            <table id="ShowInformation" align="center"><?php
                $a = "'".date("Y-m-d H:i:s", time())."' < endtime ";
                $b = "starttime < '".date("Y-m-d H:i:s", time())."' ";
                layout_shows($a, $b, "Shows currently on", 2, 2);
                $a = "'".date("Y-m-d H:i:s", time())."' < starttime ";
                $b = "starttime < '".date("Y-m-d H:i:s", time()+(2*60*60))."' ";
                layout_shows($a, $b, "Shows on soon", 3, 2);
                $a = "'".date("Y-m-d H:i:s", time()+(2*60*60))."' < starttime ";
                $tmp = getdate();
                $b = "starttime < '".$tmp['year'].'-'.$tmp['mon'].'-'.$tmp['mday']." 23:59:59' ";
                layout_shows($a, $b, "Shows on Later Today", 4, 2);
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
