<?php
    require_once('config.php');

    mysql_connect($databaseserver, $databaseuser, $databasepassword);
    mysql_select_db($databasename) or die ("Unable to select database!");

    function load_user(){
        if ($_COOKIE and array_key_exists("validuser", $_COOKIE)){
            return new DBUser($_COOKIE["validuser"]);
        }
        else{
            return new SessionUser();
        }
    }

    class User {
        public $channels;
        public $visit_count;
        public $name;

        public function __construct(){
            $this->update();
            $this->load_channels();
            $this->visit_count = ++$_SESSION['counter'];
        }

        public function update(){
            if ($_GET){
                $this->setChannel($_GET["channel"], $_GET["to"] == "on");
            }
        }
    }

    class DBUser extends User{
        public function __construct($name){
            $this->name = $name;
            parent::__construct();
        }

        public function getShows($start, $end, $minrating, $null){
            $start = str_replace("starttime", "tvshowinstance.starttime", $start);
            $start = str_replace("endtime", "tvshowinstance.endtime", $start);
            $end = str_replace("starttime", "tvshowinstance.starttime", $end);
            $end = str_replace("endtime", "tvshowinstance.endtime", $end);
            $channellist = array();
            foreach ($this->channels as $channel) {
                if ($channel["default?"]){
                    array_push($channellist, $channel["ChannelName"]);
                }
            }
            $channellist = implode("', '", $channellist);
            $query = "SELECT tvshowinstance.showname, tvshowinstance.starttime, tvshowinstance.channelname, tvshowinstance.endtime, tvshowrating.rating ";
            $query = $query." FROM tvshowinstance LEFT JOIN tvshowrating ON tvshowinstance.showname = tvshowrating.showname AND tvshowrating.username='".$this->name."' ";
            $query = $query." WHERE tvshowinstance.channelname IN ('".$channellist."') AND ".$start." AND ".$end." ";
            if ($null){
                $query = $query." AND ( ".$minrating." <= tvshowrating.rating OR tvshowrating.rating IS NULL ) ";
            }
            else {
                $query = $query." AND ".$minrating." <= tvshowrating.rating ";
            }
            $query = $query." ORDER BY starttime;";
            $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
            $answer = array();
            if (mysql_num_rows($result) > 0) {
                while($row = mysql_fetch_row($result)) {
                    $data = array("Show Name"=>$row[0], "Start Time"=>strtotime($row[1]), "End Time"=>strtotime($row[3]), "Channel Name"=>$row[2], "Rating"=>$row[4]);
                    if (!$data["Rating"]){
                        $data["Rating"] = 0;
                    }
                    array_push($answer, $data);
                }
                mysql_free_result($result);
            }
            return $answer;
        }

        public function load_channels(){
            $query = "SELECT channel.channelName, userchannels.state ";
            $query = $query." FROM channel LEFT JOIN userchannels ON channel.channelname = userchannels.channelname AND userchannels.username = '".$this->name."' ";
            $query = $query." WHERE channel.storeddays > 0 ORDER BY channel.channelname";
            $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
            $this->channels = array();
            if (mysql_num_rows($result) > 0) {
                while($row = mysql_fetch_row($result)) {
                    array_push($this->channels, array("ChannelName"=>$row[0], "default?"=>$row[1]));
                }
                mysql_free_result($result);
            }
        }

        public function setChannel($ChannelName, $state){
            $query = "UPDATE userchannels SET state='".$state."' WHERE username = '".$this->name."' AND channelname = '".$ChannelName."'";
            mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
        }
    }

    class SessionUser extends User{
        public function load_channels(){
            if($_SESSION['channels']){
                $this->channels = $_SESSION['channels'];
            }
            else{
                $query = "SELECT channelName, standard, storeddays FROM channel;";
                $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
                if (mysql_num_rows($result) > 0) {
                    $answer = array();
                    while($row = mysql_fetch_row($result)) {
                        if ($row[2] >= 0) {
                            $data = array("ChannelName"=>$row[0], "default?"=>$row[1]);
                            array_push($answer, $data);
                        }
                    }
                    mysql_free_result($result);
                    $this->channels = $answer;
                }
                else{
                    $this->channels = array();
                }
                $_SESSION['channels'] = $this->channels;
            }
        }

        public function getShows($start, $end, $minrating, $null){
            $channellist = array();
            foreach ($this->channels as $channel) {
                if ($channel["default?"]){
                    array_push($channellist, $channel["ChannelName"]);
                }
            }
            $channellist = implode("', '", $channellist);
            $query = "SELECT showname, starttime, channelname, endtime FROM tvshowinstance";
            $query = $query." WHERE channelname IN ('".$channellist."') AND ".$start." AND ".$end." ";
            $query = $query." ORDER BY starttime;";
            $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
            $answer = array();
            if (mysql_num_rows($result) > 0) {
                while($row = mysql_fetch_row($result)) {
                    $data = array("Show Name"=>$row[0], "Start Time"=>strtotime($row[1]), "End Time"=>strtotime($row[3]), "Channel Name"=>$row[2], "Rating"=>0);
                    array_push($answer, $data);
                }
                mysql_free_result($result);
            }
            return $answer;
        }

        public function setChannel($ChannelName, $state){
            foreach($_SESSION['channels'] as $key=>$info){
                if ($info['ChannelName'] == $ChannelName){
                    $_SESSION['channels'][$key]['default?'] = $state;
                }
            }
        }
    }
?>
