<?php 

    
    class verify_email extends controller{

        private $config;
        private $functions;

        //Here we will store all the required model's object
        private $model_objs=array();

        public function __construct()
        {

            if($_SERVER["REQUEST_METHOD"] !== "GET"){

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

                //store all the models
                $this->model_objs["user_obj"]=$this->model("user");
                $this->model_objs["token_obj"]=$this->model("token");

            }
        }

        public function index(){

            //validate the $_GET variable
            $_GET=filter_var_array($_GET,FILTER_SANITIZE_STRING);

            //store the selector $_GET variable
            $token_selector=(isset($_GET["selector"])) ? $_GET["selector"] : "";
            
            //conver the the validator to the random_bytes
            $url_token_validator=(isset($_GET["validator"])) ? hex2bin($_GET["validator"]) : "";
            
            //store the token_user_id user_id 
            $user_id="";

            //store the validator token from database
            $db_token_validator="";
            
            //store token expire date
            $token_expires="";

            //store the token model's object
            $token_obj=$this->model_objs["token_obj"];
            
            //fetch E-mail validation token using $token_selector
            $fetch_token=$token_obj->select(array(
                "column_name"=>"
                    tokens.token_validator,
                    tokens.token_expires,
                    tokens.user_id
                ",
                "where"=>"tokens.token_usage='email_validation' AND tokens.token_selector='{$token_selector}'"
            ));

            if($fetch_token["status"] == 1 && $fetch_token["num_rows"] == 1){

                //set the $db_token_validator to fetched token validator
                $db_token_validator=$fetch_token["fetch_all"][0]["token_validator"];
                
                //set the $token_expires to fetch token expires
                $token_expires=$fetch_token["fetch_all"][0]["token_expires"];
                
                //set the $token_expires to fetch token expires
                $user_id=$fetch_token["fetch_all"][0]["user_id"];

            }else{
                
                echo "<h2>Invalid URL</h2>";
                
                die();
            }

            //set the timezone to Asia/Dhaka
            date_default_timezone_set("Asia/Dhaka");

            //store the current time stamp
            $current_time=date("U");
            
            //store the difference between the current time and token expire time
            $time_diff=$token_expires - $current_time;
            
            //check if time difference reaches 0 or less than 
            if($time_diff <= 0){
                
                echo "Link has been expired";

                die();
            }

            //now match $url_token_validator and $db_token_validator
            if(password_verify($url_token_validator,$db_token_validator)){

                /**
                 * update the user_email_status 
                 * and user_account_status 
                 */

                //store the user model's object
                $user_obj=$this->model_objs["user_obj"];

                $update_info=$user_obj->update(array(
                    "fields"=>array(
                        "user_email_status"=>"verified",
                        "user_account_status"=>1
                    ),
                    "where"=>"users.user_id=$user_id"
                ));

                if($update_info["status"] == 1 && $update_info["affected_rows"] == 1){

                    //after update the account status let's delete the token
                    $delete_token=$token_obj->delete(array(
                        "where"=>"tokens.user_id={$user_id} AND token_usage='email_validation'"
                    ));

                    if($delete_token["status"] == 1 && $delete_token["affected_rows"] == 1){

                        echo "Successfuly Verified Your E-mail Address. Now you can close the tab";
                    }
            
                    
                }else{

                    echo "Something went wrong. <a href='#'>Resend</a> the varification link";
                }
            

            }else{

                echo "<h2>Invalid URL</h2>";
            }


        }
    }


?>