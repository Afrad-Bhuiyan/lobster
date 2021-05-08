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

    /**
     * =========================
     * All Public functions ends
     * =========================
     */
  
}



?>
