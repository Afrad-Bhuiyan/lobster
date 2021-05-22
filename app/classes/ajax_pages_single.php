<?php 

/*
 * 1. The class `ajax_users _posts` will be used to store all the function and variable
 *    fo dasboards post options
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_pages_single{

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

         //use the function to fetch ratings of `post`, `comment`, `comment reply`
        private function fetch_rates($rate_for, $rate_for_id)
        {

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

        //use the function to check logged user rated any thing  
        //such as `post`, `comment`, `comment_replies`
        private function if_user_rated($rate_for,$rate_for_id)
        {

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
        

        //use the function to check if logged user saved the post
        private function if_user_saved_the_post($post_id)
        {

            $output = null;
            
            if(!empty($this->user_info)){

                //store the `saved_post` model's object from $this->modal variable
                $sp_obj = $this->model_objs["sp_obj"];

                $check_post_saved = $sp_obj->select(array(
                     "where"=>"saved_posts.post_id={$post_id} AND saved_posts.user_id={$this->user_info['user_id']}"
                ));

                if($check_post_saved["status"] == 1 && $check_post_saved["num_rows"] == 1){
                    
                    $output = true;
                    
                }else{
                    
                    $output = false;
                 }
            }

            return $output;
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

        //(2.Single Post page) load single post comments
        public function load_comments()
        {

            $output = array();

            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);
            
            //store the post_link index from $_POST variable
            $post_link=$_POST["post_link"];

            $post_info = array();
            
            //store the post model's object from $this->modal_obj
            $post_obj=$this->model_objs["post_obj"];
            
            //store the comment model's object from $this->modal_obj
            $comment_obj=$this->model_objs["comment_obj"];

            //store the comment_replies model's object from $this->modal_obj
            $reply_obj=$this->model_objs["reply_obj"];

            //fetch post_id based on $post_link
            $fetch_post_info=$post_obj->select(array(
                "column_name"=>"posts.post_id,posts.post_author, posts.post_link",
                "where"=>"posts.post_link='{$post_link}'"
            ));

            if($fetch_post_info["status"] == 1 && $fetch_post_info["num_rows"] == 1){

                $post_info = $fetch_post_info["fetch_all"][0];

            }else{

                $output = array(
                    "error_status"=>1,
                    "errors"=>$fetch_post_info
                );

                echo json_encode($output);

                die(); 
            }


            //store all the fetched comments
            $comments = array(
                "total_comments"=>0
            );

            //fetch comments related to this post
            $fetch_comments=$comment_obj->select(array(
                "column_name"=>"
                    comments.comment_id,
                    comments.comment_content,
                    comments.comment_date,
                    comments.comment_author,
                    users.user_name
                ",
                "join"=>array(
                    "users"=>"users.user_id = comments.comment_author",
                ),
                "order"=>array(
                    "type"=>"DESC",
                    "column"=>"comment_id"
                ),
                "where"=>"comments.post_id={$post_info['post_id']}"
            ));

            if($fetch_comments["status"] == 1 && $fetch_comments["num_rows"] > 0){

                //set the total fetched comments number
                $comments["total_comments"] =  $comments["total_comments"] + $fetch_comments["num_rows"];
                
                //set all the comments in `all` index
                $comments["all"] = $fetch_comments["fetch_all"];
            
                foreach($comments["all"] as $comment_index => $comment){

                    //store comment author's profile image information
                    $comments["all"][$comment_index]["comment_author_img"] = array(
                        "profile_img"=>$this->fetch_user_files($comment["comment_author"],"profile_img")
                    );
                    
                    //fetch comment ratings `like` and `dislike`
                    $comments["all"][$comment_index]["comment_ratings"] = $this->fetch_rates("comment",$comment['comment_id']);
                    
                    //store store the fetched total replies
                    $total_replies = 0;

                    $fetch_total_replies = $reply_obj->select(array(
                        "column_name"=>"
                            COUNT(*) as total_replies
                        ",
                        "where"=>"replies.comment_id={$comment['comment_id']}"
                    ));

                    if($fetch_total_replies["status"] == 1 && $fetch_total_replies["num_rows"] == 1){
                        
                        //over ride the default $total_replies's value to fetched total_replies
                        $total_replies = $fetch_total_replies["fetch_all"][0]["total_replies"];
                    }

                    if($total_replies > 0){

                        //update total comments no with total replies
                        $comments["total_comments"] = $comments["total_comments"] + $total_replies;

                        $viewRely_btn_txt = ($total_replies > 1) ? "View {$total_replies} replies" : "View {$total_replies} reply"; 

                        $comments["all"][$comment_index]["view_replies_html"] = "
                            <div class='sp-single-comm__replies'>
                                <button class='sp-single-comm__btn sp-single-comm__btn--viewReply' type='button' data-comment_id='{$comment['comment_id']}' data-first_clicked='true'>
                                    <i class='fa fa-caret-down sp-single-comm__btn-icon'></i> 
                                    <span class='sp-single-comm__btn-txt'>
                                        {$viewRely_btn_txt} 
                                    </span>
                                </button>  

                                <div class='collapse sp-single-comm__areplies' data-total_replies='0'>
                                

                                </div>                                       
                            </div><!--sp-single-comm__replies-->
                        ";

                    }else{

                        //set the view replies htmt to blank
                        $comments["all"][$comment_index]["view_replies_html"] = "";
                    }
                }
            }

            //set the `error_status` index is 0
            $output["error_status"] = 0;

            $logged_user_profile = "";

            if(empty($this->user_info)){

                $logged_user_profile = "
                    <img class='sp-comm__form-img sp-comm__form-img--profile' src='{$this->config->domain("app/uploads/users/profile/user-placeholder-img-sm.png")}' alt='user placeholder image' width='100' height='100'/>
                ";

            }else{
                $logged_user_profile = $this->user_info["ufile_info"]["profile_img"];

                $logged_user_profile = "
                    <img class='sp-comm__form-img sp-comm__form-img--profile' src='{$this->config->domain("app/uploads/users/profile/{$logged_user_profile['name']}-sm.{$logged_user_profile['ext']}")}' alt='{$this->user_info['user_name']}' width='{$logged_user_profile['dimension']['sm']['width']}' height='{$logged_user_profile['dimension']['sm']['height']}'/>
                ";
            }

            $comments["total_comments"] =  ($comments["total_comments"] > 1) ? "{$comments['total_comments']} comments" : "{$comments['total_comments']} comment";
        

            //set form and heading in `html` index
            $output["html"] = "
                <div class='sp-comm'>
                    <h4 class='sp-comm__title sp-comm__title--main'>
                        {$comments['total_comments']}
                    </h4>

                    <form class='sp-comm__form sp-comm__form--primary'>
                        <div class='sp-comm__form-wrap'>
                            <div class='sp-comm__form-field sp-comm__form-field--textarea'>
                                <div class='sp-comm__form-img-area'>
                                    {$logged_user_profile}
                                </div>
                                <textarea class='sp-comm__form-input sp-comm__form-input--textarea' name='comment_content' placeholder='Write your comment'></textarea>
                                <input class='sp-comm__form-input sp-com__form-input--hidden' type='hidden' name='post_id' value='{$post_info['post_id']}'>
                            </div>

                            <div class='sp-comm__form-field sp-comm__form-field--actionBtns sp-comm__form-field--hide'>
                                <button class='sp-comm__form-btn sp-comm__form-btn--cancel' type='button'>
                                    <span>Cancel</span>
                                </button>
                            
                                <button class='sp-comm__form-btn sp-comm__form-btn--comment sp-comm__form-btn--disabled' type='submit'>
                                    <span>Comment</span>
                                </button>
                            </div>
                        </div>
                    </form>
            ";

                if(isset($comments["all"])):

                    foreach($comments["all"] as $comment_index=>$comment):

                        //store comment author's profile picture information
                        $comment_auth_profile = $comment["comment_author_img"]["profile_img"];
                        
                        //store the class if logged user liked on comment
                        $comment_like_active = ($this->if_user_rated("comment",$comment['comment_id']) == "like") ? "sp-single-comm__btn--rated" : "";
                        
                        //store the class if logged user disliked on comment
                        $comment_dislike_active = ($this->if_user_rated("comment",$comment['comment_id']) == "dislike") ? "sp-single-comm__btn--rated" : "";
                        
                        //store the class if post_author commented on his post
                        $uname_admin_class = ($comment["comment_author"] == $post_info['post_author']) ? "sp-single-comm__list-link--admin" : ""; 

                        //convert all special charectere in entity code
                        $comment_content = html_entity_decode($comment['comment_content'],ENT_QUOTES);

                        $comment["comment_date"] = str_replace(", "," ",$comment["comment_date"]);

                        $comment["comment_date"] = str_replace(" ","-",$comment["comment_date"]);

                        $comment["comment_date"] = str_replace("_"," ",$comment["comment_date"]);

                        $comment["comment_date"] = $this->functions->get_time_in_ago($comment["comment_date"]);

                        $output["html"] .= "  
                            <div class='sp-single-comm sp-single-comm--primary'>
                                <div class='sp-single-comm__imgArea'>
                                    <img  class='sp-single-comm__img sp-single-comm__img--profile' src='{$this->config->domain("app/uploads/users/profile/{$comment_auth_profile['name']}-sm.{$comment_auth_profile['ext']}")}' alt='{$comment['user_name']}' width='{$comment_auth_profile['dimension']['sm']['width']}' height='{$comment_auth_profile['dimension']['sm']['height']}'>
                                </div>

                                <div class='sp-single-comm__wrap'>          
                                    <div class='sp-single-comm__content sp-single-comm__content--primary'>
                                        <ul class='sp-single-comm__list sp-single-comm__list--topInfo'>
                                            <li class='sp-single-comm__list-item'>
                                                <a class='sp-single-comm__list-link sp-single-comm__list-link--uname {$uname_admin_class}' href='{$this->config->domain("users/{$comment['user_name']}")}' title=''>
                                                    {$comment['user_name']}
                                                </a>
                                            </li>

                                            <li class='sp-single-comm__list-item'>
                                                <span class='sp-single-comm__list-txt sp-single-comm__list-txt--time'>
                                                    {$comment['comment_date']}
                                                </span>
                                            </li>
                                        </ul>

                                        <div class='sp-single-comm__body'>
                                            <p>
                                                {$comment_content}
                                            </p>
                                        </div>
                                    </div>

                                    <div class='sp-single-comm__ratings'>
                                        <ul class='sp-single-comm__list sp-single-comm__list--ratings'>
                                            <li class='sp-single-comm__list-item'>
                                                <button class='sp-single-comm__btn sp-single-comm__btn--rate {$comment_like_active} sp-single-comm__btn--like' type='button' data-rate_for='comment' data-rate_for_id={$comment['comment_id']}> 
                                                    <i class='fa fa-thumbs-up sp-single-comm__btn-icon'></i>
                                                    <span class='sp-single-comm__btn-txt'>
                                                        Like ({$comment['comment_ratings']['likes']['total']})
                                                    </span>
                                                </button>                        
                                            </li>   

                                            <li class='sp-single-comm__list-item'>
                                                <button class='sp-single-comm__btn sp-single-comm__btn--rate {$comment_dislike_active} sp-single-comm__btn--dislike' type='button' data-rate_for='comment' data-rate_for_id={$comment['comment_id']}> 
                                                    <i class='fa fa-thumbs-down sp-single-comm__btn-icon'></i>
                                                    <span class='sp-single-comm__btn-txt'>
                                                        Dislike ({$comment['comment_ratings']['dislikes']['total']})
                                                    </span>
                                                </button>                        
                                            </li>  

                                            <li class='sp-single-comm__list-item'>
                                                <button class='sp-single-comm__btn sp-single-comm__btn--reply' type='button'  data-comment_id='{$comment['comment_id']}' data-reply_type='primary'>
                                                    <i class='fa fa-reply sp-single-comm__btn-icon'></i> 
                                                    <span class='sp-single-comm__btn-txt'>Reply</span>
                                                </button>                      
                                            </li>                        
                                        </ul> 
                                    </div>
                                    {$comment['view_replies_html']}
                                </div>
                        ";
                        if(!empty($this->user_info) && $this->user_info["user_id"] == $post_info['post_author']):
                            $output["html"] .= "
                                <div class='sp-single-comm__dropdown sp-single-comm__dropdown--primary'>
                                    <button class='sp-single-comm__btn sp-single-comm__btn--ddToggle' type='button'>
                                        <svg height='1.3rem' viewBox='-192 0 512 512' width='1.3rem' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='m128 256c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                            <path d='m128 64c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                            <path d='m128 448c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                        </svg>                                    
                                    </button>

                                    <div class='sp-single-comm__dropdown-opts'>
                                        <ul class='sp-single-comm__list sp-single-comm__list--primary'>
                                            <li class='sp-single-comm__list-item'>
                                                <button class='sp-single-comm__btn sp-single-comm__btn--ddOpts sp-single-comm__btn--delete' type='button' data-delete='comment' data-delete_id='{$comment['comment_id']}'>
                                                    <i class='fa fa-trash sp-single-comm__btn-icon'></i>
                                                    <span class='sp-single-comm__btn-txt'>Delete</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            ";
                        endif;

                        $output["html"] .="
                            </div><!--sp-single-comm-->
                        ";

                    endforeach;

                else:

                    $output["html"] .= "
                        <div class='sp-comm__msg sp-comm__msg--noComments'>
                            <div class='sp-comm__msg-body'>
                                <i class='fa fa-exclamation-circle sp-comm__msg-icon'></i>
                                <h5 class='sp-comm__msg-title'>
                                    No comments found. Be the first commentor
                                </h5>
                            </div>
                        </div>
                    "; 

                endif;
        
            $output["html"] .="
                    </div><!--sp-comm-->
            ";

            
            echo json_encode($output);
    
        }
    
        //(2.Single Post page) use the function to fetct comment replies
        //when clicked on view replies button
        public function fetch_comment_replies()
        {

            //store the final output
            $output = array();

            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store the `comment_id` index from $_POST variable
            $comment_id = (isset($_POST['comment_id'])) ? $_POST['comment_id'] : null;
                            
            //store the comment reply's object from $this->modal_obj
            $reply_obj=$this->model_objs["reply_obj"];
            
            //store all the fetced replies
            $replies = array();

            //fetch all comment replies
            $fetch_replies=$reply_obj->select(array(
                "column_name"=>"
                    replies.reply_id,
                    replies.reply_content,
                    replies.reply_date,
                    replies.comment_id,
                    users.user_id,
                    users.user_name,
                    comments.post_id,
                    posts.post_author
                
                ",
                "join"=>array(
                    "users"=>"users.user_id = replies.user_id",
                    "comments"=>"comments.comment_id = replies.comment_id",
                    "posts"=>"posts.post_id = comments.post_id"
                ),
                "where"=>"replies.comment_id={$comment_id}"
            ));

            if($fetch_replies["status"] == 1 && $fetch_replies["num_rows"] > 0){

                //store all the fetched replies in $replies variable
                $replies = $fetch_replies["fetch_all"];

                foreach($replies as $reply_index=>$reply){

                    //store replied user profile image information
                    $replies[$reply_index]["replied_user_profile"] = $this->fetch_user_files($reply['user_id'],"profile_img");
                    
                    //store ratigns for each reply
                    $replies[$reply_index]["reply_ratings"] = $this->fetch_rates("comment_reply", $reply["reply_id"]);
                }

            }else{

                $output = array(
                    "error_status" => 1,
                    "errors"=>$fetch_replies
                );
            }

            
            if(!empty($replies)){

                $output["error_status"] = 0;

                $output["total_response"] = count($replies);

                $output["html"] = "";

                foreach($replies as $reply_index=>$reply){
                    
                    //store the replied user's profile image
                    $profile_img = $reply["replied_user_profile"];

                    $reply_like_active = ($this->if_user_rated("comment_reply",$reply['reply_id']) == "like") ? "sp-single-comm__btn--rated" : "";
                    
                    $reply_dislike_active = ($this->if_user_rated("comment_reply",$reply['reply_id']) == "dislike") ? "sp-single-comm__btn--rated" : "";

                    $uname_admin_class = ($reply["post_author"] == $reply["user_id"]) ? "sp-single-comm__list-link--admin" : "";
                   
                    $reply['reply_content'] = html_entity_decode($reply['reply_content'],ENT_QUOTES);

                    $reply['reply_date'] = str_replace(", "," ",$reply['reply_date']);

                    $reply['reply_date'] = str_replace(" ","-",$reply['reply_date']);

                    $reply['reply_date'] = str_replace("_"," ",$reply['reply_date']);

                    $reply['reply_date'] = $this->functions->get_time_in_ago($reply['reply_date']);


                    $output["html"] .= "
                    
                        <div class='sp-single-comm sp-single-comm--secondary'>
                            <div class='sp-single-comm__imgArea'>
                                <img  class='sp-single-comm__img sp-single-comm__img--profile' src='{$this->config->domain("app/uploads/users/profile/{$profile_img['name']}-sm.{$profile_img['ext']}")}' alt='{$reply['user_name']}' width='{$profile_img['dimension']['sm']['width']}' height='{$profile_img['dimension']['sm']['height']}'>
                            </div>

                            <div class='sp-single-comm__wrap'>
                                <div class='sp-single-comm__content'>
                                    <ul class='sp-single-comm__list sp-single-comm__list--topInfo'>
                                        <li class='sp-single-comm__list-item'>
                                            <a class='sp-single-comm__list-link sp-single-comm__list-link--uname {$uname_admin_class}' href='{$this->config->domain("users/{$reply['user_name']}")}' title='{$reply['user_name']}'>
                                                {$reply['user_name']}
                                            </a>
                                        </li>

                                        <li class='sp-single-comm__list-item'>
                                            <span class='sp-single-comm__list-txt sp-single-comm__list-txt--time'>
                                                {$reply['reply_date']}
                                            </span>
                                        </li>
                                    </ul>

                                    <div class='sp-single-comm__body'>
                                        <p>
                                            {$reply['reply_content']}
                                        </p>
                                    </div>
                                </div>

                                <div class='sp-single-comm__ratings'>
                                    <ul class='sp-single-comm__list sp-single-comm__list--ratings'>
                                        <li class='sp-single-comm__list-item'>
                                            <button class='sp-single-comm__btn ${reply_like_active} sp-single-comm__btn--rate sp-single-comm__btn--like' type='button' data-rate_for='comment_reply' data-rate_for_id={$reply['reply_id']}> 
                                                <i class='fa fa-thumbs-up sp-single-comm__btn-icon'></i>
                                                <span class='sp-single-comm__btn-txt'>Like ({$reply['reply_ratings']['likes']['total']})</span>
                                            </button>                        
                                        </li>   

                                        <li class='sp-single-comm__list-item'>
                                            <button class='sp-single-comm__btn ${reply_dislike_active} sp-single-comm__btn--rate sp-single-comm__btn--dislike' type='button' data-rate_for='comment_reply' data-rate_for_id={$reply['reply_id']}> 
                                                <i class='fa fa-thumbs-down sp-single-comm__btn-icon'></i>
                                                <span class='sp-single-comm__btn-txt'>Dislike ({$reply['reply_ratings']['dislikes']['total']})</span>
                                            </button>                        
                                        </li>  

                                        <li class='sp-single-comm__list-item'>
                                            <button class='sp-single-comm__btn sp-single-comm__btn--reply' type='button' data-comment_id='{$reply['comment_id']}' data-reply_type='secondary' data-reply_id='{$reply['reply_id']}'>
                                                <i class='fa fa-reply sp-single-comm__btn-icon'></i> 
                                                <span class='sp-single-comm__btn-txt'>Reply</span>
                                            </button>                      
                                        </li>                        
                                    </ul> 
                                </div>
                            </div>

                            
                    ";

                    if(true){
                        $output["html"] .= "
                            <div class='sp-single-comm__dropdown'>
                                <button class='sp-single-comm__btn sp-single-comm__btn--ddToggle' type='button'>
                                    <svg height='1.3rem' viewBox='-192 0 512 512' width='1.3rem' xmlns='http://www.w3.org/2000/svg'>
                                        <path d='m128 256c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                        <path d='m128 64c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                        <path d='m128 448c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0'></path>
                                    </svg>                                    
                                </button>

                                <div class='sp-single-comm__dropdown-opts'>
                                    <ul class='sp-single-comm__list sp-single-comm__list--secondary'>
                                        <li class='sp-single-comm__list-item'>
                                            <button class='sp-single-comm__btn sp-single-comm__btn--ddOpts sp-single-comm__btn--delete' type='button' data-delete='comment_reply' data-delete_id='{$reply['reply_id']}'>
                                                <i class='fa fa-trash sp-single-comm__btn-icon'></i>
                                                <span class='sp-single-comm__btn-txt'>Delete</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        ";
                    }
                    
                    
                    $output["html"] .= "
                        </div><!--sp-single-comm--secondary--> 
                    ";
                }

            }

            echo json_encode($output);
        }

        //(2.Single Post page) append the reply form when click'c reply button
        public function append_reply_form()
        {

            //first validate the string
            $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

            $comment_id = isset($_POST["comment_id"]) ? $_POST["comment_id"] : null;

            $reply_type = isset($_POST["reply_type"]) ? $_POST["reply_type"] : null;
            
            $reply_id = isset($_POST["reply_id"]) ? $_POST["reply_id"] : null;
    


            $output = array();

            if(!empty($this->user_info)){
                
                $output["error_status"] = 0;

                //store the logged user's profil information
                $logged_user_profile = $this->user_info["ufile_info"]["profile_img"];

                $output["html"] = "
                    <form class='sp-comm__form sp-comm__form--secondary'>
                        <div class='sp-comm__form-wrap'>
                            <div class='sp-comm__form-field sp-comm__form-field--textarea'>
                                <div class='sp-comm__form-img-area'>
                                    <img class='sp-comm__form-img sp-comm__form-img--profile' src='{$this->config->domain("app/uploads/users/profile/{$logged_user_profile['name']}-sm.{$logged_user_profile['ext']}")}' width='{$logged_user_profile['dimension']['sm']['width']}' height='{$logged_user_profile['dimension']['sm']['height']}' alt='{$this->user_info['user_name']}'>
                                </div>
                                <textarea class='sp-comm__form-input sp-comm__form-input--textarea' name='comment_content' placeholder='Write your comment'></textarea>
                                <input type='hidden' name='comment_id' value='{$comment_id}'>        
                                <input type='hidden' name='reply_type' value='{$reply_type}'>        
                                <input type='hidden' name='reply_id' value='{$reply_id}'>        
                            </div>

                            <div class='sp-comm__form-field sp-comm__form-field--actionBtns sp-comm__form-field--hide'>
                                <button class='sp-comm__form-btn sp-comm__form-btn--cancel' type='button'>
                                    <span>Cancel</span>
                                </button>
                            
                                <button class='sp-comm__form-btn sp-comm__form-btn--comment sp-comm__form-btn--disabled' type='submit'>
                                    <span>Comment</span>
                                </button>
                            </div>
                        </div>
                    </form>
                ";


                
            }else{
                
                $output["error_status"] = 1;
            }


            echo json_encode($output);
        }
        

        //(2.Single Post page) add a comment when clicks on comment button
        public function add_comment()
        {

            //store the final output
            $output = array();

            if(!empty($this->user_info)){

                //first validate the post variable
                $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

                //store `form_type` index to indentify the form
                $form_type= (isset($_POST["form_type"])) ? $_POST["form_type"] : null;

                //store form data
                $form_data = (isset($_POST["form_data"])) ? $_POST["form_data"] : array();

                //store all models
                $comment_obj = $this->model_objs["comment_obj"];
                $nf_obj = $this->model_objs["nf_obj"];
                $post_obj = $this->model_objs["post_obj"];
                $reply_obj = $this->model_objs["reply_obj"];

                //set the time according to `Asia/Dhaka`;
                date_default_timezone_set("Asia/Dhaka");

                if($form_type == "primary"){

                    //insert comment in comment table

                    $post_info = array();

                    //fetch post related information to send notification
                    $fetch_post_info=$post_obj->select(array(
                        "column_name"=>"
                            posts.post_id, 
                            posts.post_title, 
                            posts.post_author 
                        ",
                        "where"=>"posts.post_id = {$form_data['post_id']}"
                    ));


                    if($fetch_post_info["status"] == 1 && $fetch_post_info["num_rows"] == 1){

                        //override $post_info default value with fetched post_info
                        $post_info = $fetch_post_info["fetch_all"][0];

                    }
                

                    //insert a comment in comments table
                    $insert_comment=$comment_obj->insert(array(
                        "fields"=>array(
                            "comment_content"=>htmlentities(trim($form_data["comment_content"]),ENT_QUOTES),
                            "comment_date"=>date("d F, Y_h:i:sA"),
                            "comment_author"=>$this->user_info['user_id'],
                            "post_id"=>$form_data["post_id"]
                        )
                    ));

                    if($insert_comment["status"] !== 1 && !isset($insert_comment["insert_id"])){

                        $output = array(
                            "error_status"=>100,
                            "errors"=>$insert_comment
                        );

                    }else{  

                        //Don't send notifications when post author is commenting on his/her own post
                        if($this->user_info["user_id"] !== $post_info["post_author"]){

                            //send a notification to the posta_author
                            $send_nf=$nf_obj->insert(array(
                                "fields"=>array(
                                    "nf_title"=>"<strong>{$this->user_info['user_name']} commented on your post:</strong>\r\n<span>{$this->functions->short_str_word($form_data['comment_content'],12)}</span>",
                                    "nf_date"=>date("d F, Y_h:i:sA"),
                                    "from_user_id"=>$this->user_info['user_id'],
                                    "to_user_id"=>$post_info['post_author'],
                                    "post_id"=>$post_info['post_id'],
                                    "nf_status"=>"unread"
                                )
                            ));
                                
                            if($send_nf["status"] !== 1 && !isset($send_nf["inser_id"])){
    
                                $output = array(
                                    "error_status"=>100,
                                    "errors"=>$send_nf
                                );

                            }else{

                                $output = array(
                                    "error_status"=>0
                                );
                            }

                        }else{

                            $output = array(
                                "error_status"=>0
                            );
                        }
                    }


                }elseif($form_type = "secondary"){
                    
                    /**
                     * Secondary form will be used to reply for a comment or a single reply.
                     * $reply_type = 'primary' means reply for a comment
                     * $reply_type = 'secondary' means reply for a comment reply
                     */
                    
                    //store reply type
                    $reply_type = $form_data["reply_type"];

                    //store the comment_id
                    $comment_id = $form_data["comment_id"];

                    //store the reply_id for secondary reply
                    $reply_id = $form_data["reply_id"];

                    if($reply_type == "primary"){

                        //store all the common  information add a primary comment 
                        $primary_comment_info = array();

                        //fetch primary comment information
                        $fetch_comment_info=$comment_obj->select(array(
                            "column_name"=>"
                                comments.comment_author,
                                comments.post_id,
                                users.user_name
                            ",
                            "join"=>array(
                                "users"=>"users.user_id = comments.comment_author",
                            ),
                            "where"=>"comments.comment_id={$comment_id}"

                        ));

                        if($fetch_comment_info["status"] == 1 && $fetch_comment_info["num_rows"] == 1){

                            //override $primary_comment_infos's default value
                            $primary_comment_info = $fetch_comment_info["fetch_all"][0];
                        }

                        //adding @user_name at the begining of comment_reply
                        $reply_content = "<a href='{$this->config->domain("users/{$primary_comment_info['user_name']}")}' title='{$primary_comment_info['user_name']}' target='_blank'>@{$primary_comment_info['user_name']}</a> ". $form_data["comment_content"];


                        //add a primary reply for primary comment
                        $insert_reply=$reply_obj->insert(array(
                            "fields"=>array(
                                "reply_content"=>htmlentities(trim($reply_content)),
                                "reply_date"=>date("d F, Y_h:i:sA"),
                                "user_id"=>$this->user_info["user_id"],
                                "comment_id"=>$comment_id
                            )
                        ));


                        if($insert_reply["status"] !== 1 && !isset($insert_reply['insert_id'])){

                            $output = array(
                                "error_status"=>100,
                                "errors"=>$insert_reply
                            );

                        }else{

                            //Don't send notification if comment author tries to reply his/her own comment
                            if($primary_comment_info["comment_author"] !== $this->user_info["user_id"]){

                                 //send a notification to the posta_author
                                $send_nf=$nf_obj->insert(array(
                                    "fields"=>array(
                                        "nf_title"=>"<strong>{$this->user_info['user_name']} replied on your comment:</strong>\r\n<span>{$this->functions->short_str_word($form_data['comment_content'],12)}</span>",
                                        "nf_date"=>date("d F, Y_h:i:sA"),
                                        "from_user_id"=>$this->user_info['user_id'],
                                        "to_user_id"=>$primary_comment_info['comment_author'],
                                        "post_id"=>$primary_comment_info['post_id'],
                                        "nf_status"=>"unread"
                                    )
                                ));

                                if($send_nf["status"] !== 1 && !isset($send_nf["insert_id"])){

                                    $output = array(
                                        "error_status"=>1,
                                        "errors"=>$send_nf
                                    );

                                }else{

                                    $output = array(
                                        "error_status"=>0
                                    );
                                }

                            }else{

                                $output = array(
                                    "error_status"=>0  
                                );
                            }
                           

                        }
                    
                    }elseif($reply_type == "secondary"){

                        //store all the common  information add a primary comment 
                        $secondary_comment_info = array();

                        //fetch primary comment information
                        $fetch_comment_info=$reply_obj->select(array(
                            "column_name"=>"
                                replies.user_id,
                                replies.comment_id,
                                users.user_name,
                                comments.post_id
                            ",
                            "join"=>array(
                                "users"=>"users.user_id = replies.user_id",
                                "comments"=>"comments.comment_id = replies.comment_id"
                            ),
                            "where"=>"replies.reply_id={$reply_id}"

                        ));

                        if($fetch_comment_info["status"] == 1 && $fetch_comment_info["num_rows"] == 1){

                            //override $secondary_comment_infos's default value
                            $secondary_comment_info = $fetch_comment_info["fetch_all"][0];
                        }

                        //add @user_name at begining of the content
                        $reply_content = "<a href='{$this->config->domain("users/{$secondary_comment_info['user_name']}")}' title='{$secondary_comment_info['user_name']}' target='_blank'>@{$secondary_comment_info['user_name']}</a> ". $form_data["comment_content"];
                        
                        $insert_reply = $reply_obj->insert(array(
                            "fields"=>array(
                                "reply_content"=>htmlentities(trim($reply_content)),
                                "reply_date"=>date("d F, Y_h:i:sA"),
                                "user_id"=>$this->user_info["user_id"],
                                "comment_id"=>$comment_id
                            )
                        ));

                        if($insert_reply["status"] !== 1 && !isset($insert_reply['insert_id'])){

                            $output = array(
                                "error_status"=>100,
                                "errors"=>$insert_reply
                            );

                        }else{


                             //Don't send notification if comment author tries to reply his/her own comment
                            if($secondary_comment_info["user_id"] !== $this->user_info["user_id"]){

                                 //send a notification to the posta_author
                                $send_nf=$nf_obj->insert(array(
                                    "fields"=>array(
                                        "nf_title"=>"<strong>{$this->user_info['user_name']} replied on your comment:</strong>\r\n<span>{$this->functions->short_str_word($form_data['comment_content'],12)}</span>",
                                        "nf_date"=>date("d F, Y_h:i:sA"),
                                        "from_user_id"=>$this->user_info['user_id'],
                                        "to_user_id"=>$secondary_comment_info['user_id'],
                                        "post_id"=>$secondary_comment_info['post_id'],
                                        "nf_status"=>"unread"
                                    )
                                ));

                                if($send_nf["status"] !== 1 && !isset($send_nf["insert_id"])){

                                    $output = array(
                                        "error_status"=>1,
                                        "errors"=>$send_nf
                                    );

                                }else{

                                    $output = array(
                                        "error_status"=>0
                                    );
                                }

                            }else{

                                $output = array(
                                    "error_status"=>0  
                                );
                            } 
                        }
                    }
                }
        
            
            }else{

                $output = array(
                    "error_status"=>1
                );
            }

            
     
            // print_r($output);
           echo json_encode($output);
        }

        //use the function to rate `comment` or`comment replies` or `post`
        public function  add_rate()
        {

            //Return the final output
            $output = array();

            if(!empty($this->user_info)){

                //first validate the post variable
                $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

                //store the current rate_action of user
                $rate_action = (isset($_POST["rate_action"])) ? $_POST["rate_action"] : null;
                
                //store the `rate_for` where user is trying to rate
                $rate_for = (isset($_POST["rate_for"])) ? $_POST["rate_for"] : null;
                
                //store the `rate_for_id` 
                $rate_for_id = (isset($_POST["rate_for_id"])) ? $_POST["rate_for_id"] : null;

                //store the `rate` model's object from $this->model_objs variable
                $rate_obj = $this->model_objs["rate_obj"];
                $post_obj = $this->model_objs["post_obj"];
                $comment_obj = $this->model_objs["comment_obj"];
                $reply_obj = $this->model_objs["reply_obj"];
                $nf_obj = $this->model_objs["nf_obj"];

                //first check user previously rated or not
                $prev_rating=$this->if_user_rated($rate_for,$rate_for_id);

                //store all the information to send a notification
                $nf_fields = array(
                    "nf_title"=>"",
                    "nf_date"=>date("d F, Y_h:i:sA"),
                    "from_user_id"=>$this->user_info["user_id"],
                    "to_user_id"=>"",
                    "post_id"=>"",
                    "nf_status"=>"unread"
                );

                if($rate_for == "post"){

                    $post_info = array();

                    $fetch_post_info = $post_obj->select(array(
                        "column_name"=>"
                            posts.post_id,
                            posts.post_title,
                            posts.post_author
                        ",
                        "where"=>"posts.post_id={$rate_for_id}"
                    ));

                    if($fetch_post_info["status"] == 1 && $fetch_post_info["num_rows"] == 1){

                        $post_info = $fetch_post_info["fetch_all"][0];
                    }

                    $nf_fields["nf_title"] = "<strong>{$this->user_info['user_name']} {$rate_action}d on your post: </strong>\r\n<span>{$this->functions->short_str_word($post_info['post_title'],12)}</span>";

                    $nf_fields["to_user_id"] = $post_info["post_author"];

                    $nf_fields["post_id"] = $post_info["post_id"];

                }else if($rate_for == "comment"){

                    $comment_info = array();

                    $fetch_comment_info = $comment_obj->select(array(
                        "column_name"=>"
                            comments.comment_id,
                            comments.comment_author,
                            comments.comment_content,
                            comments.post_id
                        ",
                        "where"=>"comments.comment_id={$rate_for_id}"
                    ));

                    if($fetch_comment_info["status"] == 1 && $fetch_comment_info["num_rows"] == 1){

                        $comment_info = $fetch_comment_info["fetch_all"][0];
                    }

                    $nf_fields["nf_title"] = "<strong>{$this->user_info['user_name']} {$rate_action}d on your comment: </strong>\r\n<span>{$this->functions->short_str_word($comment_info['comment_content'],12)}</span>";

                    $nf_fields["to_user_id"] = $comment_info["comment_author"];

                    $nf_fields["post_id"] = $comment_info["post_id"];

                }else if($rate_for == "comment_reply"){

                    $reply_info = array();

                    $fetch_reply_info = $reply_obj->select(array(
                        "column_name"=>"
                            replies.reply_id,
                            replies.user_id,
                            replies.reply_content,
                            replies.comment_id,
                            comments.post_id
                        ",
                        "join"=>array(
                            "comments"=>"comments.comment_id = replies.comment_id"
                        ),
                        "where"=>"replies.reply_id={$rate_for_id}"
                    ));

                    if($fetch_reply_info["status"] == 1 && $fetch_reply_info["num_rows"] == 1){

                        $reply_info = $fetch_reply_info["fetch_all"][0];
                    }

                    $reply_info["reply_content"] = html_entity_decode($reply_info["reply_content"]);

                    $reply_info["reply_content"] = strip_tags($reply_info["reply_content"]);

                    preg_match_all("/@[a-z|A-Z|0-9|_]*/",$reply_info["reply_content"],$username);
                    
                    if(isset($username[0][0])){

                        //replace the `(0-9)*` pattern with an empty string
                        $reply_info["reply_content"]=str_replace("{$username[0][0]}","",$reply_info["reply_content"]);
    
                        //remove space from left and right sides
                        $reply_info["reply_content"]=trim($reply_info["reply_content"]);
                    }
                    
                    
                    

                    $nf_fields["nf_title"] = "<strong>{$this->user_info['user_name']} {$rate_action}d on you reply: </strong>\r\n<span>{$this->functions->short_str_word($reply_info['reply_content'],12)}</span>";

                    $nf_fields["to_user_id"] = $reply_info["user_id"];

                    $nf_fields["post_id"] = $reply_info["post_id"];

                }
                


        
                if($prev_rating !== null ){

                    /**
                     * User Previously rated, Now 
                     * we have to get the previously rating
                     */
                    
                    if($prev_rating == $rate_action){

                        /**
                         * User is trying to remove his/her 
                         * previouse rating permanantly. so let's delete a record
                         */

                         //delete a rate of the current user
                         $delete_rate=$rate_obj->delete(array(
                             "where"=>"rates.rate_for='{$rate_for}' AND rates.rate_for_id={$rate_for_id} AND rates.user_id={$this->user_info['user_id']}"
                         ));

                         if($delete_rate["status"] !== 1  && $delete_rate["affected_rows"] == 0){

                            $output = array(
                                "error_status"=>100,
                                "errors"=>$delete_rate
                            );
                            
                         }else{

                            $output = array(
                                "error_status"=>0
                            );
                         }
        
                    }else{
                        
                        /**
                         * User is trying to change his/her 
                         * previouse rating. so let's update his/her previous rating
                         */

                            //delete a rate of the current user
                            $update_rate=$rate_obj->update(array(
                                "fields"=>array(
                                    "rate_action"=>$rate_action
                                ),
                                "where"=>"rates.rate_for='{$rate_for}' AND rate_for_id={$rate_for_id}"
                            ));

                            if($update_rate["status"] !== 1  && $update_rate["affected_rows"] == 0){

                                $output = array(
                                    "error_status"=>100,
                                    "errors"=>$update_rate
                                );
                            
                            }else{

                                $output = array(
                                    "error_status"=>0
                                );
                            }
                    }

                
                }else{

                    /**
                     * user didn't rate previously,
                     * let's add a rating
                     */

                    //add a rating
                    $add_rate=$rate_obj->insert(array(
                        "fields"=>array(
                            "rate_for"=>$rate_for,
                            "rate_for_id"=>$rate_for_id,
                            "user_id"=>$this->user_info["user_id"],
                            "rate_action"=>$rate_action
                        )
                    ));

                    if($add_rate["status"] !== 1 && !isset($add_rate["insert_id"])){

                        $output = array(
                            "error_status"=>100,
                            "errors"=>$add_rate
                        );
                    
                    }else{

                        if($this->user_info["user_id"] !== $nf_fields["to_user_id"]){

                            $send_nf = $nf_obj->insert(array(
                                "fields"=>$nf_fields
                            ));
                        }

                        $output = array(
                            "error_status"=>0
                        );
                    }
                }
                

            }else{

                $output = array(
                    "error_status"=>1
                );
            }
            
            echo json_encode($output); 

        }

        //use the function to delete a comments and replies
        public function delete_comments()
        {

            $output = array();

            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store the deletation type  
            $delete = (isset($_POST["delete"])) ? $_POST["delete"] : null;

            //store the delete id which will be used to delete the record
            $delete_id = (isset($_POST["delete_id"])) ? $_POST["delete_id"] : null;
        
            //store all models
            $comment_obj = $this->model_objs["comment_obj"];
            $reply_obj = $this->model_objs["reply_obj"];
            $rate_obj = $this->model_objs["rate_obj"];

    
            if($delete == "comment"){

                //first fetch all the replies of the comment
                $fetch_replies=$reply_obj->select(array(
                    "where"=>"replies.comment_id = {$delete_id}"
                ));
            
                
                if($fetch_replies["status"] == 1 && $fetch_replies["num_rows"] > 0){
                    /**
                     * If the comment has replies,
                     * first delete all of them
                     */

                    $delete_replies=$reply_obj->delete(array(
                        "where"=>"replies.comment_id = {$delete_id}"
                    ));
                }

                //first fetch all the rate of the comment
                $fetch_rate=$rate_obj->select(array(
                    "where"=>"rates.rate_for = 'comment' AND rates.rate_for_id={$delete_id}"
                ));
            
                
                if($fetch_rate["status"] == 1 && $fetch_rate["num_rows"] > 0){
                    /**
                     * If the comment has replies,
                     * first delete all of them
                     */

                    $delete_rate=$rate_obj->delete(array(
                        "where"=>"rates.rate_for ='comment' AND rates.rate_for_id={$delete_id}"
                    ));
                }

                //delete comments based where delete id matchs with comment id
                $delete_comment=$comment_obj->delete(array(
                    "where"=>"comments.comment_id={$delete_id}"
                ));
            
                if($delete_comment["status"] == 1 && $delete_comment["affected_rows"] == 1){
                    
                    $output = array(
                        "error_status"=>0
                    );

                }else{

                    $output = array(
                        "error_status"=>1,
                        "errors"=>$delete_comment
                    );
                }
         
            }else if($delete == "comment_reply"){
                

                //first fetch all the rate of the comment
                $fetch_rate=$rate_obj->select(array(
                    "where"=>"rates.rate_for = 'comment_reply' AND rates.rate_for_id={$delete_id}"
                ));
        
                
                if($fetch_rate["status"] == 1 && $fetch_rate["num_rows"] > 0){
                    /**
                     * If the comment has replies,
                     * first delete all of them
                     */

                    $delete_rate=$rate_obj->delete(array(
                        "where"=>"rates.rate_for ='comment_reply' AND rates.rate_for_id={$delete_id}"
                    ));
                }

                        
                //delete comment reply where delete_id matchs with reply_id
                $delete_reply=$reply_obj->delete(array(
                    "where"=>"replies.reply_id={$delete_id}"
                ));
            
                if($delete_reply["status"] == 1 && $delete_reply["affected_rows"] == 1){
                    
                    $output = array(
                        "error_status"=>0
                    );

                }else{

                    $output = array(
                        "error_status"=>1,
                        "errors"=>$delete_reply
                    );
                }
            }
    


            echo json_encode($output);
        
        }

        //use the function to load 'like' and `dislike` button in single post
        public function load_post_rate_btns()
        {

            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store all the info related to the post
            $post_info = array();

            //store the `post` model's object from $this->modal variable
            $post_obj = $this->model_objs["post_obj"];
            
            $fetch_post_info=$post_obj->select(array(
                "column_name"=>"posts.post_id, posts.post_link, posts.post_author",
                "where"=>"posts.post_link='{$_POST['post_link']}'"
            ));

            if($fetch_post_info["status"] == 1 && $fetch_post_info["num_rows"] == 1){
            
                $post_info = $fetch_post_info["fetch_all"][0];
            }

            //fetch post rating `like` and `dislike
            $post_rates=$this->fetch_rates("post",$post_info["post_id"]);

            //check logged use rated the current post
            $check_rated =$this->if_user_rated("post",$post_info["post_id"]);

            //store pressed class for like button
            $like_btn_pressed = ($check_rated == "like") ? "sp-content__btn--pressed" : "";
            
            //store pressed class for dilike button
            $dislike_btn_pressed = ($check_rated == "dislike") ? "sp-content__btn--pressed" : "";
            
        
            $output = array(
                "like_btn"=>"
                    <button class='sp-content__btn sp-content__btn--rating sp-content__btn--like {$like_btn_pressed}' type='button' data-rate_for='post' data-rate_for_id='{$post_info['post_id']}'>
                        <i class='fa fa-thumbs-up sp-content__btn-icon'></i>
                        <span class='sp-content__btn-txt'>{$post_rates['likes']['total']}</span>
                    </button>
                ",
                "dislike_btn"=>"
                    <button class='sp-content__btn sp-content__btn--rating sp-content__btn--dislike {$dislike_btn_pressed}' type='button' data-rate_for='post' data-rate_for_id='{$post_info['post_id']}'>
                        <i class='fa fa-thumbs-down sp-content__btn-icon'></i>
                        <span class='sp-content__btn-txt'>{$post_rates['dislikes']['total']}</span>
                    </button>
                
                "
            );

            echo json_encode($output);

        }

        //use the function to load save post button
        public function load_post_save_btn()
        {

            //first validate the post variable
            $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

            //store all the info related to the post
            $post_info = array();

            //store the `post` model's object from $this->modal variable
            $post_obj = $this->model_objs["post_obj"];

            //fetch post information
            $fetch_post_info=$post_obj->select(array(
                "column_name"=>"posts.post_id, posts.post_link, posts.post_author",
                "where"=>"posts.post_link='{$_POST['post_link']}'"
            ));

            if($fetch_post_info["status"] == 1 && $fetch_post_info["num_rows"] == 1){
            
                $post_info = $fetch_post_info["fetch_all"][0];
            }
        
            if($this->if_user_saved_the_post($post_info['post_id'])){

                $output = array(
                    "save_btn"=>"
                        <button class='sp-content__btn sp-content__btn--save sp-content__btn--pressed' type='button' data-post_id='{$post_info['post_id']}'>
                            <i class='fa fa-floppy-o sp-content__btn-icon'></i>
                            <span class='sp-content__btn-txt'>Saved</span>
                        </button>
                    "
                );

            }else{
                
                $output = array(
                    "save_btn"=>"
                        <button class='sp-content__btn sp-content__btn--save' type='button' data-post_id='{$post_info['post_id']}'>
                            <i class='fa fa-floppy-o sp-content__btn-icon'></i>
                            <span class='sp-content__btn-txt'>Save</span>
                        </button>
                    "
                );
            }

            
            echo json_encode($output);

        }

        //use the function to add a post in save list
       public function add_to_save_list()
       {

            $output = array();

            if(!empty($this->user_info)){

                //first validate the post variable
                $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

                //store `post_id` index from $_POST variable
                $post_id = isset($_POST["post_id"]) ? $_POST["post_id"] : null;

                //store the `saved_post` model's object from $this->modal variable
                $sp_obj = $this->model_objs["sp_obj"];
                
              
                if($this->if_user_saved_the_post($post_id)){

                    //user already saved the post let's remove from her/his saved list
                    $delete_saved_post=$sp_obj->delete(array(
                        "where"=>"saved_posts.user_id={$this->user_info['user_id']} AND saved_posts.post_id={$post_id}"
                    ));
                
                    if($delete_saved_post["status"] == 1 && $delete_saved_post["affected_rows"] == 1){

                        $output = array(
                            "error_status"=>0
                        );

                    }else{

                        $output = array(
                            "error_status"=>100,
                            "errors"=>$delete_saved_post
                        );

                    }

                }else{
                    
                    //user did not save the post. let's add the post in saved list
                    $saved_post=$sp_obj->insert(array(
                        "fields"=>array(
                            "user_id"=>$this->user_info["user_id"],
                            "post_id"=>$post_id
                        )
                    ));
                
                    if($saved_post["status"] == 1 && isset($saved_post["insert_id"])){

                        $output = array(
                            "error_status"=>0
                        );

                    }else{

                        $output = array(
                            "error_status"=>100,
                            "errors"=>$saved_post
                        );

                    }
                }
                
            }else{

                $output = array(
                    "error_status"=>1
                );
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
