<?php
class Content{
//==Variables===========================================================================================================
    public $table_head='
                        <table border="1">
                                <tr>
                                    <th>C/b</th>
                                    <th>Login</th>
                                    <th>Password</th>
                                    <th>e-mail</th>
                                    <th>First name</th>
                                    <th>Last name</th>
                                </tr>    
    ';
    public $table_bottom='</table>';

    public $create_form='
                         <div class="cont-frm">  
                             <input type="text" name="new_login" placeholder="Login">
                             <input type="password" name="new_pass" placeholder="Password">
                             <input type="email" name="new_email" placeholder="Email">
                             <input type="text" name="new_fname" placeholder="First name">
                             <input type="text" name="new_lname" placeholder="Last name">
                         </div>
                         <div class="cont-btn">                            
                            <input type="submit" name="submit" value="Submit">                         
                            <input type="submit" name="back" value="Back">                             
                         </div>
    
    ';

    public $work_on_array = array();
//======================================================================================================================
//==Methods=============================================================================================================
    public function __construct()
    {
        $this->get_db_array();
    }

    public function content_query($sql_query){
        global $database;
        return $database->query($sql_query);
    }

    public function get_db_array(){
        $this->work_on_array = $this->content_query("SELECT * FROM `users`")->fetch_all(MYSQLI_ASSOC);
}

    public function only_one_check($check_arr)
    {
        if (count($check_arr) == 1) {
            return true;
        }else{
            return false;
        }
    }

    public function take_one($arr){
        return $arr[0];
}

    public function check_on_equals($checking){
        $data = $this->content_query("SELECT `login` FROM `users` WHERE `login` ='$checking'");
        if(empty($data->num_rows)){
            return true;
        }else{
            return false;
        }
    }

    private function redirect_to_main(){

        session_unset();
        unset($_POST);
        header('Location: http://localhost/datagrid/index.php ');

    }


//====Table_building====================================================================================================
    public function show_table_head(){
        echo $this->table_head;
    }

    public function show_table_bottom(){
        echo $this->table_bottom;
    }

    public function build_table(){
        $table = array();
        foreach($this->work_on_array as $value) {
            $line = sprintf('
                <tr>
                    <td><input type="checkbox" name=choice[] value="%d"></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                </tr>
            ', $value['id'], $value['login'], $value['password'], $value['email'], $value['f_name'], $value['l_name']);
            array_push($table, $line);
        }
        return $table;
    }

    public function show_table(){
        $this->show_table_head();
        foreach ($this->build_table() as $value) {
            echo $value;
        }
        $this->show_table_bottom();
    }

//======================================================================================================================
//====Deleting==========================================================================================================
    public function delete(){
        foreach($_POST['choice'] as $value){
            $this->content_query("DELETE FROM `users` WHERE id = $value");
        }
        $this->redirect_to_main();
    }
//======================================================================================================================
//====Creating==========================================================================================================

    public function show_create_form(){
        echo $this->create_form;
    }

    public function create(){
        $_SESSION['create']=true;
        $this->show_create_form();

        if(!empty($_POST['back'])){
            $this->redirect_to_main();
        }

        if(!empty($_POST['new_login']) and !empty($_POST['new_pass']) and !empty($_POST['new_email'])){

            if($this->check_on_equals($_POST['new_login']) == true){

                $query ='insert into `users`(login, password, email, f_name, l_name)
                         values("'.$_POST['new_login'].'",
                                "'.$_POST['new_pass'].'",
                                "'.$_POST['new_email'].'",
                                "'.$_POST['new_fname'].'",
                                "'.$_POST['new_lname'].'")';
                $this->content_query($query);
                $this->redirect_to_main();

            }else{
                echo 'This account already exists';
            }
        }else{
            if(!empty($_POST['submit'])){
                echo'Enter information!';
            }
        }
    }

//======================================================================================================================
//====Editing===========================================================================================================

    public function show_edit($id,$login, $password, $email, $f_name, $l_name){
        echo'
             <div class="cont-frm">  
                <input type="hidden" name="id" value="'.$id.'">
                 <input type="text" name="new_login" value="'.$login.'">
                 <input type="text" name="new_pass" value="'.$password.'">
                 <input type="email" name="new_email" value="'.$email.'">
                 <input type="text" name="new_fname" value="'.$f_name.'">
                 <input type="text"name="new_lname" value="'.$l_name.'">
             </div>
             <div class="cont-btn">                            
                <input type="submit" name="update" value="Submit">                         
                <input type="submit" name="back" value="Back">                             
             </div>        
        ';
    }

    public function build_edit(){
        if(!empty($_POST['choice'])) {
            $ins_data=$_POST['choice'];
            if ($this->only_one_check($ins_data)) {

                $var = $this->take_one($ins_data);

                $data = $this->content_query("SELECT * FROM `users` WHERE id = $var")->fetch_all(MYSQLI_ASSOC);

                $value = $data[0];
                $this->show_edit($value['id'], $value['login'], $value['password'], $value['email'], $value['f_name'], $value['l_name']);
            }
        }
    }

    public function edit(){
        $_SESSION['edit']=true;
        $this->build_edit();
        if(isset($_POST['update'])){
            $this->content_query('UPDATE `users`
                                  SET `login`="' . $_POST['new_login'] . '",
                                      `password`="'.$_POST['new_pass'].'",
                                      `email`="'.$_POST['new_email'].'",
                                      `f_name`="'.$_POST['new_fname'].'",
                                      `l_name`="'.$_POST['new_lname'].'"
                                  WHERE `id`="'.$_POST['id'].'"');

            $this->redirect_to_main();
        }

        if(!empty($_POST['back'])){

            $this->redirect_to_main();
        }
        if(!empty($_POST['edit']) and empty($_POST['choice'])){
            $this->redirect_to_main();
        }


    }

//======================================================================================================================
//====Exporting=========================================================================================================

    public function write_in_file($file_name,$mode){
        $fp = fopen($file_name, "$mode");
        foreach ($_POST['choice'] as $key) {
            $data[] = $this->content_query("select * from `users` where id = $key")->fetch_all(MYSQLI_ASSOC);//
            foreach ($data as $value) {
                foreach ($value as $info) {
                    $str = $info['login'] . ': '              . "\r" .
                           ' Password: '  . $info['password'] . "\n " .
                           'Email: '      . $info['email']    . "\n " .
                           'First name: ' . $info['f_name']   . "\n " .
                           'Last name: '  . $info['l_name']   . "\n ";
                }
            }
            fwrite($fp, $str . "\r\n");
        }
        fclose($fp);
        $this->redirect_to_main();
    }

    public function export(){
        if(!empty($_POST['choice'])){
            $file_name="result/result.txt";
            if(file_exists($file_name)){
                $this->write_in_file($file_name,'w');
            }else{
                $this->write_in_file($file_name,'x');
            }
        }

        if(empty($_POST['choice']) and !empty($_POST['export'])){
            $this->redirect_to_main();
        }
    }


//======================================================================================================================
//==Main method=========================================================================================================

    public function show_cont()
    {

        if(!$_POST){
            $this->show_table();
        }

        if (!empty($_POST['delete'])) {
            $this->delete();
        }

        if (!empty($_POST['create']) or !empty($_SESSION['create'])) {

            $this->create();
        }

        if (!empty($_POST['edit']) or !empty($_SESSION['edit'])) {

            $this->edit();
        }

        if(!empty($_POST['export'])){

            $this->export();
        }

        if (!empty($_POST['info'])) {

            header('Location: https://github.com/ZeeyN/datagrid ');
        }
    }

//======================================================================================================================

}

$cont = new Content();