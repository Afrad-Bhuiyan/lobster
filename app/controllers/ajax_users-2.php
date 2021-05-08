<?php 

/*
 * 1. posts controller's all ajax reqeust will be controlled 
 *   from this `ajax_users` controller classs.
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_users extends controller {

    //store the config class object
    private $config;
    
    //store the functions class object
    private $functions;
    
    //store the PHPMailer class object
    private $mail;
    
    //Here we will store all the required model's object
    private $model_objs=array();

    public function __construct($user_name)
    {

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

            //store all the models
            $this->model_objs["user_obj"]=$this->model("user");
            $this->model_objs["ufile_obj"]=$this->model("user_files");
            $this->model_objs["nf_obj"]=$this->model("notification");
            $this->model_objs["post_obj"]=$this->model("post");
            $this->model_objs["post_files"]=$this->model("post_files");
            $this->model_objs["pr_obj"]=$this->model("post_rating");
            $this->model_objs["comment_obj"]=$this->model("comment");
            $this->model_objs["cr_obj"]=$this->model("comment_replies");
            $this->model_objs["sp_obj"]=$this->model("saved_post");
            $this->model_objs["token_obj"]=$this->model("token");

            //check if user name exists and user is logged in
            if($this->if_exist_username($user_name) == false || $this->if_user_logged_in() == false){

                echo "You can't request to this URL {$user_name}";
                die();
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


    //user the function to check username or E-mail exist
    private function check_uname_email_exists($which, $value)
    {

        //store the user model object from $this->model_objs variable
        $user_obj=$this->model_objs["user_obj"];

        $fetch_options="";

        if($which == "username"){
            
            $fetch_options = array(
                "column_name"=>"users.user_name",
                "where"=>"users.user_name='{$value}'"
            );

            
        }elseif($which == "email"){

            $fetch_options = array(
                "column_name"=>"users.user_email",
                "where"=>"users.user_email='{$value}'"
            );
        }

        //fetch data
        $fetch_data=$user_obj->select($fetch_options);

        if($fetch_data["status"] == 1 && $fetch_data["num_rows"] > 0){

            //Return true if a single row is fetched
            return true;
            
        }else{
            
            //Return false if a single row is not fetched
            return false;
        }

    }
    
    //use the function for fetching post ratings such as `like`, `comment`, 'read' 
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

    //use the function for printing my posts table
    private function print_my_posts_table(array $posts)
    {

        $output = "";

        $output .="
            <table  class='my-posts__table my-posts__table--post'>
                <thead class='my-posts__thead'>
                    <tr class='my-posts__tr my-posts__tr-thead'>
                        <th class='my-posts__th my-posts__th--60'>
                            <div class='my-posts__th-wrap my-posts__th-wrap--60'>
                                <div class='my-posts__checkbox'>
                                    <input class='my-posts__checkinput my-posts__checkinput--single' type='checkbox' name='checkinput_all' id='checkinput_all'>
                                    <label class='my-posts__label my-posts__label--all' for='checkinput_all'></label>
                                </div>
                                <h4 class='my-posts__tbl-title'>
                                    {$posts["tbl_title"]}
                                </h4>
                            </div>
                        </th>

                        <th class='my-posts__th my-posts__th--10 my-posts__th--read'>
                            <div class='my-posts__th-wrap my-posts__th-wrap--10'>
                                <i class='fa fa-eye'></i>
                                <br>
                                <span>Read</span>
                            </div>
                        </th>

                        <th class='my-posts__th my-posts__th--10 my-posts__th--likes'>
                            <div class='my-posts__th-wrap my-posts__th-wrap--10'>
                                <i class='fa fa-thumbs-up'></i>
                                <br>
                                <span>Likes</span>
                            </div>
                        </th>

                        <th class='my-posts__th my-posts__th--10 my-posts__th--dislikes'>
                            <div class='my-posts__th-wrap my-posts__th-wrap--10'>
                                <i class='fa fa-thumbs-down'></i>
                                <br>
                                <span>Dislikes</span>
                            </div>
                        </th>

                        <th class='my-posts__th my-posts__th--10 my-posts__th--comments'>
                            <div class='my-posts__th-wrap my-posts__th-wrap--10'>
                                <i class='fa fa-comment'></i>
                                <br>
                                <span>Comments</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
        ";

        foreach($posts["all"] as $post_index=>$post){

            $badge_class = ($post["post_status"] == "published") ? "my-posts__status-badge--published" : "my-posts__status-badge--draft";
            $post_date=explode("_", $post["post_date"]);
            $post_date= $post_date[0];

            $output .="
                <tr class='my-posts__tr my-posts__tr-tbody'>
                    <td class='my-posts__td my-posts__td--60'>
                        <div class='my-posts__td-wrap my-posts__td-wrap--60'>
                            
                            <div class='my-posts__checkbox'>
                                <input class='my-posts__checkinput my-posts__checkinput--single' type='checkbox' name='checkinput_single' id='checkinput_single_{$post['post_id']}'>
                                <label class='my-posts__label my-posts__label--single' for='checkinput_single_{$post['post_id']}'></label>
                            </div>

                            <img class='my-posts__img my-posts__img--post' src='{$this->config->domain("app/uploads/posts/{$post['pfile_name']}-sm.{$post['pfile_ext']}")}' alt='{$post['post_title']}'>
                        
                            <div class='my-posts__text-content'>
                                <h5 class='my-posts__post-title'>
                                    {$post['post_title']}
                                </h5>

                                <div class='my-posts__post-info'>
                                    <span class='my-posts__post-date'>
                                        {$post_date}
                                    </span>

                                    <span class='my-posts__status-badge {$badge_class}'>
                                        {$post['post_status']}
                                    </span>
                                </div>

                                <ul class='my-posts__meta-list'>
                                    <li class='my-posts__meta-item my-posts__meta-item--read'>
                                        <i class='fa fa-eye'></i>
                                        <span>{$post['post_ratings']['read']}</span>
                                    </li>

                                    <li class='my-posts__meta-item my-posts__meta-item--likes'>
                                        <i class='fa fa-thumbs-up'></i>
                                        <span>{$post['post_ratings']['likes']}</span>

                                    </li>

                                    <li class='my-posts__meta-item my-posts__meta-item--dislikes'>
                                        <i class='fa fa-thumbs-down'></i>
                                        <span>{$post['post_ratings']['dislikes']}</span>
                                    </li>

                                    <li class='my-posts__meta-item my-posts__meta-item--comments'>
                                        <i class='fa fa-comment'></i>
                                        <span>{$post['post_ratings']['comments']}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class='dropdown dropleft my-posts__option my-posts__option--single'>
                                <button class='my-posts__btn my-posts__btn--option-single' type='button' data-toggle='dropdown' id='single_post_opt' type='button' data-offset='25,0'>
                                    <i class='fa fa-ellipsis-v'></i>
                                </button>

                                <div class='dropdown-menu my-posts__option-dropdown' aria-labelledby='#single_post_opt'>
                                    <ul class='my-posts__dropdown-list'>
                                        <li class='my-posts__dropdown-item'>
                                            <a class='my-posts__dropdown-link my-posts__dropdown-link--edit' href='{$this->config->domain("users/{$_SESSION['user_name']}/dashboard?posts=edit&post_link={$post['post_link']}")}'>
                                                Edit
                                            </a>
                                        </li>

                                        <li class='my-posts__dropdown-item'>
                                            <a class='my-posts__dropdown-link my-posts__dropdown-link--view' href='{$this->config->domain("posts?v={$post['post_link']}")}' target='_blank'>
                                                View Live
                                            </a>
                                        </li>

                                        <li class='my-posts__dropdown-item'>
                                            <a class='my-posts__dropdown-link my-posts__dropdown-link--copy' role='button' data-copy='{$this->config->domain("posts?v={$post['post_link']}")}'>Copy link</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    <td class='my-posts__td my-posts__td--10 my-posts__td--read'>
                        <div class='my-posts__td-wrap my-posts__td-wrap--10'>
                            <span>{$post['post_ratings']['read']}</span>
                        </div>
                    </td>

                    <td class='my-posts__td my-posts__td--10 my-posts__td--likes'>
                        <div class='my-posts__td-wrap my-posts__td-wrap--10'>
                            <span>{$post['post_ratings']['likes']}</span>
                        </div>
                    </td>

                    <td class='my-posts__td my-posts__td--10 my-posts__td--dislikes'>
                        <div class='my-posts__td-wrap my-posts__td-wrap--10'>
                            <span>{$post['post_ratings']['dislikes']}</span>
                        </div>
                    </td>

                    <td class='my-posts__td my-posts__td--10 my-posts__td--comments'>
                        <div class='my-posts__td-wrap my-posts__td-wrap--10'>
                            <span>{$post['post_ratings']['comments']}</span>
                        </div>
                    </td>
                </tr>
            ";
        }

        $output .= "
                </tbody>
            </table>
        ";
        
        return $output;

    }

    //use the function to fetch total filtered results
    private function total_filtered_posts($post_status)
    {

        $output= "";

        $options=array();

        $logged_user_id=$_SESSION["user_id"];

        $post_obj=$this->model_objs["post_obj"];
        
        if($post_status == "all"){
        
            $options["where"]= "posts.post_author={$logged_user_id}";
            
        }else{
            
            $options["where"]= "posts.post_author={$logged_user_id} AND posts.post_status='{$post_status}'";
        }

        $fetch_filter_posts=$post_obj->select($options);

        if($fetch_filter_posts["status"] == 1){

            $output = $fetch_filter_posts["num_rows"];

        }

        return $output;
    }

    //use the function to validate the publish 
    private function publish_post_form_validate($post_variable, $files)
    {
        $post_title=htmlspecialchars(trim($post_variable["post_title"]));
        $post_category=htmlspecialchars(trim($post_variable["post_category"]));
        $post_visibility=htmlspecialchars(trim($post_variable["post_visibility"]));
        $post_desc=htmlspecialchars(trim($post_variable["post_desc"]));

        $errors=[];

        if($files["post_img"]["error"] > 0){

            $errors["file_error"]=array(

                "target"=>".pp-sec__form-row--dropzone .pp-sec__form-col--1",
                "error_msg"=>'
                        <div class="pp-sec__form-msg pp-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i> 
                                <span>Please choose a thumbnail for your post</span>
                            </p>
                        </div>
                '
            );
        }

        if(empty($post_title)){

            $errors["post_title_error"]=array(

                "target"=>".pp-sec__form-row--title .pp-sec__form-col--1",
                "error_msg"=>'
                        <div class="pp-sec__form-msg pp-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i> 
                                <span>Please choose a title</span>
                            </p>
                        </div>
                '
            );

        }elseif(strlen($post_title) > 100){

            $errors["post_title_error"]=array(

                "target"=>".pp-sec__form-row--title .pp-sec__form-col--1",
                "error_msg"=>'
                        <div class="pp-sec__form-msg pp-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i> 
                                <span>Title length must be less than 100 character</span>
                            </p>
                        </div>
                '
            );
        }


        if(empty($post_category)){

            $errors["post_category_error"]=array(

                "target"=>".pp-sec__form-row--catagory .pp-sec__form-col--1",
                "error_msg"=>'
                        <div class="pp-sec__form-msg pp-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i> 
                                <span>Please select a category</span>
                            </p>
                        </div>
                '
            );
        }

        if(empty($post_visibility)){

            $errors["post_visibility_error"]=array(

                "target"=>".pp-sec__form-row--visibility .pp-sec__form-col--1",
                "error_msg"=>'
                    <div class="pp-sec__form-msg pp-sec__form-msg--error">
                        <p>
                            <i class="fa fa-exclamation-circle"></i> 
                            <span>Choose your post status</span>
                        </p>
                    </div>
                '
            );
        }

        if(empty($post_desc)){
            
            $errors["post_desc_error"]=array(

                "target"=>".pp-sec__form-row--editor .pp-sec__form-col--1",
                "error_msg"=>'
                    <div class="pp-sec__form-msg pp-sec__form-msg--error">
                        <p>
                            <i class="fa fa-exclamation-circle"></i> 
                            <span>Description can\'t be empty</span>
                        </p>
                    </div>
                '
            );
        }

        return $errors;
    }

    //use the function to validate the publish post image field
    private function publish_post_img_validate($files)
    {
        //validate the post_img
        $uploaded_img_num=count($files["post_img"]["name"]);
        $uploaded_img_ext=pathinfo($files["post_img"]["name"][0],PATHINFO_EXTENSION);
        $uploaded_img_size=$files["post_img"]["size"][0];
        $valid_ext=["jpg","jpeg","png"];

        $errors=[];

        if($uploaded_img_num > 1){

            $errors["length_error"]=array(

                "target"=>".pp-sec__form-row--dropzone .pp-sec__form-col--1",
                "error_msg"=>'
                    <div class="pp-sec__form-msg pp-sec__form-msg--error">
                        <p>
                            <i class="fa fa-exclamation-circle"></i> 
                            <span>You can upload only 1 File</span>
                        </p>
                    </div>
                '
            );

        }elseif(!in_array($uploaded_img_ext,$valid_ext)){

            $errors["format_error"]=array(
                "target"=>".pp-sec__form-row--dropzone .pp-sec__form-col--1",
                "error_msg"=>'
                    <div class="pp-sec__form-msg pp-sec__form-msg--error">
                        <p>
                            <i class="fa fa-exclamation-circle"></i> 
                            <span>.jpg, .jpeg or.png is supported format</span>
                        </p>
                    </div>
                '
            );

        }elseif(intval($uploaded_img_size) > 1048576){

            $errors["size_error"]=array(
                "target"=>".pp-sec__form-row--dropzone .pp-sec__form-col--1",
                "error_msg"=>'
                    <div class="pp-sec__form-msg pp-sec__form-msg--error">
                        <p>
                            <i class="fa fa-exclamation-circle"></i> 
                            <span>File size must be less than 1MB</span>
                        </p>
                    </div>
                '
                
            );
        }

        return $errors;
    }

    //use the function to validate the edit form
    private function edit_form_validate($validation_type)
    {

        $errors=[];

        if($validation_type == "post_img_new"){
            
            //validate the post_img
            $uploaded_img_num=count($_FILES["post_img_new"]["name"]);
            $uploaded_img_ext=pathinfo($_FILES["post_img_new"]["name"][0],PATHINFO_EXTENSION);
            $uploaded_img_size=$_FILES["post_img_new"]["size"][0];
            $valid_ext=["jpg","jpeg","png"];

            if($uploaded_img_num > 1){

                $errors["length_error"]=array(

                    "target"=>".ep-sec__form-row--dropzone .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>You can upload only 1 File</span>
                            </p>
                        </div>
                    '
                );

            }elseif(!in_array($uploaded_img_ext,$valid_ext)){

                $errors["format"]=array(

                    "target"=>".ep-sec__form-row--dropzone .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>.jpg, .jpeg or.png is supported format</span>
                            </p>
                        </div>
                    '
                );


            }elseif(intval($uploaded_img_size) > 1048576){

                $errors["size_error"]=array(
                    
                    "target"=>".ep-sec__form-row--dropzone .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>File size must be less than 1MB</span>
                            </p>
                        </div>
                    '
                    
                );
            }

            
        }elseif($validation_type == "post_form"){

            //store the previously uploaded image name
            $post_img_old=htmlspecialchars(trim($_POST["post_img_old"]));

            //store the newly uploaded image name
            $post_img_new=htmlspecialchars(trim($_FILES["post_img_new"]["name"]));
            
            //store the post title
            $post_title=htmlspecialchars(trim($_POST["post_title"]));
            
            //store the post category
            $post_category=htmlspecialchars(trim($_POST["post_category"]));
            
            //store the post visibility
            $post_visibility=htmlspecialchars(trim($_POST["post_visibility"]));
            
            //store the post description
            $post_desc=htmlspecialchars(trim($_POST["post_desc"]));

            if(empty($post_img_old) && empty($post_img_new)){

                $errors["file_error"]=array(

                    "target"=>".ep-sec__form-row--dropzone .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>Please choose a Thumbnail for you Post</span>
                            </p>
                        </div>
                    '
                );
            }

        
            if(empty($post_title)){

                $errors["post_title_error"]=array(

                    "target"=>".ep-sec__form-row--title .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>Please choose a title</span>
                            </p>
                        </div>
                    '
                );

            }elseif(strlen($post_title) > 100){

                $errors["post_title_error"]=array(

                    "target"=>".ep-sec__form-row--title .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>Title length must be less than 100</span>
                            </p>
                        </div>
                    '
                );
            }


            if(empty($post_category)){

                $errors["post_category_error"]=array(
                    "target"=>".ep-sec__form-row--catagory .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>Please select a category</span>
                            </p>
                        </div>
                    '
                );
            }

            if(empty($post_visibility)){

                $errors["post_visibility_error"]=array(

                    
                    "target"=>".ep-sec__form-row--visibility .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>Choose your post status</span>
                            </p>
                        </div>
                    '
                );

            }

            if(empty($post_desc)){
                
                $errors["post_desc_error"]=array(
                    "target"=>".ep-sec__form-row--editor .ep-sec__form-col--1",
                    "error_msg"=>'
                        <div class="ep-sec__form-msg ep-sec__form-msg--error">
                            <p>
                                <i class="fa fa-exclamation-circle"></i>
                                <span>Description can\'t be empty</span>
                            </p>
                        </div>
                    '
                );
            }
    
        }

        return $errors;

    }

    //use the function to check $password  matchs with the db password
    private function check_pass_match_with_current($password){

        //store logged user id
        $logged_user_id=$_SESSION["user_id"];

        //store the user's model object from $this->model_objs
        $user_obj=$this->model_objs["user_obj"];

        //store the user password from the database
        $user_password="";

        //store the final ouput to return
        $output="";

        //fetch password
        $fetch_password=$user_obj->select(array(
            "column_name"=>"
                users.user_password
            ",
            "where"=>"users.user_id={$logged_user_id}"
        ));

        if($fetch_password["status"] == 1 && $fetch_password["num_rows"] == 1){
            
            //set $user_password to fetched password
            $user_password=$fetch_password["fetch_all"][0]["user_password"];
        }

        if(!empty($user_password)){
            
            if(password_verify($password,$user_password)){

                $output = true;
                
            }else{

                $output = false;
            }
        }


        return $output;
    }
    

    //use the function for add a post into the database
    public function add_the_post($username)
    {
    
        //First validate the form
        if(isset($_POST["request"]) && $_POST["request"] == "post_img"){

            $errors=$this->publish_post_img_validate($_FILES);

            if(!empty($errors)){

                echo json_encode($errors);

            }else{

                echo 1;
            }

        }elseif(isset($_POST["request"]) && $_POST["request"] == "post_form"){

            $errors=$this->publish_post_form_validate($_POST, $_FILES);
    
            if(!empty($errors)){

                echo json_encode($errors);

            }else{

                //store post model's object from $this->model_objs
                $post_obj=$this->model_objs["post_obj"];

                //store post_files model's object from $this->model_objss
                $post_files_obj=$this->model_objs["post_files"];
            

                //Genrate a random string for post link
                $post_link=$this->functions->generate_random_str(array(
                    "model_obj"=>$post_obj,
                    "column"=>"post_link",
                    "length"=>11
                ));

                $uploaded_post_img_tmp_name=$_FILES["post_img"]["tmp_name"];

                $uploaded_post_img_name=$_FILES["post_img"]["name"];

                $uploaded_post_img_new_name=$this->functions->generate_random_str(array(
                    "model_obj"=>$post_files_obj,
                    "column"=>"pfile_name",
                    "length"=>11
                ));;

                $uploaded_post_img_ext=pathinfo($uploaded_post_img_name,PATHINFO_EXTENSION);

                date_default_timezone_set("Asia/Dhaka");
                        
                $post_title=htmlspecialchars(trim($_POST["post_title"]));
                $post_content=$_POST["post_desc"];
                $post_author=$_SESSION["user_id"];
                $post_date=date("d F, Y_h:i:sA");
                $post_cat=htmlspecialchars(trim($_POST["post_category"]));
                $post_link=$post_link;
                $post_status=htmlspecialchars(trim($_POST["post_visibility"]));

                $post_obj_output=$post_obj->insert(array(
                    "fields"=>array(
                        "post_title"=>$post_title,
                        "post_content"=>$post_content,
                        "post_author"=>$post_author,
                        "post_date"=>$post_date,
                        "post_cat"=>$post_cat,
                        "post_link"=>$post_link,
                        "post_status"=>$post_status,
                    )
                ));

                if($post_obj_output["status"] == 1){

                    if(!file_exists("app/uploads/posts/{$uploaded_post_img_new_name}.{$uploaded_post_img_ext}")){

                        if(move_uploaded_file($uploaded_post_img_tmp_name,"app/uploads/posts/{$uploaded_post_img_new_name}.{$uploaded_post_img_ext}")){
                            /*
                                sm=150x80
                                md=370x250
                                lg=870x580
                            */

                            //Resize the original Image into 150x100 and upload it
                            $img_sm_output=$this->functions->resize_upload_img(array(
                                "width"=>150,
                                "height"=>100,
                                "img_url"=>"app/uploads/posts/{$uploaded_post_img_new_name}.{$uploaded_post_img_ext}",
                                "img_upload_location"=>"app/uploads/posts/{$uploaded_post_img_new_name}-sm.{$uploaded_post_img_ext}"
                            ));

                            //Resize the original Image into 100x100 and upload it
                            $img_md_output=$this->functions->resize_upload_img(array(
                                "width"=>370,
                                "height"=>250,
                                "img_url"=>"app/uploads/posts/{$uploaded_post_img_new_name}.{$uploaded_post_img_ext}",
                                "img_upload_location"=>"app/uploads/posts/{$uploaded_post_img_new_name}-md.{$uploaded_post_img_ext}"
                            ));

                            //Resize the original Image into 100x100 and upload it
                            $img_lg_output=$this->functions->resize_upload_img(array(
                                "width"=>870,
                                "height"=>580,
                                "img_url"=>"app/uploads/posts/{$uploaded_post_img_new_name}.{$uploaded_post_img_ext}",
                                "img_upload_location"=>"app/uploads/posts/{$uploaded_post_img_new_name}-lg.{$uploaded_post_img_ext}"
                            ));

                            if($img_sm_output && $img_md_output && $img_lg_output){

                                $output=$post_files_obj->insert(array(
                                    "fields"=>array(
                                        "pfile_name"=>"{$uploaded_post_img_new_name}",
                                        "pfile_ext"=>"{$uploaded_post_img_ext}",
                                        "post_id"=>"{$post_obj_output["insert_id"]}"
                                    )
                                ));

                                if($output["status"] == 1){

                                    echo 1;

                                }else{

                                    echo 0;
                                }
                            }
                        }
                    }

                }else{

                    echo 0;
                }
            }
        }
    
    }

    //use the function to edit any post
    public function edit_the_post($username)
    {

        if(isset($_POST["request"])  && $_POST["request"] == "post_img_new"){
            
            $errors=$this->edit_form_validate("post_img_new");

            if(!empty($errors)){

                echo json_encode($errors);
            }else{

                echo 1;

            }

        }elseif(isset($_POST["request"])  && $_POST["request"] == "post_form"){

            $errors=$this->edit_form_validate("post_form");

            if(!empty($errors)){

                echo json_encode($errors);
                
            }else{

                //store the previously uploaded image name
                $post_img_old=htmlspecialchars(trim($_POST["post_img_old"]));

                //store the newly uploaded image name
                $post_img_new=htmlspecialchars(trim($_FILES["post_img_new"]["name"]));
                
                //store the newly uploaded image name
                $post_img_new_tmp_name=$_FILES["post_img_new"]["tmp_name"];
                
                //store the post title
                $post_title=htmlspecialchars(trim($_POST["post_title"]));
                
                //store the post category
                $post_category=htmlspecialchars(trim($_POST["post_category"]));
                
                //store the post visibility
                $post_visibility=htmlspecialchars(trim($_POST["post_visibility"]));
                
                //store the post description
                $post_desc=trim($_POST["post_desc"]);
                
                //store the post description
                $post_id=htmlspecialchars(trim($_POST["post_id"]));

                //post model object
                $post_obj=$this->model_objs["post_obj"];
                
                $post_obj_output=$post_obj->update(array(
                    "fields"=>array(
                        "post_title"=>$post_title,
                        "post_content"=>$post_desc,
                        "post_cat"=>$post_category,
                        "post_status"=>$post_visibility,
                    ),
                    "where"=>"post_id=$post_id"
                ));

                if($post_obj_output["status"] == 1){

                    if(!empty($post_img_new)){

                        $post_files_obj=$this->model_objs["post_files"];

                        $post_files_obj_output=$post_files_obj->select(array(
                            "where"=>"post_id={$post_id}"
                        ));

                        if($post_files_obj_output["status"] == 1){

                            $post_img_old_name=$post_files_obj_output["fetch_all"][0]["pfile_name"];

                            $post_img_old_ext=$post_files_obj_output["fetch_all"][0]["pfile_ext"];

                            //As user uloaded a new image first delete all the previously uploaded images
                            unlink("app/uploads/posts/{$post_img_old_name}.{$post_img_old_ext}");

                            unlink("app/uploads/posts/{$post_img_old_name}-sm.{$post_img_old_ext}");

                            unlink("app/uploads/posts/{$post_img_old_name}-md.{$post_img_old_ext}");
                            
                            unlink("app/uploads/posts/{$post_img_old_name}-lg.{$post_img_old_ext}");

                        }

                        //Generate Random string for newly uploaded image
                        $post_img_new_name=$this->functions->generate_random_str(array(
                            "model_obj"=>$post_files_obj,
                            "column"=>"pfile_name",
                            "length"=>11
                        ));

                        $post_img_new_ext=pathinfo($post_img_new,PATHINFO_EXTENSION);
                        
                        if(!file_exists("app/uploads/posts/{$post_img_new_name}.{$post_img_new_ext}")){

                            //Firs uploaded the origianl Image
                            if(move_uploaded_file($post_img_new_tmp_name, "app/uploads/posts/{$post_img_new_name}.{$post_img_new_ext}")){
                            
                                //Resize the original Image into 150x100 and upload it
                                $img_sm_output=$this->functions->resize_upload_img(array(
                                    "width"=>150,
                                    "height"=>100,
                                    "img_url"=>"app/uploads/posts/{$post_img_new_name}.{$post_img_new_ext}",
                                    "img_upload_location"=>"app/uploads/posts/{$post_img_new_name}-sm.{$post_img_new_ext}"
                                ));

                                //Resize the original Image into 100x100 and upload it
                                $img_md_output=$this->functions->resize_upload_img(array(
                                    "width"=>370,
                                    "height"=>250,
                                    "img_url"=>"app/uploads/posts/{$post_img_new_name}.{$post_img_new_ext}",
                                    "img_upload_location"=>"app/uploads/posts/{$post_img_new_name}-md.{$post_img_new_ext}"
                                ));

                                //Resize the original Image into 100x100 and upload it
                                $img_lg_output=$this->functions->resize_upload_img(array(
                                    "width"=>870,
                                    "height"=>580,
                                    "img_url"=>"app/uploads/posts/{$post_img_new_name}.{$post_img_new_ext}",
                                    "img_upload_location"=>"app/uploads/posts/{$post_img_new_name}-lg.{$post_img_new_ext}"
                                ));

                                if($img_sm_output && $img_md_output && $img_lg_output){

                                    $output=$post_files_obj->update(array(
                                        "fields"=>array(
                                            
                                            "pfile_name"=>"{$post_img_new_name}",
                                            "pfile_ext"=>"{$post_img_new_ext}"
                                        ),
                                        "where"=>"post_id={$post_id}"
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

                    }else{

                        echo 1;
                    }


                }else{

                    echo 0;
                }

    
            }


        }
             
        
    }

    //use the function to load all posts of a user
    public function load_my_posts()
    {

        //validate $_POST variable
        $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

        //Return the final output
        $output="";

        //store the total filtered post No
        $total_filtered_posts="";

        //store `filter` index from $_POST variable 
        $filter=$_POST["filter"];
        
        //store `user_id` index from $_SESSION variable 
        $logged_user_id=$_SESSION["user_id"];

        $post_obj=$this->model_objs["post_obj"];
    
        //store all the options for fetching my posts
        $fetch_options=array(
            "column_name"=>"
                posts.post_id,
                posts.post_title,
                posts.post_date,
                posts.post_status,
                posts.post_link,
                post_files.pfile_name,
                post_files.pfile_ext
            ",
            "join"=>array(
                "post_files"=>"post_files.post_id = posts.post_id"
            ),

            "order"=>array(
                "column"=>"posts.post_id",
                "type"=>"DESC"
            )
        );

        if($filter == "all"){

            $fetch_options["where"]="posts.post_author={$logged_user_id}";

            //fetch all post's number for calculating total pages
            $total_filtered_posts=$this->total_filtered_posts("all");

            
        }elseif($filter == "published"){

            //fetch all published post's number for calculating total pages
            $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_status='published'";
            
            $total_filtered_posts=$this->total_filtered_posts("published");
            
        }elseif($filter == "draft"){

            $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_status='draft'";
            
            //fetch all draft post's number for calculating total pages
            $total_filtered_posts = $this->total_filtered_posts("draft");
        }

        //limit to show post perpage
        $limit=20;
        
        //calculate total number of pages
        $total_pages=ceil($total_filtered_posts / $limit);
        
        //store the page no from $_POST variable
        $page_no=$_POST["page"];
        
        //calculate the offset for sql query
        $offset=($page_no - 1) * $limit;
        
        //set a limit in the sql query
        $fetch_options["limit"]="{$offset}, {$limit}";


        //fetch logged user's posts
        $fetch_my_posts=$post_obj->select($fetch_options);

        //all posts will be stored
        $my_posts=array(
            "tbl_title"=>"$filter Posts",
        );

        if($fetch_my_posts["status"] == 1 && $fetch_my_posts["num_rows"] > 0){
            
            //store all the fetched posts
            $my_posts["all"] = $fetch_my_posts["fetch_all"];

            //fetch post ratings for each posts
            foreach($my_posts["all"] as $post_index=>$post){

                $my_posts["all"][$post_index]["post_ratings"]=$this->fetch_post_rating($post['post_id']);
            }
        }

        //$total_pages=3 but user is trying to access the 4 number page. In that throw this error
        if($total_pages != 0 && $page_no > $total_pages){

            $output .="
                <div class='my-posts__msg my-posts__msg--empty'>
                    <h4 class='my-posts__msg-title my-posts__msg-title--empty'>
                        <span>Page doesn't exist</span>
                    </h4>
                </div>       
            ";

            echo $output;   

            die();
        }

        if(isset($my_posts["all"])){

            ///finally return the output in a table format
            $output .= $this->print_my_posts_table($my_posts);

        }else{

            //throw this error when did not find any posts
            $output .="
                <div class='my-posts__msg my-posts__msg--empty'>
                    <h4 class='my-posts__msg-title my-posts__msg-title--empty'>
                        <span>Empty $filter Posts</span>
                    </h4>
                </div>       
            ";
        }


        if($total_filtered_posts > $limit){
            
            $output .= "
                <div class='my-posts__pagination'>
                    <ul class='my-posts__pagination-list'>
            ";
                    if($page_no > 1){

                        $prev_page_no = $page_no - 1;

                        $output .= "
                            <li class='my-posts__pagination-item'>
                                <a class='my-posts__pagination-link my-posts__pagination-link--prev' href='{$this->config->domain("users/{$_SESSION['user_name']}/dashboard?posts=myposts&filter=$filter&page=$prev_page_no")}'>
                                    <i class='fa fa-angle-left'></i>
                                </a>
                            </li>
                        ";
                    }

                    for($a=1; $a <= $total_pages; $a++){
                        
                        $active_link=($page_no == $a) ? "my-posts__pagination-link--active" : "";

                        $output .= "
                            <li class='my-posts__pagination-item'>
                                <a class='my-posts__pagination-link my-posts__pagination-link--num {$active_link}' href='{$this->config->domain("users/{$_SESSION['user_name']}/dashboard?posts=myposts&filter=$filter&page=$a")}'>
                                    {$a}
                                </a>
                            </li>
                        ";
                    }
                

                    if($page_no < $total_pages){

                        $next_page_no = $page_no + 1;

                        $output .= "
                            <li class='my-posts__pagination-item'>
                                <a class='my-posts__pagination-link my-posts__pagination-link--next' href='{$this->config->domain("users/{$_SESSION['user_name']}/dashboard?posts=myposts&filter=$filter&page=$next_page_no")}'>
                                    <i class='fa fa-angle-right'></i>
                                </a>
                            </li>
                        ";
                    }
                
            $output .= "
                    </ul>
                </div>
            ";

        }

        echo $output;

    }

    //user the function to filter my posts from the topbar's select input
    public function filter_my_posts($user_name)
    {

        //validate the $_POST variable
        $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

        $output="";

        //store the `selected_value` index from $_POST variable
        $selected_value=$_POST["selected_value"];
        
        //store the `filter` index from $_POST variable
        $filter=$_POST["filter"];

        $logged_user_id=$_SESSION["user_id"];

        //store the post model's  object from $this->model_objs variable
        $post_obj=$this->model_objs["post_obj"];
        
        //store the post_ratings model's  object from $this->model_objs variable
        $pr_obj=$this->model_objs["pr_obj"];

        if($selected_value !== "most_read_posts"){
            
            /**
             * store an array in $all_info variable. 
             * the index of array will be the total post_rating and value will be the post_id
             * such as:
             *      $all_info=array(
             *       3=>62
             *      )
             * 
             * Here index 3 is the total_like or total_dislike and 62 is the post_id 
             */
            
            $all_info=[];

            $fetch_options=array(
                "column_name"=>"
                    posts.post_id,
                    posts.post_author,
                    posts.post_status
                "
            );

            if($filter == "all"){

                //fetch all post of logged user's
                $fetch_options["where"]="posts.post_author={$logged_user_id}";
                
            }elseif($filter == "published"){
                
                //fetch all published posts of logged user's
                $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_status='published'";
                
            }elseif($filter == "draft"){
                
                //fetch all draft posts of logged user's
                $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_status='draft'";
            }

            //fetch logged user's posts
            $fetch_posts=$post_obj->select($fetch_options);
        
            if($fetch_posts["status"] == 1 && $fetch_posts["num_rows"] > 0){

                //fetch post rating for each post
                foreach($fetch_posts["fetch_all"] as $post_index=>$post){

                    //store all options for fetching post ratings
                    $fetch_options=array();

                    if($selected_value == "most_liked_posts"){ 

                        //set posts.pr_action ='like' if user select to see most liked posts
                        $fetch_options["where"]="post_ratings.post_id={$post['post_id']} AND post_ratings.pr_action='like'";
                        
                    }elseif($selected_value == "most_disliked_posts"){
                        
                        //set posts.pr_action ='dislike' if user select to see most disliked posts
                        $fetch_options["where"]="post_ratings.post_id={$post['post_id']} AND post_ratings.pr_action='dislike'";
                    }

                    //fetch post rating for each user's posts
                    $fetch_post_rating=$pr_obj->select($fetch_options);

                    if($fetch_post_rating["status"] == 1 && $fetch_post_rating["num_rows"] > 0){
                        
                        //store the array in $all_info
                        $all_info[$fetch_post_rating["num_rows"]]=$post["post_id"];
                        
                    }
                }
                
                //sorting the array according to indexes
                krsort($all_info);

                if(!empty($all_info)){

                    $posts=array(
                        "tbl_title"=>"{$filter} Posts",
                        "all"=>array()
                    );
                    
                    //$rating_no == 'like' or 'dislike' total numbers
                    foreach($all_info as $rating_no=>$post_id){

                        $fetch_filtered_posts=$post_obj->select(array(
                            "column_name"=>"
                                posts.post_id,
                                posts.post_title,
                                posts.post_status,
                                posts.post_link,
                                posts.post_date,
                                post_files.pfile_name,
                                post_files.pfile_ext
                            ",
                            "join"=>array(
                                "post_files"=>"post_files.post_id = posts.post_id"
                            ),
                            "where"=>"posts.post_id={$post_id}"
                        ));

                        $posts["all"][]=$fetch_filtered_posts['fetch_all'][0];

                        foreach($posts["all"] as $post_index=>$post){

                            $posts["all"][$post_index]["post_ratings"]=$this->fetch_post_rating($post['post_id']);
                        }
                    }

                    $output .= $this->print_my_posts_table($posts);
        
                }else{

                    $output .="
                        <div class='row my-posts__msg-row my-posts__msg-row--notfound'>
                            <div class='col-12 my-posts__msg-col my-posts__msg-col--notfound'>
                                <h4 class='my-posts__msg-title my-posts__msg-title--notfound'>
                                    <span>Nothing Found</span>
                                </h4>
                            </div>
                        </div>
                    ";
                }

            }else{
                $output .="
                    <div class='row my-posts__msg-row my-posts__msg-row--notfound'>
                        <div class='col-12 my-posts__msg-col my-posts__msg-col--notfound'>
                            <h4 class='my-posts__msg-title my-posts__msg-title--notfound'>
                                <span>Nothing Found</span>
                            </h4>
                        </div>
                    </div>
                ";

            }

        }else{

            $fetch_options=array(
                "column_name"=>"
                    posts.post_id,
                    posts.post_title,
                    posts.post_author,
                    posts.post_read,
                    posts.post_link,
                    posts.post_date,
                    posts.post_status,
                    post_files.pfile_name,
                    post_files.pfile_ext

                ",
                "join"=>array(
                    "post_files"=>"post_files.post_id = posts.post_id"
                ),

                "order"=>array(
                    "column"=>"posts.post_read",
                    "type"=>"DESC"
                )
            );

            if($filter == "all"){

                //fetch all post of logged user's
                $fetch_options["where"]="posts.post_author={$logged_user_id}";
                
            }elseif($filter == "published"){
                
                //fetch all published posts of logged user's
                $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_status='published'";
                
            }elseif($filter == "draft"){
                
                //fetch all draft posts of logged user's
                $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_status='draft'";
            }

            $posts=array(
                "tbl_title"=>"$filter Posts",
            );

            //fetch logged user's posts
            $fetch_posts=$post_obj->select($fetch_options);

            if($fetch_posts["status"] == 1 && $fetch_posts["num_rows"] > 0){

                $posts["all"]=$fetch_posts["fetch_all"];
                
                foreach($posts["all"] as $post_index=>$post){
                    
                    $posts["all"][$post_index]["post_ratings"]=$this->fetch_post_rating($post['post_id']);
                }
                
                $output .= $this->print_my_posts_table($posts);


            }else{

                $output .="
                    <div class='row my-posts__msg-row my-posts__msg-row--notfound'>
                        <div class='col-12 my-posts__msg-col my-posts__msg-col--notfound'>
                            <h4 class='my-posts__msg-title my-posts__msg-title--notfound'>
                                <span>Nothing Found</span>
                            </h4>
                        </div>
                    </div>
                ";

            }
        }


        echo $output;

        // print_r($output);

    }

    //use the function to search from my posts
    public function search_my_posts()
    {

        //Store the final output to return
        $output="";

        //validate the $_POST variable
        $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

        //store the `seach_query` index from $_POST variable
        $search_query=$_POST["search_query"];
        
        //store the `filter` index from $_POST variable
        $filter=$_POST["filter"];

        //store the `user_id` index from $_SESSION variable
        $logged_user_id=$_SESSION["user_id"];

        //store the post model's  object from $this->model_objs variable
        $post_obj=$this->model_objs["post_obj"];
        
        //store the post_ratings model's  object from $this->model_objs variable
        $pr_obj=$this->model_objs["pr_obj"];

        $fetch_options=array(
            "column_name"=>"
                posts.post_id,
                posts.post_title,
                posts.post_status,
                posts.post_link,
                posts.post_date,
                post_files.pfile_name,
                post_files.pfile_ext
            ",

            "join"=>array(
                "post_files"=>"post_files.post_id = posts.post_id"
            )
        );

        if($filter == "all"){

            //fetch all post of logged user's
            $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_title LIKE '%{$search_query}%'";
            
        }elseif($filter == "published"){
            
            //fetch all published posts of logged user's
            $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_status='published' AND posts.post_title LIKE '%{$search_query}%'";
            
        }elseif($filter == "draft"){
            
            //fetch all draft posts of logged user's
            $fetch_options["where"]="posts.post_author={$logged_user_id} AND posts.post_status='draft' AND posts.post_title LIKE '%{$search_query}%'";
        }

        //store all the options for printing the table
        $posts=array(
            "tbl_title"=>"$filter Posts"
        );

        //fetch logged user's posts
        $fetch_posts=$post_obj->select($fetch_options);

        if($fetch_posts["status"] == 1 && $fetch_posts["num_rows"] > 0){
            
            $posts["all"]=$fetch_posts["fetch_all"];

            //fetch post_ratings for each posts
            foreach($posts["all"] as $post_index=>$post){
                
                //store all ratings in $posts["all"]
                $posts["all"][$post_index]["post_ratings"]=$this->fetch_post_rating($post['post_id']);
            }

            //store the HTML table with fetch posts
            $output .= $this->print_my_posts_table($posts);


        }else{

            $output .="
                <div class='row my-posts__msg-row my-posts__msg-row--notfound'>
                    <div class='col-12 my-posts__msg-col my-posts__msg-col--notfound'>
                        <h4 class='my-posts__msg-title my-posts__msg-title--notfound'>
                            <span>No Result Found</span>
                        </h4>
                    </div>
                </div>
            ";
        }

        echo $output;
    }

    //use the function to delete saved posts
    public function delete_saved_posts()
    {

        //Store the final output to return
        $output=[];

        //validate the $_POST variable
        $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

        //store the `seach_query` index from $_POST variable
        $sp_id=$_POST["sp_id"];

        //store the saved_posts model object from $this->model_objs variable
        $sp_obj=$this->model_objs["sp_obj"];

        $delete_saved_post=$sp_obj->delete(array(
            "where"=>"saved_posts.sp_id={$sp_id}"
        ));

        if($delete_saved_post["status"] == 1 && $delete_saved_post["affected_rows"] > 0){
        
            $output["error"] = 0;

            //fetch total saved posts
            $fetch_saved_posts=$sp_obj->select();

            if($fetch_saved_posts["status"] == 1){

                $output["total_saved_posts"] = $fetch_saved_posts["num_rows"];
            }
            
            
        }else{
            
            $output["error"] = 1;
        }


        echo json_encode($output);

    }


    //index method is the default method
    public function index($user_name)
    {

        echo "index method from  ajax_users";
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


    //load personal information in account settings
    public function load_personal_information()
    {

        //Return the final output
        $output="";

        //store the logged users's user_id
        $logged_user_id=$_SESSION["user_id"];
        
        //store th user object model from $model_objs variable
        $user_obj=$this->model_objs["user_obj"];

        //fetched logged user's all information
        $fetch_logged_userInfo=$user_obj->select(array(
            "column_name"=>"
                users.user_id,
                users.user_fname,
                users.user_lname,
                users.user_name,
                users.user_role,
                users.user_email,
                users.user_email_status,
                users.user_desc,
                users.user_country,
                users.user_joining_date
            ",
            "where"=>"users.user_id={$logged_user_id}"
        ));

        if($fetch_logged_userInfo["status"] == 1 && $fetch_logged_userInfo["num_rows"] == 1){

            //store the fetch information
            $logged_user_info=$fetch_logged_userInfo["fetch_all"][0];

            //store the E-mail Verification Message
            $verification_msg="";

            //store the badge for verified or unverfied
            $badge="";

            //store the description 
            $desc="Add a description to let people know about yourself";

            if($logged_user_info["user_email_status"] == "verified"){

                $badge="
                    <span class='acc-sett__badge acc-sett__badge--verified'>
                        verified
                    </span>
                ";

            }else{

                $badge="
                    <span class='acc-sett__badge acc-sett__badge--unverified'>
                        unverified
                    </span>
                ";

                $verification_msg="
                    <div class='acc-sett__msg acc-sett__msg--verification'>
                        <p>
                            To verify you E-mail account, A verification mail was sent to <span>{$logged_user_info['user_email']}</span>. Didn't you receive the mail? <a class='acc-sett__link acc-sett__link--resend' role='button' data-send_code='email_verification'>Resend</a>
                        </p>
                    </div>
                ";
            }

            if($logged_user_info["user_desc"] != "false"){

                $desc = $logged_user_info["user_desc"];
            }

        
            $output .= "
                <div class='row acc-sett__opt acc-sett__opt--pi'>
                    <div class='col-12'>
                        <h4 class='acc-sett__opt-title'>
                            Personal Information
                        </h4>

                        <p class='acc-sett__opt-subtitle'>
                            You can your account's personal information. You can alos modify them
                        </p>

                        <form class='acc-sett__form acc-sett__form--pi'>
                            <div class='row acc-sett__form-row acc-sett__form-row--fname'>
                                <div class='col-12 col-md-3 acc-sett__form-col acc-sett__form-col--label'>
                                    <div class='acc-sett__form-label'>
                                        <span>First Name</span>
                                        <strong>:</strong>
                                    </div>
                                </div>

                                <div class='col-12 col-md-9 acc-sett__form-col acc-sett__form-col--field'>
                                    <div class='acc-sett__form-field'>
                                        <div class='acc-sett__ff-value' data-editable='true'>
                                            <p class='acc-sett__ff-value-text'>
                                                {$logged_user_info['user_fname']}
                                            </p>
                                            <button class='acc-sett__btn acc-sett__btn--edit' name='user_fname' type='button'>
                                                <i class='fa fa-edit'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class='row acc-sett__form-row acc-sett__form-row--lname'>
                                <div class='col-12 col-md-3 acc-sett__form-col acc-sett__form-col--label'>
                                    <div class='acc-sett__form-label'>
                                        <span>Last Name</span>
                                        <strong>:</strong>
                                    </div>
                                </div>

                                <div class='col-12 col-md-9 acc-sett__form-col acc-sett__form-col--field'>
                                    <div class='acc-sett__form-field'>
                                        <div class='acc-sett__ff-value' data-editable='true'>
                                            <p class='acc-sett__ff-value-text'>
                                                {$logged_user_info['user_lname']}
                                            </p>
                                            <button class='acc-sett__btn acc-sett__btn--edit' name='user_lname' type='button'>
                                                <i class='fa fa-edit'></i>
                                            </button>
                                        </div>
                                    </div>  
                                </div>
                            </div>
                            
                            <div class='row acc-sett__form-row acc-sett__form-row--username'>
                                <div class='col-12 col-md-3 acc-sett__form-col acc-sett__form-col--label'>
                                    <div class='acc-sett__form-label'>
                                        <span>Username</span>
                                        <strong>:</strong>
                                    </div>
                                </div>

                                <div class='col-12 col-md-9 acc-sett__form-col acc-sett__form-col--field'>
                                    <div class='acc-sett__form-field'>
                                        <div class='acc-sett__ff-value' data-editable='false'>
                                            <p class='acc-sett__ff-value-text'>
                                                {$logged_user_info['user_name']}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class='row acc-sett__form-row acc-sett__form-row--urole'>
                                <div class='col-12 col-md-3 acc-sett__form-col acc-sett__form-col--label'>
                                    <div class='acc-sett__form-label'>
                                        <span>Role</span>
                                        <strong>:</strong>
                                    </div>
                                </div>

                                <div class='col-12 col-md-9 acc-sett__form-col acc-sett__form-col--field'>
                                    <div class='acc-sett__form-field'>
                                        <div class='acc-sett__ff-value' data-editable='false'>
                                            <p class='acc-sett__ff-value-text'>
                                                {$logged_user_info['user_role']}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class='row acc-sett__form-row acc-sett__form-row--email'>
                                <div class='col-12 col-md-3 acc-sett__form-col acc-sett__form-col--label'>
                                    <div class='acc-sett__form-label'>
                                        <span>E-mail Address</span>
                                        <strong>:</strong>
                                    </div>
                                </div>

                                <div class='col-12 col-md-9 acc-sett__form-col acc-sett__form-col--field'>
                                    <div class='acc-sett__form-field'>
                                        <div class='acc-sett__ff-value' data-editable='true'>
                                            <p class='acc-sett__ff-value-text'>
                                                {$logged_user_info['user_email']} {$badge}
                                            </p>
                                            <button class='acc-sett__btn acc-sett__btn--edit' name='user_email' type='button'>
                                                <i class='fa fa-edit'></i>
                                            </button>
                                        </div>
                                    </div>
                                    {$verification_msg}
                                </div>
                            </div>
                        
                            <div class='row acc-sett__form-row acc-sett__form-row--desc'>
                                <div class='col-12 col-md-3 acc-sett__form-col acc-sett__form-col--label'>
                                    <div class='acc-sett__form-label'>
                                        <span>Description</span>
                                        <strong>:</strong>
                                    </div>
                                </div>

                                <div class='col-12 col-md-9 acc-sett__form-col acc-sett__form-col--field'>
                                    <div class='acc-sett__form-field'>
                                        <div class='acc-sett__ff-value' data-editable='true'>
                                            <p class='acc-sett__ff-value-text'>
                                               {$desc}
                                            </p>
                                            <button class='acc-sett__btn acc-sett__btn--edit' name='user_desc' type='button'>
                                                <i class='fa fa-edit'></i>
                                            </button>
                                        </div>

                                 
                                    </div>
                                </div>
                            </div>

                            <div class='row acc-sett__form-row cc-sett__form-row--country'>
                                <div class='col-12 col-md-3 acc-sett__form-col acc-sett__form-col--label'>
                                    <div class='acc-sett__form-label'>
                                        <span>Country</span>
                                        <strong>:</strong>
                                    </div>
                                </div>

                                <div class='col-12 col-md-9 acc-sett__form-col acc-sett__form-col--field'>
                                    <div class='acc-sett__form-field'>
                                        <div class='acc-sett__ff-value' data-editable='false'>
                                            <p class='acc-sett__ff-value-text'>
                                                {$logged_user_info['user_country']}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='row acc-sett__form-row cc-sett__form-row--jd'>
                                <div class='col-12 col-md-3 acc-sett__form-col acc-sett__form-col--label'>
                                    <div class='acc-sett__form-label'>
                                        <span>Joining Date</span>
                                        <strong>:</strong>
                                    </div>
                                </div>

                                <div class='col-12 col-md-9 acc-sett__form-col acc-sett__form-col--field'>
                                    <div class='acc-sett__form-field'>
                                            <div class='acc-sett__ff-value' data-editable='false'>
                                                <p class='acc-sett__ff-value-text'>
                                                    {$logged_user_info['user_joining_date']}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            ";


            //finaly return the ouptut
            echo $output;   
        }
    }


    //use the function to update the profile and bg_img in user_files table
    public function update_user_files()
    {

        //validate the $_POST variable
        $_POST = filter_var_array($_POST,FILTER_SANITIZE_STRING);

        //validate the $_FILES variable
        $_FILES = filter_var_array($_FILES,FILTER_SANITIZE_STRING);

        //store the `name_attr` index from $_POST variable
        $name_attr=$_POST["name_attr"];

        //store the uploaded file info
        $file_info=$_FILES[$name_attr];
    
        //store the uploaded file name
        $file_name=$file_info["name"];
        
        //store the uploaded file extension
        $file_ext=pathinfo($file_name,PATHINFO_EXTENSION);
        
        //store the uploaded file size
        $file_size=$file_info["size"];
        
        //store the uploaded file tmp_name
        $file_tmp_name=$file_info["tmp_name"];

        //store all the valid extensions
        $valid_ext=["jpg","jpeg","png"];
        
        //store user_files's object $this->model_obj variable
        $ufile_obj=$this->model_objs["ufile_obj"];
    
        //store all the errors
        $errors=array();

        //store all the ouputs
        $output=array();

        //store all the ouputs
        $logged_user_id=$_SESSION["user_id"];

        $update_options=array();

        if($name_attr == "profile_img"){

            if(!in_array($file_ext,$valid_ext)){

                $errors["format_error"]=array(
                    "error_msg"=>'
                        <div class="acc-sett__msg acc-sett__msg--error">
                            <p>.jpg, .jpeg or .png is supported format</p>
                        </div>
                    '
                );

            }elseif(intval($file_size) > 1048576){

                $errors["format_error"]=array(
                    "error_msg"=>'
                        <div class="acc-sett__msg acc-sett__msg--error">
                            <p>File size must be less than 1MB</p>
                        </div>
                    '
                );

            }else{

                //fetch user's previously uploaded profile picture
                $fetch_prev_file=$ufile_obj->select(array(
                    "where"=>"user_files.user_id={$logged_user_id} AND user_files.ufile_usage='profile_img' AND user_files.ufile_status=1"    
                ));

                //condition true means than user uploaded the profile picture before
                if($fetch_prev_file["status"] == 1 && $fetch_prev_file['num_rows'] == 1){

                    /**
                     * now we have to delete the 
                     * previous image from the server 
                     */

                     //store the previouse file name
                     $prev_file_name=$fetch_prev_file["fetch_all"][0]["ufile_name"];
                     
                     //store the previouse file extension
                    $prev_file_ext=$fetch_prev_file["fetch_all"][0]["ufile_ext"];

                    //delete the original file first
                    unlink("app/uploads/users/profile/{$prev_file_name}.{$prev_file_ext}");
                    
                    //delete the small file of the original one
                    unlink("app/uploads/users/profile/{$prev_file_name}-sm.{$prev_file_ext}");
                    
                    //delete the medium file of the original one
                    unlink("app/uploads/users/profile/{$prev_file_name}-md.{$prev_file_ext}");
                    
                    //delete the large file of the original one
                    unlink("app/uploads/users/profile/{$prev_file_name}-lg.{$prev_file_ext}");

                }

                //generate a unique name for upload file
                $file_name=$this->functions->generate_random_str(array(
                    "model_obj"=>$ufile_obj,
                    "column"=>"ufile_name",
                    "length"=>11
                ));

                if(!file_exists("app/uploads/users/profile/{$file_name}.{$file_ext}")){

                    if(move_uploaded_file($file_tmp_name,"app/uploads/users/profile/{$file_name}.{$file_ext}")){

                        /* sm=100x100 | md=150x1500 | lg=200x200
                         */

                        //Resize the original Image into 100x100 and upload it
                        $file_sm_output=$this->functions->resize_upload_img(array(
                            "width"=>100,
                            "height"=>100,
                            "img_url"=>"app/uploads/users/profile/{$file_name}.{$file_ext}",
                            "img_upload_location"=>"app/uploads/users/profile/{$file_name}-sm.{$file_ext}"
                        ));

                        //Resize the original Image into 150x150 and upload it
                        $file_md_output=$this->functions->resize_upload_img(array(
                            "width"=>150,
                            "height"=>150,
                            "img_url"=>"app/uploads/users/profile/{$file_name}.{$file_ext}",
                            "img_upload_location"=>"app/uploads/users/profile/{$file_name}-md.{$file_ext}"
                        ));

                        //Resize the original Image into 200x200 and upload it
                        $file_lg_output=$this->functions->resize_upload_img(array(
                            "width"=>200,
                            "height"=>200,
                            "img_url"=>"app/uploads/users/profile/{$file_name}.{$file_ext}",
                            "img_upload_location"=>"app/uploads/users/profile/{$file_name}-lg.{$file_ext}"
                        ));

                        if($file_sm_output && $file_md_output && $file_lg_output){

                            $update_options=array(
                                "fields"=>array(
                                    "ufile_name"=>$file_name,
                                    "ufile_ext"=>$file_ext,
                                    "ufile_status"=>1
                                ),
                                "where"=>"user_files.user_id={$logged_user_id} AND user_files.ufile_usage='profile_img'"
                            );
                        }
                    }
                }
            }

        }elseif($name_attr == "bg_img"){

            if(!in_array($file_ext,$valid_ext)){

                $errors["format_error"]=array(
                    "error_msg"=>'
                        <div class="acc-sett__msg acc-sett__msg--error">
                            <p>.jpg, .jpeg or .png is supported format</p>
                        </div>
                    '
                );

            }elseif(intval($file_size) > (1048576 * 2)){

                $errors["format_error"]=array(
                    "error_msg"=>'
                        <div class="acc-sett__msg acc-sett__msg--error">
                            <p>File size must be less than 2MB</p>
                        </div>
                    '
                );

            }else{  

                //fetch user's previously uploaded profile picture
                $fetch_prev_file=$ufile_obj->select(array(
                    "where"=>"user_files.user_id={$logged_user_id} AND user_files.ufile_usage='bg_img' AND user_files.ufile_status=1"    
                ));

                //condition true means than user uploaded the profile picture before
                if($fetch_prev_file["status"] == 1 && $fetch_prev_file['num_rows'] == 1){

                    /**
                     * now we have to delete the 
                     * previous image from the server 
                     */

                     //store the previouse file name
                     $prev_file_name=$fetch_prev_file["fetch_all"][0]["ufile_name"];
                     
                     //store the previouse file extension
                    $prev_file_ext=$fetch_prev_file["fetch_all"][0]["ufile_ext"];

                    //delete the original file first
                    unlink("app/uploads/users/bg/{$prev_file_name}.{$prev_file_ext}");
                    
                    //delete the small file of the original one
                    unlink("app/uploads/users/bg/{$prev_file_name}-sm.{$prev_file_ext}");
                
                    //delete the large file of the original one
                    unlink("app/uploads/users/bg/{$prev_file_name}-lg.{$prev_file_ext}");

                }


                 //generate a unique name for upload file
                 $file_name=$this->functions->generate_random_str(array(
                    "model_obj"=>$ufile_obj,
                    "column"=>"ufile_name",
                    "length"=>11
                ));

                if(!file_exists("app/uploads/users/bg/{$file_name}.{$file_ext}")){

                    if(move_uploaded_file($file_tmp_name,"app/uploads/users/bg/{$file_name}.{$file_ext}")){

                        /* sm=100x100 | md=150x1500 | lg=200x200
                         */

                        //Resize the original Image into 100x100 and upload it
                        $file_sm_output=$this->functions->resize_upload_img(array(
                            "width"=>250,
                            "height"=>100,
                            "img_url"=>"app/uploads/users/bg/{$file_name}.{$file_ext}",
                            "img_upload_location"=>"app/uploads/users/bg/{$file_name}-sm.{$file_ext}"
                        ));

                        //Resize the original Image into 200x200 and upload it
                        $file_lg_output=$this->functions->resize_upload_img(array(
                            "width"=>1400,
                            "height"=>350,
                            "img_url"=>"app/uploads/users/bg/{$file_name}.{$file_ext}",
                            "img_upload_location"=>"app/uploads/users/bg/{$file_name}-lg.{$file_ext}"
                        ));

                        if($file_sm_output && $file_lg_output){

                            $update_options=array(
                                "fields"=>array(
                                    "ufile_name"=>$file_name,
                                    "ufile_ext"=>$file_ext,
                                    "ufile_status"=>1
                                ),
                                "where"=>"user_files.user_id={$logged_user_id} AND user_files.ufile_usage='bg_img'"
                            );
                        }
                    }
                }
            }

        }
        
        if(!empty($errors)){

            $output["error_status"] = 1;

            $output["errors"] = $errors;

        }else{

            $update_ufile=$ufile_obj->update($update_options);

            if($update_ufile["status"] == 1 && $update_ufile["affected_rows"] > 0){
                
                $output["error_status"] = 0;
            }
        }
        
        
        echo json_encode($output);
        
    }
    
    //use the function to update personal information from dashboard
    public function update_personal_info()
    {

        $output = array();

        //store the logged users's user_id
        $logged_user_id=$_SESSION["user_id"];

        //validate the $_POST variable
        $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

        //store the name attr's valuer where db column name is stored
        $name_attr=$_POST["name_attr"];

        //store the value of input element when user want to update information
        $input_value= isset($_POST["input_value"]) ? $_POST["input_value"] : null;

        //store the action from $_POST;
        $action=$_POST["action"];

        //store th user object model from $model_objs variable
        $user_obj=$this->model_objs["user_obj"];

        if($action == "edit"){

            $fetch_options=array(
                "column_name"=>"users.$name_attr",
                "where"=>"users.user_id=$logged_user_id"
            );
    
            $fetch_col=$user_obj->select($fetch_options);

            
            if($fetch_col["status"] == 1 && $fetch_col["num_rows"] == 1){
                
                //store the fetch  column all information
                $fetched_info=$fetch_col["fetch_all"][0];
                
                //store value for input element
                $input_value = ($fetched_info[$name_attr] == "false") ? "" : $fetched_info[$name_attr];
                
                //store the length when 
                $input_length=($input_value == "false") ? 0 : strlen($input_value); 

                if($name_attr == "user_desc"){

                    $output=array(
                        "error_status"=>0,
                        "html"=>"
                            <div class='acc-sett__input-wrap'>
                                <textarea class='acc-sett__form-input acc-sett__form-input--textarea' name='{$name_attr}'>{$input_value}</textarea>
                                <div class='acc-sett__input-lc'>
                                    <span>{$input_length}</span>/<strong>600</strong>
                                </div>
                            </div>                    
                        "
                    );

                }else{

                    $output=array(
                        "error_status"=>0,
                        "html"=>"
                            <div class='acc-sett__input-wrap'>
                                <input class='acc-sett__form-input' type='text' name='{$name_attr}' value='$input_value'>
                            </div> 
                        "
                    );
                }

            }
    

        }elseif($action == "update"){

            //store all the errros
            $errors=array();

            //store all the options for update a record
            $update_options=array();

            if($name_attr == "user_fname"){

                if(strlen($input_value) < 3){

                    $errors["fname-error"]=array(
                        "error_msg"=>"
                            <div class='acc-sett__msg acc-sett__msg--error'>
                                <p>
                                    Minimum length must be 3
                                </p>
                            </div>
                        "
                    );
    
                }elseif(strlen($input_value) > 15){
                    
                    $errors["fname-error"]=array(
                        "error_msg"=>"
                            <div class='acc-sett__msg acc-sett__msg--error'>
                                <p>
                                    Maximum length must be 15
                                </p>
                            </div>
                        "
                    );
                    
                }else{
                    
                    $update_options=array(
                        "fields"=>array(
                            "users.user_fname"=>$input_value
                        ),
                        "where"=>"users.user_id={$logged_user_id}"
                    );
                }
                    

            }elseif($name_attr == "user_lname"){

                if(strlen($input_value) < 3){

                    $errors["lname-error"]=array(
                        "error_msg"=>"
                            <div class='acc-sett__msg acc-sett__msg--error'>
                                <p>
                                    Minimum length must be 3
                                </p>
                            </div>
                        "
                    );
                    
                }elseif(strlen($input_value) > 15){
    
                    $errors["lname-error"]=array(
                        "error_msg"=>"
                            <div class='acc-sett__msg acc-sett__msg--error'>
                                <p>
                                    Maximum length must be 15
                                </p>
                            </div>
                        "
                    );
    
                }else{

                    $update_options=array(
                        "fields"=>array(
                            "users.user_lname"=>$input_value
                        ),
                        "where"=>"users.user_id={$logged_user_id}"
                    );

                }

            }elseif($name_attr=="user_email"){

                $input_value=strtolower($input_value);

                if(!filter_var($input_value,FILTER_VALIDATE_EMAIL)){
    
                    $errors["email-error"]=array(
                        "error_msg"=>"
                            <div class='acc-sett__msg acc-sett__msg--error'>
                                <p>
                                    Enter a valid E-mail address
                                </p>
                            </div>
                        "
                    );
    
                }else{

                    //check if user is trying to update with the previous E-mail address
                    $fetch_prev_email=$user_obj->select(array(
                        "where"=>"users.user_id={$logged_user_id} AND users.user_email='{$input_value}'"
                    ));
                    
                    //condition true means that user wrote a new E-mail address.
                    if($fetch_prev_email["status"] == 1 && $fetch_prev_email["num_rows"] == 0){

                        //Now let's check if the new E-mail exist or not
                        if($this->check_uname_email_exists("email",$input_value)){

                            $errors["email-error"]=array(
                                "error_msg"=>"
                                    <div class='acc-sett__msg acc-sett__msg--error'>
                                        <p>The E-mail is already in used. Try with another one</p>
                                    </div>
                                "
                            );
                            
                        }else{

                            $update_options=array(
                                "fields"=>array(
                                    "users.user_email"=>$input_value,
                                    "users.user_email_status"=>"unverified"
                                ),
                                "where"=>"users.user_id={$logged_user_id}"
                            );

                            //set the timezone to Asia/Dhaka
                            date_default_timezone_set("Asia/Dhaka");
                                    
                            //store the token model's object from $this->model_objs variable
                            $token_obj=$this->model_objs["token_obj"];
                            
                            //delete any existing token of this user
                            $token_obj->delete(array(
                                "where"=>"tokens.user_id={$logged_user_id}"
                            ));

                            //Generate a selector
                            $token_selector=bin2hex(random_bytes(8));
                                    
                            //Generate a validator
                            $random_bytes=random_bytes(32);
                            
                            //token validator for URL
                            $url_token_validator=bin2hex($random_bytes);
                            
                            //token validato to store into the database
                            $db_token_validator=password_hash($random_bytes,PASSWORD_DEFAULT);

                            //insert the token into the database
                            $insert_token=$token_obj->insert(array(
                                "fields"=>array(
                                    "token_selector"=>"$token_selector",
                                    "token_validator"=>"$db_token_validator",
                                    "token_expires"=>date("U") + 3600,
                                    "token_usage"=>"email_validation",
                                    "user_id"=>$logged_user_id
                                )
                            ));

                            if($insert_token["status"] !== 1 && !isset($insert_token["insert_id"])){

                                $output["inser_token"]=$insert_token;

                                echo json_encode($output);
                                die();
                            }

                            /**
                             * Send the token to the 
                             * logged  user
                             */

                            // //send a verification E-mail to the user to verifiy his/her E-mail address
                            $verify_email_link=$this->config->domain("verify_email?selector={$token_selector}&validator={$url_token_validator}");

                            $send_mail=$this->functions->send_mail($this->mail,array(
                                "receiver"=>"{$input_value}",
                                "subject"=>"Email Confirmation",
                                "body"=>"Here is your link <a href='{$verify_email_link}' target='_blank'>{$verify_email_link}</a>",
                                "alt_body"=>"Here is your link <a href='{$verify_email_link}' target='_blank'>{$verify_email_link}</a>"
                            ));
                        }

                    }else{
                        
                        $update_options=array(
                            "fields"=>array(
                                "users.user_email"=>$input_value,
                            ),
                            "where"=>"users.user_id={$logged_user_id}"
                        );
                    }
                }
            
            }elseif($name_attr == "user_desc"){

                //store only 600 charecters
                $input_value = substr($input_value,0,600);

                $update_options=array(
                    "fields"=>array(
                        "users.user_desc"=>$input_value
                    ),
                    "where"=>"users.user_id={$logged_user_id}"
                );
            }
            
            if(!empty($errors)){

                $output=array(
                    "error_status"=>1,
                    "errors"=>$errors
                );

            }else{
            
                $update_record=$user_obj->update($update_options);

                if($update_record["status"] == 1){

                    $output=array(
                        "error_status"=>0,
                    );
                }
            }
        }

        echo json_encode($output);


    }

    //use the for the  security setting
    public function security_settings()
    {
        
        //validate the $_POST variable
        $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);
        
        //store the `setting` index from $_POST variable
        $setting_type=isset($_POST["setting"]) ? isset($_POST["setting"]) : "";

        //store the user's model object from $this->model_objs
        $user_obj=$this->model_objs["user_obj"];

        //store the final output to return
        $output = array();

        //store all the errros
        $errors=[];

        //store logged user id
        $logged_user_id=$_SESSION["user_id"];

        if($setting_type == "change"){
        
            //store the `current_password` index $_POST variable
            $current_pass=$_POST["current_password"];
        
            //store the `new_password` index $_POST variable
            $new_pass=$_POST["new_password"];

            //store the `confirm_password` index $_POST variable
            $confirm_pass=$_POST["confirm_password"];

            if(empty($current_pass) || empty($new_pass) || empty($confirm_pass)){

                if(empty($current_pass)){

                    $errors["current_pass_error"]=array(
                        "target"=>".secu-sett__form-field--currPass",
                        "error_msg"=>'
                            <div class="secu-sett__form-msg secu-sett__form-msg--error">
                                <p class="secu-sett__form-msg__text">
                                    Please fill out this field
                                </p>
                            </div> 
                        '
                    );
                }
                
                if(empty($new_pass)){

                    $errors["new_pass_error"]=array(
                        "target"=>".secu-sett__form-field--newPass",
                        "error_msg"=>'
                            <div class="secu-sett__form-msg secu-sett__form-msg--error">
                                <p class="secu-sett__form-msg__text">
                                    Please fill out this field
                                </p>
                            </div> 
                        '
                    );
                }

                
                if(empty($confirm_pass)){

                    $errors["confirm_pass_error"]=array(
                        "target"=>".secu-sett__form-field--conPass",
                        "error_msg"=>'
                            <div class="secu-sett__form-msg secu-sett__form-msg--error">
                                <p class="secu-sett__form-msg__text">
                                    Please fill out this field
                                </p>
                            </div> 
                        '
                    );
                }

            }elseif(!$this->check_pass_match_with_current($current_pass)){

                $errors["current_pass_error"]=array(
                    "target"=>".secu-sett__form-field--currPass",
                    "error_msg"=>'
                        <div class="secu-sett__form-msg secu-sett__form-msg--error">
                            <p class="secu-sett__form-msg__text">
                                Your Current Password did not match
                            </p>
                        </div> 
                    '
                );

            }elseif(strlen($new_pass) < 8){
            
                $errors["new_pass_error"]=array(
                    "target"=>".secu-sett__form-field--newPass",
                    "error_msg"=>'
                        <div class="secu-sett__form-msg secu-sett__form-msg--error">
                            <p class="secu-sett__form-msg__text">
                                Password length must be greater than 7
                            </p>
                        </div> 
                    '
                );

            }elseif(strlen($new_pass) > 20){

                $errors["new_pass_error"]=array(
                    "target"=>".secu-sett__form-field--newPass",
                    "error_msg"=>'
                        <div class="secu-sett__form-msg secu-sett__form-msg--error">
                            <p class="secu-sett__form-msg__text">
                                Password length must be less than 21
                            </p>
                        </div> 
                    '
                );

            }elseif(!preg_match("/(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[!@#$%^&*()\-_.]).*/",$new_pass)){

                $errors["new_pass_error"]=array(
                    "target"=>".secu-sett__form-field--newPass",
                    "error_msg"=>'
                        <div class="secu-sett__form-msg secu-sett__form-msg--error">
                            <p class="secu-sett__form-msg__text">
                                Password didn\'t meet the requirement
                            </p>
                        </div> 
                    '
                );

            }elseif($new_pass == $current_pass){

                $errors["new_pass_error"]=array(
                    "target"=>".secu-sett__form-field--newPass",
                    "error_msg"=>'
                        <div class="secu-sett__form-msg secu-sett__form-msg--error">
                            <p class="secu-sett__form-msg__text">
                                You\'ve already used the password. choose a unique one
                            </p>
                        </div> 
                    '
                );

            }elseif($new_pass !== $confirm_pass){
                
                $errors["new_pass_error"]=array(
                    "target"=>".secu-sett__form-field--conPass",
                    "error_msg"=>'
                        <div class="secu-sett__form-msg secu-sett__form-msg--error">
                            <p class="secu-sett__form-msg__text">
                                Passwords did not match
                            </p>
                        </div> 
                    '
                );
            }

            if(!empty($errors)){

                $output=array(
                    "error_status"=>1,
                    "errors"=>$errors,
                );

            }else{

                //set $new_pass to the hashed format
                $new_pass=password_hash($new_pass, PASSWORD_DEFAULT);

                //Let's update the password
                $update_password=$user_obj->update(array(
                    "fields"=>array(
                        "user_password"=>"{$new_pass}"
                    ),
                    "where"=>"user_id={$logged_user_id}"
                ));


                if($update_password["status"] == 1 && $update_password["affected_rows"] > 0){

                    //let's logout the user 
                    session_destroy();

                    $output=array(
                        "error_status"=>0
                    );

                }else{

                    $output=array(
                        "error_status"=>100,
                        "errors"=>$update_password
                    );
                }

            }


            echo json_encode($output);

            
        }elseif(false){



        }


    }

}

?>

