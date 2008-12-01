<?php
    require_once('config.php');

    mysql_connect($databaseserver, $databaseuser, $databasepassword);
    mysql_select_db($databasename) or die ("Unable to select database!");

    function get_all_channels(){
        $query = "SELECT channelName, url, standard, storeddays FROM channel;";
        $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
        if (mysql_num_rows($result) > 0) {
            $answer = array();
            while($row = mysql_fetch_row($result)) {
                if ($row[3] >= 0) {
                    $data = array("ChannelName"=>$row[0], "URL"=>$row[1], "default?"=>$row[2]);
                    array_push($answer, $data);
                }
            }
            mysql_free_result($result); 
            return $answer;
        }
        else{
            return array();
        }
    }

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
            parent::__construct();
            $this->name = $name;
        }

        public function load_channels(){
            $this->channels = get_all_channels();
        }
    }

    class SessionUser extends User{
        public function load_channels(){
            $this->channels = get_all_channels();
            $_SESSION['channels'] = $this->channels;
        }
    }
?>
