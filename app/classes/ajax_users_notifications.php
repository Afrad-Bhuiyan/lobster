<?php 

/*
 * 1. The class `ajax_users _posts` will be used to store all the function and variable
 *    fo dasboards post options
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_users_notifications{

    //store the config class object
    private $config;
        
    //store the functions class object
    private $functions;
    
    //store the PHPMailer class object
    private $mail;
    
    //Here we will store all the required model's object
    private $model_objs=array();

    //store logged user's information
    private $user_info;

    /**
     * =============================
     * All magic functions  starts 
     * =============================
     */
    
    public function __construct($objs)
    {

        if($objs !== null){
            
            //store the `config` class from $objs variable
            $this->config=$objs["config"];
            
            //store the `functions` class from $objs variable
            $this->functions=$objs["functions"];
            
            //store the `model_objs` class from $objs variable
            $this->model_objs=$objs["model_objs"];
            
            //store the `mail class from $thie variable
            $this->mail=$objs["mail"];

            //store the `mail class from $thie variable
            $this->user_info = $this->logged_user_info($_SESSION["user_id"]);
        }
    }



    /**
     * =============================
     * All private functions  starts 
     * =============================
     */
       


   


    /**
     * ===========================
     * All private functions  ends 
     * ===========================
     */


    /**
     * ===========================
     * All Public functions starts
     * ===========================
     */


      

     
       

        public function ab(){


            echo "I am ab from from ajax_users notifications";
            
        }

    /**
     * =========================
     * All Public functions ends
     * =========================
     */
  
}



?>
