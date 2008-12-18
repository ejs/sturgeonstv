<?php
    require_once("config.php");
    require_once("models.php");

    function log_message_to_file($message){
        global $logfile;
        $sink = fopen($logfile, 'a');
        fwrite($sink, $message."\n");
        fclose($sink);
    }

    log_message_to_file("Log in.");
    session_start();

    $user_name = $_POST['username'];
    $password = $_POST['password'];
    $passwordtwo = $_POST['password2'];

    function validate($username, $password, $passwordtwo){
        $query = 'SELECT username FROM user WHERE username ="'.$username.'";';
        $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
        return ($username && $password && mysql_num_rows($result) == 0 && $password === $passwordtwo);
    }

    if (validate($user_name, $password, $passwordtwo)){
        # add user to database
        $query = 'INSERT user SET username ="'.$user_name.'", password = SHA1("'.$password.'"), created= NOW();';
        $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());

        # set default channels
        $query = "SELECT channelName FROM channel WHERE standard = 1;";
        $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
        if (mysql_num_rows($result) > 0) {
            while($row = mysql_fetch_row($result)) {
                $query = "INSERT userchannels SET username = '".$user_name."', channelname = '".$row[0]."', state=1, set_on=NOW();";
                mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
            }
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
