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

        public function getShows($start, $end){
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
                    $data = array("Show Name"=>$row[0], "Start Time"=>strtotime($row[1]), "End Time"=>strtotime($row[3]), "Channel Name"=>$row[2]);
                    array_push($answer, $data);
                }
                mysql_free_result($result);
            }
            return $answer;
        }
    }

    class DBUser extends User{
        public function __construct($name){
            $this->name = $name;
            parent::__construct();
        }

        public function load_channels(){
            $query = "SELECT channelName FROM channel WHERE storeddays > 0 ORDER BY channelname";
            $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
            if (mysql_num_rows($result) > 0) {
                $answer = array();
                while($row = mysql_fetch_row($result)) {
                    array_push($answer, $this->loadChannel($row[0]));
                }
                mysql_free_result($result);
                $this->channels = $answer;
            }
            else{
                $this->channels = array();
            }
        }

        private function loadChannel($channelName){
            $query = "SELECT state FROM userchannels WHERE username = '".$this->name."' and channelname='".$channelName."';";
            $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
            $answer = array("ChannelName"=>$channelName, "default?"=>0);
            if(mysql_num_rows($result) > 0) {
                $tmp = mysql_fetch_row($result);
                $answer["default?"] = $tmp[0];
            }
            else{
                $query = "INSERT userchannels set username='".$this->name."', channelName='".$channelName."', state=0, set_on=NOW();";
                mysql_query($query) or die ("Error in query:".$query." ".mysql_error());
            }
            return $answer;
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

        public function setChannel($ChannelName, $state){
            foreach($_SESSION['channels'] as $key=>$info){
                if ($info['ChannelName'] == $ChannelName){
                    $_SESSION['channels'][$key]['default?'] = $state;
                }
            }
        }
    }
?>
