<?php 
/*
 * 1. pages controller's all ajax reqeust will be controlled 
 *   from this `ajax_pages` controller classs.
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_pages extends ajax_controller
{
   
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

        public function __construct($method = null)
        {

            $this->config=new config;

            $this->functions=new functions;

            if($_SERVER["REQUEST_METHOD"] !== "POST"){

                //store the funcitons obj
                $this->functions=new functions;

                //call the error_404 to show the 404 error
                $this->functions->error_pages()->error_404();  
                
                die();

            }else{



                //set $this->config to config class object
                $this->config=new config;

                //set $this->functions to functions class object
                $this->functions=new functions;

                //store the PHPMailer class object
                $this->mail= new PHPMailer;

                //store all the models
                $this->model_objs=array(
                    "user_obj"=>$this->model("user"),
                    "ufile_obj"=>$this->model("user_files"),
                    "post_obj"=>$this->model("post"),
                    "pfile_obj"=>$this->model("post_files"),
                    "comment_obj"=>$this->model("comment"),
                    "cr_obj"=>$this->model("comment_replies"),
                    "reply_obj"=>$this->model("reply"),
                    "rate_obj"=>$this->model("rate"),
                    "cat_obj"=>$this->model("catagory"),
                    "nf_obj"=>$this->model("notification")
                );

                if($this->if_user_logged_in()){

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
                        "user_info"=>$this->user_info,
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

        //use the function to fetch ratings of `post`, `comment`, `comment reply`
        private function fetch_rates($rate_for, $rate_for_id){

            //Default output 
            $output = array(
                "likes"=>array(
                    "total"=>0,
                    "users"=>array()
                ),

                "dislikes"=>array(
                    "total"=>0,
                    "users"=>array()
                )
            );

            //store catagory rate's object from $this->model_obj variable
            $rate_obj = $this->model_objs["rate_obj"];

            //fetch like ratings
            $fetch_like_rate=$rate_obj->select(array(
            "where"=>"rates.rate_for='{$rate_for}' AND rates.rate_for_id={$rate_for_id} AND rates.rate_action='like'"
            ));

            //fetch dislike rating
            $fetch_dislike_rate=$rate_obj->select(array(
            "where"=>"rates.rate_for='{$rate_for}' AND rates.rate_for_id={$rate_for_id} AND rates.rate_action='dislike'"
            ));

            if($fetch_like_rate["status"] == 1 && $fetch_like_rate["num_rows"] > 0){

                $output["likes"]["total"] = $fetch_like_rate["num_rows"];

                foreach($fetch_like_rate["fetch_all"] as $rate_index=>$single_rate){

                    $output["likes"]["users"][] = $single_rate["user_id"]; 
                }
            }
            
            if($fetch_dislike_rate["status"] == 1 && $fetch_dislike_rate["num_rows"] > 0){

                $output["dislikes"]["total"] = $fetch_dislike_rate["num_rows"];

                foreach($fetch_dislike_rate["fetch_all"] as $rate_index=>$single_rate){

                    $output["dislikes"]["users"][] = $single_rate["user_id"]; 
                }
            }

            return $output;
        }

        //cuse the function to check logged user rated any thing  
        //such as `post`, `comment`, `comment_replies`
        private function if_user_rated($rate_for,$rate_for_id){

            $output = null;

            if(!empty($this->user_info)){

                //store catagory rate's object from $this->model_obj variable
                $rate_obj = $this->model_objs["rate_obj"];

                $fetch_user_rating=$rate_obj->select(array(
                    "where"=>"rates.rate_for='{$rate_for}' AND rates.rate_for_id={$rate_for_id} AND rates.user_id={$this->user_info['user_id']}"
                ));

                if($fetch_user_rating["status"] == 1 && $fetch_user_rating["num_rows"] == 1){

                    $output = $fetch_user_rating["fetch_all"][0]["rate_action"];
                    
                }
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
     * =============================
     * All public functions  starts And All pages functions will be writter here 
     *  1. Home page 
     *  2. Single Post page 
     *  3. Result page
     * 
     * =============================
     */

        //(1.Home page) use the function to load all posts on home page
        public function load_posts()
        {
                    
            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            $output=[];
            
            $html="";

            //store the catagory_id index from $_POST variable
            $catagory_id=$_POST["catagory_id"];

            //store the load_position index from $_POST variable
            $load_position=$_POST["load_position"];

            //include all the required model
            $post_obj=$this->model("post");

            if($load_position == "home"){

                $post_param=array(
                    "column_name"=>"
                        posts.post_id, 
                        posts.post_title, 
                        posts.post_content, 
                        posts.post_link, 
                        posts.post_read, 
                        posts.post_date,
                        posts.post_author, 
                        posts.post_cat,
                        posts.post_status,
                        post_files.pfile_name, 
                        post_files.pfile_ext,
                        users.user_name, 
                        user_files.ufile_name,
                        user_files.ufile_ext
                    ",
                    "order"=>array(
                        "column"=>"posts.post_id",
                        "type"=>"DESC"
                    ),
                    "join"=>array(
                        "post_files"=>"post_files.post_id = posts.post_id",
                        "users"=>"users.user_id = posts.post_author",
                        "user_files"=>"user_files.user_id = users.user_id"
                    ),
                    "where"=>"posts.post_status='published'"
                );

                if($catagory_id != 0){

                    $post_param["where"]="posts.post_cat={$catagory_id} AND posts.post_status='published'";
                }
                
                $fetch_posts=$post_obj->select($post_param);

                if($fetch_posts["status"] == 1 && $fetch_posts["num_rows"] > 0){

                    $html .= "
                        <div class='row main-wrap__row main_wrap__row--post'>  
                    ";

                    foreach($fetch_posts["fetch_all"] as $post_index=>$post){

                        $post_date=str_replace(", ", "-", $post['post_date']);
                        $post_date=str_replace(" ","-",$post_date);
                        $post_date_formated=str_replace("_"," ",$post_date);

                        $html .= "
                            <div class='col-12 col-md-6 col-lg-4 post'>
                                <div class='post__wrap'>
                                    <div class='post__thumb'>
                                        <a href='{$this->config->domain("posts?v={$post['post_link']}")}'>
                                            <img src='{$this->config->domain("app/uploads/posts/{$post['pfile_name']}-md.{$post['pfile_ext']}")}' alt='{$post['post_title']}'>
                                        </a>
                                    </div>

                                    <div class='post__info'>
                                        <h4 class='post__title'>
                                            <a href='{$this->config->domain("posts?v={$post['post_link']}")}'>

                                                {$this->functions->short_str_word($post['post_title'], 15)}
                                            </a>
                                        </h4>
                                        <div class='post__meta'>
                                            <span class='post__view'>{$post['post_read']} read</span>
                                            <i class='fa fa-circle post__icon post__icon--circle'></i>
                                            <span class='post__time'>{$this->functions->get_time_in_ago($post_date_formated)}</span>
                                        </div>
                                        <div class='post__author'>  
                                            <img src='{$this->config->domain("app/uploads/users/{$post['ufile_name']}-md.{$post['ufile_ext']}")}' alt='{$post['user_name']}'s profile picture on lobster'>
                                            <a href='{$this->config->domain("users/{$post['user_name']}")}'>{$post['user_name']}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ";
                    }

                    $html .= "
                        </div>
                    ";

                    $output["error"] = 0;

                    $output["html"] = $html;

                }else{
                    
                    $output["error"] = 1;

                    $html .= "
                        <div class='row main-wrap__row main-wrap__row--msg main-wrap__row--notfound'>
                            <div class='col-12'>
                                <p>
                                    <i class='fa fa-exclamation-circle'></i>
                                    <span>No posts found</span>
                                </p>
                            </div>
                        </div>  
                    ";

                    $output["html"] = $html;

                }

            }else if($load_position == "sidebar"){

                //posts will be on sidebar in single blog page

                //store the post link when posts have to be loaded in sidebar
                $post_link = $_POST["post_link"];

                $post_param=array(
                    "column_name"=>"
                        posts.post_id,
                        posts.post_title,
                        posts.post_content,
                        posts.post_link,
                        posts.post_read,
                        posts.post_date,
                        posts.post_author,
                        posts.post_status,
                        post_files.pfile_name,
                        post_files.pfile_ext,
                        users.user_name
                    ",
                    "join"=>array(
                        "users"=>"posts.post_author = users.user_id",
                        "post_files"=>"posts.post_id = post_files.post_id"
                    ),
                );

                if($catagory_id == 0){

                    $post_param["where"]="NOT posts.post_link='{$post_link}' AND posts.post_status='published'";
                    
                }else{
            
                    $post_param["where"]="NOT posts.post_link='{$post_link}' AND posts.post_cat={$catagory_id} AND posts.post_status='published'";
                }
                

                $fetch_posts=$post_obj->select($post_param);

                if($fetch_posts["status"] == 1 && $fetch_posts["num_rows"] > 0){

                    foreach($fetch_posts["fetch_all"] as $post_index=>$post){

                        $post_date=str_replace(", ", "-", $post['post_date']);
                        $post_date=str_replace(" ","-",$post_date);
                        $post_date_formated=str_replace("_"," ",$post_date);

                        $html .="
                            <div class='sidebar-lg__sp'>
                                <a class='sidebar-lg__sp-thumb' href='{$this->config->domain("posts?v={$post['post_link']}")}'>
                                    <img  src='{$this->config->domain("app/uploads/posts/{$post['pfile_name']}-sm.{$post['pfile_ext']}")}' alt='{$post['post_title']}'>
                                </a>
                                
                                <div class='sidebar-lg__sp-content'>
                                    <h6 class='sidebar-lg__sp-title'>
                                        <a href='{$this->config->domain("posts?v={$post['post_link']}")}'>
                                            {$this->functions->short_str_word($post['post_title'], 6)}
                                        </a>    
                                    </h6>
                                    <a class='sidebar-lg__sp-username' href='{$this->config->domain("users/{$post['user_name']}")}'>{$post['user_name']}</a>
                                    
                                    <div class='sidebar-lg__sp-meta'>
                                        <span class='sidebar-lg__sp-meta-item sidebar-lg__sp-meta-item--read'>{$post['post_read']} read</span>
                                        <span class='sidebar-lg__sp-meta-item sidebar-lg__sp-meta-item--time'>{$this->functions->get_time_in_ago($post_date_formated)}</span>
                                    </div>
                                </div>
                            </div>
                        ";
                    }

                    $output["error"]=0;

                    $output["html"]=$html;
                    

                }else{

                    $html .= "
                        <div class='sidebar-lg__msg sidebar-lg__sp--notfound'>
                            <div class='sidebar-lg__msg-body'>
                                <p>Not found any posts</p>
                            </div>
                        </div>
                    ";

                    $output["error"]=1;

                    $output["html"]=$html;
                }

            }

            
            echo json_encode($output);

        }

        //(2.Single Post page)
        public function fetch_post_rating($param)
        {

            //store the final output which will be returned
            $output=[];
    
            //store post_rating model object
            $pr_obj=$param["pr_obj"];   
            
            //store the post_id
            $post_id=$param["post_id"];
            
            //store the pr_action `like` or `dislike`
            $pr_action=$param["pr_action"];
    
            //fetch post_ratings
            $fetch_post_rating=$pr_obj->select(array(
                "where"=>"post_ratings.post_id={$post_id} AND post_ratings.pr_action='{$pr_action}'"
            ));
    
            if($fetch_post_rating["status"] == 1){
                
                if( $fetch_post_rating["num_rows"] > 0){
    
                    //store the total rating in $output with an index named `total_rating`
                    $output["total_rating"]=$fetch_post_rating["num_rows"];
                                
                    //store the user_ids who rated the post
                    $output["user_id"]=array();
    
                    foreach($fetch_post_rating["fetch_all"] as $pr_index=>$pr){
    
                        //push all the user_ids in $output["user_id"]
                        $output["user_id"][]=$pr["user_id"];
                    }
    
                }else{
    
                    //did not find any rating. so,set rating to 0
                    $output["total_rating"]=0;
                }
            }
    
            return $output;
        }
    
        //(2.Single Post page)
        public function fetch_subscribers($param){
            /*
                $param=array(
                    "sub_obj"=>"",
                    "sub_owner"=>8,
                )
             */
            $output = [
                "total_sub"=>0,
                "user_id"=>array(),
            ];
    
             $sub_obj=$param["sub_obj"];
    
             $sub_owner=$param["sub_owner"];
    
            $fetch_subscribers=$sub_obj->select(array(
                "where"=>"subscribers.sub_owner={$sub_owner}"
            ));
    
            if($fetch_subscribers["status"] == 1 && $fetch_subscribers["num_rows"] > 0 ){
    
                $output["total_sub"]=$fetch_subscribers["num_rows"];
    
                foreach($fetch_subscribers["fetch_all"] as $sub_index=>$sub){
    
                    $output["user_id"][]=$sub["user_id"];
                }
            
            }
    
            return $output;
        }

        //(2.Single Post page) load subscribe button and total subscriber
        public function load_total_subs_and_btn()
        {

            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store the post_link index from $_POST variable
            $post_link=$_POST["post_link"];

            //Here will store the sub_owner
            $sub_owner="";

            //include all the requried models
            $post_obj=$this->model("post");
            $sub_obj=$this->model("subscriber");

            //fetch post_id based on $post_link
            $fetch_post_id=$post_obj->select(array(
                "column_name"=>"posts.post_author",
                "where"=>"posts.post_link='{$post_link}'"
            ));

            if($fetch_post_id["status"] == 1 && $fetch_post_id["num_rows"] == 1){

                //set the $sub_owner with fetched post_author
                $sub_owner = $fetch_post_id["fetch_all"][0]["post_author"];

            }else{

                echo (isset($fetch_post_id["error"])) ? $fetch_post_id["error"] : "somehting went wrong in ajax_request on line 60";
                die();
            }
            
            //Here we will store the final output
            $output=[
                "total_sub"=>"0 Subscriber",
                "user_id"=>array(),
                "sub_btn"=>"            
                    <button class='sp-content__btn sp-content__btn--subscribe' data-sub_owner='{$sub_owner}'>
                        <span>Subscribe</span>
                    </button>
                "
            ];

            $fetch_subscribers=$sub_obj->select(array(
                "where"=>"subscribers.sub_owner={$sub_owner}"
            ));

            if($fetch_subscribers["status"] == 1 && $fetch_subscribers["num_rows"] > 0 ){

                if($fetch_subscribers["num_rows"] > 1){
                    //store total subscirbers number in $output's `total_sub` index
                    $output["total_sub"]="{$fetch_subscribers["num_rows"]} Subscribers";
                    
                }else{
                    
                    $output["total_sub"]="{$fetch_subscribers["num_rows"]} Subscriber";

                }

                foreach($fetch_subscribers["fetch_all"] as $sub_index=>$sub){

                    //countinue a loop for each uesr_id
                    $output["user_id"][]=$sub["user_id"];
                }
            }

            if($this->functions->if_user_logged_in()){

                $logged_in_user_id=$_SESSION["user_id"];

                if(!empty($output["user_id"]) && in_array($logged_in_user_id, $output["user_id"])){
                    
                    $output["sub_btn"]="
                        <button class='sp-content__btn sp-content__btn--subscribe sp-content__btn--pressed' data-sub_owner='{$sub_owner}'>
                            <span>Subcribed</span>
                        </button>
                    ";
                }
            }

            //return the final output
            echo json_encode($output);
            
        }
        
        //(2.Single Post page) load post rating `like`  & `dislike`
        public function load_post_rating()
        {
            
            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //Here we will store the final output
            $output=[];

            //store the post_link index from $_POST variable
            $post_link=$_POST["post_link"];

            //Here will store the post_id
            $post_id="";

            //include the post model
            $post_obj=$this->model("post");

            //include the post_rating model
            $pr_obj=$this->model("post_rating");

            //fetch post_id based on $post_link
            $fetch_post_id=$post_obj->select(array(
                "column_name"=>"posts.post_id,posts.post_author",
                "where"=>"posts.post_link='{$post_link}'"
            ));

            if($fetch_post_id["status"] == 1 && $fetch_post_id["num_rows"] == 1){

                //set the $post_id with fetched post_id
                $post_id=$fetch_post_id["fetch_all"][0]["post_id"];
                
                //set the $post_author with fetched post_author
                $post_author=$fetch_post_id["fetch_all"][0]["post_author"];

            }else{

                echo (isset($fetch_post_id["error"])) ? $fetch_post_id["error"] : "something went wrong in ajax_posts on line 191";
                
                die();
            }

            //Post Ratingh all the information will be stored
            $pr_info=[];
            
            //this variable will be used for printing the sp-content__meta-btn--pressed class for like button
            $meta_btn_pressed_like="";
            
            //this variable will be used for printing the sp-content__meta-btn--pressed class for dislike button
            $meta_btn_pressed_dislike="";

            //call the $this->fetch_post_rating function for getting like details of this post
            $pr_info["like"]=$this->fetch_post_rating(array(
                "pr_obj"=>$pr_obj,
                "post_id"=>$post_id,
                "pr_action"=>"like"
            ));
            
            //call the $this->fetch_post_rating function for getting dislike details of this post
            $pr_info["dislike"]=$this->fetch_post_rating(array(
                "pr_obj"=>$pr_obj,
                "post_id"=>$post_id,
                "pr_action"=>"dislike"
            ));

            if($this->functions->if_user_logged_in()){
            
                //store the logged in user_id from $_SESSION variable
                $logged_in_user_id=$_SESSION["user_id"];
                
                //check if logged in user's user_id exists in $pr_info["like"]["user_id"] array
                if(isset($pr_info["like"]["user_id"]) &&  in_array($logged_in_user_id, $pr_info["like"]["user_id"])){
                    
                    //if the user_id exists the set the class name
                    $meta_btn_pressed_like="sp-content__meta-btn--pressed";

                //check if logged in user's user_id exists in $pr_info["dislike"]["user_id"] array
                }elseif(isset($pr_info["dislike"]["user_id"]) &&  in_array($logged_in_user_id, $pr_info["dislike"]["user_id"])){
                    
                    //if the user_id exists the set the class name
                    $meta_btn_pressed_dislike="sp-content__meta-btn--pressed";
                }
            }
        
            //store the like button in `like_btn` index
            $output["like_btn"]="
                <button class='sp-content__meta-btn {$meta_btn_pressed_like} sp-content__meta-btn--rating sp-content__meta-btn--like' type='button' data-post_id='{$post_id}'>
                    <i class='fa fa-thumbs-up'></i>
                    <span class='sp-content__meta-btn-text'>{$pr_info['like']['total_rating']}</span>
                </button>
            ";

            //store the like button in `dislike_btn` index
            $output["dislike_btn"]="
                <button class='sp-content__meta-btn {$meta_btn_pressed_dislike} sp-content__meta-btn--rating sp-content__meta-btn--dislike' type='button' data-post_id='{$post_id}'>
                    <i class='fa fa-thumbs-down'></i>
                    <span class='sp-content__meta-btn-text'>{$pr_info['dislike']['total_rating']}</span>
                </button>
            ";

            // // //finally echo the $output to the client side
            echo json_encode($output);

        }

        //(2.Single Post page) load save post button
        public function load_save_post_btn()
        {

            $output = [];

            //first validate the $_POST variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store the post_link from $_POST variable
            $post_link=$_POST["post_link"];

            //Here we will store post_id based on $post_link
            $post_id="";

            //include all required models
            $post_obj=$this->model("post");
            $sp_obj=$this->model("saved_post");

            //fetch  post_id based on post_link
            $fetch_post_id=$post_obj->select(array(
                "column_name"=>"posts.post_id",
                "where"=>"posts.post_link='{$post_link}'"
            ));

            if($fetch_post_id["status"] == 1 && $fetch_post_id["num_rows"] == 1){

                //set $post_id value to fetched post_id
                $post_id = $fetch_post_id["fetch_all"][0]["post_id"];
            }

            if($this->functions->if_user_logged_in()){
        
                //Here we will store post_id based on $post_link
                $logged_in_user_id=$_SESSION["user_id"];

                $fetch_saved_posts=$sp_obj->select(array(
                    "where"=>"saved_posts.post_id={$post_id} AND user_id={$logged_in_user_id}"
                ));

                if($fetch_saved_posts["status"] == 1){
                
                    $output["error"]=0;

                    if($fetch_saved_posts["num_rows"] == 1){

                        $output["save_btn"]="
                            <button class='sp-content__meta-btn sp-content__meta-btn--pressed sp-content__meta-btn--save' type='button' title='Add to save list' data-post_id='{$post_id}'>
                                <i class='fa fa-floppy-o'></i>
                                <span>Saved</span>
                            </button>
                        ";

                    }else{

                        $output["save_btn"]="
                            <button class='sp-content__meta-btn sp-content__meta-btn--save' type='button' title='Add to save list' data-post_id='{$post_id}'>
                                <i class='fa fa-floppy-o'></i>
                                <span>Save</span>
                            </button>
                        ";
                    }

                }else{
                    
                    $output["error"]=1;

                    $output["error_msg"]=$fetch_saved_posts["error"];
                }

            }else{

                $output["error"]=0;

                $output["save_btn"]="
                    <button class='sp-content__meta-btn sp-content__meta-btn--save' type='button' title='Add to save list' data-post_id='{$post_id}'>
                        <i class='fa fa-floppy-o'></i>
                        <span>Save</span>
                    </button>
                ";
            }

            echo json_encode($output);


        }

    
        //(2.Single Post page) delete comment and replies
        public function delete_comment_and_replies()
        {

            //first validate the $_POST variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            $commnet_type = $_POST["comment_type"];

            if($commnet_type == "primary_comment"){
                
                //store the comment_id for deleting the primary comment
                $comment_id=$_POST["comment_id"];

                /**
                 * before deleting the comments 
                 * we have to delete all the replies of that comment
                 */
                
                //include the comment_replies model
                $cr_obj=$this->model("comment_replies");
                
                $cr_all_id=[];

                //fetch all comment_replies based on $comment_id
                $fetch_comment_replies=$cr_obj->select(array(
                    "where"=>"comment_replies.comment_id={$comment_id}"
                ));

                if($fetch_comment_replies["status"] == 1 && $fetch_comment_replies["num_rows"] > 0){

                    //store all the cr_id in $cr_all_id variable
                    foreach($fetch_comment_replies["fetch_all"] as $cr_index=>$cr){

                        $cr_all_id[]=$cr["cr_id"];
                    }
                }

                if(!empty($cr_all_id)){

                    $cr_all_id=implode("', '", $cr_all_id);

                    $delete_cr=$cr_obj->delete(array(
                        "where"=>"comment_replies.cr_id IN ('$cr_all_id')"
                    ));

                    if($delete_cr["status"] == 0 && $delete_cr["affected_rows"] == 0){
                    
                        echo (isset($delete_cr["error"])) ? $fetch_post_id["error"] : "somehting went wrong in ajax_request on line 1067";
                        die();
                    }
                }

                //include the comment model
                $comment_obj=$this->model("comment");

                $delete_comment=$comment_obj->delete(array(
                    "where"=>"comment_id={$comment_id}"
                ));

                if($delete_comment["status"] == 1 &&  $delete_comment["affected_rows"] > 0){

                    echo 1;

                }else{

                    echo 0;
                }
                

            }elseif($commnet_type == "secondary_comment"){

                //store the comment_id for deleting the primary comment
                $cr_id=$_POST["cr_id"];

                //include the comment model
                $cr_obj=$this->model("comment_replies");

                $delete_cr=$cr_obj->delete(array(
                    "where"=>"cr_id={$cr_id}"
                ));
    
                if($delete_cr["status"] == 1 &&  $delete_cr["affected_rows"] > 0){
    
                    echo 1;
    
                }else{
    
                    echo 0;
                }
            }
                    
        }

        //(2.Single Post page) add post rating `like` or `dislike`
        public function post_rating()
        {
    
            //store the final output
            $output=[];

            if($this->functions->if_user_logged_in()){

                //first validate the $_POST variable
                $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);
            

                //include all required models
                $post_obj=$this->model("post");
                $pr_obj=$this->model("post_rating");
                $nf_obj=$this->model("notification");

                //store the post_id from $_POST variable
                $post_id=$_POST["post_id"];

                //Here we will store post author's id based on $post_id
                $post_author="";

                //title will be need for sending notification
                $post_title="";

                //fetch post_author usering the $post_id
                $fetch_post_author=$post_obj->select(array(
                    "column_name"=>"posts.post_author,posts.post_title",
                    "where"=>"posts.post_id={$post_id}"
                ));

                if($fetch_post_author["status"] == 1 && $fetch_post_author["num_rows"] == 1){

                    //set the post_author to fetched post_author
                    $post_author = $fetch_post_author["fetch_all"][0]["post_author"];
                    
                    //set the post_title to fetched post_title
                    $post_title = $fetch_post_author["fetch_all"][0]["post_title"];
                }

                //store the recent post rating action from $_POST variable
                $pr_action=$_POST["pr_action"];

                //store the loggedin user's user_id $_SESSION variable
                $logged_in_user_id=$_SESSION["user_id"];
                
                //store the loggedin user's user_name $_SESSION variable 
                $logged_in_user_name=$_SESSION["user_name"];


                $fetch_post_rating=$pr_obj->select(array(   
                    "where"=>"post_ratings.post_id={$post_id} AND post_ratings.user_id={$logged_in_user_id}"
                ));

                if($fetch_post_rating["status"] == 1 && $fetch_post_rating["num_rows"] > 0){

                    /**
                     * user previously rated our post 
                     * now we need to update the rating
                     */

                    $prev_pr_action=$fetch_post_rating["fetch_all"][0]["pr_action"];

                    //user tries to remove the previous rating
                    if($prev_pr_action == $pr_action){

                        //delete the previou rating based on user_id & post_id
                        $delete_prev_rating=$pr_obj->delete(array(
                            "where"=>"post_ratings.post_id={$post_id} AND post_ratings.user_id={$logged_in_user_id}"
                        ));

                        if($delete_prev_rating["status"] == 1 && $delete_prev_rating["affected_rows"] > 0){
                            
                            $output["error"]=0;

                        }else{

                            $output["error"]=1;
                        }
                    
                    }else{
                        
                        $update_pr_aciton=$pr_obj->update(array(
                            "fields"=>array(
                                "pr_action"=>$pr_action
                            ),
                            "where"=>"post_ratings.post_id={$post_id} AND post_ratings.user_id={$logged_in_user_id}"
                        ));
                        
                        if($update_pr_aciton["status"] == 1 && $update_pr_aciton["affected_rows"] > 0){

                            /**
                             * Don't send notification if post_author
                             * liked or disliked his/her post
                             */
                            if($post_author !== $logged_in_user_id){

                                $pr_action=($pr_action == "like") ? "liked" : "disliked";

                                $nf_param=array(
                                    "nf_obj"=>$nf_obj,
                                    "nf_title"=>"<strong>{$logged_in_user_name} {$pr_action} on your post:</strong> <span>{$post_title}</span>",
                                    "from_user_id"=>$logged_in_user_id,
                                    "to_user_id"=>$post_author,
                                    "post_id"=>$post_id
                                );

                                if($this->functions->add_notification($nf_param)){

                                    $output["error"]=0;

                                    $output["pr_action"]=$pr_action;
                            
                                }else{
                                    
                                    $output["error"]=1;

                                    $output["pr_action"]=$pr_action;
                                }

                            }else{

                                $output["error"]=0;

                                $output["pr_action"]=$pr_action;

                            }
                    
                        }else{

                            echo 0;
                        }
                    }

                }else{

                    /**
                     * User did not rated the post previously. 
                     * Now, we have to insert a new record
                     */

                    $insert_pr=$pr_obj->insert(array(
                        "fields"=>array(
                            "post_id"=>$post_id,
                            "pr_action"=>$pr_action,
                            "user_id"=>$logged_in_user_id
                        )
                    ));
                    
                    if($insert_pr["status"] == 1 && isset($insert_pr["insert_id"])){
                    
                        /**
                         * Don't send notification if post_author
                         * liked or disliked his post
                         */
                        if($post_author !== $logged_in_user_id){

                            $pr_action=($pr_action == "like") ? "liked" : "disliked";

                            $nf_param=array(
                                "nf_obj"=>$nf_obj,
                                "nf_title"=>"<strong>{$logged_in_user_name} {$pr_action} on your post:</strong>\r\n<span>{$post_title}</span>",
                                "from_user_id"=>$logged_in_user_id,
                                "to_user_id"=>$post_author,
                                "post_id"=>$post_id
                            );

                            if($this->functions->add_notification($nf_param)){

                                $output["error"]=0;

                                $output["pr_action"]=$pr_action;
                                
                                
                            }else{
                                
                                $output["error"]=1;

                                $output["pr_action"]=$pr_action;
                            }

                        }else{

                            $output["error"]=0;

                        }

                    }else{

                        $output["error"]=1;
                    }
                }
                
                echo json_encode($output);


            }else{
            
                $output["error"]=100;

                echo json_encode($output);

            }

        }
    

        //(2.Single Post page) save a post in saved list
        public function save_posts(){

            $output=[];

            if($this->functions->if_user_logged_in()){
                
                //first validate the $_POST variable
                $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

                //store the post_id from $_POST variable
                $post_id=$_POST["post_id"];
                
                //store the loggedin user's user_id $_SESSION variable
                $logged_in_user_id=$_SESSION["user_id"];

                //include all required models
                $sp_obj=$this->model("saved_post");

                $fetch_saved_posts=$sp_obj->select(array(
                    "where"=>"saved_posts.post_id={$post_id} AND saved_posts.user_id={$logged_in_user_id}"
                ));

                if($fetch_saved_posts["status"] == 1){

                    if($fetch_saved_posts["num_rows"] == 1){

                        //condition true means that user wants to unsave the post
                        $unsave_post=$sp_obj->delete(array(
                            "where"=>"saved_posts.post_id={$post_id} AND saved_posts.user_id={$logged_in_user_id}"
                        ));

                        if($unsave_post["status"] == 1 && $unsave_post["affected_rows"] == 1){

                            $output["error"]=0;

                            $output["action"]="unsave";
                        
                        }else{

                            $output["error"]=1;

                            $output["error_msg"]=(isset($unsave_post["error"])) ? $unsave_post["error"] : "somethin went wrong in ajax_posts on in 1596";
                        }
                    
                    }else{

                        //condition false means that user wants to save the post
                        $add_to_save_posts=$sp_obj->insert(array(
                            "fields"=>array(
                                "user_id"=>$logged_in_user_id,
                                "post_id"=>$post_id,

                            )
                        ));

                        if($add_to_save_posts["status"] == 1 && isset($add_to_save_posts["insert_id"])){

                            $output["error"]=0;

                            $output["action"]="save";
                            

                        }else{

                            $output["error"]=1;

                            $output["error_msg"]=(isset($add_to_save_posts["error"])) ? $add_to_save_posts["error"] : "somethin went wrong in ajax_posts on in 1620";
                        }
                    }

                }else{

                    $output["error"]=1;

                    $output["error_msg"]=$fetch_saved_posts["error"];
                }

            }else{

                $output["error"]=100;
            }
            

            echo json_encode($output);
        }


        //(2.Single Post page) add a subscriber into the database
        public function subscribe(){

            $output=[];

            if($this->functions->if_user_logged_in()){

                //first validate the $_POST variable
                $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

                $sub_owner=$_POST["sub_owner"];

                $logged_in_user_id=$_SESSION["user_id"];

                if($logged_in_user_id !== $sub_owner){

                    //include allr required model
                    $sub_obj=$this->model("subscriber");

                    $fetch_subscribers=$this->fetch_subscribers(array(
                        "sub_obj"=>$sub_obj,
                        "sub_owner"=>$sub_owner
                    ));

                    if(!empty($fetch_subscribers["user_id"]) &&  in_array($logged_in_user_id,$fetch_subscribers["user_id"])){

                        /**
                         * user already subscribed 
                         * now we have to unsubscribe
                         */
                        
                        //To unsubscibe we have delete the the entire row
                        $unsubscribe=$sub_obj->delete(array(
                            "where"=>"subscribers.sub_owner={$sub_owner} AND subscribers.user_id={$logged_in_user_id}"
                        ));

                        if($unsubscribe["status"] == 1 && $unsubscribe["affected_rows"] == 1){
                            
                            $output["error"]=0;
                
                            $output["action"]="unsubscribe";
                            
                        }else{

                            $output["error"]=1;

                            $output["error_msg"]=(isset($unsubscribe["error"])) ? $unsubscribe["error"] : "somehting went wrong in ajax_posts on line 1724";
                        }

                    }else{
                        
                        /**
                         * user did not subscribed 
                         * now we have to subscribe
                         */

                        date_default_timezone_set("Asia/Dhaka"); 
                        //To subscribe we have to insert a row
                        $subscribe=$sub_obj->insert(array(
                            "fields"=>array(
                                "sub_owner"=>$sub_owner,
                                "user_id"=>$logged_in_user_id,
                                "sub_date"=>date("d F, Y_h:i:sA")
                            )
                        ));

                        if($subscribe["status"] == 1 && isset($subscribe["insert_id"])){

                                $output["error"]=0;
                                $output["action"]="subscribe";

                        }else{

                            $output["error"]=1;
                            $output["error_msg"]=(isset($subscribe["error"])) ? $subscribe["error"] : "somehting went wrong in ajax_posts on line 1754";
                        }
                    
                    }

                }else{

                    $output["error"]=1;

                    $output["error_msg"]="you can't subscriber yourself";

                }

            }else{

                $output["error"]=100;
            }


            echo json_encode($output);

        }


        //Default method
        public function index(){}


        //use the funtion to fetch notifications when clicked on bell icon
        public function fetch_notifications()
        {
            $output = "
                <div class='ph-nav__dropdown-header ph-nav__dropdown-header--nf'>
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
                    <div class='ph-nav__dropdown-body ph-nav__dropdown-body--nf'>
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
                        <a class='ph-nav__dropdown-snf' href='{$this->config->domain("posts?v={$post_info['link']}")}' target='_blank'>
                            <img class='ph-nav__dropdown-img ph-nav__dropdown-img--fromUser' src='{$this->config->domain("app/uploads/users/profile/{$profile_img['name']}-sm.{$profile_img['ext']}")}' alt='{$from_user['user_name']}' width='{$profile_img['dimension']['sm']['width']}' height='{$profile_img['dimension']['sm']['height']}'>
                            
                            <div class='ph-nav__dropdown-snf-text'>
                                <h6 class='ph-nav__dropdown-snf-title'>
                                 {$notification['nf_title']}
                                </h6>
                                
                                <span class='ph-nav__dropdown-snf-time'>
                                    {$notification['nf_date']}
                                </span>
                            </div>

                            <img class='ph-nav__dropdown-img ph-nav__dropdown-img--post' src='{$this->config->domain("app/uploads/posts/{$post_thumb['name']}-sm.{$post_thumb['ext']}")}' alt='{$post_info['title']}' width='{$post_thumb['dimension']['md']['width']}' height='{$post_thumb['dimension']['md']['height']}'>
                        </a>
                    ";
                }

                $output .= "
                    </div><!--dropwdown-body-->
                ";

                if($notifications["total_nf"] > $limit){

                    
                    $output .= "
                        <div class='ph-nav__dropdown-footer'>
                            <a class='ph-nav__link ph-nav__link--dfl' href='{$this->config->domain("users/{$this->user_info['user_name']}/notifications")}' target='_blank'>
                                View more ({$notifications["existing_nf"]})
                            </a>
                        </div> 
                    ";


                }


            }else{

                $output .= "
                    <div class='ph-nav__dropdown-body ph-nav__dropdown-body--nf'>
                        <div class='ph-nav__dropdown-msg ph-nav__dropdown-msg--notfound'>
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
        public function get_unread_notifications(){

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
                        <button class='ph-nav__btn ph-nav__btn--bell' type='button'>
                            <i class='fa fa-bell ph-nav__btn-icon'></i>
                            <span class='ph-nav__badge ph-nav__badge--nf'>
                                {$badge_txt}
                            </span>
                        </button>
                    ";

                }else{

                    $output["title_tag"]=$title_tag;

                    $output["bell_btn"]="
                        <button class='ph-nav__btn ph-nav__btn--bell' type='button'>
                            <i class='fa fa-bell ph-nav__btn-icon'></i>
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
     * =============================
     * All public functions  ends 
     * =============================
     */



    

 
}

?>

