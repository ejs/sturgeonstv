<?php
    require_once('config.php');

    mysql_connect($databaseserver, $databaseuser, $databasepassword);
    mysql_select_db($databasename) or die ("Unable to select database!");

    function load_user(){
        if ($_COOKIE and array_key_exists("validuser", $_COOKIE))
            return new DBUser($_COOKIE["validuser"]);
        else
            return new SessionUser();
    }

    function log_event($message, $to){
        $log = fopen($to, 'a');
        fwrite($log, date("D j M Y - G:i:s")."\n\t".$message."\n");
        fclose($log);
    }

    function escape($s){
        $res = str_replace('"', '\"', $s);
        return $res;
    }

    function run_sql($query){
        global $dblog;
        log_event($query, $dblog);
        $tmp = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
        return $tmp;
    }

    class User {
        public $channels;
        public $name;

        public function __construct(){
            $this->load_channels();
        }

        public function update_channel(){
            if ($_GET) $this->setChannel($_GET["channel"], $_GET["to"] == "on");
        }

        public function update_show(){
            if ($_GET) $this->setShow($_GET["show"], $_GET["rating"]);
        }

        public function getShowInstance($start, $end, $name){
            $channellist = array();
            foreach ($this->channels as $channel) {
                if ($channel["default?"]) array_push($channellist, $channel["ChannelName"]);
            }
            $channellist = implode('", "', $channellist);
            $query = 'SELECT showname, starttime, channelname, endtime, discription FROM tvshowinstance';
            $query = $query.' WHERE channelname IN ("'.$channellist.'") AND '.$start.' AND '.$end.' AND "'.escape($name).'"= showname ';
            $query = $query.' ORDER BY starttime;';
            $result = run_sql($query);
            $answer = array();
            if (mysql_num_rows($result) > 0) {
                while($row = mysql_fetch_row($result)) {
                    $data = array("Show Name"=>$row[0], "Start Time"=>strtotime($row[1]), "End Time"=>strtotime($row[3]), "Channel Name"=>$row[2], "Description"=>$row[4]);
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

        public function setShow($name, $rating){
            if ($rating){
                $result = run_sql('SELECT * FROM tvshowrating WHERE username="'.escape($this->name).'" AND showname="'.escape($name).'";');
                if (mysql_num_rows($result) > 0 )
                    run_sql('UPDATE tvshowrating SET rating='.escape($rating).', lastset=NOW() WHERE username="'.escape($this->name).'" AND showname="'.escape($name).'";');
                else
                    run_sql('INSERT tvshowrating SET rating='.escape($rating).', lastset=NOW(), username="'.escape($this->name).'", showname="'.escape($name).'";');
            }
            else{
                run_sql('DELETE FROM tvshowrating WHERE username="'.escape($this->name).'" AND showname="'.escape($name).'";');
            }
        }

        public function getShows($start, $end, $minrating, $null){
            $channellist = array();
            foreach ($this->channels as $channel) {
                if ($channel["default?"]) array_push($channellist, $channel["ChannelName"]);
            }
            $channellist = implode('", "', $channellist);
            $query = 'SELECT tvshowinstance.showname, starttime, channelname, endtime, rating ';
            $query = $query.' FROM tvshowinstance LEFT JOIN tvshowrating ON tvshowinstance.showname = tvshowrating.showname AND username="'.escape($this->name).'" ';
            $query = $query.' WHERE channelname IN ("'.$channellist.'") AND '.$start.' AND '.$end.' ';
            if ($null)
                $query = $query.' AND ( '.escape($minrating).' <= rating OR rating IS NULL ) ';
            else
                $query = $query.' AND '.escape($minrating).' <= rating ';
            $query = $query.' ORDER BY starttime;';
            $result = run_sql($query);
            $answer = array();
            if (mysql_num_rows($result) > 0) {
                while($row = mysql_fetch_row($result)) {
                    $data = array("Show Name"=>$row[0], "Start Time"=>strtotime($row[1]), "End Time"=>strtotime($row[3]), "Channel Name"=>$row[2], "Rating"=>$row[4]);
                    $data["HTML Name"] = htmlentities($data["Show Name"], ENT_QUOTES);
                    $data["URL Name"] = urlencode($data["Show Name"]);
                    if (!$data["Rating"])
                        $data["Rating"] = 0;
                    array_push($answer, $data);
                }
                mysql_free_result($result);
            }
            return $answer;
        }


        public function load_channels(){
            $query = 'SELECT channel.channelName, state ';
            $query = $query.' FROM channel LEFT JOIN userchannels ON channel.channelname = userchannels.channelname AND username = "'.escape($this->name).'" ';
            $query = $query.' WHERE storeddays > 0 ORDER BY channel.channelname';
            $result = run_sql($query);
            $this->channels = array();
            if (mysql_num_rows($result) > 0) {
                while($row = mysql_fetch_row($result))
                    array_push($this->channels, array("ChannelName"=>$row[0], "default?"=>$row[1]));
                mysql_free_result($result);
            }
        }

        public function setChannel($ChannelName, $state){
            $result = run_sql('SELECT * FROM userchannels WHERE username="'.escape($this->name).'" AND channelname="'.escape($ChannelName).'";');
            if (mysql_num_rows($result) > 0)
                run_sql('UPDATE userchannels SET state="'.escape($state).'" WHERE username = "'.escape($this->name).'" AND channelname = "'.escape($ChannelName).'";');
            else
                run_sql('INSERT userchannels SET state="'.escape($state).'", username = "'.escape($this->name).'", channelname = "'.escape($ChannelName).'";');
        }
    }

    class SessionUser extends User{
        public function load_channels(){
            if($_SESSION['channels']){
                $this->channels = $_SESSION['channels'];
            }
            else{
                $this->channels = array();
                $result = run_sql('SELECT channelName, standard, storeddays FROM channel WHERE storeddays > 0 ORDER BY channelname;');
                if (mysql_num_rows($result) > 0) {
                    while($row = mysql_fetch_row($result)) {
                        $data = array("ChannelName"=>$row[0], "default?"=>$row[1]);
                        array_push($this->channels, $data);
                    }
                    mysql_free_result($result);
                }
                $_SESSION['channels'] = $this->channels;
            }
        }

        public function getShows($start, $end, $minrating, $null){
            $channellist = array();
            foreach ($this->channels as $channel) {
                if ($channel["default?"]) array_push($channellist, $channel["ChannelName"]);
            }
            $channellist = implode('", "', $channellist);
            $query = 'SELECT showname, starttime, channelname, endtime FROM tvshowinstance';
            $query = $query.' WHERE channelname IN ("'.$channellist.'") AND '.$start.' AND '.$end.' ';
            $query = $query.' ORDER BY starttime;';
            $result = run_sql($query);
            $answer = array();
            if ($null){
                if (mysql_num_rows($result) > 0) {
                    while($row = mysql_fetch_row($result)) {
                        $data = array("Show Name"=>$row[0], "Start Time"=>strtotime($row[1]), "End Time"=>strtotime($row[3]), "Channel Name"=>$row[2], "Rating"=>0);
                        array_push($answer, $data);
                    }
                    mysql_free_result($result);
                }
            }
            return $answer;
        }

        public function setChannel($ChannelName, $state){
            foreach($_SESSION['channels'] as $key=>$info){
                if ($info['ChannelName'] == $ChannelName) $_SESSION['channels'][$key]['default?'] = $state;
            }
        }

        public function setShow($name, $rating){ }
    }
?>
