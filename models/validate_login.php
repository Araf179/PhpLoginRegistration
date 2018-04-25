<?php 
 function validate_user_registration(){
    $min = 3;
    $max = 20;
    $errors = [];

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $first_name = clean($_POST["first_name"]);
        $last_name = clean($_POST["last_name"]);
        $username = clean($_POST["username"]);
        $email = clean($_POST["email"]);
        $password = clean($_POST["password"]);
        $confirm_password = clean($_POST["confirm_password"]);

        if(strlen($first_name) < $min){
            $errors[] = "Your first name cannot be less than {$min} characters";
        }
        if(strlen($first_name) > $max){
            $errors[] = "Your first name cannot be more than {$max} characters";
        }
        if(strlen($last_name) < $min){
            $errors[] = "Your last name cannot be less than {$min} characters";
        }
        if(strlen($last_name) > $max){
            $errors[] = "Your last name cannot be more than {$max} characters";
        }
        if(strlen($username) < $min){
            $errors[] = "Your first name cannot be less than {$min} characters";
        }
        if(username_exists($username)){
            $errors[] = "Your username exists {$username}";
        }
        if(strlen($username) > $max){
            $errors[] = "Your first name cannot be more than {$max} characters";
        }
        if(strlen($email) < $min) {
            $errors[] = "Your email cannot be more than {$min} characters";
        }
        if(email_exists($email)){
            $errors[] = "Your email exists {$email}";
        }
        if($password !== $confirm_password){
            $errors[] = "Your passwords do not match";
        }
        if(!empty($errors)){
            foreach ($errors as $error){
                //You can add a delimiter
                echo validation_errors($error);
            }
        }else{
            if (register_user($first_name, $last_name, $username, $email, $password)){
                set_message("<p class='bg-success text-center'>Please check your email or spam folder for an activation link</p>");
                redirect("index.php");
                echo "we registered properly";
            }else {
                set_message("<p class='bg-danger text-center'>We could not register user</p>");
                redirect("index.php");
            }
        }
    }
}

function validate_code(){
    if(isset($_COOKIE['temp_access_code'])){
        if(!isset($_GET['email']) && isset($_GET['code'])){
            redirect("index.php");
            }else if (empty($_GET['email']) || empty($_GET['code'])){
                redirect("index.php");
            }else {
                if(isset($_POST['code'])){
                    $email = clean($_GET['email']);
                    $validation_code = clean($_POST['code']);
                    $sql = "SELECT id FROM users WHERE validation_code = '" .escape($validation_code). "' AND email = '".escape($email)."'";
                    $result = query($sql);
                    if(row_count($result) == 1){
                        setcookie('temp_access_code', $validation_code, time() + 300);
                        redirect("reset.php?email=$email&code=$validation_code");
                    }else{
                        echo validation_errors("Sorry wrong validation code");
                    }
                    echo "getting post from form";
                }    
            }
        }else{
            set_message("<p class='bg-danger'>Sorry validation code has expired</p>");
            redirect("recover.php");
        }
    }

    function validate_user_login(){
        $min = 3;
        $max = 20;
        $errors = [];

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            echo 'it works';
            $email = clean($_POST['email']);
            $password = clean($_POST['password']);
            $remember= isset($_POST['remember']);
            if(empty($email)){
                $errors[] = "Email fields cannot be empty";
            }
            if(empty($password)){
                $errors[] = "password fields cannot be empty";
            }
            if(!empty($errors)){
                foreach ($errors as $error){
                    //You can add a delimiter
                    echo validation_errors($error);
                }
            }else {
                echo "No errors";
                if(login_user($email, $password, $remember)){
                    redirect("admin.php");
                }else{
                    echo validation_errors("Your credentials are not correct");
                }
            }
        }
    }

    function activate_user(){
        if($_SERVER['REQUEST_METHOD'] == "GET"){
            if(isset($_GET['email'])){
                echo $email = clean($_GET['email']);
                echo $validation_code = clean($_GET['code']);

                $sql = "SELECT id FROM users WHERE email = '".escape($_GET['email'])."' AND validation_code ='".escape($_GET['code'])."'";
                $result = query($sql);
                if (row_count($result) == 1){ 
                    $sql2 = "UPDATE users SET active = 1, validation_code = 0 Where email = '".escape($email)."' AND validation_code ='".escape($validation_code)."'";
                    $result2 = query($sql2);
                    set_message("<p class='bg-sucess'>Your account has been activated, please logig</p>");
                    redirect("login.php");
                }
            }
        }
    }
?>