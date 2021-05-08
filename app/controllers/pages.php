<?php 
  
    class pages extends controller{

        //store config class object
        private $config;
    
        //store functions class object
        private $functions;
        
        //store all model's object
        private $model_objs=array();
        
        //store all the data
        private $data=array(
            "common_info"=>array()
        );
 
        public function __construct()
        {

            if($_SERVER["REQUEST_METHOD"] !== "GET"){

                //store the funcitons obj
                $this->functions=new functions;
                
                //call the not_found method
                $this->functions->not_found();  
                
                die();

            }else{

        
                //set $this->config to config class object
                $this->config=new config;

                //set $this->functions to functions class object
                $this->functions=new functions;
                
                //store all model's class to the $this->model_objs variable
                $this->model_objs["user_obj"]=$this->model("user");
                $this->model_objs["ufile_obj"]=$this->model("user_files");
                $this->model_objs["post_obj"]=$this->model("post");
                $this->model_objs["cat_obj"]=$this->model("catagory");
                
                //start the session to check if user logged in
                session_start();
            
                if($this->functions->if_user_logged_in()){

                    //store the logged user's user_id from $_SESSION variable
                    $logged_user_id=$_SESSION["user_id"];

                    //store the logged user's user_id from $_SESSION variable
                    $logged_user_type=$_SESSION["user_type"];

                    //store logged user's othe information
                    $logged_user_info=array();

                    //store the user's model from $this->model_objs variable
                    $user_obj=$this->model_objs["user_obj"];

                    //fetched logged user's other information
                    $fetch_logged_user_info=$user_obj->select(array(
                        "column_name"=>"
                            users.user_name,
                            users.user_email,
                            users.user_role,
                            users.user_email_status
                        ",
                        "where"=>"users.user_id={$logged_user_id}"
                    ));

                    if($fetch_logged_user_info["status"] == 1 && $fetch_logged_user_info["num_rows"] == 1){

                        $logged_user_info=$fetch_logged_user_info["fetch_all"][0]; 
                    }

                    if(!empty($logged_user_info)){

                        $this->data["common_info"]["user_info"]=array(
                            "user_id"=>$logged_user_id,
                            "user_name"=>$logged_user_info["user_name"],
                            "user_email"=>$logged_user_info["user_email"],
                            "user_email_status"=>$logged_user_info["user_email_status"],
                            "user_role"=>$logged_user_info["user_role"],
                            "user_type"=>$logged_user_type,
                            "ufile_info"=>array(
                                "profile_img"=>$this->fetch_logged_user_files("profile_img"),
                                "bg_img"=>$this->fetch_logged_user_files("bg_img")
                            )
                        );
                    }
                }
            }

        }

        
        private function fetch_logged_user_files($ufile_usage)
        {

            $output = "";

            //store the user_files's model from $this->model_objs variable
            $ufile_obj=$this->model_objs["ufile_obj"];

            $fetch_file=$ufile_obj->select(array(
                "where"=>"user_files.user_id={$_SESSION['user_id']} AND user_files.ufile_usage='{$ufile_usage}'"
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

        public function index(){
            
            //include all required models
            $post_obj=$this->model_objs["post_obj"];
            
            $cat_obj=$this->model_objs["cat_obj"];

            $fetch_cat=$cat_obj->select();

            if($fetch_cat["status"] == 1 && $fetch_cat["num_rows"] > 0){

                $this->data["catagories"]=$fetch_cat["fetch_all"];
            }

            $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Lobster | Home" : "Lobster | Home";


           $this->view("pages/index",$this->data);
        }
        
        //show search results
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
                    
                    //set the page title tag
                    $this->data["title_tag"] = "{$_GET['search_query']} | Lobster";

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
                    
        
                    $this->view("pages/result",$this->data);
                    

                }else{

                    header("Location:{$this->config->domain()}");
                }

            }
        }
        
        //when any page or URL doesn't exists
        public function not_found()
        {
            echo "not_found from Pages class";
        }

    }


    
?>