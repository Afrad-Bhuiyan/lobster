<?php 
  
    class pages extends controller{

        //store config class object
        private $config;
    
        //store functions class object
        private $functions;
        
        //store all model's object
        private $model_objs=array();

        //store logged user's information
        private $user_info = array();

        //store all the data
        private $data=array(
            "common_info"=>array()
        );

        /**
         * =============================
         * All magic functions  starts 
         * =============================
         */
 
            public function __construct()
            {

                if($_SERVER["REQUEST_METHOD"] !== "GET"){

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
                    
                    //store all model's class to the $this->model_objs variable
                    $this->model_objs=array(
                        "user_obj"=>$this->model("user"),
                        "ufile_obj"=>$this->model("user_files"),
                        "post_obj"=>$this->model("post"),
                        "pfile_obj"=>$this->model("post_files"),
                        "cat_obj"=>$this->model("catagory"),
                        "rate_obj"=>$this->model("rate"),
                        "nf_obj"=>$this->model("notification")
                    );

                    if($this->if_user_logged_in()){

                        //store the logged user' info in $this->user_info variable
                        $this->user_info=$this->logged_user_info($_SESSION["user_id"]);
                            
                        //store the logged user' info in $this->data variable
                        $this->data["common_info"] = array(
                            "user_info"=>$this->logged_user_info($_SESSION["user_id"]),
                            "nf_info"=>$this->fetch_nf_info($this->user_info['user_id'])
                        );
                       
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
            

            //use the function to fetch logged user's notification related information
            private function fetch_nf_info($user_id = null){

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
         * All publich functions  starts 
         * =============================
         */

            //use the function to home page
            public function index()
            {
                
                //include all required models
                $post_obj=$this->model_objs["post_obj"];
                
                $cat_obj=$this->model_objs["cat_obj"];

                $fetch_cat=$cat_obj->select();

                if($fetch_cat["status"] == 1 && $fetch_cat["num_rows"] > 0){

                    $this->data["catagories"]=$fetch_cat["fetch_all"];
                }

                if(isset($this->data["common_info"]["nf_info"]) && $this->data["common_info"]["nf_info"]["unread"] > 0){

                    $this->data["title_tag"]= "({$this->data["common_info"]["nf_info"]["unread"]}) Lobster | Home";

                }else{
                    
                    $this->data["title_tag"]= "Lobster | Home";
                }
            
            


                $this->view("pages/index",$this->data);
            }
        
            //use the function to show all search resutls
            public function result()
            {
                
                $output="";

                if($_SERVER["REQUEST_METHOD"] == "POST"){

                    $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

                    $search_query=$_POST["search_query"];

                    $post_obj=$this->model_objs["post_obj"];

                    $fetch_searched_posts=$post_obj->select(array(
                        "column_name"=>"posts.post_title",
                        "where"=>"posts.post_title LIKE '%{$search_query}%'"
                    ));

                    $output=$fetch_searched_posts["fetch_all"];

                    echo json_encode($output);
            

                }elseif($_SERVER["REQUEST_METHOD"] == "GET"){

                    $_GET=filter_var_array($_GET,FILTER_SANITIZE_STRING);

                    if(isset($_GET["search_query"]) && $_GET["search_query"] !== ""){
                        

                        if(isset($this->data["common_info"]["nf_info"]) && $this->data["common_info"]["nf_info"]["unread"] > 0){

                            $this->data["title_tag"]= "({$this->data["common_info"]["nf_info"]["unread"]}) {$_GET['search_query']} | Lobster";
        
                        }else{
                            
                            $this->data["title_tag"]= "{$_GET['search_query']} | Lobster";
                        }

                        //store the searched query
                        $this->data["search_query"] = $_GET['search_query'];

                        //store the search_query index from $_GET
                        $search_query=$_GET["search_query"];

                        //include all required models
                        $post_obj=$this->model_objs["post_obj"];;
                    
                        $fetch_result=$post_obj->select(array(
                            "column_name"=>"
                                posts.post_id,
                                posts.post_title,
                                posts.post_content,
                                posts.post_author,
                                posts.post_date,
                                posts.post_read,
                                posts.post_link,
                                post_files.pfile_name,
                                post_files.pfile_ext,
                                users.user_name,
                                user_files.ufile_name,
                                user_files.ufile_ext
                            ",
                            "join"=>array(
                                "users"=>"users.user_id = posts.post_author",
                                "user_files"=>"user_files.user_id = posts.post_author",
                                "post_files"=>"post_files.post_id = posts.post_id"
                            ),
                            "where"=>"posts.post_title LIKE '%{$search_query}%' OR posts.post_content LIKE '%{$search_query}%'"
                        ));

                        if($fetch_result["status"] == 1 && $fetch_result["num_rows"] > 0){

                            $this->data["total_results"]=$fetch_result["num_rows"];

                            $this->data["results"]=$fetch_result["fetch_all"];
                            
                        }else{

                            $this->data["total_results"]=$fetch_result["num_rows"];
                        }

                        // echo "<pre>";
                        // print_r($this->data);
                        // echo "</pre>";
                        
            
                        //use the function to show all search resutls
                        $this->view("pages/result",$this->data);
                        

                    }else{

                        header("Location:{$this->config->domain()}");
                    }

                }
            }
    

            //use the function to show a single post
            public function posts()
            {

                //validate the $_GET variable
                $_GET = filter_var_array($_GET,FILTER_SANITIZE_STRING);

                //store the `v` index from $_GET variable
                $post_link = isset( $_GET["v"]) ?  $_GET["v"] : null;
                
                //store post model's object from $this->model_obj variable
                $post_obj = $this->model_objs["post_obj"];

                //store catagory model's object from $this->model_obj variable
                $cat_obj = $this->model_objs["cat_obj"];

                //store fetced single posts
                $single_post = array();
                
                //store all the catagores
                $catagories = array();
                

                //fetch a single posts
                $fetch_post = $post_obj->select(array(
                    "column_name"=>"
                        posts.post_id,
                        posts.post_title,
                        posts.post_content,
                        posts.post_author,
                        posts.post_date,
                        posts.post_read,
                        users.user_name
                    ",
                    "join"=>array(
                        "users"=>"users.user_id = posts.post_author"
                    ),
                    "where"=>"posts.post_link='{$post_link}'"
                ));

                if($fetch_post["status"] == 1 && $fetch_post["num_rows"] == 1){

                    $single_post = $fetch_post["fetch_all"][0];
                    
                    //fetch post image
                    $single_post["pfile_info"] = $this->fetch_post_files($single_post["post_id"],"post_thumb");
                    
                    //fetch post author's profile image
                    
                    $single_post["post_auth_info"] = array(
                        "id"=>$single_post["post_author"],
                        "user_name"=>$single_post["user_name"],
                        "ufile_info"=>  array(
                            "profile_img"=>$this->fetch_user_files($single_post["post_author"],"profile_img")  
                        )
                    );
               
                }

                
            
                //fetch a catagories
                $fetch_catagories = $cat_obj->select();

                if($fetch_catagories["status"] == 1 && $fetch_catagories["num_rows"] > 0){

                    $catagories = $fetch_catagories["fetch_all"];
                }

                if(isset($this->data["common_info"]["nf_info"]) && $this->data["common_info"]["nf_info"]["unread"] > 0){

                    $this->data["title_tag"]= "({$this->data["common_info"]["nf_info"]["unread"]}) {$single_post['post_title']} | Lobster";

                }else{
                    
                    $this->data["title_tag"]= "{$single_post['post_title']} | Lobster";
                }
                
                $this->data["single_post"] = $single_post;

                $this->data["catagories"] = $catagories;

                $this->view("pages/single_post",$this->data);

            }
        

            //when any page or URL doesn't exists
            public function not_found()
            {
                echo "not_found from Pages class";
            }
        /**
         * =============================
         * All publich functions  ends 
         * =============================
         */

    }


    
?>