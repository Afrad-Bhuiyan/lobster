<?php 

    class users extends controller{

        private $config;
        private $functions;
                
        //Here we will store all the required model's object
        private $model_objs=array();
        
        private $data=array(
            "common_info"=>array()
        );

        public function __construct($user_name, $method)
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
                $this->model_objs["nf_obj"]=$this->model("notification");
                $this->model_objs["post_obj"]=$this->model("post");
                $this->model_objs["cat_obj"]= $this->model("catagory");
                $this->model_objs["pr_obj"]= $this->model("post_rating");
                $this->model_objs["comment_obj"]= $this->model("comment");
                $this->model_objs["cr_obj"]= $this->model("comment_replies");
                $this->model_objs["sp_obj"]= $this->model("saved_post");
                $this->model_objs["ufile_obj"]= $this->model("user_files");

                if($method == "about" || $method == "index" || $method == "search"){

                    if($this->if_exist_username($user_name) == false){
                        
                        //user is not logged. Redirect to the home page
                        header("Location: {$this->config->domain()}");
                    }

                }else{

                    if($this->if_exist_username($user_name) == false || $this->if_user_logged_in() == false){

                        //user is not logged. Redirect to the home page
                        // header("Location: {$this->config->domain()}");
                        echo 1;

                    }else{

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

                    $where = "post_author='{$_SESSION['user_id']}'";

                }elseif($key == "published_posts_num"){

                    $where = "post_author='{$_SESSION['user_id']}' AND post_status='published'";
                
                }elseif($key == "draft_posts_num"){

                    $where = "post_author='{$_SESSION['user_id']}' AND post_status='draft'";
                }

                //$post_obj is post model object passed
                $output=$post_obj->select(array(
                    "where"=>"$where"
                ));

                if($output["status"] == 1){

                    $user_post_meta[$key]=$output["num_rows"];
                }
            }

            return $user_post_meta;

        }

        private function total_filter_posts($post_status, $logged_user_id)
        {

            $output= "";

            $options=array();

            $post_obj=$this->model_objs["post_obj"];
            
            if($post_status == "all"){
            
                $options["where"]= "posts.post_author={$logged_user_id}";
                
            }else{
                
                $options["where"]= "posts.post_author={$logged_user_id} AND posts.post_status='{$post_status}'";
            }

            $fetch_filter_posts=$post_obj->select($options);

            if($fetch_filter_posts["status"] == 1){

                $output= $fetch_filter_posts["num_rows"];
            }

            return $output;
        }
        
        public function delete_posts($username)
        {

            if($this->if_user_logged_in($username)  && $this->if_exist_username($username) ){

                //Sanitize $_POST variable
                filter_var_array($_POST,FILTER_SANITIZE_STRING);

                $post_links_array=$_POST["post_links"];
                $post_links_str=implode($post_links_array,"','");

                $post_obj=$this->model("post");

                $output=$post_obj->select(array(
                    "where"=>"post_link IN ('$post_links_str')"
                ));

                if($output["status"] == 1){
                    
                    if($output["num_rows"]  > 0){
                        
                        $final_output=[];

                        foreach($output["fetch_all"] as $key=>$post){
                            
                            if(unlink("app/uploads/posts/{$post['post_thumbnail']}")){
                                
                                $post_obj->delete(array(
                                    "where"=>"post_link='{$post['post_link']}'"
                                ));

                                $final_output[]=true;

                            }else{
                                
                                $final_output[]=false;
                            }
                        }

                        if(!in_array(false,$final_output)){

                            echo 1;

                        }else{

                            echo 0;
                        }
                    }

                }else{
                    echo $output["error"];
                }

            }else{

                header("Location: {$this->config->domain()}");
            }
        }

        //update user info from account settings
        public function update_user_info($username)
        {

            $user_obj=$this->model_objs["user_obj"];
        
            if(isset($_POST["request"]) && $_POST["request"] == "profile_img"){

                //Reqeust got for updating the user profile image

                // echo "Update the user information";
                $uploaded_img_num=count($_FILES["profile_img"]["name"]);
                $uploaded_img_ext=pathinfo($_FILES["profile_img"]["name"][0],PATHINFO_EXTENSION);
                $uploaded_img_size=$_FILES["profile_img"]["size"][0];
                $valid_ext=["jpg","jpeg","png"];

                $error=[];

                if($uploaded_img_num > 1){

                    $error["length_error"]=array(
                        "error_msg"=>'
                            <div class="ac-info__form-msg ac-info__form-msg--error ac-info__form-msg--pp">
                                <p>You can upload only 1 File</p>
                            </div>
                        '
                    
                    );

                }elseif(!in_array($uploaded_img_ext,$valid_ext)){

                    $error["format"]=array(
                        "error_msg"=>'
                            <div class="ac-info__form-msg ac-info__form-msg--error ac-info__form-msg--pp">
                                <p>.jpg, .jpeg or.png is supported format</p>
                            </div>
                        '
                    );

                }elseif(intval($uploaded_img_size) > 1048576){

                    $error["size_error"]=array(
                        "error_msg"=>'
                            <div class="ac-info__form-msg ac-info__form-msg--error ac-info__form-msg--pp">
                                <p>File size must be less than 1MB</p>
                            </div>
                        '
                    );
                }

                if(!empty($error)){

                    echo json_encode($error);

                }else{

                    $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

                    $user_files_obj=$this->model("user_files");

                    //Generate a randome string for user profile picutre name
                    $random_str=$this->functions->generate_random_str(array(
                            "model_obj"=>$user_files_obj,
                            "column"=>"ufile_name",
                            "length"=>11
                    ));

                        //Get the previously uploaded Image name from input[type='hidden' value='imagename|jpg']
                    $prev_uploaded_img_name_ext=$_POST["profile_picture_old"];

                    //Get the uploaded Image name from $_FILES
                    $uploaded_img_name=$_FILES["profile_img"]["name"][0];

                    //Get the uploaded Image name from $_FILES
                    $uploaded_img_tmp_name=$_FILES["profile_img"]["tmp_name"][0];

                    //Rename the uploaded image name with a random string
                    $uploaded_img_new_name=$random_str;

                    //Get the uploaded Image extension from $uploaded_img_name
                    $uploaded_img_ext=pathinfo($uploaded_img_name, PATHINFO_EXTENSION);

                    if($prev_uploaded_img_name_ext !== "false"){
                        
                        //Convert $prev_uploaded_img_name_ext into an array
                        $prev_uploaded_img_name_ext_array=explode("|",$prev_uploaded_img_name_ext);

                        //Sperate the name from $prev_uploaded_img_name_ext_array
                        $prev_uploaded_img_name=$prev_uploaded_img_name_ext_array[0];

                        //Sperate the extension from $prev_uploaded_img_name_ext_array
                        $prev_uploaded_img_ext=$prev_uploaded_img_name_ext_array[1];

                        //Delete the previouly uploaded image
                        unlink("app/uploads/users/$prev_uploaded_img_name.$prev_uploaded_img_ext");

                        unlink("app/uploads/users/$prev_uploaded_img_name-sm.$prev_uploaded_img_ext");
                    
                        unlink("app/uploads/users/$prev_uploaded_img_name-md.$prev_uploaded_img_ext");
                    }

                    if(!file_exists("app/uploads/users/{$uploaded_img_new_name}.{$uploaded_img_ext}")){

                        //Upload the original Image first
                        if(move_uploaded_file($uploaded_img_tmp_name, "app/uploads/users/{$uploaded_img_new_name}.{$uploaded_img_ext}")){

                            //Resize the original Image into 200x200 and upload it
                            $img_md_output=$this->functions->resize_upload_img(array(
                                "width"=>200,
                                "height"=>200,
                                "img_url"=>"app/uploads/users/{$uploaded_img_new_name}.{$uploaded_img_ext}",
                                "img_upload_location"=>"app/uploads/users/{$uploaded_img_new_name}-md.{$uploaded_img_ext}"
                            ));

                            //Resize the original Image into 100x100 and upload it
                            $img_sm_output=$this->functions->resize_upload_img(array(
                                "width"=>100,
                                "height"=>100,
                                "img_url"=>"app/uploads/users/{$uploaded_img_new_name}.{$uploaded_img_ext}",
                                "img_upload_location"=>"app/uploads/users/{$uploaded_img_new_name}-sm.{$uploaded_img_ext}"
                            ));

                            if($img_md_output && $img_sm_output){

                                $output=$user_files_obj->update(array(
                                    "fields"=>array(
                                        "ufile_name"=>"{$uploaded_img_new_name}",
                                        "ufile_ext"=>"{$uploaded_img_ext}",
                                    ),
                                    "where"=>"user_id='{$_SESSION['user_id']}'"
                                ));
                            
                                if($output["status"] == 1){
                                    
                                    echo 1;
                                    
                                }else{

                                    echo 0;
                                }
                                
                            }else{

                                echo 0;
                            }
                        }

                    }
                }

            }else{

                //Reqeust got for updatin gother's Information
                    
                //validate $_POST variable first
                $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);
                
                //store the actual value from the input
                $form_input_value=$_POST["form_input_value"];

                //store input's name attribute value
                $form_input_name=$_POST["form_input_name"];

                //store all the errors
                $error=[];

                if(empty($form_input_value)){

                    
                    $error["empty_error"]=array(
                        "error_msg"=>'
                            <div class="ac-info__form-msg ac-info__form-msg--error">
                                <p>You can\'t update with an empty value</p>
                            </div>
                        '
                    );

                }else{

                    if($form_input_name == "user_fname" || $form_input_name == "lname"){
                        
                        if(strlen($form_input_value) < 3){

                            $error["length_short"]=array(
                                "error_msg"=>'
                                    <div class="ac-info__form-msg ac-info__form-msg--error">
                                        <p>Length is too short</p>
                                    </div>
                                '
                            );
                        }
                        
                        if(strlen($form_input_value) > 15){

                            $error["length_long"]=array(
                                "error_msg"=>'
                                    <div class="ac-info__form-msg ac-info__form-msg--error">
                                        <p>Length is too long</p>
                                    </div>
                                '
                            );

                        }

                    }elseif($form_input_name == "user_email"){

                        if(!filter_var($form_input_value, FILTER_VALIDATE_EMAIL)){

                            $error["email_error"]=array(
                                "error_msg"=>'
                                    <div class="ac-info__form-msg ac-info__form-msg--error">
                                        <p>Enter a E-mail Address</p>
                                    </div>
                                '
                            );
                            
                        }else{
                        
                            $output=$user_obj->select(array(
                                "column_name"=>"user_email",
                                "where"=>"user_email='{$form_input_value}'"
                            ));

                            if($output["status"] == 1){

                                if($output["num_rows"] > 0){

                                    $error["email_error"]=array(
                                        "error_msg"=>'
                                            <div class="ac-info__form-msg ac-info__form-msg--error">
                                                <p>The E-mail Addresss is already in used</p>
                                            </div>
                                        '
                                    );
                                }

                            }else{

                                echo $output["error"];
                            }
                        }
                    }
                }

                if(!empty($error)){

                echo  json_encode($error);
                    
                }else{

                    $user_obj_opt=array(
                        "fields"=>array(
                            "{$form_input_name}"=>"{$form_input_value}"
                        ),
                        "where"=>"user_name='{$_SESSION['user_name']}'"
                    );

                    if($form_input_name == "user_email"){

                        $user_obj_opt["fields"]["user_email_status"]="unverified";
                    }

                    $output=$user_obj->update($user_obj_opt);


                    if($output["status"] == 1){

                        echo 1;

                    }else{

                        echo 0;
                    }
                }

            }


    
            
        }
     
        //fetch posts `read`, `like`, `dislike`, `comments`
        private function fetch_post_rating($post_id)
        {
            
            //store comment model's object
            $comment_obj=$this->model_objs["comment_obj"];

            //store post model's object
            $post_obj=$this->model_objs["post_obj"];

            //store post_rating model's object
            $pr_obj=$this->model_objs["pr_obj"];

            //store comemnt_replies model's object
            $cr_obj=$this->model_objs["cr_obj"];

            //Return the final output
            $output=[];
            
            //fetch post's read
            $fetch_post_read=$post_obj->select(array(
                "column_name"=>"posts.post_read",
                "where"=>"posts.post_id={$post_id}"
            ));

            if($fetch_post_read["status"] == 1 && $fetch_post_read["num_rows"]){

                //store the  posts read
                $output["read"]=$fetch_post_read["fetch_all"][0]["post_read"];
            }

            //fetch post's like
            $fetch_post_like=$pr_obj->select(array(
                "where"=>"post_ratings.post_id={$post_id} AND post_ratings.pr_action='like'"
            ));
            
            if($fetch_post_like["status"] == 1){
        
                //store the  posts read
                $output["likes"]=$fetch_post_like["num_rows"];
            }

            //fetch post's dislike
            $fetch_post_dislike=$pr_obj->select(array(
                "where"=>"post_ratings.post_id={$post_id} AND post_ratings.pr_action='dislike'"
            ));
            
            if($fetch_post_dislike["status"] == 1){
        
                //store the  posts read
                $output["dislikes"]=$fetch_post_dislike["num_rows"];
            }

            //fetch comment based on $post_id
            $fetch_post_comments=$comment_obj->select(array(
                "where"=>"comments.post_id={$post_id}"
            ));
        
            if($fetch_post_comments["status"] == 1){

                //store the total comments
                $output["comments"]=$fetch_post_comments["num_rows"];
                
                if($fetch_post_comments["num_rows"] > 0){

                    foreach($fetch_post_comments["fetch_all"] as $comment_index=>$comment){

                        //fetch comment replies based on $comment["comment_id"]
                        $fetch_comment_replies=$cr_obj->select(array(
                            "where"=>"comment_replies.comment_id={$comment['comment_id']}"
                        ));

                        if($fetch_comment_replies["status"]  == 1 && $fetch_comment_replies["num_rows"] > 0){

                            //do addition total comment replies with total comments
                            $output["comments"]=$output["comments"] + $fetch_comment_replies["num_rows"];
                        }
                    }
                }
            
            }

            return $output;
    
        }


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

    
            $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) {$username} | Lobster" : "{$username} | Lobster";
            
            $main_content=file_get_contents($this->config->domain("app/views/pages/profile.about.php"));

            $this->data["main_content"]=$main_content;
                
            $this->view("templates/profile.tmp",$this->data);
          
        }
  
        public function search()
        {

            echo "Search from users controller";
        }        
 
        //show user dashboard based on username
        public function dashboard($user_name)
        {

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
                    die($this->functions->not_found());


                }else{

                    $this->data["title_tag"]= (isset($this->data["common_info"]["total_unread_nf"])) ? "({$this->data["common_info"]["total_unread_nf"]}) Dashboard | Overview" : "Dashboard | Overview" ;

                    //view the dashboard home page
                    $this->view("dashboard/dashboard",$this->data);
                }
            }

        }


        public function notifications($username){

            echo "show {$username}'s notifications";
            
        }

        
    }

?>