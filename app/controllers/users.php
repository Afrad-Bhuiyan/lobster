<?php 

    class users extends controller{

         //store the config class object
         private $config;
            
         //store the functions class object
         private $functions;
         
                
        //Here we will store all the required model's object
        private $model_objs=array();
        
        //store logged user's information
        private $user_info;

        //store all the information for passing in views
        private $data=array();
        /**
         * =============================
         * All magic functions  starts 
         * =============================
         */
        
            public function __construct($user_name, $method)
            {

                if($_SERVER["REQUEST_METHOD"] !== "GET"){

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

                    //store all the models
                    $this->model_objs=array(
                        "user_obj"=>$this->model("user"),
                        "nf_obj"=>$this->model("notification"),
                        "post_obj"=>$this->model("post"),
                        "cat_obj"=>$this->model("catagory"),
                        "pr_obj"=>$this->model("post_rating"),
                        "comment_obj"=>$this->model("comment"),
                        "cr_obj"=>$this->model("comment_replies"),
                        "sp_obj"=>$this->model("saved_post"),
                        "ufile_obj"=>$this->model("user_files")
                    );

                    if($method == "about" || $method == "index" || $method == "search"){

                        if($this->if_exist_username($user_name) == false){
                            
                            //if method doesn't exist, show the 404 error page
                            $this->functions->error_pages()->error_404();
                        }

                    }else{

                        if($this->if_exist_username($user_name) == false || $this->if_user_logged_in() == false){

                            //user is not logged. Redirect to the home page
                            header("Location: {$this->config->domain()}");

                        }else{

                            //store the logged user' info in $this->user_info variable
                            $this->user_info=$this->logged_user_info($_SESSION["user_id"]);
                        
                            //store the logged user' info in $this->data variable
                            $this->data["common_info"] = array(
                                "user_info"=>$this->logged_user_info($this->user_info['user_id']),
                                "nf_info"=>$this->fetch_nf_info($this->user_info['user_id'])
                            );

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
         * All private functions  ends 
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

            //check if the username is existing
            private function if_exist_username($username)
            {

                $user_obj=$this->model_objs["user_obj"];
            
                $output=$user_obj->select(array(
                    "table_name"=>"users",
                    "where"=>"user_name='{$username}'"
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

            //03.(fetch post's meta info according to logged in user) 
            private function user_post_meta()
            {

                //store the post  model's object
                $post_obj=$this->model_objs["post_obj"];

                $user_post_meta=array(
                    "all_posts_num"=>"",
                    "published_posts_num"=>"",
                    "draft_posts_num"=>""
                );

                foreach($user_post_meta as $key=>$value){

                    $where="";

                    if($key == "all_posts_num"){

                        $where = "post_author='{$this->user_info['user_id']}'";

                    }elseif($key == "published_posts_num"){

                        $where = "post_author='{$this->user_info['user_id']}' AND post_status='published'";
                    
                    }elseif($key == "draft_posts_num"){

                        $where = "post_author='{$this->user_info['user_id']}' AND post_status='draft'";
                    }

                    //$post_obj is post model object passed
                    $output=$post_obj->select(array(
                        "column_name"=>"
                            COUNT(*) as total
                        ",
                        "where"=>"$where"
                    ));

                    if($output["status"] == 1 && $output["num_rows"] == 1){

                        $user_post_meta[$key]=$output["fetch_all"][0]["total"];
                    }
                }

                return $user_post_meta;

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
         * All public functions  starts 
         * =============================
         */

            //show user profile based on username
            public function index($username)
            {
            
                $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) {$username} | Lobster" : "{$username} | Lobster";

                $main_content=file_get_contents($this->config->domain("app/views/pages/profile.home.php"));

                $this->data["main_content"]=$main_content;

                $this->view("templates/profile.tmp",$this->data);
            
            }

            public function about($username)
            {                
                $main_content=file_get_contents($this->config->domain("app/views/pages/profile.about.php"));

                $this->data["main_content"]=$main_content;
                    
                $this->view("templates/profile.tmp",$this->data);
            
            }
    
            public function search()
            {

                echo "Search from users controller";
            }        
    
            //show user dashboard based on username
            public function dashboard($param)
            {

                if(!empty($param)){
                    
                    //store the main option from $param variable
                    $option = $param[0];
                    
                    $_GET["option"] = $option;
                    
                    //store the sub option in main option from $_GET variable
                    $sub_option = (isset($_GET["sub_option"])) ? $_GET["sub_option"] : null;

                    if($option == "posts"){

                        if($sub_option == "my_posts"){
                         
                            //store the logged user's user_id from $_SESSION variable
                            $logged_user_id=$_SESSION["user_id"];

                            //store p   ost model's object from $this->model_objs variable
                            $post_obj=$this->model_objs["post_obj"];

                            //store the options for fetching data
                            $this->data["user_post_meta"]=$this->user_post_meta();
                            
                            // $this->data["total_posts"]=$total_posts;
                            $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Posts | My Posts" : "Dashboard | Posts | My Posts";
                        
                            //view for showing all the posts
                            $this->view("dashboard/posts/all.posts", $this->data);
                            
                        }elseif($sub_option == "publish"){
                            
                            $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Posts | Publish a post" : "Dashboard | Posts | Publish a post" ;
                        

                            //store catagory model's object from $this->model_objs
                            $cat_obj=$this->model_objs["cat_obj"];
    
                            //fetch all catagories
                            $fetch_all_cats=$cat_obj->select();
    
                            if($fetch_all_cats["status"] == 1 && $fetch_all_cats["num_rows"] > 0){
    
                                //store all catagories in `catagories` index
                                $this->data["catagories"]=$fetch_all_cats["fetch_all"];
                            }
    
                            //finally return the view
                            $this->view("dashboard/posts/publish.posts",$this->data);
                            
                            
                        }elseif($sub_option == "edit"){

                            if(isset($_GET["link"]) && $_GET["link"] !== ""){

                                //validate the $_GET variable
                                $_GET=filter_var_array($_GET, FILTER_SANITIZE_URL);
                                
                                //store the post link from $_GET variable
                                $post_link=$_GET["link"];
    
                                //store the post model's object
                                $post_obj=$this->model_objs["post_obj"];
    
                                //store the catagory model's object
                                $cat_obj=$this->model_objs["cat_obj"];
    
                                //fetch a single post using the $post_link
                                $fetch_single_post=$post_obj->select(array(
                                    "join"=>array(
                                        "catagories"=>"posts.post_cat=catagories.cat_id",
                                        "post_files"=>"posts.post_id = post_files.post_id"
                                    ),
                                    "where"=>"posts.post_link='{$post_link}'"
                                ));
    
                                if($fetch_single_post["status"] == 1 && $fetch_single_post["num_rows"] == 1){
    
                                    $this->data["title_tag"]="Dashboard | Posts | Edit - {$fetch_single_post["fetch_all"][0]["post_title"]} ";
                        
                                    if(isset($this->data["common_info"]["total_unread_nf"])){
    
                                        $this->data["title_tag"]=  "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Posts | Edit - {$fetch_single_post["fetch_all"][0]["post_title"]}";
                                    
                                    }else{
                                        
                                        $this->data["title_tag"]=  "Dashboard | Posts | Edit - {$fetch_single_post["fetch_all"][0]["post_title"]}";
                                    }
    
                                    $this->data["posts"]=array(
                                        "num_rows"=>$fetch_single_post["num_rows"],
                                        "fetch_all"=>$fetch_single_post["fetch_all"]
                                    );
                                
                                }else{
    
                                    //if method doesn't exist, show the 404 error page
                                    $this->functions->error_pages()->error_404();
                                    die();
                                }
    
    
                                //fetch all catagories
                                $fetch_all_cats=$cat_obj->select();
    
                                if($fetch_all_cats["status"] == 1 && $fetch_all_cats["num_rows"] > 0){
    
                                    //store all the catagories in data variable
                                    $this->data["catagories"]=$fetch_all_cats["fetch_all"];
                                
                                }else{
    
                                    //if method doesn't exist, show the 404 error page
                                    $this->functions->error_pages()->error_404();
                                    die();

                                }
    
                                $this->view("dashboard/posts/edit.posts",$this->data);
    
                            }else{
    
                                
                                //if method doesn't exist, show the 404 error page
                                $this->functions->error_pages()->error_404();
                                die();
                     
                            }
    


                            
                            echo "edit  posts";
                            
                        }elseif($sub_option == "saved_posts"){

                            $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Posts | Saved Posts" : "Dashboard | Posts | Saved Posts";
            
                            $logged_user_id = $_SESSION["user_id"];
                            //$logged_user_id = 11;
    
                            $sp_obj=$this->model_objs["sp_obj"];
    
                            $fetch_saved_posts=$sp_obj->select(array(
                                "column_name"=>"
                                    saved_posts.sp_id,
                                    posts.post_id,
                                    posts.post_title,
                                    posts.post_author,
                                    posts.post_link,
                                    posts.post_date,
                                    posts.post_status,
                                    post_files.pfile_name,
                                    post_files.pfile_ext,
                                    users.user_name,
                                    user_files.ufile_name,
                                    user_files.ufile_ext
                                ",
                                "join"=>array(
                                    "posts"=>"saved_posts.post_id = posts.post_id",
                                    "users"=>"posts.post_author = users.user_id",
                                    "user_files"=>"posts.post_author = user_files.user_id",
                                    "post_files"=>"posts.post_id = post_files.post_id"
                                ),
                                "where"=>"saved_posts.user_id={$logged_user_id} AND posts.post_status='published'",
                                "order"=>array(
                                    "column"=>"saved_posts.sp_id",
                                    "type"=>"DESC"
                                )
                            )); 
    
                            if($fetch_saved_posts["status"] == 1 && $fetch_saved_posts["num_rows"] > 0){
    
                                $this->data["total_saved_posts"]=$fetch_saved_posts["num_rows"];
                                $this->data["saved_posts"]=$fetch_saved_posts["fetch_all"];
                            }
                                
                            $this->view("dashboard/posts/saved.posts",$this->data);
                            
                        }else{

                            //if method doesn't exist, show the 404 error page
                            $this->functions->error_pages()->error_404();
                            die();
                        }
                    

                    }elseif($option == "settings"){

                        if($sub_option == "account"){
                                
                            //set the title tag
                            $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Settings | Account" : "Dashboard | Settings | Account" ;

                            $user_id=$this->data["common_info"]["user_info"]['user_id'];
                            
                            //store the  user's model object from $this->model obj array
                            $user_obj=$this->model_objs["user_obj"];

                            //fetch logged user information
                            $fetch_logged_user_info=$user_obj->select(array(
                                "where"=>"users.user_id='$user_id'"
                            ));
                        
                            if($fetch_logged_user_info["status"] == 1 && $fetch_logged_user_info["num_rows"] == 1){

                                //store logged user information
                                $this->data["user"]=$fetch_logged_user_info["fetch_all"][0];
                                
                            }else{

                                //print the error
                                echo $fetch_logged_user_info["error"];
                                die();
                            }


                            //finaly return the view
                            $this->view("dashboard/settings/account.settings",$this->data);


                        }elseif($sub_option == "security"){
                            
                            //store the  user's model object from $this->model obj array
                            $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Settings | Security" : "Dashboard | Settings | Security" ;
                            
                            //finaly return the view
                            $this->view("dashboard/settings/security.settings",$this->data);


                        }else{
                            
                            //if method doesn't exist, show the 404 error page
                            $this->functions->error_pages()->error_404();
                            die();

                        }
                        


                    }elseif($option == "admin_options"){

                        if($this->user_info["user_role"] == "admin"){

                            if($_GET["sub_option"] == "users"){

                                //set the title tag
                                $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Admin Options | Users" : "Dashboard | Admin Options | Users";
                                
                                $this->view("dashboard/admin_opt/users.admin_opt",$this->data);


                            }elseif($_GET["sub_option"] == "catagories"){

                                //set the title tag
                                $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Admin Options | Catagories" : "Dashboard | Admin Options | Catagories";
                                                        
                                $this->view("dashboard/admin_opt/catagories.admin_opt",$this->data);


                            }else{
                                //if method doesn't exist, show the 404 error page
                                $this->functions->error_pages()->error_404();
                                die();

                            }

                          

                        }else{
                            
                            //if method doesn't exist, show the 404 error page
                            $this->functions->error_pages()->error_404();
                            die();
                        }
                    
                    }else{

                        //if method doesn't exist, show the 404 error page
                        $this->functions->error_pages()->error_404();
                        die();
                    }


                }else{

                    $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Overview" : "Dashboard | Overview" ;

                    //view the dashboard home page
                    $this->view("dashboard/dashboard",$this->data);

                }
               
                die();
                //validate the $_GET variable
                $_GET=filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);

                if(isset($_GET["posts"])){

                    if($_GET["posts"] == "myposts"){

                        //store the logged user's user_id from $_SESSION variable
                        $logged_user_id=$_SESSION["user_id"];

                        //store p   ost model's object from $this->model_objs variable
                        $post_obj=$this->model_objs["post_obj"];

                        //here we will store the total posts
                        $total_posts=null;

                        //fetch total posts 
                        $fetch_total_posts=$post_obj->select(array(
                            "column_name"=>"count(*) AS total_posts",
                            "where"=>"posts.post_author={$logged_user_id}"
                        ));

                        if($fetch_total_posts["status"] == 1){

                            //here we will store the total posts
                            $total_posts=$fetch_total_posts["fetch_all"][0]["total_posts"];

                            //$total_posts=0;
                        }

                        //check if user postet a single posts
                        if($total_posts > 0){

                            //store the options for fetching data
                            $this->data["user_post_meta"]=$this->user_post_meta();
                        }

                        
                        $this->data["total_posts"]=$total_posts;

                        $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Posts | My Posts" : "Dashboard | Posts | My Posts";
                        

                        //view for showing all the posts
                        $this->view("dashboard/posts/all.posts", $this->data);
                        
                    }elseif($_GET["posts"] == "publish"){

                        $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Posts | Publish a post" : "Dashboard | Posts | Publish a post" ;
                        

                        //store catagory model's object from $this->model_objs
                        $cat_obj=$this->model_objs["cat_obj"];

                        //fetch all catagories
                        $fetch_all_cats=$cat_obj->select();

                        if($fetch_all_cats["status"] == 1 && $fetch_all_cats["num_rows"] > 0){

                            //store all catagories in `catagories` index
                            $this->data["catagories"]=$fetch_all_cats["fetch_all"];
                        }

                        //finally return the view
                        $this->view("dashboard/posts/publish.posts",$this->data);


                    }elseif($_GET["posts"] == "edit"){

                        if(isset($_GET["post_link"]) && $_GET["post_link"] !== ""){

                            //validate the $_GET variable
                            $_GET=filter_var_array($_GET, FILTER_SANITIZE_URL);
                            
                            //store the post link from $_GET variable
                            $post_link=$_GET["post_link"];

                            //store the post model's object
                            $post_obj=$this->model_objs["post_obj"];

                            //store the catagory model's object
                            $cat_obj=$this->model_objs["cat_obj"];

                            //fetch a single post using the $post_link
                            $fetch_single_post=$post_obj->select(array(
                                "join"=>array(
                                    "catagories"=>"posts.post_cat=catagories.cat_id",
                                    "post_files"=>"posts.post_id = post_files.post_id"
                                ),
                                "where"=>"posts.post_link='{$post_link}'"
                            ));

                            if($fetch_single_post["status"] == 1 && $fetch_single_post["num_rows"] == 1){

                                $this->data["title_tag"]="Dashboard | Posts | Edit - {$fetch_single_post["fetch_all"][0]["post_title"]} ";
                    
                                if(isset($this->data["common_info"]["total_unread_nf"])){

                                    $this->data["title_tag"]=  "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Posts | Edit - {$fetch_single_post["fetch_all"][0]["post_title"]}";
                                
                                }else{
                                    
                                    $this->data["title_tag"]=  "Dashboard | Posts | Edit - {$fetch_single_post["fetch_all"][0]["post_title"]}";
                                }

                                $this->data["posts"]=array(
                                    "num_rows"=>$fetch_single_post["num_rows"],
                                    "fetch_all"=>$fetch_single_post["fetch_all"]
                                );
                            
                            }else{

                                //print the error
                                echo $post_obj_output["error"];
                                die();
                            }


                            //fetch all catagories
                            $fetch_all_cats=$cat_obj->select();

                            if($fetch_all_cats["status"] == 1 && $fetch_all_cats["num_rows"] > 0){

                                //store all the catagories in data variable
                                $this->data["catagories"]=$fetch_all_cats["fetch_all"];
                            
                            }else{

                                echo $fetch_all_cats["error"];
                                die();
                            }

                            $this->view("dashboard/posts/edit.posts",$this->data);

                        }else{

                            $this->functions->not_found();
                        }

                    }elseif($_GET["posts"] == "saved"){

                        $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Posts | Saved Posts" : "Dashboard | Posts | Saved Posts";
            
                        $logged_user_id = $_SESSION["user_id"];
                        //$logged_user_id = 11;

                        $sp_obj=$this->model_objs["sp_obj"];

                        $fetch_saved_posts=$sp_obj->select(array(
                            "column_name"=>"
                                saved_posts.sp_id,
                                posts.post_id,
                                posts.post_title,
                                posts.post_author,
                                posts.post_link,
                                posts.post_date,
                                posts.post_status,
                                post_files.pfile_name,
                                post_files.pfile_ext,
                                users.user_name,
                                user_files.ufile_name,
                                user_files.ufile_ext
                            ",
                            "join"=>array(
                                "posts"=>"saved_posts.post_id = posts.post_id",
                                "users"=>"posts.post_author = users.user_id",
                                "user_files"=>"posts.post_author = user_files.user_id",
                                "post_files"=>"posts.post_id = post_files.post_id"
                            ),
                            "where"=>"saved_posts.user_id={$logged_user_id} AND posts.post_status='published'",
                            "order"=>array(
                                "column"=>"saved_posts.sp_id",
                                "type"=>"DESC"
                            )
                        )); 

                        if($fetch_saved_posts["status"] == 1 && $fetch_saved_posts["num_rows"] > 0){

                            $this->data["total_saved_posts"]=$fetch_saved_posts["num_rows"];
                            $this->data["saved_posts"]=$fetch_saved_posts["fetch_all"];
                        }
                            
                        $this->view("dashboard/posts/saved.posts",$this->data);

                    }else{

                        die($this->functions->not_found());
                    }


                
                }elseif(isset($_GET["settings"])){

                    if($_GET["settings"] == "account"){

                        //set the title tag
                        $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Settings | Account" : "Dashboard | Settings | Account" ;

                        $user_id=$this->data["common_info"]["user_info"]['user_id'];
                        
                        //store the  user's model object from $this->model obj array
                        $user_obj=$this->model_objs["user_obj"];

                        //fetch logged user information
                        $fetch_logged_user_info=$user_obj->select(array(
                            "where"=>"users.user_id='$user_id'"
                        ));
                        
                        if($fetch_logged_user_info["status"] == 1 && $fetch_logged_user_info["num_rows"] == 1){

                            //store logged user information
                            $this->data["user"]=$fetch_logged_user_info["fetch_all"][0];
                            
                        }else{

                            //print the error
                            echo $fetch_logged_user_info["error"];
                            die();
                        }


                        //finaly return the view
                        $this->view("dashboard/settings/account.settings",$this->data);

                    }elseif($_GET["settings"] == "security"){

                        //store the  user's model object from $this->model obj array
                        $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Settings | Security" : "Dashboard | Settings | Security" ;
                        
                        //finaly return the view
                        $this->view("dashboard/settings/security.settings",$this->data);
                        
                    }else{

                        //prevent users to passed dashboard?sdfsda
                        die($this->functions->not_found());
                    }
                    
                }elseif(isset($_GET["admin_options"]) && $this->data["common_info"]["user_info"]["user_role"] == "admin"){

        
                    if($_GET["admin_options"] == "users"){

                        //set the title tag
                        $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Admin Options | Users" : "Dashboard | Admin Options | Users";
                        
                        $this->view("dashboard/admin_opt/users.admin_opt",$this->data);

                    }elseif($_GET["admin_options"] == "catagories"){

                        //set the title tag
                        $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Admin Options | Catagories" : "Dashboard | Admin Options | Catagories";
                        
                        $this->view("dashboard/admin_opt/catagories.admin_opt",$this->data);
                        
                    }else{
                        //prevent users to passed dashboard?sdfsda
                        die($this->functions->not_found());
                    }


                
                }else{

                    if(count($_GET) > 1){
                        
                        //prevent users to passed dashboard?sdfsda
                        $this->functions->error_pages()->error_404();
                        die();
                
                    }else{

                        $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Overview" : "Dashboard | Overview" ;

                        //view the dashboard home page
                        $this->view("dashboard/dashboard",$this->data);
                    }
                }

            }


            public function notifications(){

                echo "show all notifications";
                
            }
        /**
         * =============================
         * All public functions  ends 
         * =============================
         */

    }

?>