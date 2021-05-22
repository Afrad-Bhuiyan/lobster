<?php 

/*
 * 1. The class `ajax_users _posts` will be used to store all the function and variable
 *    fo dasboards post options
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_users_posts{

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
            $this->user_info=$objs["user_info"];
        }
    }



    /**
     * =============================
     * All private functions  starts 
     * =============================
     */
    
        //use the function for fetching post ratings for a single posts 
        //such as `like`, `comment`, 'read' 
        private function fetch_post_rating($post_id)
        {
            
            //store comment model's object
            $comment_obj=$this->model_objs["comment_obj"];

            //store post model's object
            $post_obj=$this->model_objs["post_obj"];

            //store post_rating model's object
            $pr_obj=$this->model_objs["pr_obj"];

            //store comemnt_replies model's object
            $reply_obj=$this->model_objs["reply_obj"];

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
                        $fetch_comment_replies=$reply_obj->select(array(
                            "where"=>"replies.replies_for='comment_reply' AND replies.replies_for_id={$comment['comment_id']}"
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

            $output = "
                <div class='my-posts__tbl-wrap'>
                    <table class='my-posts__tbl'>
                        <thead class='my-posts__thead'>
                            <tr class='my-posts__tr my-posts__tr--thead'>
                                <th class='my-posts__th my-posts__th--title'>
                                    <div class='my-posts__th-wrap'>
                                        <div class='my-posts__checkbox'>
                                            <input class='my-posts__input my-posts__input--checkbox my-posts__input--checkAll' type='checkbox' name='all_posts'>
                                            <div class='my-posts__label my-posts__label--checkAll'></div>
                                        </div>
                                        
                                        <h4 class='my-posts__title my-posts__title--tbl'>
                                            {$posts['tbl_title']}
                                        </h4>
                                    </div>
                                </th>
                                <th class='my-posts__th my-posts__th--read'>
                                    <div class='my-posts__th-wrap'>
                                        <div class='my-posts__th-text'>
                                            <i class='fa fa-eye'></i>
                                            <br>
                                            <span>Read</span>
                                        </div>
                                    </div>
                                </th>

                                <th class='my-posts__th my-posts__th--likes'>
                                    <div class='my-posts__th-wrap'>
                                       <div class='my-posts__th-text'>
                                            <i class='fa fa-thumbs-up'></i>
                                            <br>
                                            <span>Likes</span>
                                       </div>
                                    </div>
                                </th>

                                <th class='my-posts__th my-posts__th--dislikes'>
                                    <div class='my-posts__th-wrap'>
                                        <div  class='my-posts__th-text'>
                                            <i class='fa fa-thumbs-down'></i>
                                            <br>
                                            <span>Dislikes</span>
                                        </div>
                                    </div>
                                </th>

                                <th class='my-posts__th my-posts__th--comments'>
                                    <div class='my-posts__th-wrap'>
                                        <div class='my-posts__th-text'>
                                            <i class='fa fa-comment'></i>
                                            <br>
                                            <span>Comments</span>
                                        <div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class='my-posts__tbody'>
                           
            
            ";

                    foreach($posts["all"] as $post_index=>$post):

                        $badge_class = ($post["post_status"] == "published") ? "my-posts__badge--published" : "my-posts__badge--draft";
                    
                        //convert html entities to HTML tag
                        $post["post_title"] = html_entity_decode($post["post_title"]);

                        //remove the underscore from data string
                        $post_date=explode("_", $post["post_date"]);
                        $post_date= $post_date[0];

                        $pfile_info= $post["pfile_info"];
                        $post_ratings= $post["post_ratings"];

                        $output .= "
                            <tr class='my-posts__tr my-posts__tr--tbody'>
                                <td class='my-posts__td my-posts__td--title'>
                                    <div class='my-posts__td-wrap'>
                                        <div class='my-posts__checkbox my-posts__checkbox--single'>
                                            <input class='my-posts__input my-posts__input--checkbox my-posts__input--single' type='checkbox' name='single_post' value='{$post['post_id']}'>
                                            <div class='my-posts__label my-posts__label--single'></div>
                                        </div>

                                        <img class='my-posts__img my-posts__img--post' src='{$this->config->domain("app/uploads/posts/{$pfile_info['name']}-sm.{$pfile_info['ext']}")}' alt='{$post['post_title']}'>
                                       
                                        <div class='my-posts__tbl-content'>
                                        
                                            <h5 class='my-posts__title my-posts__title--post'>
                                                {$post["post_title"]}
                                            </h5>
                                            
                                            <ul class='my-posts__meta'>
                                                <li class='my-posts__meta-item'>
                                                    <i class='fa fa-calendar my-posts__meta-icon'></i>
                                                    <span class='my-posts__meta-text'>
                                                        {$post_date}
                                                    </span>
                                                </li>

                                                <li class='my-posts__meta-item'>
                                                    <i class='fa fa-cube my-posts__meta-icon'></i>
                                                    <span class='my-posts__meta-text'>
                                                        {$post["cat_name"]}
                                                    </span>
                                                </li>

                                                <li class='my-posts__meta-item'>
                                                    <span class='my-posts__badge my-posts__badge--status {$badge_class}'>
                                                        {$post["post_status"]}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                        
                                        <div class='my-posts__dropdown'>
                                            <button class='my-posts__btn my-posts__btn--ddToggle' type='button'>
                                                <svg height='1.3rem' viewBox='-192 0 512 512' width='1.3rem' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='m128 256c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                                    <path d='m128 64c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                                    <path d='m128 448c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                                </svg>
                                            </button>

                                            <div class='my-posts__dropdown-opts'>
                                                <ul class='my-posts__dropdown-list'>
                                                    <li class='my-posts__dropdown-item'>
                                                        <a class='my-posts__dropdown-link' href='{$this->config->domain("users/{$this->user_info['user_name']}/dashboard/posts?sub_option=edit&link={$post['post_link']}")}'>
                                                            <i class='fa fa-edit my-posts__dropdown-icon'></i>
                                                            <span class='my-posts__dropdown-text'>Edit</span>
                                                        </a>
                                                    </li>
                                                    <li class='my-posts__dropdown-item'>
                                                        <a class='my-posts__dropdown-link' href='{$this->config->domain("posts?v={$post['post_link']}")}' target='_blank'>
                                                            <i class='fa fa-eye-slash my-posts__dropdown-icon'></i>
                                                            <span class='my-posts__dropdown-text'>View</span>
                                                        </a>
                                                    </li>
                                                    <li class='my-posts__dropdown-item'>
                                                        <a class='my-posts__dropdown-link' role='button'>
                                                            <i class='fa fa-clipboard my-posts__dropdown-icon'></i>
                                                            <span class='my-posts__dropdown-text'>Copy link</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class='my-posts__td my-posts__td--read'>
                                    <div class='my-posts__td-wrap'>
                                        <div class='my-posts__td-text'>
                                            {$post_ratings["read"]}
                                        <div>
                                    </div>
                                </td>

                                <td class='my-posts__td my-posts__td--likes'>
                                    <div class='my-posts__td-wrap'>
                                        <div class='my-posts__td-text'>
                                            {$post_ratings["likes"]}
                                        <div>
                                    </div>
                                </td>

                                <td class='my-posts__td my-posts__td--dislikes'>
                                    <div class='my-posts__td-wrap'>
                                        <div class='my-posts__td-text'>
                                            {$post_ratings["dislikes"]}
                                        <div>
                                    </div>
                                </td>

                                <td class='my-posts__td my-posts__td--comments'>
                                    <div class='my-posts__td-wrap'>
                                        <div class='my-posts__td-text'>
                                            {$post_ratings["comments"]}
                                        <div>
                                    </div>
                                </td>
                            </tr>
                        ";
                
                    endforeach;

            $output .= "
                        </tbody>
                    </table>   
                </div>
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

        //use the function to validate the publish post form
        private function validate_the_publish_form($validation_type)
        {
            
            //store all the errors
            $errors=[];

            if($validation_type == "post_img"){

                //store total uploaded image number
                $uploaded_img_num=count($_FILES["post_img"]["name"]);

                //store uploaded image's extension
                $uploaded_img_ext=pathinfo($_FILES["post_img"]["name"][0],PATHINFO_EXTENSION);
                
                //store the size of uploaded image
                $uploaded_img_size=$_FILES["post_img"]["size"][0];
                
                //all the valid extension
                $valid_ext=["jpg","jpeg","png"];

        
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

            }elseif($validation_type == "post_form"){
            
                //store the `post_title` index from $_POST variable
                $post_title=htmlspecialchars(trim($_POST["post_title"]));
                
                //store the `post_catagory` index from $_POST variable
                $post_category=htmlspecialchars(trim($_POST["post_category"]));
                
                //store the `post_visibilty` index from $_POST variable
                $post_visibility=htmlspecialchars(trim($_POST["post_visibility"]));
                
                //store the `post_desc` index from $_POST variable
                $post_desc=htmlspecialchars(trim($_POST["post_desc"]));

    
                if($_FILES["post_img"]["error"] > 0){
    
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
                    catagories.cat_name
                 
                ",
                "join"=>array(
                    "catagories"=>"catagories.cat_id = posts.post_cat"
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

                    $my_posts["all"][$post_index]["pfile_info"]=$this->fetch_post_files($post["post_id"],"post_thumb");
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

                //finally return the output in a table format
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
                        <ul class='my-posts__page-list'>
                ";
                        if($page_no > 1){

                            $prev_page_no = $page_no - 1;

                            $output .= "
                                <li class='my-posts__page-item'>
                                    <a class='my-posts__page-link my-posts__page-link--prev' href='{$this->config->domain("users/{$this->user_info['user_name']}/dashboard/posts?sub_option=my_posts&filter=$filter&page=$prev_page_no")}'>
                                        <i class='fa fa-angle-left'></i>
                                    </a>
                                </li>
                            ";
                        }

                        for($a=1; $a <= $total_pages; $a++){
                            
                            $active_link=($page_no == $a) ? "my-posts__page-link--active" : "";

                            $output .= "
                                <li class='my-posts__pagination-item'>
                                    <a class='my-posts__page-link my-posts__page-link--num {$active_link}' href='{$this->config->domain("users/{$this->user_info['user_name']}/dashboard/posts?sub_option=my_posts&filter=$filter&page=$a")}'>
                                        {$a}
                                    </a>
                                </li>
                            ";
                        }
                    

                        if($page_no < $total_pages){

                            $next_page_no = $page_no + 1;

                            $output .= "
                                <li class='my-posts__page-item'>
                                    <a class='my-posts__page-link my-posts__page-link--next' href='{$this->config->domain("users/{$this->user_info['user_name']}/dashboard/posts?sub_option=my_posts&filter=$filter&page=$next_page_no")}'>
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

        //use the function for add a post into the database
        public function add_the_post()
        {

            //validate the $_FILES variable
            $_FILES = filter_var_array($_FILES,FILTER_SANITIZE_STRING);

            //store the `request` index from $_POST variable
            $request = isset($_POST["request"]) ? $_POST["request"] : null;

            //store the final output
            $output = array();
    
            //First validate the form
            if($request == "post_img"){

                //store all the errors after calling the function
                $errors = $this->validate_the_publish_form("post_img");

                if(!empty($errors)){

                    $output = array(
                        "error_status"=>1,
                        "errors"=>$errors
                    );

                }else{

                    $output = array(
                        "error_status"=>0
                    );
                }

            }elseif($request == "post_form"){

                //store all the errors after calling the function
                $errors = $this->validate_the_publish_form("post_form");

                if(!empty($errors)){

                    $output = array(
                        "error_status"=>1,
                        "errors"=>$errors
                    );

                }else{

         

                    //store post model's object from $this->model_objs
                    $post_obj=$this->model_objs["post_obj"];

                    //store post_files model's object from $this->model_objss
                    $pfile_obj=$this->model_objs["pfile_obj"];
            
                    //genrate a random string for post link
                    $random_str_link=$this->functions->generate_random_str(array(
                        "model_obj"=>$post_obj,
                        "column"=>"post_link",
                        "length"=>11
                    ));
                    
                    //genrate a random string for post image
                    $random_str_name=$this->functions->generate_random_str(array(
                        "model_obj"=>$pfile_obj,
                        "column"=>"pfile_name",
                        "length"=>11
                    ));

                    //store the tmp name of uploaded post image 
                    $post_img_tmpName=$_FILES["post_img"]["tmp_name"];
                    
                    //store the upload post image name 
                    $post_img_name=$_FILES["post_img"]["name"];

                    //cut out extension from the $post_img_name
                    $post_img_ext=pathinfo($post_img_name,PATHINFO_EXTENSION);

                    $post_img_name=array(
                        "original"=> "{$random_str_name}.{$post_img_ext}",
                        "sm"=> "{$random_str_name}-sm.{$post_img_ext}",
                        "md"=> "{$random_str_name}-md.{$post_img_ext}",
                        "lg"=> "{$random_str_name}-lg.{$post_img_ext}",
                    );

                
                    //set the timezone to `Dhaka/Asia`
                    date_default_timezone_set("Asia/Dhaka");
                    
                    //insert the post to the database
                    $insert_post=$post_obj->insert(array(
                        "fields"=>array(
                            "post_title"=>htmlentities(trim($_POST["post_title"]), ENT_QUOTES),
                            "post_content"=>htmlentities(trim($_POST["post_desc"]), ENT_QUOTES),
                            "post_author"=>$this->user_info["user_id"],
                            "post_date"=>date("d F, Y_h:i:sA"),
                            "post_cat"=>$_POST["post_category"],
                            "post_link"=>$random_str_link,
                            "post_status"=>$_POST["post_visibility"],
                            "post_read"=>0
                        )
                    ));

                    if($insert_post["status"] !== 1 && !isset($insert_post["insert_id"])){

                        $output = array(
                            "error_status"=>100,
                            "errors"=>$insert_post
                        );

                    }else{

                        if(!file_exists("app/uploads/posts/{$post_img_name["original"]}")){

                            if(move_uploaded_file($post_img_tmpName,"app/uploads/posts/{$post_img_name["original"]}")){

                                //store the dimension for post image
                                $post_img_dimension = array(
                                    "sm"=>array(
                                        "width"=>150,
                                        "height"=>100
                                    ),
                                    "md"=>array(
                                        "width"=>370,
                                        "height"=>250
                                    ),
                                    "lg"=>array(
                                        "width"=>870,
                                        "height"=>580
                                    )
                                );

                                //Resize the original Image into 150x100 and upload it
                                $img_sm_output=$this->functions->resize_upload_img(array(
                                    "width"=>$post_img_dimension["sm"]["width"],
                                    "height"=>$post_img_dimension["sm"]["height"],
                                    "img_url"=>"app/uploads/posts/{$post_img_name["original"]}",
                                    "img_upload_location"=>"app/uploads/posts/{$post_img_name["sm"]}"
                                ));

                                //Resize the original Image into 150x100 and upload it
                                $img_md_output=$this->functions->resize_upload_img(array(
                                    "width"=>$post_img_dimension["md"]["width"],
                                    "height"=>$post_img_dimension["md"]["height"],
                                    "img_url"=>"app/uploads/posts/{$post_img_name["original"]}",
                                    "img_upload_location"=>"app/uploads/posts/{$post_img_name["md"]}"
                                ));

                                //Resize the original Image into 150x100 and upload it
                                $img_lg_output=$this->functions->resize_upload_img(array(
                                    "width"=>$post_img_dimension["lg"]["width"],
                                    "height"=>$post_img_dimension["lg"]["height"],
                                    "img_url"=>"app/uploads/posts/{$post_img_name["original"]}",
                                    "img_upload_location"=>"app/uploads/posts/{$post_img_name["lg"]}"
                                ));

                                //convert the array into a string to store it into the database
                                $post_img_dimension=serialize($post_img_dimension);

                                //add `\` before the `""` double quotation mark
                                $post_img_dimension=str_replace('"','\"',$post_img_dimension);

                                if($img_sm_output && $img_md_output && $img_lg_output){

                                    $insert_pfile=$pfile_obj->insert(array(
                                        "fields"=>array(
                                            "pfile_name"=>"{$random_str_name}",
                                            "pfile_ext"=>"{$post_img_ext}",
                                            "pfile_usage"=>"post_thumb",
                                            "pfile_dimension"=>$post_img_dimension,
                                            "pfile_status"=>1,
                                            "post_id"=>$insert_post["insert_id"]
                                        )
                                    ));

                                    if($insert_pfile["status"] == 1 && isset($insert_pfile)){

                                        $output = array(
                                            "error_status"=>0,
                                        );
                                
                                    }else{

                                        $output = array(
                                            "error_status"=>100,
                                            "errors"=>$insert_pfile
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            

            echo json_encode($output);

        }
            
        //use the function to edit any post
        public function edit_the_post()
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

        //use the function to delete my_post from dashboard
        public function delete_my_posts()
        {

            //validate the $_POST variable
            $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

            $post_ids = $_POST["post_ids"];


            print_r($_POST);

        }


        //user the function to filter my posts from the topbar's select input
        public function filter_my_posts()
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
                    
                    //sorting the array in DESC format according to indexes
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
                                    catagories.cat_name
                                ",
                                "join"=>array(
                                    "catagories"=>"catagories.cat_id = posts.post_cat"
                                ),
                                "where"=>"posts.post_id={$post_id}"
                            ));

                            $posts["all"][]=$fetch_filtered_posts['fetch_all'][0];

                            foreach($posts["all"] as $post_index=>$post){

                                $posts["all"][$post_index]["post_ratings"]=$this->fetch_post_rating($post['post_id']);
                                
                                $posts["all"][$post_index]["pfile_info"]=$this->fetch_post_files($post['post_id'],"post_thumb");
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
                        catagories.cat_name
                    ",
                    "join"=>array(
                        "catagories"=>"catagories.cat_id = posts.post_cat"
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
                        $posts["all"][$post_index]["pfile_info"]=$this->fetch_post_files($post['post_id'],"post_thumb");
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
                    catagories.cat_name
                ",

                "join"=>array(
                    "catagories"=>"catagories.cat_id = posts.post_cat"
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

                    $posts["all"][$post_index]["pfile_info"]=$this->fetch_post_files($post['post_id'],"post_thumb");
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

    /**
     * =========================
     * All Public functions ends
     * =========================
     */
  
}



?>
