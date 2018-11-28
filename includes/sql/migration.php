<?php
class Database
{

    public $connection;
    public $files;
//    public $msg;


    public function __construct()
    {
        $this->open_db_connection();
        $this->start_migration();
    }

    public function open_db_connection(){

        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($this->connection->connect_errno){

            die("Database connection failed badly".$this->connection->connect_error);

        }
    }

    public function query($sql_query){

        return $this->connection->query($sql_query);

    }

    public function get_migration_files($connection){
        $sql_folder = str_replace('\\','/', realpath(dirname(__FILE__)).'/');
        $all_files = glob($sql_folder.'*.sql');

        $query = sprintf('show tables from `%s` like "%s"',DB_NAME, DB_TABLE_VERSIONS);
        $data = $this->query($query);
        if(empty($data->num_rows)){
            return $all_files;
        } else {


            $version_files = array();
            $query = sprintf('select `name` from `%s`', DB_TABLE_VERSIONS);
            $data = $this->query($query)->fetch_all(MYSQLI_ASSOC);

        foreach($data as $row){
            array_push($version_files, $sql_folder.$row['name']);
        }

        return array_diff($all_files, $version_files);
    }

}
    public function migrate($file){
        $command = sprintf('%s -u%s -h %s -D %s < %s', DB_MYSQL_DIR,DB_USER, DB_HOST, DB_NAME, $file);
        exec($command, $output,$result);


        $base_name = basename($file);
        $query = sprintf('insert into `%s` (`name`) values("%s")', DB_TABLE_VERSIONS, $base_name);
        $this->query($query);
    }

    public function start_migration(){

        $this->files = $this->get_migration_files($this->connection);
        if(empty($this->files)){
//            echo 'Your database in actual stance';
        } else{
//            echo 'Starting migration...<br><br>';

            foreach($this->files as $file){
                $this->migrate($file);
//                echo basename($file).'<br>';
            }

//            echo '<br>Migration complete.';
        }
    }


}

$db = new Database();












