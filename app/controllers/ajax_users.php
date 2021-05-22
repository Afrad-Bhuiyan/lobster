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

    //store logged user's information
    private $user_info = array();


    
    /**
     * =============================
     * All magic functions  starts 
     * =============================
     */
    
        public function __construct($user_name = null, $method = null)
        {

            //validate the $_GET variable
            $_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);

            if($_SERVER["REQUEST_METHOD"] !== "POST"){

                //store the funcitons obj
                $this->functions=new functions;
            
              
                //if method doesn't exist, show the 404 error page
                $this->functions->error_pages()->error_404();
                
                die();
                
            }else{
                
                //store the config obj
                $this->config=new config;

                //store the funcitons obj
                $this->functions=new functions;

                //store the PHPMailer class object
                $this->mail= new PHPMailer;

                //store all the models
                $this->model_objs=array(
                    "user_obj"=>$this->model("user"),
                    "ufile_obj"=>$this->model("user_files"),
                    "nf_obj"=>$this->model("notification"),
                    "post_obj"=>$this->model("post"),
                    "pfile_obj"=>$this->model("post_files"),
                    "pr_obj"=>$this->model("post_rating"),
                    "comment_obj"=>$this->model("comment"),
                    "reply_obj"=>$this->model("reply"),
                    "sp_obj"=>$this->model("saved_post"),
                    "token_obj"=>$this->model("token")
                );


                //check if user name exists and user is logged in
                if($this->if_exist_username($user_name) == false || $this->if_user_logged_in() == false){

                    echo "You can't request to this URL {$user_name}";
                    die();

                }else{

                    //store the logged user' info in $this->user_info variable
                    $this->user_info=$this->logged_user_info($_SESSION["user_id"]);

                }
                
                if($method == "index"){

                    /**
                     * Now, we are going to include class 
                     * for diffrent dashboard methods
                     */
                
                    //set the $this->class's value to $_GET["class"]
                    $class=isset($_GET["class"]) ? $_GET["class"] : null;
                    
                    //set the $this->method's value to $_GET["method"]
                    $method=isset($_GET["method"]) ? $_GET["method"] : null;

                    if($class == null || $method == null){
                
                        echo "To request to the index method `<strong>class</strong>` and `<strong>method</strong>` query strings are required";

                        die();
                    }
                
                    //store the options to pass while including the class
                    $class_options=array(
                        "model_objs"=>$this->model_objs,
                        "config"=>$this->config,
                        "functions"=>$this->functions,
                        "mail"=>$this->mail,
                        "user_info"=>$this->user_info
                    );

                    //store the sub class object
                    $class_obj=$this->load_class(__CLASS__."_{$class}", $class_options);

                    //check wheather method exists or not 
                    if(!method_exists($class_obj,$method)){
                        
                        //stop the code and return an error 
                        echo "method '{$method}' doesn't exist";
                        die();

                    }else{
                    
                        //call the method
                        $class_obj->$method();
                    }

                }
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

        //use the function for fetching 'profile' and `bg` image of a single user
        private function fetch_user_files($user_id,$ufile_usage)
        {

            $output = array();

            //store the user_files's model from $this->model_objs variable
            $ufile_obj=$this->model_objs["ufile_obj"];

            $fetch_file=$ufile_obj->select(array(
                "where"=>"user_files.user_id={$user_id} AND user_files.ufile_usage='{$ufile_usage}'"
            ));

            if($fetch_file["status"] == 1 && $fetch_file["num_rows"] == 1){

                $fetched_file=$fetch_file["fetch_all"][0];
                
                $output=array(
                    "name"=>$fetched_file["ufile_name"],
                    "ext"=>$fetched_file["ufile_ext"],
                    "status"=>$fetched_file["ufile_status"],
                    "dimension"=>unserialize($fetched_file["ufile_dimension"])
                );
            }

            return $output;
        }
        
        //use the function for fetching 'profile' and `bg` image of a single user
        private function fetch_post_files($post_id,$pfile_usage)
        {

            $output = array();

            //store the user_files's model from $this->model_objs variable
            $pfile_obj=$this->model_objs["pfile_obj"];

            $fetch_file=$pfile_obj->select(array(
                "where"=>"post_files.post_id={$post_id} AND post_files.pfile_usage='{$pfile_usage}'"
            ));

            if($fetch_file["status"] == 1 && $fetch_file["num_rows"] == 1){

                $fetched_file=$fetch_file["fetch_all"][0];
                
                $output=array(
                    "name"=>$fetched_file["pfile_name"],
                    "ext"=>$fetched_file["pfile_ext"],
                    "status"=>$fetched_file["pfile_status"],
                    "dimension"=>unserialize($fetched_file["pfile_dimension"])
                );
            }

            return $output;
        }
        
        //user the function to fetch logged user's information
        private function logged_user_info($user_id)
        {


            //store store the final output
            $output = array();

            //store the user's model from $this->model_objs variable
            $user_obj=$this->model_objs["user_obj"];

            //fetched logged user's other information
            $fetch_user_info=$user_obj->select(array(
                "column_name"=>"
                    users.user_id,
                    users.user_name,
                    users.user_email,
                    users.user_email_status,
                    users.user_role
                ",
                "where"=>"users.user_id={$_SESSION["user_id"]}"
            ));

            if($fetch_user_info["status"] == 1 && $fetch_user_info["num_rows"] == 1){

                $output = $fetch_user_info["fetch_all"][0];

                $output["user_type"] = $_SESSION["user_type"];

                $output["ufile_info"] =array(
                    "profile_img"=>$this->fetch_user_files($_SESSION["user_id"],"profile_img"),
                    "bg_img"=>$this->fetch_user_files($_SESSION["user_id"],"bg_img")
                );
            }

            return $output;
        }

        //use the function to fetch logged user's notification related information
        private function fetch_nf_info($user_id = null)
        {

            //Default outout
            $output = array(
                "total"=>0,
                "read"=>0,
                "unread"=>0
            );

            //include the notification model
            $nf_obj=$this->model_objs['nf_obj'];

            //fetch total notifications
            $fetch_total_nf=$nf_obj->select(array(
                "column_name"=>"COUNT(*) AS unread",
                "where"=>"notifications.to_user_id={$user_id}"
            ));

            if($fetch_total_nf["status"] == 1 && $fetch_total_nf["num_rows"] == 1){

                //set the `total` index value to fetched total notifications
                $output["total"] = $fetch_total_nf["fetch_all"][0]["unread"];
            }
                
            //fetch read notifications
            $fetch_read_nf=$nf_obj->select(array(
                //`READ` is keyword in SQL Query. so we useed `aread` instead of using read
                "column_name"=>"COUNT(*) AS aread",
                "where"=>"notifications.to_user_id={$user_id} AND notifications.nf_status='read'"
            ));
        
            if($fetch_read_nf["status"] == 1 && $fetch_read_nf["num_rows"] == 1){
            
                //set the `unread` index value to fetched unread notifications
                $output["read"] = $fetch_read_nf["fetch_all"][0]["aread"];

            }

            //fetch unread notifications
            $fetch_unread_nf=$nf_obj->select(array(
                "column_name"=>"COUNT(*) AS unread",
                "where"=>"notifications.to_user_id={$user_id} AND notifications.nf_status='unread'"
            ));
        
            if($fetch_unread_nf["status"] == 1 && $fetch_unread_nf["num_rows"] == 1){
                
                //set the `unread` index value to fetched unread notifications
                $output["unread"] = $fetch_unread_nf["fetch_all"][0]["unread"];
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
    
        //index method is the default method
        public function index($user_name){

        }

        //use the funtion to fetch notifications when clicked on bell icon
        public function fetch_notifications()
        {

            $output = "
                <div class='navbar__dropdown-header navbar__dropdown-header--nf'>
                    <h5>Notifications</h5>
                </div>
            ";

            //logged users's user_id will be to_user_id
            $to_user_id=$this->user_info["user_id"];
          
            //include the notification model
            $nf_obj=$this->model_objs['nf_obj'];

            //fetch notification related info such as total `read`, `unread`, 'total_nf' number
            $nf_info=$this->fetch_nf_info($this->user_info['user_id']);

            $notifications = array(
                "total_nf"=>$nf_info["total"],
                "existing_nf"=>0,
                "all"=>array()
            );

            //First update notification nf_status to read
            $nf_obj->update(array(
                "fields"=>array(
                    "nf_status"=>"read"
                ),
                "where"=>"notifications.to_user_id={$to_user_id} AND notifications.nf_status='unread'"
            ));
        
            //limit to show notification 
            $limit=20;

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
                    posts.post_id,
                    posts.post_title,
                    posts.post_link
                ",
                "order"=>array(
                    "column"=>"notifications.nf_id",
                    "type"=>"DESC",
                ),
                "limit"=>"{$limit}",
                "join"=>array(
                    "users"=>"users.user_id = notifications.from_user_id",
                    "posts"=>"posts.post_id = notifications.post_id"
                ),
                
                "where"=>"notifications.to_user_id={$to_user_id}"
            ));


            if($fetch_notifications["status"] == 1 && $fetch_notifications["num_rows"] > 0){

                //calculate existing notifications 
                $notifications["existing_nf"]  = $notifications["total_nf"] - $fetch_notifications["num_rows"];
                

                foreach($fetch_notifications["fetch_all"] as $nf_index=>$notification){

                    $notifications["all"][]=array(
                        "nf_id"=>$notification["nf_id"],
                        "nf_title"=>$notification["nf_title"],
                        "nf_date"=>$notification["nf_date"],
                        "nf_status"=>$notification["nf_status"],
                        "from_user"=>array(
                            "id"=>$notification["from_user_id"],
                            "user_name"=>$notification["user_name"],
                            "profile_img"=>$this->fetch_user_files($notification["from_user_id"],"profile_img")
                        ),
                        "post_info"=>array(
                            "id"=>1,
                            "title"=>$notification["post_title"],
                            "link"=>$notification["post_link"],
                            "thumb"=>$this->fetch_post_files($notification["post_id"],"post_thumb")
                        )
                    );
                }
            }

            
            if(!empty($notifications["all"])){

                $output .= "
                    <div class='navbar__dropdown-body navbar__dropdown-body--nf'>
                ";

                foreach($notifications["all"]  as $nf_index=>$notification){

                    $notification["nf_title"] = html_entity_decode($notification["nf_title"]);

                    $notification["nf_date"] = str_replace(", "," ",$notification["nf_date"]);
                    $notification["nf_date"] = str_replace(" ","-",$notification["nf_date"]);
                    $notification["nf_date"] = str_replace("_"," ",$notification["nf_date"]);
                    $notification["nf_date"] = $this->functions->get_time_in_ago($notification["nf_date"]);

                    //store the from user's profile image
                    $from_user = $notification["from_user"];

                    //store the from user's profile image
                    $post_info = $notification["post_info"];

                    //store the from user's profile image
                    $profile_img = $from_user["profile_img"];

                    //store the from user's profile image
                    $post_thumb = $post_info["thumb"];
            
                    $output .= "
                        <a class='navbar__dropdown-snf' href='{$this->config->domain("posts?v={$post_info['link']}")}' target='_blank'>
                            <img class='navbar__img navbar__img--fromUser' src='{$this->config->domain("app/uploads/users/profile/{$profile_img['name']}-sm.{$profile_img['ext']}")}' alt='{$from_user['user_name']}' width='{$profile_img['dimension']['sm']['width']}' height='{$profile_img['dimension']['sm']['height']}'>
                            
                            <div class='navbar__dropdown-snf-text'>
                                <h6 class='navbar__dropdown-snf-title'>
                                 {$notification['nf_title']}
                                </h6>
                                
                                <span class='navbar__dropdown-snf-time'>
                                    {$notification['nf_date']}
                                </span>
                            </div>

                            <img class='navbar_img navbar__img--post' src='{$this->config->domain("app/uploads/posts/{$post_thumb['name']}-sm.{$post_thumb['ext']}")}' alt='{$post_info['title']}' width='{$post_thumb['dimension']['md']['width']}' height='{$post_thumb['dimension']['md']['height']}'>
                        </a>
                    ";
                }

                $output .= "
                    </div><!--dropwdown-body-->
                ";

                if($notifications["total_nf"] > $limit){

                    
                    $output .= "
                        <div class='navbar__dropdown-footer'>
                            <a class='navbar__link navbar__link--dfl' href='{$this->config->domain("users/{$this->user_info['user_name']}/notifications")}' target='_blank'>
                                View more ({$notifications["existing_nf"]})
                            </a>
                        </div> 
                    ";


                }


            }else{

                $output .= "
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
            // print_r($notifications);



       

    
        }
        
        //use the function to load unread notifications
        public function get_unread_notifications()
        {

            $output = array();

            if(!empty($this->user_info)){

                //first validate the post variable
                $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);
           
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
        
                //logged users's user_id will be to_user_id
                $to_user_id=$this->user_info["user_id"];

                //store notificaton related information such as `unread`, `read`, `total`
                $nf_info=$this->fetch_nf_info($to_user_id);

                //update the error status
                $output["error_status"] = 0;

                if($nf_info["unread"] > 0){

                    $badge_txt=($nf_info["unread"] > 9) ? "9+" : $nf_info["unread"];

                    $output["title_tag"]="({$nf_info["unread"]}) $title_tag";
                    
                    $output["bell_btn"]="
                        <button class='navbar__btn navbar__btn--bell' type='button'>
                            <i class='fa fa-bell navbar__btn-icon'></i>
                            <span class='navbar__badge navbar__badge--nf'>
                                {$badge_txt}
                            </span>
                        </button>
                    ";

                }else{

                    $output["title_tag"]=$title_tag;

                    $output["bell_btn"]="
                        <button class='navbar__btn navbar__btn--bell' type='button'>
                            <i class='fa fa-bell navbar__btn-icon'></i>
                        </button>
                    ";
                }


            }else{

                //update the error status
                $output["error_status"] = 1;
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

