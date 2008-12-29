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

        # set default channels
        $result = run_sql('SELECT channelName FROM channel WHERE standard = 1;');
        if (mysql_num_rows($result) > 0) {
            while($row = mysql_fetch_row($result))
                run_sql('INSERT userchannels SET username = "'.escape($user_name).'", channelname = "'.escape($row[0]).'", state=1, set_on=NOW();');
            mysql_free_result($result);
        }

        # log user in
        setcookie("validuser", $user_name, time()+60*60*24*30);
        header('Location: tvlisting.php');
    }
    else{
        include("registerscreen.php");
    }
?>
