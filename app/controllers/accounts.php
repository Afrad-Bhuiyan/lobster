<?php 

    

    class accounts extends controller{

        //store the config class object
        private $config;

        //store the functions class object
        private $functions;
                
        //store all the required model's object
        private $model_objs=array();
        
        //store all the information for passing to the view
        private $data=array();
    
        public function __construct()
        {

            if($_SERVER["REQUEST_METHOD"] !== "GET"){

                //store the funcitons obj
                $this->functions=new functions;
                
                //call the not_found method
                $this->functions->not_found();  
                
                die();

            }else{
                
                 //store the config obj
                 $this->config=new config;
    
                //store the funcitons obj
                $this->functions=new functions;

                //store all the models
                $this->model_objs["user_obj"]=$this->model("user");
                $this->model_objs["token_obj"]=$this->model("token");
                
                session_start();

                //if user tries to access the controller after logging in then redirect him to the home page
                if($this->functions->if_user_logged_in()){

                    header("Location:{$this->config->domain()}");
                }

            }
        }

        
        //Default method
        public function index(){

            //if user tries to access only the controller. the redirect him to the login page
            header("Location:{$this->config->domain('accounts/login')}");
        }


        //View the signup form
        public function signup(){
            
            $this->data["title_tag"]="Lobster | Accounts | Signup";
        
            $this->view("accounts/signup",$this->data);
        }

        //view the login form
        public function login(){
            
            $this->data["title_tag"]="Lobster | Accounts | Login";
            
            $this->view("accounts/login",$this->data);
        }


        //view the forgot password form
        public function forgot_password(){

            $this->data["title_tag"]="Lobster | Accounts | Forgot Password";

            $this->view("accounts/forgot_password",$this->data);
        }


        //view the reset password form
        public function reset_password(){
            
             //validate the $_GET variable
             $_GET=filter_var_array($_GET,FILTER_SANITIZE_STRING);

             if(!isset($_GET["selector"])  || !isset($_GET["validator"])){

                header("Location:{$this->config->domain()}");
                die();                
             }

             //store the selector $_GET variable
             $token_selector=(isset($_GET["selector"])) ? $_GET["selector"] : "";
             
             //conver the the validator to the random_bytes
             $url_token_validator=(isset($_GET["validator"])) ? hex2bin($_GET["validator"]) : "";
        
             //store all information related to the token
             $token_info=array();
 
             //store the token model's object
             $token_obj=$this->model_objs["token_obj"];
             
             //fetch E-mail validation token using $token_selector
             $fetch_token=$token_obj->select(array(
                 "column_name"=>"
                     tokens.token_validator,
                     tokens.token_expires,
                     tokens.user_id
                 ",
                 "where"=>"tokens.token_usage='password_reset' AND tokens.token_selector='{$token_selector}'"
             ));
 
             if($fetch_token["status"] == 1 && $fetch_token["num_rows"] == 1){
 
                $token_info=$fetch_token["fetch_all"][0];

             }else{
                 
                 echo "<h2>The URL is no longer available</h2>";

                 die();
             }
 

             if(!empty($token_info)){
                 
                    //set the timezone to Asia/Dhaka
                    date_default_timezone_set("Asia/Dhaka");
        
                    //store the current time stamp
                    $current_time=date("U");
                    
                    //store the difference between the current time and token expire time
                    $time_diff=$token_info["token_expires"] - $current_time;
                
                    //check if time difference reaches 0 or less than 
                    if($time_diff <= 0){

                        echo "The link has already been expired";

                        die();
                    }

                     //now match $url_token_validator and $db_token_validator
                    if(password_verify($url_token_validator,$token_info["token_validator"])){
        
                        /**
                         * update the user_email_status 
                         * and user_account_status 
                         */

                        $this->data["title_tag"]="Lobster | Accounts | Reset Password";

                        $this->view("accounts/reset_password",$this->data);

                    }else{

                        $this->data["title_tag"]="Lobster | Invalid URL";

                        echo "<h2>Invalid URL</h2>";
                    }

                    
             }

 
            

            
           
        }


        //logout the user
        public function logout(){

            session_start();

            session_reset();
            
            if(session_destroy()){
                
                header("Location: {$this->config->domain('accounts/login')}");

            }else{

                echo "
                    <p>Please try again. Go Back To <a href='{$this->config->domain()}'>Home</a></p>
                ";
            }
        }

    }

?>