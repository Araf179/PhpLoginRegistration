<?php 
    function clean($string){
        return htmlentities($string);
    }

    function redirect($location) {
        return header("Location: {$location}");
    }

    function set_message($message){
        if(!empty($message)){
            $_SESSION['message'] = $message;
            
        }else {
            $message = "";
        }
    }

    function display_message(){
        if(isset($_SESSION['message'])){
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
    }

    function token_generator(){
        $token = $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        return $token;
    }

    function send_email($email, $subject, $msg, $headers){
        return mail($email, $subject, $msg, $headers);
    }

    //Validation Functions
    function validation_errors($error_message){
        return '<div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Warning!</strong>'. $error_message . '</div>';
    }

    function email_exists($email){
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $result = query($sql);
        if(row_count($result) == 1) {
            return true;
        }else{
            return false;
        }
    }

    function username_exists($username){
        $sql = "SELECT id FROM users WHERE username = '$username'";
        $result = query($sql);
        if(row_count($result) == 1) {
            return true;
        }else{
            return false;
        }
    }
    
    function logged_in(){
        if(isset($_SESSION['email']) || isset($_COOKIE['email'])){
            return true;
        }else{return false;}
    }
   
    //Recover password while sending a validation code
    function recover_password(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            if($_SESSION['token'] && $_POST['token'] == $_SESSION['token']){
                $email = clean($_POST['email']);
                if(email_exists($email)){
                    $validation_code = md5($email . microtime());
                    setcookie('temp_access_code', $validation_code, time() + 900);
                    $sql = "UPDATE users SET validation_code = '".escape($validation_code)."' WHERE email = '" .escape($email)."'";
                    $result = query($sql);
                    $subject = "Please reset your password";
                    $message= "Here is your activation code {$validation_code}
                    CLick here to reset your password http://localhost/login/code.php?email=$email&code=$validation_code";
                    $headers = "From: noreply@yourwebsite.com";
                    if(!send_email($email, $subject, $message, $headers)){
                        echo validation_errors("Email could not be sent");
                    }
                    set_message("<p class='bg-success'> Please check spam folder for a password reset code</p>");
                    redirect("index.php");
                } else{
                    echo validation_errors("This email does not exist");
                }
                echo "it works";
            }else{
                redirect("index.php");
            }
        }
        if(isset($_POST['cancel-submit'])){
            redirect('login.php');
        }
    }
//Code Validation
    function password_reset(){
        if($_COOKIE['temp_access_code'] != null && $_GET['email'] != null && $_GET['code'] != null && $_SESSION['token'] != null){
            if ($_POST['token'] == $_SESSION['token'] && $_POST['password'] == $_POST["confirm_password"]){
                
                $updated_password = md5($_POST['password']);
                $email = $_GET['email'];
                $sql = "UPDATE users SET password = '".escape($updated_password)."', validation_code = 0 WHERE email = '".escape($email)."'";
                query($sql);
                set_message("<p class='bg-success'>Your password has been updated</p>");
                redirect("login.php");
            }
        }else{
            set_message("<p class='bg-danger'>Sorry Session Has Ended</p>");
            redirect("recover.php");
        }
    }

//User Login
    function login_user($email, $password,$remember){
        $sql = "SELECT password, id FROM users WHERE email = '".escape($email)."' AND active = 1";
        $result = query($sql);
        if(row_count($result) == 1){
            $row = fetch_array($result);
            $db_password = $row['password'];
            if(md5($password) == $db_password) { 
                $_SESSION['email'] = $email;
                if($remember == "on"){
                    setcookie('email', $email, time() + 60);
                }
                return true; 
            } else { return false; }
        }
    }

    function register_user($first_name, $last_name, $username, $email, $password){
        $first_name = escape($first_name);
        $last_name = escape($last_name);
        $username = escape($username);
        $email = escape($email);
        $password = escape($password);
        if(email_exists($email)){
            return false;
        }else if(username_exists($username)){
            return false;
        }else {
            $password = md5($password);
            $validation_code = md5($username . microtime());
            $sql = "INSERT INTO users(first_name, last_name, username, email, password, validation_code, active)";
            $sql .= " VALUES('$first_name','$first_name','$username', '$email','$password','$validation_code', '0')";
            $result = query($sql);
            

            $subject = "Activate Your Account";
            $msg = "Please Click the Link below to activate your account
            http://localhost/login/activate.php?email=$email&code=$validation_code
            ";
            $header = "From: noreply@yourwebsite.com";
            send_email($email, $subject, $msg, $headers);
            return true;
        }
    }
?>