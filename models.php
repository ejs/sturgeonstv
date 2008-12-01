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
            $this->load_channels();
            $this->visit_count = ++$_SESSION['counter'];
        }
    }

    class DBUser extends User{
        public function __construct($name){
            $this->name = $name;
            parent::__construct();
        }

        public function load_channels(){
            $query = "SELECT channelName, state FROM userchannels where username = '".$this->name."'";
            $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
            if (mysql_num_rows($result) > 0) {
                $answer = array();
                while($row = mysql_fetch_row($result)) {
                    $data = array("ChannelName"=>$row[0], "URL"=>"", "default?"=>($row[1] == 1));
                    array_push($answer, $data);
                }
                mysql_free_result($result);
                $this->channels = $answer;
            }
            else{
                $this->channels = array();
            }
        }
    }

    class SessionUser extends User{
        public function load_channels(){
            $query = "SELECT channelName, standard, storeddays FROM channel;";
            $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
            if (mysql_num_rows($result) > 0) {
                $answer = array();
                while($row = mysql_fetch_row($result)) {
                    if ($row[2] >= 0) {
                        $data = array("ChannelName"=>$row[0], "URL"=>"", "default?"=>$row[1]);
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
?>
