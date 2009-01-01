<?php
    require_once("config.php");
    require_once("models.php");

    session_start();

    $user_name = $_POST['username'];
    $password = $_POST['password'];
    $passwordtwo = $_POST['password2'];

    function validate($username, $password, $passwordtwo){
        $result = run_sql('SELECT username FROM user WHERE username ="'.escape($username).'";');
        return ($username && $password && mysql_num_rows($result) == 0 && $password === $passwordtwo);
    }

    if (validate($user_name, $password, $passwordtwo)){
        # add user to database
        $result = run_sql('INSERT user SET username ="'.escape($user_name).'", password = SHA1("'.escape($password).'"), created= NOW();');

        # set active channels
        foreach($_SESSION['channels'] as $channelData){
            if ($channelData['default?'] == 1)
                run_sql('INSERT userchannels SET username = "'.escape($user_name).'", channelname = "'.escape($channelData["ChannelName"]).'", state=1, set_on=NOW();');
        }

        # move over show ratings
        foreach($_SESSION["shows"] as $show=>$rating){
            run_sql('INSERT tvshowrating SET username = "'.escape($user_name).'", showname = "'.escape($show).'", rating = '.$rating.', lastset = NOW();');
        }
        # log user in
        setcookie("validuser", $user_name, time()+60*60*24*30);
        header('Location: tvlisting.php');
    }
    else{
        include("registerscreen.php");
    }
?>
