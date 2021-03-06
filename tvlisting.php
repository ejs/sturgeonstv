<?php

require_once("config.php");
require_once("models.php");
require_once("layout.php");
session_start();
$client = load_user();

function convertDate($offset){
    $tmp = getdate(time()+($offset*24*60*60));
    return " '{$tmp['year']}-${tmp['mon']}-${tmp['mday']} 00:00:00' ";
}

function convertTime($offset){
    return " '".date("Y-m-d H:i:s", time()+($offset*60*60))."' ";
}

include("head.php")
?>
<body>
    <div id="sidebar">
        <div id="userinfo">
            <p> <?php if ($client->name){ ?> <a href=""><?php echo $client->name;?></a> <?php } else { ?> <a href="register.php">Register</a> <?php } ?> </p>
            <p> <?php if ($client->name){ ?> <a href="logout.php">Logout</a> <?php } else { ?> <a href="login.php">Login</a> <?php } ?> </p>
        </div>
        <div id="channelinfo">
        <ul>
<?php foreach($client->channels as $channelData){
        if ($channelData['default?'] == 1){?>
            <li class='active' id='<?php echo str_replace(' ', '_', $channelData["ChannelName"]); ?>'>
                <a href='switch.php?to=off&amp;channel=<?php echo urlencode($channelData["ChannelName"]); ?>' onclick='return channelSwitch("<?php echo $channelData["ChannelName"]; ?>")'><?php echo$channelData["ChannelName"] ?></a>
<?php   } else{ ?>
            <li class='inactive' id='<?php echo str_replace(' ', '_', $channelData["ChannelName"]); ?>'>
                <a href='switch.php?to=on&amp;channel=<?php echo urlencode($channelData["ChannelName"]); ?>'><?php echo$channelData["ChannelName"] ?></a>
<?php   }?>
            </li>
<?php } ?>
        </ul>
        </div>
        <div id="infobar">
            <ul>
                <li><a href='mailto:tv.whuffie@spamgourmet.com'>Contact</a></li>
                <li><a href=''>About</a></li>
                <li><a href=''>FAQ</a></li>
            </ul>
        </div>
    </div>
    <div id="main">
        <table id="ShowInformation">
            <tbody class="tbody">
<?php layout_shows(convertTime(0)." < endtime ", "starttime < ".convertTime(0), "On Now", 'now', 2, $client->getTimeFlag("now")); ?>
<?php layout_shows(convertTime(0)." < starttime ", "starttime < ".convertTime(2)." AND starttime < ".convertDate(1), "Soon", 'soon', 3, $client->getTimeFlag("soon")); ?>
<?php layout_shows(convertTime(2)." < starttime ", "starttime < ".convertDate(1), "Later Today", 'later', 4, $client->getTimeFlag("later")); ?>
<?php layout_shows(convertDate(1)." < starttime ", "starttime < ".convertDate(2), "Tomorrow", 'tomorrow', 4, $client->getTimeFlag("tomorrow")); ?>
<?php for($c = 2; $c < 7; $c += 1){
    $tmp = getdate(time()+($c*24*60*60));
    layout_shows(convertDate($c)." < starttime ", "starttime < ".convertDate($c+1), "${tmp['mday']}  ${tmp['month']} ${tmp['year']}", "plus${c}days", 5, 0);
} ?>
            </tbody>
        </table>
    </div>
</body>
</html>
