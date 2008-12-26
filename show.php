<?php
    require_once("config.php");
    require_once("models.php");

    session_start();
    $show_name = $_GET['name'];
    $client = load_user();

    function layout_shows($start, $end, $message)
    {
        global $client;
        global $show_name;
        echo "        <tr class='Infob'>\n";
        echo "            <td colspan='3' style='text-align: center'>".$message."</td>\n";
        echo "        </tr>\n";
        foreach($client->getShowInstance($start, $end, $show_name) as $showinfo){
            echo "            <tr class='Show'>\n";
            echo "                <td>".strftime("%d/%m/%Y %H:%M", $showinfo["Start Time"])." - ".strftime("%H:%M", $showinfo["End Time"])."</td>\n";
            echo "                <td>".$showinfo["Channel Name"]."</td>\n";
            echo "                <td>".$showinfo["Description"]."</a></td>\n";
            echo "</td>\n";
            echo "            </tr>\n";

        }
    }
?>
<?php include("head.php") ?>
<body>
<?php echo $show_name; ?>
            <table id="ShowInformation" align="center" cellpadding="10"><?php
                $tmp = getdate();
                $a = "'".$tmp['year'].'-'.$tmp['mon'].'-'.$tmp['mday']." 00:00:00'";
                $tmp = getdate(time()+(24*60*60));
                $b = "'".$tmp['year'].'-'.$tmp['mon'].'-'.$tmp['mday']." 00:00:00'";
                layout_shows("1=1",            "starttime < ".$a, "Recent");
                layout_shows($a."< starttime", "starttime < ".$b, "Today");
                layout_shows($b."< starttime", "1=1", "Soon");
            ?>
            </table>
</body>
</html>
