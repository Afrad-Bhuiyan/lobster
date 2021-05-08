<?php 

    class posts extends controller{

        private $config;
        private $functions;
        private $data=[];

        //value will be override in __contruct function
        private $nf_obj="";
        
        function __construct(){

            $this->config=new config;
            $this->functions=new functions;
            

            session_start();
            
            if($this->functions->if_user_logged_in()){
                
                //include the user model
                $user_obj=$this->model("user");

                //include the notification model
                $nf_obj=$this->model("notification");
                
                //set the $this->nf_obj value
                $this->nf_obj=$nf_obj;
                
                $output=$user_obj->select(array(
                    "column_name"=>"users.user_id, users.user_name, users.user_role, user_files.ufile_name, user_files.ufile_ext",
                    "join"=>array(
                        "user_files"=>"users.user_id = user_files.user_id"
                    ),
                    "where"=>"user_name='{$_SESSION["user_name"]}'"
                ));

                if($output["status"] == 1){

                    if($output["num_rows"] > 0){

                        $this->data["common_info"]["user_info"]=$output["fetch_all"][0];

                        $nf_obj_output=$nf_obj->select(array(
                            "where"=>"to_user_id={$_SESSION['user_id']} AND nf_status='unread'"
                        ));

                        if($nf_obj_output["status"] == 1){

                            if($nf_obj_output["num_rows"] > 0){

                                $this->data["common_info"]["total_unread_nf"]=$nf_obj_output["num_rows"];
                            }
                        }
                    }

                }else{

                    echo $output["error"];

                }
            }
        }
    
        //Default method
        public function index($x){

            if(isset($_GET["v"])){

                //store `v` parameter's value from $_GET variable and sanitize it
                $post_link=filter_var($_GET['v'],FILTER_SANITIZE_URL);
            
                //include post model
                $post_obj=$this->model("post");
                $cat_obj=$this->model("catagory");

                //fetch the single post using the post link
                $fetch_single_post=$post_obj->select(array(
                    "column_name"=>"
                        posts.post_id,
                        posts.post_title,
                        posts.post_content,
                        posts.post_read,
                        posts.post_date,
                        posts.post_author,
                        post_files.pfile_name,
                        post_files.pfile_ext,
                        users.user_name,
                        user_files.ufile_name,
                        user_files.ufile_ext
                    ",
                    "join"=>array(
                        "users"=>"posts.post_author = users.user_id",
                        "post_files"=>"posts.post_id = post_files.post_id",
                        "user_files"=>"user_files.user_id = users.user_id",
                    ),
                    "where"=>"posts.post_link='{$post_link}'"
                ));

                if($fetch_single_post["status"] == 1 && $fetch_single_post["num_rows"] == 1){

                    //store the title tag in $post_title variable
                    $post_title=$fetch_single_post['fetch_all'][0]['post_title'];

                    //store the title tag in $this->data variable
                    $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) {$post_title} | Lobster" : "{$post_title} | Lobster";
                    
                    //store the single post's all information $this->data variable with index `post`
                    $this->data["post"]=$fetch_single_post["fetch_all"][0];
                    
                }else{

                    echo (isset($post_output["error"])) ? $post_output["error"] : "somehting went wrong in posts controller on line 131";
                }

                //fetch catagories
                $fetch_catagories=$cat_obj->select();

                if($fetch_catagories["status"] == 1 && $fetch_catagories["num_rows"] > 0){
                    
                    $this->data["catagories"]=$fetch_catagories["fetch_all"];
                }


                $this->view("posts/single_post",$this->data);

        
            }else{

                $this->functions->not_found();
               

            }
        }
    
    }


?>