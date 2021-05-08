<?php 
/*
 * 1. posts controller's all ajax reqeust will be controlled 
 *   from this `ajax_posts` controller classs.
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_posts extends controller{
    
    private $config;
    private $functions;

    public function __construct(){

        $this->config=new config;
        $this->functions=new functions;

        if($_SERVER["REQUEST_METHOD"] !== "POST"){

            $this->functions->not_found();
            die();

        }else{
            //some time we need to access session variable. so, it should start at first
            session_start();
        }
    }  

    //fetch post rating such as 'like' and 'dislike'
    public function fetch_post_rating($param){

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

    //load subscribe button and total subscriber
    public function load_total_subs_and_btn(){

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
    
    //load post rating `like`  & `dislike`
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

    //load single post comments
    public function load_comments(){
        //to laod comments we need a post link

        //first validate the post variable
        $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);
        
        //store the post_link index from $_POST variable
        $post_link=$_POST["post_link"];

        /* 
            we need the post_id for fetching the comments.
            so, we going fetch the post_id using the $post_link  
        */

        //Here will store the post_id
        $post_id="";

        //Here will store the post_id
        $post_author="";

        //store the logged in user's user_id
        $logged_in_user_id=(isset($_SESSION['user_id'])) ? $_SESSION["user_id"] : null;

        //include the post model
        $post_obj=$this->model("post");

        $fetch_post_id=$post_obj->select(array(
            "column_name"=>"
                posts.post_id,
                posts.post_author
            ",
            "where"=>"posts.post_link='{$post_link}'"
        ));

        if($fetch_post_id["status"] == 1 && $fetch_post_id["num_rows"] == 1){

            //set the $post_id with fetched post_id
            $post_id=$fetch_post_id["fetch_all"][0]["post_id"];
            
            //set the $post_author with fetched post_author
            $post_author=$fetch_post_id["fetch_all"][0]["post_author"];

        }else{

            echo (isset($fetch_post_id["error"])) ? $fetch_post_id["error"] : "somehting went wrong in ajax_request on line 50";
            
            die();
        }
    
        /* 
            Now we need fetch all comments 
            based on fetched post
        */

        //include the post comment model
        $comment_obj=$this->model("comment");
        
        //Here will store all the comments
        $comments=[];

        $fetch_comments=$comment_obj->select(array(
            "column_name"=>"
                comments.comment_id,
                comments.comment_content,
                comments.comment_date,
                comments.comment_author,
                users.user_id,
                users.user_name,
                user_files.ufile_name,
                user_files.ufile_ext
            ",
            "join"=>array(
                "users"=>"users.user_id=comments.comment_author",
                "user_files"=>"users.user_id = user_files.user_id"
                
            ),
            "order"=>array(
                "type"=>"DESC",
                "column"=>"comment_id"
            ),
            "where"=>"comments.post_id={$post_id}"
        ));

        if($fetch_comments["status"] == 1 && $fetch_comments["num_rows"] > 0){

            //store all the fetch result in $comments varaible
            $comments["total_comments"]=$fetch_comments["num_rows"];

            $comments["comments"]=$fetch_comments["fetch_all"];

        }

        /* 
            Finally we have fetch all replies for each comment 
        */

        //firs check if we have comments
        if(!empty($comments)){

            //include comment_replies model
            $cr_obj=$this->model("comment_replies");

            foreach($comments["comments"] as $comment_index=>$comment){

                //store the comment id
                $comment_id=$comment["comment_id"];

                $fetch_comment_replies=$cr_obj->select(array(
                    "column_name"=>"
                        comment_replies.cr_id,
                        comment_replies.cr_content,
                        comment_replies.cr_date,
                        users.user_name,
                        users.user_id,
                        user_files.ufile_name,
                        user_files.ufile_ext
                        
                    ",
                    "join"=>array(
                        "users"=>"users.user_id= comment_replies.cr_author",
                        "user_files"=>"users.user_id= user_files.user_id"
                    ),
                    "order"=>array(
                        "column"=>"comment_replies.cr_id",
                        "type"=>"ASC"
                    ),
                    "where"=>"comment_replies.comment_id={$comment_id}"
                ));

                if($fetch_comment_replies["status"] == 1 && $fetch_comment_replies["num_rows"] > 0){

                    //Also add replies in total comments
                    $comments["total_comments"]= $comments["total_comments"] + $fetch_comment_replies["num_rows"];
                    
                    //create a new index in $comments array as comment_replies and store all the replies
                    $comments["comments"][$comment_index]["comment_replies"]=$fetch_comment_replies["fetch_all"];

                }
            }

            ?>

            <div class="sp-comm">
                <div class="sp-comm__title">
                    <h4>
                        <?php 
                            echo ($comments["total_comments"] > 1) ? "<span>{$comments['total_comments']}</span> <strong>comments</strong>" :  "<span>{$comments['total_comments']}</span> <strong>comment</strong>" 
                        ?> 
                    </h4> 
                </div>

                <form class="sp-comm__form sp-comm__form--lg sp-comm__form--primary">
                    <div class="sp-comm__form-side-wrap">
                        <div class="sp-comm__form-side sp-comm__form-side--top">
                            <div class="sp-comm__logged-user-info">
                                <?php 

                                    if($this->functions->if_user_logged_in()){
                                        
                                        //store the logged in user's user_id
                                        $user_id=$_SESSION["user_id"];

                                        $user_files_obj=$this->model("user_files");

                                        $fetch_userimg=$user_files_obj->select(array(
                                            "where"=>"user_files.user_id={$user_id}"
                                        ));
                                        
                                        //Here we wil store the ufile_name
                                        $ufile_name="";

                                        //Here we wil store the ufile_ext
                                        $ufile_ext="";

                                        if($fetch_userimg["status"] == 1 && $fetch_userimg["num_rows"] == 1){
                                            
                                            //Here we wil store the ufile_name
                                            $ufile_name= $fetch_userimg["fetch_all"][0]["ufile_name"];
                                            
                                            //Here we wil store the ufile_ext
                                            $ufile_ext= $fetch_userimg["fetch_all"][0]["ufile_ext"];

                                        }else{

                                            echo (isset($fetch_post_id["error"])) ? $fetch_post_id["error"] : "somehting went wrong in ajax_request on line 187";
                                            die();
                                        }

                                        echo "
                                            <img src='{$this->config->domain("app/uploads/users/{$ufile_name}-sm.{$ufile_ext}")}' alt='{$_SESSION['user_name']}'s profile picture on lobster'>
                                        ";

                                    }else{
                                        
                                        echo "
                                            <img src='{$this->config->domain("app/uploads/users/user-placeholder-img.png")}' alt=' Placeholder profile picture on lobster'>
                                        ";
                                    }
                                ?>
                            </div>

                            <div class="sp-comm__form-field sp-comm__form-field--textarea">
                                <textarea class="sp-comm__form-input sp-comm__form-input-textarea" name="comment_content" placeholder="Write your comment"></textarea>
                                <input class="sp-comm__form-input sp-com__form-input--hidden" type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            </div>
                        </div>

                        <div class="sp-comm__form-side sp-comm__form-side--bottom sp-comm__form-side--hide">
                            <div class="sp-comm__form-btn-wrap">
                                <button class="sp-comm__form-btn sp-comm__form-btn--cancel" type="button">Cancel</button>
                                <button class="sp-comm__form-btn sp-comm__form-btn--comment sp-comm__form-btn--disabled" type="button">
                                    <span>Comment</span>
                                </button>
                            </div>
                        </div>
                    </div>     
                </form>
                
                <?php foreach($comments["comments"] as $comment_index=>$comment): ?>
                    <div class="sp-single-comm sp-single-comm--primary">
                        <div class="sp-single-comm__userimg sp-single-comm__userimg--primary">
                            <?php
                            
                                if($comment["ufile_name"] == "false" || $comment["ufile_ext"] == "false"){

                                    echo "
                                        <img class='sp-content__auth-img' src='{$this->config->domain('app/uploads/users/user-placeholder-img.png')}' alt='{$comment['user_name']}'s placeholder Profile picture on lobster'>
                                    ";

                                }else{

                                    echo "
                                        <img class='sp-content__auth-img' src='{$this->config->domain("app/uploads/users/{$comment['ufile_name']}-sm.{$comment['ufile_ext']}")}' alt='{$comment['user_name']}'s Profile picture on lobster'>
                                    ";
                                }
                            ?>
                        </div>
                        <div class="sp-single-comm__wrap sp-single-comm__wrap--primary">          
                            <div class="sp-single-comm__content sp-single-comm__content--primary">
                                <div class="sp-single-comm__username sp-single-comm__username--primary <?php echo  ($comment["user_id"] == $post_author) ? "sp-single-comm__username--admin" : ""; ?>">
                                    <a href="<?php echo $this->config->domain("users/{$comment["user_name"]}") ?>"><?php echo $comment["user_name"]; ?></a>
                                    <i class="fa fa-circle"></i>
                                    <span>
                                        <?php 
                                            $comment_date=str_replace(", ", "-", $comment['comment_date']);
                                            $comment_date=str_replace(" ","-",$comment_date);
                                            $comment_date_formated=str_replace("_"," ",$comment_date);

                                            echo $this->functions->get_time_in_ago($comment_date_formated); 
                                        
                                        ?>
                                    </span>
                                </div>

                                <div class="sp-single-comm__body sp-single-comm__body--primary">
                                    <p>
                                        <?php echo $comment["comment_content"]; ?>
                                    </p>
                                </div>

                                <div class="sp-single-comm__reply sp-single-comm__reply--primary">
                                    <button class="sp-single-comm__btn sp-single-comm__btn--reply sp-single-comm__btn--primary" type="button" data-comment_id="<?php echo $comment['comment_id']; ?>">Reply</button>
                                </div>
                            </div>
                            
                            <?php 
                            
                                if(isset($comment["comment_replies"]) && !empty($comment["comment_replies"])):
                                    foreach($comment["comment_replies"] as $comment_reply_index=>$comment_reply):
                            ?>
                                    <div class="sp-single-comm sp-single-comm--secondary">
                                        <div class="sp-single-comm__userimg sp-single-comm__userimg--secondary">
                                            <?php
                                                
                                                if($comment_reply["ufile_name"] == "false" || $comment_reply["ufile_ext"] == "false"){

                                                    echo "
                                                        <img class='sp-content__auth-img' src='{$this->config->domain('app/uploads/users/user-placeholder-img.png')}' alt='{$comment_reply['user_name']}'s placeholder Profile picture on lobster'>
                                                    ";

                                                }else{

                                                    echo "
                                                        <img class='sp-content__auth-img' src='{$this->config->domain("app/uploads/users/{$comment_reply['ufile_name']}-sm.{$comment_reply['ufile_ext']}")}' alt='{$comment_reply['user_name']}'s Profile picture on lobster'>
                                                    ";
                                                }
                                            ?>
                                        </div>

                                        <div class="sp-single-comm__content sp-single-comm__content--secondary">
                                            <div class="sp-single-comm__username sp-single-comm__username--secondary <?php echo  ($comment_reply["user_id"] == $post_author) ? "sp-single-comm__username--admin" : ""; ?>">
                                                <a href="<?php echo $this->config->domain("users/{$comment_reply['user_name']}") ?>"><?php echo $comment_reply['user_name']?></a>
                                                <i class="fa fa-circle"></i>
                                                <span>
                                                    <?php 
                                                        $cr_date=str_replace(", ", "-", $comment_reply['cr_date']);
                                                        $cr_date=str_replace(" ","-",$cr_date);
                                                        $cr_date_formated=str_replace("_"," ",$cr_date);

                                                        echo $this->functions->get_time_in_ago($cr_date_formated); 
                                                    ?>
                                                
                                                </span>
                                            </div>

                                            <div class="sp-single-comm__body sp-single-comm__body--secondary">
                                                <p>
                                                    <?php echo $comment_reply['cr_content']?>
                                                </p>
                                            </div>
                                            <div class="sp-single-comm__reply sp-single-comm__reply--secondary">
                                                <button class="sp-single-comm__btn sp-single-comm__btn--reply sp-single-comm__btn--secondary" type="button" data-comment_id="<?php echo $comment['comment_id']; ?>">
                                                    Reply
                                                </button>
                                            </div>
                                        </div>
                                        <?php if($logged_in_user_id !== null  && $logged_in_user_id == $post_author): ?>
                                        <div class="btn-group dropleft sp-single-comm__opt sp-single-comm__opt--secondary">
                                            <button class="sp-single-comm__btn sp-single-comm__btn--opt sp-single-comm__btn--secondary" data-toggle="dropdown" id="dropdown-secondary" type="button" data-offset="10,0">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>

                                            <div class="dropdown-menu sp-single-comm__dropdown sp-single-comm__dropdown--secondary" aria-labelledby="#dropdown-secondary">
                                                <ul class="sp-single-comm__dropdown-list">
                                                    <li class="sp-single-comm__dropdown-item">
                                                        <a class="sp-single-comm__dropdown-link sp-single-comm__dropdown-link--delete" role="button" data-comment_type="secondary_comment" data-cr_id="<?php echo $comment_reply["cr_id"] ?>">
                                                            <i class="fa fa-trash"></i>
                                                            <span>Delete</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>   
                                        <?php endif; ?>                                            
                                    </div>
                            <?php 
                                    endforeach; 
                                endif;
                            ?>
                        </div>
                        <?php if($logged_in_user_id !== null  && $logged_in_user_id == $post_author): ?>
                        <div class="btn-group dropleft sp-single-comm__opt sp-single-comm__opt--primary">
                            <button class="sp-single-comm__btn sp-single-comm__btn--opt sp-single-comm__btn--primary" data-toggle="dropdown" id="dropdown-primary" type="button" data-offset="10,0">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu sp-single-comm__dropdown sp-single-comm__dropdown--primary" aria-labelledby="#dropdown-primary">
                                <ul class="sp-single-comm__dropdown-list">
                                    <li class="sp-single-comm__dropdown-item">
                                        <a class="sp-single-comm__dropdown-link sp-single-comm__dropdown-link--delete" role="button" data-comment_type="primary_comment" data-comment_id="<?php echo $comment["comment_id"] ?>">
                                            <i class="fa fa-trash"></i>
                                            <span>Delete</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?> 
                    
                    </div>
                <?php endforeach; ?>
            </div><!--sp-comm-->
                  



            <?php



        }else{
            ?>

            <div class="sp-comm">
                <div class="sp-comm__title">
                    <h4>0 comments</h4> 
                </div>

                <form class="sp-comm__form sp-comm__form--lg sp-comm__form--primary">
                    <div class="sp-comm__form-side-wrap">
                        <div class="sp-comm__form-side sp-comm__form-side--top">
                            <div class="sp-comm__logged-user-info">
                            <?php 

                                if($this->functions->if_user_logged_in()){
                                    
                                    //store the logged in user's user_id
                                    $user_id=$_SESSION["user_id"];

                                    $user_files_obj=$this->model("user_files");

                                    $fetch_userimg=$user_files_obj->select(array(
                                        "where"=>"user_files.user_id={$user_id}"
                                    ));
                                    
                                    //Here we wil store the ufile_name
                                    $ufile_name="";

                                    //Here we wil store the ufile_ext
                                    $ufile_ext="";

                                    if($fetch_userimg["status"] == 1 && $fetch_userimg["num_rows"] == 1){
                                        
                                        //Here we wil store the ufile_name
                                        $ufile_name= $fetch_userimg["fetch_all"][0]["ufile_name"];
                                        
                                        //Here we wil store the ufile_ext
                                        $ufile_ext= $fetch_userimg["fetch_all"][0]["ufile_ext"];

                                    }else{

                                        echo (isset($fetch_post_id["error"])) ? $fetch_post_id["error"] : "somehting went wrong in ajax_request on line 187";
                                        die();
                                    }

                                    echo "
                                        <img src='{$this->config->domain("app/uploads/users/{$ufile_name}-sm.{$ufile_ext}")}' alt='{$_SESSION['user_name']}'s profile picture on lobster'>
                                    ";

                                }else{
                                    
                                    echo "
                                        <img src='{$this->config->domain("app/uploads/users/user-placeholder-img.png")}' alt=' Placeholder profile picture on lobster'>
                                    ";
                                }
                            ?>
                            
                            </div>

                            <div class="sp-comm__form-field sp-comm__form-field--textarea">
                                <textarea class="sp-comm__form-input sp-comm__form-input-textarea" name="comment_content" placeholder="Write your comment"></textarea>
                                <input class="sp-comm__form-input sp-com__form-input--hidden" type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            </div>
                        </div>

                        <div class="sp-comm__form-side sp-comm__form-side--bottom sp-comm__form-side--hide">
                            <div class="sp-comm__form-btn-wrap">
                                <button class="sp-comm__form-btn sp-comm__form-btn--cancel" type="button">Cancel</button>
                                <button class="sp-comm__form-btn sp-comm__form-btn--comment sp-comm__form-btn--disabled" type="button">
                                    <span>Comment</span>
                                </button>
                            </div>
                        </div>
                    </div>     
                </form>
                <div class="sp-comm__msg">
                    <h5><i class="fa fa-info-circle"></i> No comments found. Be the first commentor</h5>
                </div>
            </div><!--sp-comm-->
              
            <?php
         
        }

    }
    
    //load save post button
    public function load_save_post_btn(){

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

    //append the reply form when click'c reply button
    public function append_reply_form(){

        //first validate the string
        $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

        //store the comment_id from $_POST variable
        $comment_id=$_POST["comment_id"];
        
        //Here we will store the final output
        $output=[];

        if($this->functions->if_user_logged_in()){

            //store the logged in user's user_id
            $user_id=$_SESSION["user_id"];

            $user_files_obj=$this->model("user_files");

            $fetch_userimg=$user_files_obj->select(array(
                "where"=>"user_files.user_id={$user_id}"
            ));
            

            //store the actual logged in user image
            $logged_in_user_img="";

            if($fetch_userimg["status"] == 1 && $fetch_userimg["num_rows"] == 1){
                

                if($fetch_userimg["fetch_all"][0]["ufile_name"] == "false" || $fetch_userimg["fetch_all"][0]["ufile_ext"] == "false"){

                    //user did not upload any image use the place holder image
                    $logged_in_user_img="user-placeholder-img.png";
                    
                }else{
                    
                    //use upload an image and use it
                    $logged_in_user_img="{$fetch_userimg["fetch_all"][0]["ufile_name"]}-sm.{$fetch_userimg["fetch_all"][0]["ufile_ext"]}";
                }
                
                

            }else{

                echo (isset($fetch_post_id["error"])) ? $fetch_post_id["error"] : "somehting went wrong in ajax_request on line 515";
                die();
            }



            //set the error index to 1
            $output["error"]=1;

            $output["form"]="
                <form class='sp-comm__form sp-comm__form--md sp-comm__form--secondary'>
                    <div class='sp-comm__form-side-wrap'>
                        <div class='sp-comm__form-side sp-comm__form-side--top'>
                            <div class='sp-comm__logged-user-info'>
                                <img src='{$this->config->domain("app/uploads/users/{$logged_in_user_img}")}' alt='Profile Picture on lobster'>
                            </div>

                            <div class='sp-comm__form-field sp-comm__form-field--textarea'>
                                <textarea class='sp-comm__form-input sp-comm__form-input-textarea' name='comment_content' placeholder='Reply'></textarea>
                                <input type='hidden' name='comment_id' value='{$comment_id}'>
                            </div>
                        </div>

                        <div class='sp-comm__form-side sp-comm__form-side--bottom'>
                            <div class='sp-comm__form-btn-wrap'>
                                <button class='sp-comm__form-btn sp-comm__form-btn--cancel' type='button'>Cancel</button>
                                <button class='sp-comm__form-btn sp-comm__form-btn--comment sp-comm__form-btn--disabled' type='button'><span>Comment</span></button>
                            </div>
                        </div>
                    </div>     
                </form> 
            
            ";
            
        }else{
            
            $output["error"]=0;
        }

        echo json_encode($output);

    }

     //add a comment when clicks on comment button
    public function add_comment(){

        $output=[];

        if($this->functions->if_user_logged_in()){

            //user logged in

            //first validate $_POST variable
            $_POST=filter_var_array($_POST,FILTER_SANITIZE_STRING);

            //set the time zone to Asia/Dhaka
            date_default_timezone_set("Asia/Dhaka");

            $commnet_type=$_POST["comment_type"];

            if($commnet_type == "primary_comment"){

                //store the post_id from $_POST variable
                $post_id=$_POST["post_id"];
                
                //Here will store the post_author id
                $post_author="";

                //include the post model
                $post_obj=$this->model("post");
                
                //fetch the post_author's id
                $fetch_post_author=$post_obj->select(array(
                    "column_name"=>"posts.post_author",
                    "where"=>"posts.post_id={$post_id}"
                ));

                if($fetch_post_author["status"] == 1 && $fetch_post_author["num_rows"] == 1){
            
                    //set $post_author to fetched post_author
                    $post_author = $fetch_post_author["fetch_all"][0]["post_author"];
                }

                //store the comment_content from $_POST variable
                $comment_content=$_POST["comment_content"];

                //comment_author(logged in user's id will be comment_author)
                $comment_author=$_SESSION['user_id'];
            
                //comment_author_name(logged in user's name will be comment_author)
                $comment_author_name=$_SESSION['user_name'];
            
                //include the comment model
                $comment_obj=$this->model("comment");

                $insert_comment=$comment_obj->insert(array(
                    "fields"=>array(
                        "comment_content"=>$comment_content,
                        "comment_date"=>date("d F, Y_h:i:sA"),
                        "comment_author"=>$comment_author,
                        "post_id"=>$post_id,  
                    )
                ));
                
                if($insert_comment["status"] == 1 && isset($insert_comment["insert_id"])){


                    /*Don't send notification when post author comment's on his/her post */
                    if($post_author !== $comment_author){

                        //comment_author's user_id will be the $from_user_id
                        $from_user_id=$comment_author;

                        //post_author's id will be the $to_user_id
                        $to_user_id=$post_author;

                        //include the notification model
                        $nf_obj=$this->model("notification");

                        $nf_param=array(
                            "nf_obj"=>$nf_obj,
                            "nf_title"=>"
                                <strong>{$comment_author_name} commented your post:</strong>\r\n
                                <span>{$comment_content}</span>\r\n
                            ",
                            "from_user_id"=> $from_user_id,
                            "to_user_id"=>$to_user_id,
                            "post_id"=>$post_id
                        );

                        if( $this->functions->add_notification($nf_param)){
                            
                            echo 1;

                        }else{

                            echo 0;

                        }

                    }else{

                        echo 1;
                        
                    }

                }else{

                    echo 0;
                }

            }elseif($commnet_type == "secondary_comment"){

                //store the comment_content from $_POST variable
                $comment_content=$_POST["comment_content"];

                //store the comment_id from $_POST variable
                $comment_id=$_POST["comment_id"];

                //Here we will store the post_id
                $comment_author="";

                //Here we will store the comment_author's id
                $post_id="";

                //logged in user's user_id will the $cr_author_id
                $cr_author_id=$_SESSION['user_id'];

                //logged in user's user_name will the $cr_author_name
                $cr_author_name=$_SESSION['user_name'];

                //include the comment model
                $comment_obj=$this->model("comment");

                $fetch_comment_auhtor=$comment_obj->select(array(
                    "column_name"=>"comments.comment_author,comments.post_id",
                    "where"=>"comments.comment_id={$comment_id}"
                ));

                if($fetch_comment_auhtor["status"] == 1 && $fetch_comment_auhtor["num_rows"] == 1){

                    //set $comment_author value to fetched comment author
                    $comment_author = $fetch_comment_auhtor["fetch_all"][0]["comment_author"];

                    //set $comment_author value to fetched comment author
                    $post_id = $fetch_comment_auhtor["fetch_all"][0]["post_id"];
                }

                //include th comment_replies model
                $cr_obj=$this->model("comment_replies");

                $insert_cr=$cr_obj->insert(array(
                    "fields"=>array(
                        "cr_content"=>$comment_content,
                        "cr_date"=>date("d F, Y_h:i:sA"),
                        "cr_author"=>$cr_author_id,
                        "comment_id"=>$comment_id
                    )
                ));

                if($insert_cr["status"] == 1 && isset($insert_cr["insert_id"])){

                    //send a notification

                    //$to_user_id value will the $comment_author value
                    $to_user_id=$comment_author;

                    //$to_user_id value will the $comment_author value
                    $from_user_id=$cr_author_id;

                    //include the notification model
                    $nf_obj=$this->model("notification");

                    $nf_param=array(
                        "nf_obj"=>$nf_obj,
                        "nf_title"=>"
                            <strong>{$cr_author_name} replied to your comment:</strong>\r\n
                            <span>{$comment_content}</span>\r\n
                        ",
                        "from_user_id"=> $from_user_id,
                        "to_user_id"=>$to_user_id,
                        "post_id"=>$post_id
                    );

                    if( $this->functions->add_notification($nf_param)){
                        
                        echo 1;

                    }else{

                        echo 0;

                    }

                }else{

                    echo 0;
                }

            }


        }else{

            //user not logged in
            echo 10;


        }
    }

  
    //delete comment and replies
    public function delete_comment_and_replies(){

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
    
    //add post rating `like` or `dislike`
    public function post_rating(){
  
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

    //save  a post in saved list
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


    //add a subscriber into the database
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

}

?>

