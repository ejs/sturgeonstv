<?php
function layout_shows($start, $end, $message, $name, $minrating=1, $null=1)
{
    global $client;
    $shows = $client->getShows($start, $end, $minrating, $null);
    if (!($shows or $null))
        return;
    if($null){ ?>
            <tr class='Infoa' id=<?php echo $name; ?> onclick='toggle(this);' unrated="<?php echo $null==2?"on":"off" ?>" minrating='<?php echo $minrating; ?>'>
<?php  } else{ ?>
            <tr class='Infob' id=<?php echo $name; ?> unrated="on" minrating='<?php echo $minrating; ?>'>
<?php  } ?>
                <td colspan='4' style='text-align: center'><?php echo $message ?></td>
            </tr>
<?php foreach($client->getShows($start, $end, $minrating, $null) as $showinfo){?>
            <?php if($showinfo["Rating"] or $null==2) {?><tr class='Show' onmouseover='hover(this);' onmouseout='leave(this)' ><?php } else { ?><tr class='Show' style='display:none' onmouseover='hover(this);' onmouseout='leave(this)' ><?php }?>

                <td class="time" starttime="<?php echo strftime("%d:%m:%Y:%H:%M", $showinfo["Start Time"]);?>" endtime="<?php echo strftime("%d:%m:%Y:%H:%M", $showinfo["End Time"]);?>"><?php echo strftime("%H:%M", $showinfo["Start Time"]);?> - <?php echo strftime("%H:%M", $showinfo["End Time"]);?></td>
                <td class="channel"><?php echo $showinfo['Channel Name']; ?></td>
                <td class="show"><a href="show.php?name=<?php echo $showinfo["URL Name"]; ?>" onclick='window.open(this.href, ""); return false;'><?php echo $showinfo["HTML Name"]; ?></a></td>
                <td class="ratings:<?php echo $showinfo['Rating']; ?>"><a class="0" href="set.php?show=<?php echo $showinfo["URL Name"]; ?>&amp;rating=0" onclick='return setRating(0, "<?php echo $showinfo["HTML Name"]; ?>")'><img class="img" src="x.png" alt="0"/></a><?php
          for($a=1 ; $a <= $showinfo["Rating"] ; $a = $a + 1){
              ?><a class="<?php echo $a; ?>" href="set.php?show=<?php echo $showinfo["URL Name"];?>&amp;rating=<?php echo $a; ?>" onclick='return setRating(<?php echo $a; ?>, "<?php echo $showinfo["HTML Name"]; ?>")'><img class="img" src="black.png" alt="<?php echo $a; ?>"/></a><?php
          }
          for(;$a <= 5; $a = $a + 1) {
              ?><a class="<?php echo $a; ?>" href="set.php?show=<?php echo $showinfo["URL Name"]; ?>&amp;rating=<?php echo $a; ?>" onclick='return setRating(<?php echo $a; ?>, "<?php echo $showinfo["HTML Name"]; ?>")'><img class="img" src="white.png" alt="<?php echo $a;?>"/></a><?php
          } ?></td>
            </tr>
<?php }
} ?>
