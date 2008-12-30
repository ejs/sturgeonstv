<?php
function layout_shows($start, $end, $message, $minrating=1, $null=1)
{
    global $client;
    $shows = $client->getShows($start, $end, $minrating, $null);
    if (!($shows or $null))
        return;
    if($null){ ?>
            <tr class='Infoa' onclick='toggle(this);'>
<?php  } else{ ?>
            <tr class='Infob'>
<?php  } ?>
                <td colspan='4' style='text-align: center'><?php echo $message ?></td>
            </tr>
<?php foreach($client->getShows($start, $end, $minrating, $null) as $showinfo){?>
            <?php if($showinfo["Rating"] or $null==2) {?><tr class='Show'><?php } else { ?><tr class='Show' style='display:none'><?php }?>

                <td><?php echo strftime("%H:%M", $showinfo["Start Time"]);?> - <?php echo strftime("%H:%M", $showinfo["End Time"]);?></td>
                <td><?php echo $showinfo['Channel Name']; ?></td>
                <td><a href="show.php?name=<?php echo urlencode($showinfo["Show Name"]); ?>" target='_blank'><?php echo $showinfo["Show Name"]; ?></a></td>
                <td class="ratings:<?php echo $showinfo['Rating']; ?>"><a href="set.php?show=<?php echo urlencode($showinfo["Show Name"]); ?>&rating=0" onclick="return setRating(0, '<?php echo $showinfo["Show Name"]; ?>')"><img src="x.png" /></a><?php
          for($a=0 ; $a < $showinfo["Rating"] ; $a = $a + 1){
                    ?><a href="set.php?show=<?php echo urlencode($showinfo["Show Name"]); ?>&rating=<?php echo $a+1; ?>" onclick='return setRating(<?php echo $a+1; ?>, "<?php echo $showinfo["Show Name"]; ?>")'><img src="black.png" /></a><?php
          }
          for(;$a < 5; $a = $a + 1) {
                    ?><a href="set.php?show=<?php echo urlencode($showinfo["Show Name"]); ?>&rating=<?php echo $a+1; ?>" onclick='return setRating(<?php echo $a+1; ?>, "<?php echo $showinfo["Show Name"]; ?>")'><img src="white.png" /></a><?php
          } ?></td>
            </tr>
<?php }
} ?>
