<?php
    require_once('config.php');

    mysql_connect($databaseserver, $databaseuser, $databasepassword);
    mysql_select_db($databasename) or die ("Unable to select database!"); ;

    function get_all_channels(){
        $query = "SELECT channelName, url, standard, storeddays FROM channel;";
        $result = mysql_query($query) or die ("Error in query:". $query." ".mysql_error());
        if (mysql_num_rows($result) > 0) {
            $answer = array();
            while($row = mysql_fetch_row($result)) {
                if ($row[3] >= 0){
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

    class User {
        public $channels;

        public function __construct(){
            $this->channels = get_all_channels();
        }
    }
?>
