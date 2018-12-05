<?php
class Migration
{

    public $files;


    public function __construct()
    {
        $this->start_migration();
    }


    public function get_migration_files(){
        global $database;
        $sql_folder = str_replace('\\','/', realpath(dirname(__FILE__)).'/');
        $all_files = glob($sql_folder.'*.sql');

        $query = sprintf('show tables from `%s` like "%s"',DB_NAME, DB_TABLE_VERSIONS);
        $data = $database->query($query);
        if(empty($data->num_rows)){
            return $all_files;
        } else {


            $version_files = array();
            $query = sprintf('select `name` from `%s`', DB_TABLE_VERSIONS);
            $data = $database->query($query)->fetch_all(MYSQLI_ASSOC);

        foreach($data as $row){
            array_push($version_files, $sql_folder.$row['name']);
        }

        return array_diff($all_files, $version_files);
    }

}
    public function migrate($file){
        global $database;
        $command = sprintf('%s -u%s -h %s -D %s < %s', DB_MYSQL_DIR,DB_USER, DB_HOST, DB_NAME, $file);
        exec($command, $output,$result);


        $base_name = basename($file);
        $query = sprintf('insert into `%s` (`name`) values("%s")', DB_TABLE_VERSIONS, $base_name);
        $database->query($query);
    }

    public function start_migration(){

        $this->files = $this->get_migration_files();
        if(!empty($this->files)){
            foreach($this->files as $file){
                $this->migrate($file);
            }
        }
    }


}













