<?php
function layout_shows($start, $end, $message, $minrating=1, $null=1)
{
    global $client;
    $shows = $client->getShows($start, $end, $minrating, $null);
    if (!($shows or $null))
        return;
    if($null){ ?>
            <tr class='Infoa' onclick='toggle(this);' unrated="<?php echo $null==2?"on":"off" ?>" minrating='<?php echo $minrating; ?>'>
<?php  } else{ ?>
            <tr class='Infob' unrated="on" minrating='<?php echo $minrating; ?>'>
<?php  } ?>
                <td colspan='4' style='text-align: center'><?php echo $message ?></td>
            </tr>
<?php foreach($client->getShows($start, $end, $minrating, $null) as $showinfo){?>
            <?php if($showinfo["Rating"] or $null==2) {?><tr class='Show' onMouseOver='hover(this);' onMouseOut='leave(this)' ><?php } else { ?><tr class='Show' style='display:none' onMouseOver='hover(this);' onMouseOut='leave(this)' ><?php }?>

                <td starttime="<?php echo strftime("%d:%m:%Y:%H:%M", $showinfo["Start Time"]);?>" endtime="<?php echo strftime("%d:%m:%Y:%H:%M", $showinfo["End Time"]);?>"><?php echo strftime("%H:%M", $showinfo["Start Time"]);?> - <?php echo strftime("%H:%M", $showinfo["End Time"]);?></td>
                <td><?php echo $showinfo['Channel Name']; ?></td>
                <td><a href="show.php?name=<?php echo urlencode($showinfo["Show Name"]); ?>" target='_blank'><?php echo $showinfo["Show Name"]; ?></a></td>
                <td class="ratings:<?php echo $showinfo['Rating']; ?>"><a href="set.php?show=<?php echo urlencode($showinfo["Show Name"]); ?>&amp;rating=0" onclick="return setRating(0, '<?php echo $showinfo["Show Name"]; ?>')"><img src="x.png" alt="0"/></a><?php
          for($a=0 ; $a < $showinfo["Rating"] ; $a = $a + 1){
              ?><a href="set.php?show=<?php echo urlencode($showinfo["Show Name"]); ?>&amp;rating=<?php echo $a+1; ?>" onclick='return setRating(<?php echo $a+1; ?>, "<?php echo $showinfo["Show Name"]; ?>")'><img src="black.png" alt="<?php echo $a+1; ?>"/></a><?php
          }
          for(;$a < 5; $a = $a + 1) {
              ?><a href="set.php?show=<?php echo urlencode($showinfo["Show Name"]); ?>&amp;rating=<?php echo $a+1; ?>" onclick='return setRating(<?php echo $a+1; ?>, "<?php echo $showinfo["Show Name"]; ?>")'><img src="white.png" alt="<?php echo $a+1;?>"/></a><?php
          } ?></td>
            </tr>
<?php }
} ?>
