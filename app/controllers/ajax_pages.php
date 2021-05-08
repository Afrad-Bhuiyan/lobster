<?php 
/*
 * 1. pages controller's all ajax reqeust will be controlled 
 *   from this `ajax_pages` controller classs.
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_pages extends controller{
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

    //load all posts or catagory wise post
    public function load_posts(){
                
        //first validate the post variable
        $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

        $output=[];
        
        $html="";

        //store the catagory_id index from $_POST variable
        $catagory_id=$_POST["catagory_id"];

        //store the load_position index from $_POST variable
        $load_position=$_POST["load_position"];

        //include all the required model
        $post_obj=$this->model("post");

        if($load_position == "home"){

            $post_param=array(
                "column_name"=>"
                    posts.post_id, 
                    posts.post_title, 
                    posts.post_content, 
                    posts.post_link, 
                    posts.post_read, 
                    posts.post_date,
                    posts.post_author, 
                    posts.post_cat,
                    posts.post_status,
                    post_files.pfile_name, 
                    post_files.pfile_ext,
                    users.user_name, 
                    user_files.ufile_name,
                    user_files.ufile_ext
                ",
                "order"=>array(
                    "column"=>"posts.post_id",
                    "type"=>"DESC"
                ),
                "join"=>array(
                    "post_files"=>"post_files.post_id = posts.post_id",
                    "users"=>"users.user_id = posts.post_author",
                    "user_files"=>"user_files.user_id = users.user_id"
                ),
                "where"=>"posts.post_status='published'"
            );

            if($catagory_id != 0){

                $post_param["where"]="posts.post_cat={$catagory_id} AND posts.post_status='published'";
            }
            
            $fetch_posts=$post_obj->select($post_param);

            if($fetch_posts["status"] == 1 && $fetch_posts["num_rows"] > 0){

                $html .= "
                    <div class='row main-wrap__row main_wrap__row--post'>  
                ";

                foreach($fetch_posts["fetch_all"] as $post_index=>$post){

                    $post_date=str_replace(", ", "-", $post['post_date']);
                    $post_date=str_replace(" ","-",$post_date);
                    $post_date_formated=str_replace("_"," ",$post_date);

                    $html .= "
                        <div class='col-12 col-md-6 col-lg-4 post'>
                            <div class='post__wrap'>
                                <div class='post__thumb'>
                                    <a href='{$this->config->domain("posts?v={$post['post_link']}")}'>
                                        <img src='{$this->config->domain("app/uploads/posts/{$post['pfile_name']}-md.{$post['pfile_ext']}")}' alt='{$post['post_title']}'>
                                    </a>
                                </div>

                                <div class='post__info'>
                                    <h4 class='post__title'>
                                        <a href='{$this->config->domain("posts?v={$post['post_link']}")}'>

                                            {$this->functions->short_str_word($post['post_title'], 15)}
                                        </a>
                                    </h4>
                                    <div class='post__meta'>
                                        <span class='post__view'>{$post['post_read']} read</span>
                                        <i class='fa fa-circle post__icon post__icon--circle'></i>
                                        <span class='post__time'>{$this->functions->get_time_in_ago($post_date_formated)}</span>
                                    </div>
                                    <div class='post__author'>  
                                        <img src='{$this->config->domain("app/uploads/users/{$post['ufile_name']}-md.{$post['ufile_ext']}")}' alt='{$post['user_name']}'s profile picture on lobster'>
                                        <a href='{$this->config->domain("users/{$post['user_name']}")}'>{$post['user_name']}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ";
                }

                $html .= "
                    </div>
                ";

                $output["error"] = 0;

                $output["html"] = $html;

            }else{
                
                $output["error"] = 1;

                $html .= "
                     <div class='row main-wrap__row main-wrap__row--msg main-wrap__row--notfound'>
                        <div class='col-12'>
                            <p>
                                <i class='fa fa-exclamation-circle'></i>
                                <span>No posts found</span>
                            </p>
                        </div>
                     </div>  
                ";

                $output["html"] = $html;

            }

        }else if($load_position == "sidebar"){

            //posts will be on sidebar in single blog page

            //store the post link when posts have to be loaded in sidebar
            $post_link = $_POST["post_link"];

            $post_param=array(
                "column_name"=>"
                    posts.post_id,
                    posts.post_title,
                    posts.post_content,
                    posts.post_link,
                    posts.post_read,
                    posts.post_date,
                    posts.post_author,
                    posts.post_status,
                    post_files.pfile_name,
                    post_files.pfile_ext,
                    users.user_name
                ",
                "join"=>array(
                    "users"=>"posts.post_author = users.user_id",
                    "post_files"=>"posts.post_id = post_files.post_id"
                ),
            );

            if($catagory_id == 0){

                $post_param["where"]="NOT posts.post_link='{$post_link}' AND posts.post_status='published'";
                
            }else{
        
                $post_param["where"]="NOT posts.post_link='{$post_link}' AND posts.post_cat={$catagory_id} AND posts.post_status='published'";
            }
            

            $fetch_posts=$post_obj->select($post_param);

            if($fetch_posts["status"] == 1 && $fetch_posts["num_rows"] > 0){

                foreach($fetch_posts["fetch_all"] as $post_index=>$post){

                    $post_date=str_replace(", ", "-", $post['post_date']);
                    $post_date=str_replace(" ","-",$post_date);
                    $post_date_formated=str_replace("_"," ",$post_date);

                    $html .="
                        <div class='sidebar-lg__sp'>
                            <a class='sidebar-lg__sp-thumb' href='{$this->config->domain("posts?v={$post['post_link']}")}'>
                                <img  src='{$this->config->domain("app/uploads/posts/{$post['pfile_name']}-sm.{$post['pfile_ext']}")}' alt='{$post['post_title']}'>
                            </a>
                            
                            <div class='sidebar-lg__sp-content'>
                                <h6 class='sidebar-lg__sp-title'>
                                    <a href='{$this->config->domain("posts?v={$post['post_link']}")}'>
                                        {$this->functions->short_str_word($post['post_title'], 6)}
                                    </a>    
                                </h6>
                                <a class='sidebar-lg__sp-username' href='{$this->config->domain("users/{$post['user_name']}")}'>{$post['user_name']}</a>
                                
                                <div class='sidebar-lg__sp-meta'>
                                    <span class='sidebar-lg__sp-meta-item sidebar-lg__sp-meta-item--read'>{$post['post_read']} read</span>
                                    <span class='sidebar-lg__sp-meta-item sidebar-lg__sp-meta-item--time'>{$this->functions->get_time_in_ago($post_date_formated)}</span>
                                </div>
                            </div>
                        </div>
                    ";
                }

                $output["error"]=0;

                $output["html"]=$html;
                

            }else{

                $html .= "
                    <div class='sidebar-lg__msg sidebar-lg__sp--notfound'>
                        <div class='sidebar-lg__msg-body'>
                            <p>Not found any posts</p>
                        </div>
                    </div>
                ";

                $output["error"]=1;

                $output["html"]=$html;
            }

        }

        
        echo json_encode($output);

    }

    //load all the notification when clicks on notification bell button
    public function load_notifications(){

        //first validate the $_POST variable
        $_POST=filter_var_array($_POST, FILTER_SANITIZE_STRING);

        //Store the final output
        $output="";

        //store the to_user_id index from $_POST variable
        $to_user_id=$_POST["to_user_id"];
        
        //Here we will store all the notifications which will be fetched based on $to_user_id
        $notifications=[];

        //include the notification model
        $nf_obj=$this->model("notification");

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
                <div class='ph-nav__dropdown-body ph-nav__dropdown-body--nf'>
            ";


            foreach($notifications["notifications"] as $notifications_index=>$notification):
                
                $from_user_info=$notification["from_user_info"];
                $post_info=$notification["post_info"];

                $nf_date=str_replace(", ", "-", $notification['nf_date']);
                $nf_date=str_replace(" ","-",$nf_date);
                $nf_date_formated=str_replace("_"," ",$nf_date);

                $output .="
                    <a class='ph-nav__dropdown-snf' href='{$this->config->domain("posts?v={$post_info['post_link']}")}' target='_blank'>
                        <img class='ph-nav__dropdown-img ph-nav__dropdown-img--userimg' src='{$this->config->domain("app/uploads/users/{$from_user_info['ufile_name']}-sm.{$from_user_info['ufile_ext']}")}' alt=\"{$from_user_info['user_name']}'s profile picture on lobster\">
                        <div class='ph-nav__dropdown-snf-text'>
                            <h6 class='ph-nav__dropdown-snf-title'>
                                {$notification['nf_title']}
                            </h6>
                            <span class='ph-nav__dropdown-snf-time'>{$this->functions->get_time_in_ago($nf_date_formated)}</span>
                        </div>
                        <img class='ph-nav__dropdown-img ph-nav__dropdown-img--postimg' src='{$this->config->domain("app/uploads/posts/{$post_info['pfile_name']}-sm.{$post_info['pfile_ext']}")}' alt='{$post_info['post_title']}'>
                    </a>
                ";

            endforeach;
            
            $output .="
                </div>
            ";
            
            if($notifications["existing_notification"] > 0){

                $output .= "
                    <div class='ph-nav__dropdown-footer'>
                        <a class='ph-nav__link ph-nav__link--dfl' href='{$this->config->domain("users/{$_SESSION['user_name']}/notifications")}' target='_blank'>View more({$notifications["existing_notification"]})</a>
                    </div>
                
                ";
            }


        }else{

            $output .="
                <div class='ph-nav__dropdown-body ph-nav__dropdown-body--nf'>
                    <div class='ph-nav__dropdown-msg ph-nav__dropdown-msg--notfound'>
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

    //update the title tag and notification badge when clicks on notification bell button
    public function update_title_tag_and_bell(){

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
        
       //include the notification model
       $nf_obj=$this->model("notification");
       
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
                   <button class='ph-nav__btn ph-nav__btn--bell' type='button'>
                       <i class='fa fa-bell ph-nav__btn-icon'></i>
                       <span class='ph-nav__btn-badge ph-nav__btn-badg--nf'>{$nf_badge_num}</span>
                   </button>
               ";

               $output["title_tag"]="({$nf_obj_output['num_rows']}) $title_tag";
           

           }else{

               $output["bell_icon"]="
                   <button class='ph-nav__btn ph-nav__btn--bell' type='button'>
                       <i class='fa fa-bell ph-nav__btn-icon'></i>
                   </button>
               ";

               $output["title_tag"]="$title_tag";
           }

           echo json_encode($output);
           
       }else{

           echo $nf_obj_output["error"];
       }

    }


 
}

?>

