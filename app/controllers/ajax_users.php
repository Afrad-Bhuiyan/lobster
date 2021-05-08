<?php 

/*
 * 1. users controller's all ajax reqeust will be controlled 
 *   from this `ajax_users` controller classs.
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_users extends ajax_controller {

    //store the config class object
    private $config;
    
    //store the functions class object
    private $functions;
    
    //store the PHPMailer class object
    private $mail;
    
    //Here we will store all the required model's object
    private $model_objs=array();

    //Here we will store class parameter from URL
    private $class;
    
    //Here we will store method parameter from URL
    private $method;


    public function __construct($user_name)
    {

        //validate the $_GET variable
        $_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);

        if($_SERVER["REQUEST_METHOD"] !== "POST"){

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

            //store the PHPMailer class object
            $this->mail= new PHPMailer;

            //set the $this->class's value to $_GET["class"]
            $this->class=isset($_GET["class"]) ? $_GET["class"] : null;
            
            //set the $this->method's value to $_GET["method"]
            $this->method=isset($_GET["class"]) ? $_GET["method"] : null;

            //store all the models
            $this->model_objs=array(
                "user_obj"=>$this->model("user"),
                "ufile_obj"=>$this->model("user_files"),
                "nf_obj"=>$this->model("notification"),
                "post_obj"=>$this->model("post"),
                "post_files"=>$this->model("post_files"),
                "pr_obj"=>$this->model("post_rating"),
                "comment_obj"=>$this->model("comment"),
                "cr_obj"=>$this->model("comment_replies"),
                "sp_obj"=>$this->model("saved_post"),
                "token_obj"=>$this->model("token")
            );

            //check if user name exists and user is logged in
            if($this->if_exist_username($user_name) == false || $this->if_user_logged_in() == false){

                echo "You can't request to this URL {$user_name}";
                die();

            }elseif($this->class == null || $this->method == null){
            
                echo "class and method query strings are required";
                die();

            }else{  

                /**
                 * Now, we are going to include class 
                 * for diffrent dashboard methods
                 */

                //store the options to pass while including the class
                $class_options=array(
                    "model_objs"=>$this->model_objs,
                    "config"=>$this->config,
                    "functions"=>$this->functions,
                    "mail"=>$this->mail
                );

                //store the sub class object
                 $class_obj=$this->load_class(__CLASS__."_{$this->class}", $class_options);

                //check wheather method exists or not 
                if(!method_exists($class_obj,$this->method)){
                    
                    //stop the code and return an error 
                    echo "method '{$this->method}' doesn't exist";
                    die();

                }else{
                
                    //call the method
                   $class_obj->{$this->method}();
                }
            }
        }
    }  

    //check if the username is existing
    private function if_exist_username($username)
    {

        $user_obj=$this->model_objs['user_obj'];

        $output=$user_obj->select(array(
            "where"=>"users.user_name='{$username}'"
        ));

        if($output["status"] == 1){

            if($output["num_rows"] == 1 && $output["fetch_all"][0]["user_name"] == $username){
                
                return true;
                
            }else{
                
                return false;
            }
        
        }else{

            return $output["error"];
        }

    }

    //check if the username is logged in
    private function if_user_logged_in()
    {
            
        session_start();

        if(!empty($_SESSION)){

            if(isset($_SESSION["user_id"]) && isset($_SESSION["user_type"])){
                
                return true;

            }else{

                return false;
            }

        }else{

            return false;
        }
    }


    //index method is the default method
    public function index($user_name)
    {

       


    }


    //use the function to load notification of a user
    public function load_notifications()
    {

        //first validate the $_POST variable
        $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

        //Store the final output
        $output="";

        //store the to_user_id index from $_POST variable
        $to_user_id=$_POST["to_user_id"];
        
        //Here we will store all the notifications which will be fetched based on $to_user_id
        $notifications=[];

        //include the notification model
        $nf_obj=$this->model_objs['nf_obj'];

        //First update notification nf_status to read
        $nf_obj->update(array(
            "fields"=>array(
                "nf_status"=>"read"
            ),
            "where"=>"to_user_id={$to_user_id}"
        ));

        //Here we will store total notifications number
        $total_notifications=0;
        
        //fetch all notification number
        $fetch_all_nf=$nf_obj->select(array(
            "where"=>"notifications.to_user_id={$to_user_id}"
        )); 

        if($fetch_all_nf["status"] == 1  && $fetch_all_nf["num_rows"] > 0){

            //set $total_notifications  value to fetch num_rows value
            $total_notifications = $fetch_all_nf["num_rows"];
        }
        

        //fetch all notifications
        $fetch_notifications=$nf_obj->select(array(
            "column_name"=>"
                notifications.nf_id,
                notifications.nf_title,
                notifications.nf_date,
                notifications.from_user_id,
                notifications.to_user_id,
                notifications.nf_status,
                users.user_name,
                user_files.ufile_name,
                user_files.ufile_ext,
                posts.post_id,
                posts.post_title,
                posts.post_link,
                post_files.pfile_name,
                post_files.pfile_ext
                
            ",
            "join"=>array(
                "users"=>"users.user_id = notifications.from_user_id",
                "user_files"=>"user_files.user_id = notifications.from_user_id",
                "posts"=>"posts.post_id = notifications.post_id",
                "post_files"=>"post_files.post_id = notifications.post_id"

            ),
            "order"=>array(
                "column"=>"nf_id",
                "type"=>"DESC",
            ),
            "limit"=>"20",
            "where"=>"to_user_id={$to_user_id}"
        ));

        if($fetch_notifications["status"] == 1 && $fetch_notifications["num_rows"] > 0){
            
            //create a new index and store existing notifications
            $notifications["existing_notification"]= $total_notifications - $fetch_notifications["num_rows"];
            
            foreach($fetch_notifications["fetch_all"] as $index=>$value){

                //create a new index name  `notifications` and store all the fetched result
                $notifications["notifications"][$index]=$value;
                
                //create a new index inside `notification` array and store from_user_info
                $notifications["notifications"][$index]["from_user_info"]=array(
                    "user_id"=>$notifications["notifications"][$index]["from_user_id"],
                    "user_name"=>$notifications["notifications"][$index]["user_name"],
                    "ufile_name"=>$notifications["notifications"][$index]["ufile_name"],
                    "ufile_ext"=>$notifications["notifications"][$index]["ufile_ext"]
                );
                
                //create a new index inside `notification` array and store post_info
                $notifications["notifications"][$index]["post_info"]=array(
                    "post_id"=>$notifications["notifications"][$index]["post_id"],
                    "post_title"=>$notifications["notifications"][$index]["post_title"],
                    "post_link"=>$notifications["notifications"][$index]["post_link"],
                    "pfile_name"=>$notifications["notifications"][$index]["pfile_name"],
                    "pfile_ext"=>$notifications["notifications"][$index]["pfile_ext"]
                );
            }
        }


        if(!empty($notifications)){


            $output .="
                <div class='navbar__dropdown-header navbar__dropdown-header--nf'>
                    <h5>Notifications</h5>
                </div>

                <div class='navbar__dropdown-body navbar__dropdown-body--nf'>
            ";


            foreach($notifications["notifications"] as $notifications_index=>$notification):
                
                $from_user_info=$notification["from_user_info"];
                $post_info=$notification["post_info"];

                $nf_date=str_replace(", ", "-", $notification['nf_date']);
                $nf_date=str_replace(" ","-",$nf_date);
                $nf_date_formated=str_replace("_"," ",$nf_date);

                $output .="
                    <a class='navbar__dropdown-snf' href='{$this->config->domain("posts?v={$post_info['post_link']}")}' target='_blank'>
                        <img class='navbar__img navbar__img--user' src='{$this->config->domain("app/uploads/users/{$from_user_info['ufile_name']}-sm.{$from_user_info['ufile_ext']}")}' alt=\"{$from_user_info['user_name']}'s profile picture on lobster\">
                        
                        <div class='navbar__dropdown-snf-text'>
                            <h6 class='navbar__dropdown-snf-title'>
                                {$notification['nf_title']}
                            </h6>
                            <span class='navbar__dropdown-snf-time'>{$this->functions->get_time_in_ago($nf_date_formated)}</span>
                        </div>

                        <img class='navbar__img navbar__img--postimg' src='{$this->config->domain("app/uploads/posts/{$post_info['pfile_name']}-sm.{$post_info['pfile_ext']}")}' alt='{$post_info['post_title']}'>
                    </a>
                ";

            endforeach;
            
            $output .="
                </div>
            ";
            
            if($notifications["existing_notification"] > 0){

                $output .= "
                    <div class='navbar__dropdown-footer'>
                        <a class='navbar__link navbar__link--dfl' href='{$this->config->domain("users/{$_SESSION['user_name']}/notifications")}' target='_blank'>View more({$notifications["existing_notification"]})</a>
                    </div>
            
                ";
            }


        }else{

            $output .="
                <div class='navbar__dropdown-header navbar__dropdown-header--nf'>
                    <h5>Notifications</h5>
                </div>

                <div class='navbar__dropdown-body navbar__dropdown-body--nf'>
                    <div class='navbar__dropdown-msg navbar__dropdown-msg--notfound'>
                        <p>
                            <i class='fa fa-info-circle'></i>
                            <span>All Notifications Will appear here.</span>
                        </p>
                    </div>
                </div>
            "; 
        }

        echo $output;

    }

     //use the function to update the page title & notification badge when clicks on notification bell button
    public function update_title_tag_and_bell()
    {

        //first validate the post variable
        $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);
           
        //store the to_user_id
        $to_user_id=$_POST['to_user_id'];
        
        //store the title tag text
        $title_tag=$_POST['title_tag'];
        
        //first find `(0-9)*` pattern from $title_tag
        preg_match_all("/\([0-9]*\)/i",$title_tag,$nf_num);

        if(isset($nf_num[0][0])){
            
            //replace the `(0-9)*` pattern with an empty string
            $title_tag=str_replace("{$nf_num[0][0]}","",$title_tag);

            //remove space from left and right sides
            $title_tag=trim($title_tag);
        }
        
       //store the notification's model object
       $nf_obj=$this->model_objs["nf_obj"];
       
       //store the final output 
       $output=[];

       /*First fetch all unread notfication with bell button*/
       $nf_obj_output=$nf_obj->select(array(
           "where"=>"to_user_id={$to_user_id} AND nf_status='unread'"
       ));

       if($nf_obj_output["status"] == 1){
           
           if($nf_obj_output["num_rows"] > 0){
       
               $nf_badge_num=($nf_obj_output["num_rows"] > 9) ? "9+" : $nf_obj_output["num_rows"];

               $output["bell_icon"]="
                   <a class='navbar__link navbar__link--bell' role='button'>
                       <i class='fa fa-bell ph-nav__btn-icon'></i>
                       <span class='ph-nav__btn-badge ph-nav__btn-badg--nf'>{$nf_badge_num}</span>
                   </a>
               ";

               $output["title_tag"]="({$nf_obj_output['num_rows']}) $title_tag";
           

           }else{

               $output["bell_icon"]="
                    <a class='navbar__link navbar__link--bell' role='button'>
                        <i class='fa fa-bell ph-nav__btn-icon'></i>
                    </a>
               ";

               $output["title_tag"]="$title_tag";
           }

          
           
       }else{

           echo $nf_obj_output["error"];
           die();
       }

       echo json_encode($output);

    }


 
}

?>

