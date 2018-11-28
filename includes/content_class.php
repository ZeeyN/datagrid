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
    public $db;
//======================================================================================================================
//==Methods=============================================================================================================
    public function __construct()
    {
        $this->db = new Database();
        $this->get_db_array();
    }

    public function show_table_head(){
        echo $this->table_head;
    }

    public function show_table_bottom(){
        echo $this->table_bottom;
    }

    public function get_db_array(){
        $this->work_on_array = $this->db->query('select * from `users`')->fetch_all(MYSQLI_ASSOC);
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

    public function delete(){
        foreach($_POST['choice'] as $value){
            $this->db->query('delete from `users` where id = "'.$value.'"');
        }
        header('Location: http://localhost/datagrid/index.php ');
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

    public function show_edit($id,$login, $password, $email, $f_name, $l_name){
        echo'
             <div class="cont-frm">  
                <input type="hidden" name="id" value="'.$id.'">
                 <input type="text" name="new_login" value="'.$login.'">
                 <input type="text" name="new_pass" value="'.$password.'">
                 <input type="email" name="new_email" value="'.$email.'">
                 <input type="text" name="new_fname" value="'.$f_name.'">
                 <input type="text" name="new_lname" value="'.$l_name.'">
             </div>
             <div class="cont-btn">                            
                <input type="submit" name="update" value="Submit">                         
                <input type="submit" name="back" value="Back">                             
             </div>        
        ';
    }

    public function show_create_form(){
        echo $this->create_form;
    }

    public function check_on_equals($checking){
        $query = sprintf('select `login` from `users` where `login` = "%s"', $checking);
        $data = $this->db->query($query);
        if(empty($data->num_rows)){
            return true;
        }else{
            return false;
        }
    }

    public function create(){
        $_SESSION['create']=true;
        $this->show_create_form();

        if(!empty($_POST['back'])){
            session_unset();
            unset($_POST['back']);
            header('Location: http://localhost/datagrid/index.php ');
        }

        if(!empty($_POST['new_login']) and !empty($_POST['new_pass']) and !empty($_POST['new_email'])){

            if($this->check_on_equals($_POST['new_login']) == true){

                $query ='insert into `users`(login, password, email, f_name, l_name)
                  values("'.$_POST['new_login'].'","'.$_POST['new_pass'].'","'.$_POST['new_email'].'","'.$_POST['new_fname'].'","'.$_POST['new_lname'].'")';
                $this->db->query($query);
                session_unset();
                unset($_POST);
                header('Location: http://localhost/datagrid/index.php ');

            }else{
                echo 'This account already exists';
            }
        }else{
            if(!empty($_POST['submit'])){
                echo'Enter information!';
            }
        }
    }

    public function build_edit(){
        if(!empty($_POST['choice'])) {
            $_SESSION['choice']=$_POST['choice'];
            if ($this->only_one_check($_SESSION['choice'])) {

                $var = $this->take_one($_SESSION['choice']);

                $data = $this->db->query('SELECT * FROM `users` WHERE id = "' . $var . '"')->fetch_all(MYSQLI_ASSOC);

                $value = $data[0];
                $this->show_edit($value['id'], $value['login'], $value['password'], $value['email'], $value['f_name'], $value['l_name']);
            }
        }
    }

    public function edit(){
        $_SESSION['edit']=true;
        $this->build_edit();
        if(isset($_POST['update'])){
            $this->db->query('UPDATE `users`
                      SET `login`="' . $_POST['new_login'] . '",
                       `password`="'.$_POST['new_pass'].'",
                        `email`="'.$_POST['new_email'].'",
                         `f_name`="'.$_POST['new_fname'].'",
                          `l_name`="'.$_POST['new_lname'].'"
                           where `id`="'.$_POST['id'].'"');

            session_unset();
            unset($_POST);
            header('Location: http://localhost/datagrid/index.php ');
        }

        if(!empty($_POST['back'])){

            session_unset();
            unset($_POST);
            header('Location: http://localhost/datagrid/index.php ');
        }

            if(empty($_SESSION['choise']) or $this->only_one_check($_SESSION['choice'])==false){
                session_unset();
                unset($_POST);
                header('Location: http://localhost/datagrid/index.php ');
            }

    }








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





        if (!empty($_POST['info'])) {

            header('Location: https://github.com/ZeeyN/testwork/blob/master/testwork.nr/README.md ');
        }
    }














}

$cont = new Content();