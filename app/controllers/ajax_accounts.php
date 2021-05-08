<?php 
/*
 * 1. accounts controller's all ajax reqeust will be controlled 
 *   from this `ajax_accounts` controller classs.
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_accounts extends controller{
    
    //store config class objec
    private $config;
    
    //store functions class objec
    private $functions;

    //store the PHPMailer class object
    private $mail;

    //Here we will store all the required model's object
    private $model_objs=array();


    /**
     * =============================
     * All magic functions  starts 
     * =============================
     */

        public function __construct()
        {

            if($_SERVER["REQUEST_METHOD"] !== "POST"){

                //store the functions class object
                $this->functions=new functions;
                
                //Redirect to the not found page
                $this->functions->not_found();
                
                die();
                
            }else{
                
                //set $this->functions to functions class object
                $this->functions=new functions;
                
                //set $this->config to functions class object
                $this->config=new config;

                //store the PHPMailer class object
                $this->mail= new PHPMailer;

                //store all the models
                $this->model_objs["user_obj"]=$this->model("user");
                $this->model_objs["ufiles_obj"]=$this->model("user_files");
                $this->model_objs["token_obj"]=$this->model("token");
            
            }
        }

    /**
     * =============================
     * All magic functions  ends 
     * =============================
     */



    /**
     * =============================
     * All private functions  starts 
     * =============================
     */

        //use the function to check Username or E-mail Address does exist 
        private function check_uname_email_exists($which, $value)
        {

            //store the user model object from $this->model_objs variable
            $user_obj=$this->model_objs["user_obj"];

            $fetch_options="";

            if($which == "username"){
                
                $fetch_options = array(
                    "column_name"=>"users.user_name",
                    "where"=>"users.user_name='{$value}'"
                );

                
            }elseif($which == "email"){

                $fetch_options = array(
                    "column_name"=>"users.user_email",
                    "where"=>"users.user_email='{$value}'"
                );
            }

            //fetch data
            $fetch_data=$user_obj->select($fetch_options);

            if($fetch_data["status"] == 1 && $fetch_data["num_rows"] > 0){

                //Return true if a single row is fetched
                return true;
                
            }else{
                
                //Return false if a single row is not fetched
                return false;
            }

        }

        //Logged into the user when a login request comes
        private function logged_in($param)
        {
            //store the final output
            $output = "";

            //store the uname_email field from $param variable
            $uname_email=$param["uname_email"];

            //store the password
            $password=$param["password"];   

            //storet the login type
            $login_type=$param["login_type"];
        

            //store fetch options for the sql query
            $fetch_options=array(
                "column_name"=>"
                    users.user_id,
                    users.user_name,
                    users.user_email,
                    users.user_role,
                    users.user_password
                "
            );

            //store the message for no exists
            $exists_msg="";

            if($login_type =="username"){
                
                //set the message
                $exists_msg="Username doesn't exist";

                $fetch_options["where"]="users.user_name='$uname_email'";
                
            }elseif($login_type =="email"){

                $exists_msg="E-mail address doesn't exist";

                $fetch_options["where"]="users.user_email='$uname_email'";
        
            }

            //store the user model's object from $this->model_objs variable
            $user_obj=$this->model_objs["user_obj"];

            //fetch user information
            $fetch_user=$user_obj->select($fetch_options);
            
            if($fetch_user["status"] == 1 && $fetch_user["num_rows"] == 1){

                //store the fatched user_id
                $user_id=$fetch_user["fetch_all"][0]["user_id"];
                
                //store the fatched user_name
                $user_name=$fetch_user["fetch_all"][0]["user_name"];
                
                //store the fatched user_email
                $user_email=$fetch_user["fetch_all"][0]["user_email"];
                
                //store the fatched user_role
                $user_role=$fetch_user["fetch_all"][0]["user_role"];

                //store the fetched  password
                $user_password=$fetch_user["fetch_all"][0]["user_password"];

                if(password_verify($password,$user_password)){

                    session_start();

                    //login user by using email
                    $_SESSION["user_id"]=$user_id;
                    $_SESSION["user_type"]="old";
                    // $_SESSION["user_name"]=$user_name;
                    // $_SESSION["user_email"]=$user_email;
                    // $_SESSION["user_role"]=$user_role;

                    $output = true;

                }else{
                    
                    $output=array(
                        "target"=>".ac-form__col--password",
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Password did not match</p>
                            </div>
                        ",
                        "error_name"=>"password_error"
                    );
                }

            }else{
                
                $output=array(
                    "target"=>".ac-form__col--uname-email",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>$exists_msg</p>
                        </div>
                    ",
                    "error_name"=>"uname_email_error"
                );
            } 
            
            return $output;

        }

        //use the function for check password matchs with the previous one
        //while updating password
        private function password_match_with_prev($user_id,$password)
        {

            //store user's model object from $this->model_obj variable
            $user_obj=$this->model_objs["user_obj"];

            //store the previous password 
            $user_password="";

            //store the final ouptput
            $output="";
            
            //fetch password
            $fetch_password=$user_obj->select(array(
                "column_name"=>"
                    users.user_password
                ",
                "where"=>"users.user_id={$user_id}"
            ));

            if($fetch_password["status"] == 1 && $fetch_password["num_rows"] == 1){

                //set $user_password to fetched password
                $user_password=$fetch_password["fetch_all"][0]["user_password"];
            }

            if(!empty($user_password)){

                if(password_verify($password,$user_password)){

                    $output=true;

                }else{

                    $output=false;
                }

            }

            return $output;
        
        }

    /**
     * =============================
     * All private functions  ends 
     * =============================
     */


    /**
     * ===========================
     * All Public functions starts
     * ===========================
     */
    

        //validate the signup form while typing
        public function signup_form_validate()
        {
            
            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);
            
            //store the`input_value` index from $_POST variable
            $input_value=trim($_POST["input_value"]);

            //store the`name_attr` index from $_POST variable
            $name_attr=trim($_POST["name_attr"]);

            //store all error
            $errors=[];

            //validate first name
            if($name_attr=="fname"){

                if(strlen($input_value) < 3){

                    $errors["fname-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Minimum length must be 3</p>
                            </div>
                        "
                    );

                }elseif(strlen($input_value) > 15){
                    
                    $errors["fname-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Maximum length must be 15</p>
                            </div>
                        "
                    );
                    
                }
                
            }elseif($name_attr == "lname"){

                if(strlen($input_value) < 3){

                    $errors["lname-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Minimum length must be 3</p>
                            </div>
                        "
                    );
                    
                }elseif(strlen($input_value) > 15){

                    $errors["lname-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Maximum length must be 15</p>
                            </div>
                        "
                    );

                }

            }elseif($name_attr == "username"){

                //store the username in lowercase
                $username=strtolower($input_value);

                if(strlen($username) < 6){
                    
                    $errors["username-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>6 is the minimum length for username</p>
                            </div>
                        "
                    );

                    
                }elseif(!preg_match("/^[^0-9\W][a-zA-Z\d\_]+$/",$username)){
                    
                    $errors["username-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Except underscore(_) any special Charecters are not allowed</p>
                            </div>
                        "
                    );
                
                }else{
                
                    if($this->check_uname_email_exists("username",$username)){

                        $errors["username-error"]=array(
                            "error_msg"=>"
                                <div class='ac-form__msg ac-form__msg--error'>
                                    <p class='ac-form__msg--text'>Username already taken. Choose a different one</p>
                                </div>
                            "
                        );
                    }
                }
            }elseif($name_attr=="email"){

                $email=strtolower($input_value);

                if(!filter_var($email,FILTER_VALIDATE_EMAIL)){

                    $errors["email-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Enter a valid E-mail address</p>
                            </div>
                        "
                    );

                }else{

                    if($this->check_uname_email_exists("email",$email)){

                        $errors["email-error"]=array(
                            "error_msg"=>"
                                <div class='ac-form__msg ac-form__msg--error'>
                                    <p class='ac-form__msg--text'>The E-mail is already in used. Try with another one</p>
                                </div>
                            "
                        );
                        
                    }
                }

                
            }elseif($name_attr=="cre-pass"){
            
                if(strlen($input_value) < 8){
                    
                    $errors["cre-pass-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>8 is the minimum length for password</p>
                            </div>
                        "
                    );
                    
                }elseif(strlen($input_value) > 20){

                    $errors["cre-pass-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>20 is the maximum length for password</p>
                            </div>
                        "
                    );
                    
                }elseif(!preg_match("/(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[!@#$%^&*()\-_.]).*/",$input_value)){
                    
                    $errors["cre-pass-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>
                                    Password didn't meet the minimum requirements. <a href='#'>Check requirements?</a>
                                </p>
                            </div>
                        "
                    );
                }
            }elseif($name_attr=="con-pass"){

                //store the create password
                $cre_pass=trim($_POST["cre_pass"]);

                //check if confirm password matchs with create password
                if($cre_pass !== $input_value){

                    $errors["con-pass-error"]=array(
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Passwords did not match</p>
                            </div>
                        "
                    );
                }
            }


            
            if(!empty($errors)){

                echo json_encode($errors);

            }else{

                echo 1;
            }
            
        }
        
        //After validating the form let's create the account
        public function create_account()
        {
            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store the `fname` index from $_POST variable
            $user_fname=trim($_POST["fname"]);

            //store the `lname` index from $_POST variable
            $user_lname=trim($_POST["lname"]);

            //store the `username` index from $_POST variable
            $user_name=trim(strtolower($_POST["username"]));

            //store the `email` index from $_POST variable
            $user_email=trim(strtolower($_POST["email"]));

            //Default status of a Email addresss is `unverified`
            $user_email_status='unverified';

            //store the `cre-pass` index from $_POST variable
            $cre_pass=$_POST["cre-pass"];

            //store the `con-pass` index from $_POST variable
            $con_pass=$_POST["con-pass"];

            //store the acceptable password for database
            $user_password=($cre_pass == $con_pass) ? password_hash($con_pass, PASSWORD_DEFAULT) : "";

            //store the account creatino time
            $user_joining_date=date("d F, Y");
            
            //store the user country
            $user_country="";

            //getting the user's country using the free API
            $get_contry=file_get_contents("http://ip-api.com/json");

            $get_contry_array=json_decode($get_contry,true);

            if($get_contry_array["status"] == "success"){

                $user_country=$get_contry_array["country"];
            }

            //Intially account status will be 0
            $user_account_status=0;
            
            //Every normal user is a creator
            $user_role="creator";

            /**
             * Insert the user's information to 
             * the database
             */
            
            //store the user's model object from $this->model_objs variable
            $user_obj=$this->model_objs["user_obj"];
            
            //store the user_files's model object from $this->model_objs variable
            $ufiles_obj=$this->model_objs["ufiles_obj"];

            //insert user's information to the database
            $insert_user=$user_obj->insert(array(
                "fields"=>array(
                    "user_fname"=>$user_fname,
                    "user_lname"=>$user_lname,
                    "user_name"=>$user_name,
                    "user_email"=>$user_email,
                    "user_email_status"=>$user_email_status,
                    "user_password"=>$user_password,
                    "user_joining_date"=>$user_joining_date,
                    "user_country"=>$user_country,
                    "user_account_status"=>$user_account_status,
                    "user_role"=>$user_role,
                    "user_desc"=>"false"
                )
            ));

            if($insert_user["status"] !== 1 && !isset($insert_user["insert_id"])){

                print_r($insert_user);

                die();
            }   

            /**
             * Insert a Default picture
             * to use as a profile picture
             */

            //username's first letter will be the default picture's name
            $profile_img_name=substr($user_name,0,1);

            //store the profile width and height
            $profile_img_dimension=array(
                "lg"=>array(
                    "width"=>200,
                    "height"=>200
                ),
                "md"=>array(
                    "width"=>150,
                    "height"=>150
                ),
                "sm"=>array(
                    "width"=>100,
                    "height"=>100
                )
            );
            //convert the array into a string to store it into the database
            $profile_img_dimension=serialize($profile_img_dimension);

            //add `\` before the `""` double quotation mark
            $profile_img_dimension=str_replace('"','\"',$profile_img_dimension);


            //insert a default Profile Image for the user
            $insert_profile_img=$ufiles_obj->insert(array(
                "fields"=>array(
                    "ufile_name"=>$profile_img_name,
                    "ufile_ext"=>"jpg",
                    "ufile_usage"=>"profile_img",
                    "ufile_dimension"=>$profile_img_dimension,
                    "user_id"=>$insert_user["insert_id"]
                )
            ));

            if($insert_profile_img["status"] !== 1 && !isset($insert_profile_img["insert_id"])){

                print_r($insert_profile_img);
                die();
            }

            /**
             * Insert a default color
             * to use as Background Image
             */

            //store the background width and height
            $bg_img_dimension=array(
                "lg"=>array(
                    "width"=>1400,
                    "height"=>350
                ),
                "sm"=>array(
                    "width"=>250,
                    "height"=>100
                )
            );
            //convert the array into a string to store it into the database
            $bg_img_dimension=serialize($bg_img_dimension);
            
            //add `\` before the `""` double quotation mark
            $bg_img_dimension=str_replace('"','\"',$bg_img_dimension);
    
            //insert a default Background Image for the user
            $insert_bg_img=$ufiles_obj->insert(array(
                "fields"=>array(
                    "ufile_name"=>"default-bg",
                    "ufile_ext"=>"jpg",
                    "ufile_usage"=>"bg_img",
                    "ufile_dimension"=>$bg_img_dimension,
                    "user_id"=>$insert_user["insert_id"]
                )
            ));

            if($insert_bg_img["status"] !== 1 && !isset($insert_bg_img["insert_id"])){

                print_r($insert_bg_img);
                die();
            }

            /**
             * Generate the a token
             * to validate the E-mail Address
             */

            //set the timezone to Asia/Dhaka
            date_default_timezone_set("Asia/Dhaka");
            
            //store the token model's object from $this->model_objs variable
            $token_obj=$this->model_objs["token_obj"];
            
            //delete any existing token of this user
            $token_obj->delete(array(
                "where"=>"tokens.user_id={$insert_user['insert_id']}"
            ));

            //Generate a selector
            $token_selector=bin2hex(random_bytes(8));
            
            //Generate a validator
            $random_bytes=random_bytes(32);
            
            //token validator for URL
            $url_token_validator=bin2hex($random_bytes);
            
            //token validato to store into the database
            $db_token_validator=password_hash($random_bytes,PASSWORD_DEFAULT);

            //insert the token into the database
            $insert_token=$token_obj->insert(array(
                "fields"=>array(
                    "token_selector"=>"$token_selector",
                    "token_validator"=>"$db_token_validator",
                    "token_expires"=>date("U") + 3600,
                    "token_usage"=>"email_validation",
                    "user_id"=>$insert_user["insert_id"]
                )
            ));

            if($insert_token["status"] !== 1 && !isset($insert_token["insert_id"])){

                print_r($insert_token);
                die();
            }

            /**
             * Send the token to the 
             * register  user
             */

            //send a verification E-mail to the user to verifiy his/her E-mail address
            $verify_email_link=$this->config->domain("verify_email?selector={$token_selector}&validator={$url_token_validator}");

            //Get the verify E-mail template
            $verifiy_email_tmp=file_get_contents($this->config->domain("app/email/email-verification.html"));

            $verify_email_tmp_params=array(
                "{SITE_URL}"=>$this->config->domain(),
                "{VERIFY_LINK}"=>$verify_email_link,
                "{EMAIL_ADDRESS}"=>$user_email
            );

            foreach($verify_email_tmp_params as $param_index=>$param){

                //set verify E-mail template parameters
                $verifiy_email_tmp=str_replace("{$param_index}","$param",$verifiy_email_tmp);
            }

            $send_mail=$this->functions->send_mail($this->mail,array(
                "receiver"=>"{$user_email}",
                "receiver_name"=>"{$user_fname} {$user_lname}",
                "subject"=>"Email Confirmation",
                "body"=>$verifiy_email_tmp,
                "alt_body"=>"Here is your link <a href='{$verify_email_link}' target='_blank'>{$verify_email_link}</a>"
            ));

            if(!$send_mail){

                //mail was not sent
                echo 0;
                
            }else{

                //set $_SESSION veriabled
                session_start();
                $_SESSION["user_id"]=$insert_user["insert_id"];
                $_SESSION["user_type"]="new";

                /**
                 * Send an E-mail to the all admins, 
                 * when a new user register on our website
                 */
                
                //fetch admin's information
                $fetch_admin_info=$user_obj->select(array(
                    "column_name"=>"
                        users.user_fname,
                        users.user_lname,
                        users.user_email
                    ",
                    "where"=>"users.user_role='admin'"
                ));

                if($fetch_admin_info["status"] == 1 &&  $fetch_admin_info["num_rows"] > 0){

                    foreach($fetch_admin_info["fetch_all"] as $index=>$admin_info){

                        //store admin email addresss
                        $admin_email=$admin_info["user_email"];
                        
                        //store admin's first name
                        $admin_fname=$admin_info["user_fname"];
                        
                        //store admin's last name
                        $admin_lname=$admin_info["user_lname"];

                        //send E-mails to all the admins
                        $send_mail=$this->functions->send_mail($mail, array(
                            "receiver"=>"{$admin_email}",
                            "receiver_name"=>"{$admin_fname} {$admin_lname}",
                            "subject"=>"A new user registered on your website - Lobster",
                            "body"=>"
                            
                                <div>
                                    <h3>Register User's Information</h3>
                                    <ul style='list-style:none; margin:0; padding:0;'>
                                        <li>First Name : {$user_fname}</li>
                                        <li>Last Name : {$user_lname}</li>
                                        <li>Username : {$user_name}</li>
                                        <li>E-mail : {$user_email}</li>
                                    </ul>
                                </div>
                            ",

                            "alt_body"=>"
                                <div>
                                    <h3>Register User's Information</h3>
                                    <ul style='list-style:none; margin:0; padding:0;'>
                                        <li>First Name : {$user_fname}</li>
                                        <li>Last Name : {$user_lname}</li>
                                        <li>Username : {$user_name}</li>
                                        <li>E-mail : {$user_email}</li>
                                    </ul>
                                </div>
                            "
                        ));
                    }
                }

                //mail sent
                echo 1;    
            
            }
        }  


        //Accept the login request
        public function login()
        {
            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store the `uname_email` index from $_POST variable
            $uname_email=trim(strtolower($_POST['uname-email']));

            //store the `password` index from $_POST variable
            $password=$_POST['password'];

            //store  all the errors
            $errors=[];

            if(empty($uname_email) || empty($password)){

                //check if the input fields are empty
                if(empty($uname_email)){

                    $errors["uname_email_error"]=array(
                        "target"=>".ac-form__col--uname-email",
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Enter your E-mail or Username address</p>
                            </div>
                        "
                    );

                }
                if(empty($password)){

                    $errors["password_error"]=array(
                        "target"=>".ac-form__col--password",
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Enter your Password</p>
                            </div>
                        "
                    );
                }

            }elseif(filter_var($uname_email,FILTER_VALIDATE_EMAIL)){

                //check if the user is trying to log in using the E-mail Address
                $login_output=$this->logged_in(array(
                    "login_type"=>"email",
                    "uname_email"=>$uname_email,
                    "password"=>$password
                )); 

                
                if($login_output !== true){

                    $errors[$login_output["error_name"]]=$login_output;
                }

            }elseif(preg_match("/^[^0-9\W][a-zA-Z\d\_]+$/",$uname_email)){

                //chcek if the user is trying to log in using the Username
                $login_output=$this->logged_in(array(
                    "login_type"=>"username",
                    "uname_email"=>$uname_email,
                    "password"=>$password
                ));

                if($login_output !== true){

                    $errors[$login_output["error_name"]]=$login_output;

                }

                
            }else{

                //Both Username and E-mail Address are invalid
                $errors["unknown_error"]=array(

                    "target"=>".ac-form__col--uname-email",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>Please enter a valid E-mail or Usename</p>
                        </div>
                    "
                );
            }

            if(!empty($errors)){

                echo json_encode($errors);

            }else{

                echo 1;
            }
            
        }
  
        //use the function to send the reset link 
        public function forgot_password()
        {
            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store the `uname-email` index from $_POST variable
            $uname_email=$_POST["uname-email"];

            //store the user's model object from $this->model_objs
            $user_obj=$this->model_objs["user_obj"];

            //store the token's model object from $this->model_objs
            $token_obj=$this->model_objs["token_obj"];

            //store the request user's all information
            $user_info="";
            
            //store all the erros
            $errors = [];

            $output=array();

            if(empty($uname_email)){

                $errors=array(
                    "target"=>".ac-form__col--uname-email",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>Enter your E-mail address or Username</p>
                        </div>
                    ",
                );

            }elseif(preg_match("/^[^0-9\W][a-zA-Z\d\_]+$/",$uname_email)){


                if($this->check_uname_email_exists("username",$uname_email) == false){

                    $errors=array(
                        "target"=>".ac-form__col--uname-email",
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>Username doesn't exist</p>
                            </div>
                        ",
                    );
                }

            

            }elseif(filter_var($uname_email,FILTER_VALIDATE_EMAIL)){
                
                if($this->check_uname_email_exists("email",$uname_email) == false){

                    $errors=array(
                        "target"=>".ac-form__col--uname-email",
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>E-mail Address doesn't exist</p>
                            </div>
                        ",
                    );
                }

            }else{

                $errors=array(
                    "target"=>".ac-form__col--uname-email",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>please enter a E-mail address or Username</p>
                        </div>
                    ",
                );
            }


            if(!empty($errors)){

                $output=array(
                    "error_status"=>1,
                    "errors"=>array($errors)
                );
            
            }else{

                //fetch requested user's all information from database
                $fetch_user_info=$user_obj->select(array(
                    "column_name"=>"
                        users.user_id,
                        users.user_fname,
                        users.user_lname,
                        users.user_email
                    ",
                    "where"=>"users.user_name='{$uname_email}' OR users.user_email='{$uname_email}'"
                ));
            
                if($fetch_user_info["status"] == 1 && $fetch_user_info["num_rows"] == 1){

                    //set the $user_info to fetched user_info
                    $user_info=$fetch_user_info["fetch_all"][0];
                }

                
                if(!empty($user_info)){

                    /**
                     * let's create a token 
                     * to reset the pasword 
                     */

                    //create a random hex number for the token selector
                    $token_selector=bin2hex(random_bytes(8));
                    
                    //create a random hex number for the url and db validator
                    $random_bytes=random_bytes(32);

                    //encrypte the random bytes to store into the database
                    $db_token_validator=password_hash($random_bytes,PASSWORD_DEFAULT);

                    //convert the random byte into a hex number to pass value in the URL
                    $url_token_validator=bin2hex($random_bytes);

                    //first delete any existing token of user
                    $token_obj->delete(array(
                        "where"=>"tokens.user_id={$user_info['user_id']} AND tokens.token_usage='password_reset'"
                    ));

                    //insert the token into the database 
                    $insert_token=$token_obj->insert(array(
                        "fields"=>array(
                            "token_selector"=>$token_selector,
                            "token_validator"=>$db_token_validator,
                            "token_expires"=>date("U") + 3600,
                            "token_usage"=>"password_reset",
                            "user_id"=>"{$user_info['user_id']}"
                        )
                    ));

                    if($insert_token["status"] !== 1 && !isset($insert_token["inser_id"])){

                        //something went wrong 
                        $output=array(
                            "error_status"=>100,
                            "error"=>$insert_token
                        );

                    }else{

                        //successfully inser the token to the database. Now send it to the user
                        $send_mail=$this->functions->send_mail($this->mail,array(
                            "receiver"=>"{$user_info['user_email']}",
                            "receiver_name"=>"{$user_info['user_fname']} {$user_info['user_lname']}",
                            "subject"=>"Forgot Password Confirmation",
                            "body"=>" 
                                <div>
                                    <p>
                                        Hi <b>{$user_info['user_fname']}</b> <br>
                                        A request has been made to reset you password on <a href='{$this->config->domain()}' target='_blank'>Lobster</a>.
                                        if you have made the requst to reset your password, then click on link below. Otherwise <a href='#'>click here</a> to prevent the request.<br>
                                        <b>Link:</b> 
                                        <a href='{$this->config->domain("accounts/reset_password?selector={$token_selector}&validator={$url_token_validator}")}'>
                                            {$this->config->domain("accounts/reset_password?selector={$token_selector}&validator={$url_token_validator}")}
                                        </a>
                                    </p>
                                </div>    
                            ",
                            "alt_body"=>"
                                <div>
                                    <p>
                                        Hi <b>{$user_info['user_fname']}</b> <br>
                                        A request has been made to reset you password on <a href='{$this->config->domain()}' target='_blank'>Lobster</a>.
                                        if you have made the requst to reset your password, then click on link below. Otherwise <a href='#'>click here</a> to prevent the request.<br>
                                        <b>Link:</b> 
                                        <a href='{$this->config->domain("accounts/reset_password?selector={$token_selector}&validator={$url_token_validator}")}'>
                                            {$this->config->domain("accounts/reset_password?selector={$token_selector}&validator={$url_token_validator}")}
                                        </a>
                                    </p>
                                </div>  
                            "
                        ));

                        if(!$send_mail){

                            $output=array(
                                "error_status"=>200,
                                "link"=>"{$this->config->domain("accounts/reset_password?selector={$token_selector}&validator={$url_token_validator}")}"
                            );

                        }else{

                            $output=array(
                                "error_status"=>0,
                                "link"=>"{$this->config->domain("accounts/reset_password?selector={$token_selector}&validator={$url_token_validator}")}"
                            );
                        }

                    }
                }
            }


            echo json_encode($output);

        
        }

        

        //use the function to reset the password after getting the forget link
        public function reset_password()
        {

            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store the `selector` index from $_POST variable
            $token_selector=$_POST["selector"];

            //store the token info
            $token_info=array();
            
            //store the `cre-pass` index from $_POST variable
            $cre_pass=$_POST["cre-pass"];
            
            //store the `con-pass` index from $_POST variable
            $con_pass=$_POST["con-pass"];

            //store all the erros
            $errors=array();

            //store the final output to return
            $output=array();

            //store the user's model object from $this->model_objs
            $user_obj=$this->model_objs["user_obj"];

            //store the token's model object from $this->model_objs
            $token_obj=$this->model_objs["token_obj"];

            //fetch token information
            $fetch_token_info=$token_obj->select(array(
                "where"=>"tokens.token_selector='{$token_selector}'"
            ));
            
            if($fetch_token_info["status"] == 1 && $fetch_token_info["num_rows"] == 1){
                
                //set $token_info variable
                $token_info=$fetch_token_info["fetch_all"][0];
            }


            if(empty($cre_pass) || empty($con_pass)){

                if(empty($cre_pass)){

                    $errors["cre-pass"]=array(
                        "target"=>".ac-form__col--cre-pass",
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>This field is required</p>
                            </div>
                        ",
                    );

                }

                if(empty($con_pass)){

                    $errors["con-pass"]=array(
                        "target"=>".ac-form__col--con-pass",
                        "error_msg"=>"
                            <div class='ac-form__msg ac-form__msg--error'>
                                <p class='ac-form__msg--text'>This field is required</p>
                            </div>
                        ",
                    );
                }


            }elseif(strlen($cre_pass) < 8){


                $errors["cre-pass"]=array(
                    "target"=>".ac-form__col--cre-pass",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>8 is the minimum length for the password</p>
                        </div>
                    ",
                );

            }elseif(strlen($cre_pass) > 20){
                
                $errors["cre-pass"]=array(
                    "target"=>".ac-form__col--cre-pass",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>20 is the maximum length for the password</p>
                        </div>
                    ",
                );
                
            }elseif(!preg_match("/(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[!@#$%^&*()\-_.]).*/",$cre_pass)){

                $errors["cre-pass"]=array(
                    "target"=>".ac-form__col--cre-pass",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>Password didn't meet the minimum requirements.</p>
                        </div>
                    ",
                );
                
            }elseif($this->password_match_with_prev($token_info["user_id"],$cre_pass)){
                
                $errors["cre-pass"]=array(
                    "target"=>".ac-form__col--cre-pass",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>You have already used the password. Choose a unique one</p>
                        </div>
                    ",
                );

            }elseif($cre_pass !== $con_pass){

                $errors["con-pass"]=array(
                    "target"=>".ac-form__col--con-pass",
                    "error_msg"=>"
                        <div class='ac-form__msg ac-form__msg--error'>
                            <p class='ac-form__msg--text'>Passwords did not match</p>
                        </div>
                    ",
                );

            }                          


            if(!empty($errors)){

                $output=array(
                    "error_status"=>1,
                    "errors"=>$errors
                );

            }else{

                /**
                 * let's update the password
                 */
                $db_password=password_hash($cre_pass,PASSWORD_DEFAULT);

                //update the password
                $update_pass=$user_obj->update(array(
                    "fields"=>array(
                        "user_password"=>$db_password
                    ),
                    "where"=>"users.user_id={$token_info['user_id']}"
                ));

                if($update_pass["status"] == 1 && $update_pass["affected_rows"] == 1){

                    //password updated successfully. Now you can delete the tokens
                    $token_obj->delete(array(
                        "where"=>"tokens.token_id={$token_info['token_id']}"
                    ));

                    $output=array(
                        "error_status"=>0,
                    );

                }else{

                    $output=array(
                        "error_status"=>100,
                        "errors"=>$update_pass
                    );

                }
            
            }

            echo json_encode($output);
        }

    /**
     * ===========================
     * All Public functions ends
     * ===========================
     */

}

?>

