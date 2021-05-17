<?php 

/*
 * 1. The class `ajax_users _posts` will be used to store all the function and variable
 *    fo dasboards post options
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_users_admin_options{

        //store the config class object
        private $config;
            
        //store the functions class object
        private $functions;
        
        //store the PHPMailer class object
        private $mail;
        
        //Here we will store all the required model's object
        private $model_objs=array();

        //store logged user's information
        private $user_info=array();

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


        //use the function to fetch a single user's information
        private function fetch_user_info($user_id)
        {

            $output = array();

            //store the user's model from $this->model_objs variable
            $user_obj=$this->model_objs["user_obj"];

            //fetch user
            $fetch_user=$user_obj->select(array(
                "column_name"=>"
                    users.user_fname,
                    users.user_lname,
                    users.user_email,
                    users.user_name
                ",
                "where"=>"users.user_id={$user_id}"
            ));

            if($fetch_user["status"] == 1 && $fetch_user["num_rows"] == 1){
                
                $output=$fetch_user["fetch_all"][0];
            }

            
            return $output;
        }
    

        //use the function for fetching  total posts of a user
        private function fetch_total_posts($user_id)
        {

            //store the final ouput
            $output=null;
            
            //store the posts's model objec from $this->model_objs
            $post_obj=$this->model_objs["post_obj"];

            //fetch all posts of a single user
            $fetch_post=$post_obj->select(array(    
                "column_name"=>"COUNT(*) AS total_posts",
                "where"=>"posts.post_author={$user_id}"
            ));
            
            if($fetch_post["status"] == 1){

                $output=$fetch_post["fetch_all"][0]["total_posts"];

            }

            //Return the final output
            return $output;
        

        }

        //user the function just for printing the user's table
        private function print_all_user_table($all_users)
        {

        
            $output = "
                <div class='user-list__tbl-wrap'>
                    <table class='user-list__tbl'>
                        <thead class='user-list__thead'>
                            <tr class='user-list__tr user-list__tr--thead'>
                                <th class='user-list__th user-list__th--id'>
                                    <div class='user-list__th-wrap'>
                                        <span class='user-list__txt user-list__txt--th'>
                                            ID
                                        </span>
                                    </div>
                                </th>

                                <th class='user-list__th user-list__th--uname'>
                                    <div class='user-list__th-wrap'>
                                        <span class='user-list__txt user-list__txt--th'>
                                            Username
                                        </span>
                                    </div>
                                </th>

                                <th class='user-list__th user-list__th--role'>
                                    <div class='user-list__th-wrap'>
                                        <span class='user-list__txt user-list__txt--th'>
                                            Role
                                        </span>
                                    </div>
                                </th>

                                <th class='user-list__th user-list__th--email'>
                                    <div class='user-list__th-wrap'>
                                        <span class='user-list__txt user-list__txt--th'>
                                            E-mail
                                        </span>
                                    </div>
                                </th>

                                <th class='user-list__th user-list__th--jd'>
                                    <div class='user-list__th-wrap'>
                                        <span class='user-list__txt user-list__txt--th'>
                                            Joining Date
                                        </span>
                                    </div>
                                </th>

                                <th class='user-list__th user-list__th--tp'>
                                    <div class='user-list__th-wrap'>
                                        <span class='user-list__txt user-list__txt--th'>
                                            Total Posts
                                        </span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class='user-list__tbody'>
            ";

            foreach($all_users as $index=>$single_user):

                $profile_img=$single_user["ufile_info"]["profile_img"];

                $admin_input_attr = ($single_user["user_role"] == "admin") ? "checked" : ""; 

                $admin_input_class = ($single_user["user_role"] == "admin") ? "user-list__radio--checked" : "";

                $creator_input_attr = ($single_user["user_role"] == "creator") ? "checked" : "";

                $creator_input_class = ($single_user["user_role"] == "creator") ? "user-list__radio--checked" : "";
    
                $output .= "
                        <tr class='user-list__tr user-list__tr--tbody'>
                            <td class='user-list__td user-list__td--id'>
                                <div class='user-list__td-wrap'>
                                    <span class='user-list__txt user-list__txt--td'>
                                        {$single_user['user_id']}
                                    </span>
                                </div>
                            </td>

                            <td class='user-list__td user-list__td--uname'>
                                <div class='user-list__td-wrap'>
                                    <img class='user-list__img user-list__img--profile' src='{$this->config->domain("app/uploads/users/profile/{$profile_img['name']}-sm.{$profile_img['ext']}")}' alt=''>
                                    <a class='user-list__link user-list__link--uname' href='{$this->config->domain("users/{$single_user['user_name']}")}' target='_blank'>
                                        {$single_user['user_name']}
                                    </a>
                                    <span class='user-list__badge user-list__badge--{$single_user['user_role']}'>
                                        {$single_user['user_role']}
                                    </span>
                                </div>
                            </td>

                            <td class='user-list__td user-list__td--role'>
                                <div class='user-list__td-wrap'>
                                    <form class='user-list__form user-list__form--role user-list__form--{$single_user['user_id']}'>
                                        <input class='user-list__input user-list__input--hidden' type='hidden' name='user_id' value='{$single_user['user_id']}'/>

                                        <div class='user-list__opt'>
                                            <input class='user-list__radio user-list__radio--admin {$admin_input_class}' type='radio' name='user_role' {$admin_input_attr} value='admin'>
                                            <span class='user-list__radio-circle'></span>
                                            <span class='user-list__label'>
                                                Admin
                                            </span>
                                        </div>

                                        <div class='user-list__opt'>
                                            <input class='user-list__radio user-list__radio--creator {$creator_input_class}' type='radio' name='user_role' {$creator_input_attr} value='creator'>
                                            <span class='user-list__radio-circle'></span>
                                            <span class='user-list__label'>
                                                Creator
                                            </span>
                                        </div>  
                                    </form>                                             
                                </div>
                            </td>
                            
                            <td class='user-list__td user-list__td--email'>
                                <div class='user-list__td-wrap'>
                                    <span class='user-list__txt user-list__txt--td'>
                                        {$single_user['user_email']}
                                    </span>
                                </div>
                            </td>

                            <td class='user-list__td user-list__td--jd'>
                                <div class='user-list__td-wrap'>
                                    <span class='user-list__txt user-list__txt--td'>
                                        {$single_user['user_joining_date']}
                                    </span>
                                </div>
                            </td>

                            <td class='user-list__td user-list__td--tp'>
                                <div class='user-list__td-wrap'>
                                    <span class='user-list__txt user-list__txt--td'>
                                        {$single_user['total_posts']}
                                    </span>
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

        //use the function to load all 
        public function load_all_users()
        {

            //validate the $_POST variable
            $_POST = filter_var_array($_POST,FILTER_SANITIZE_STRING);

            //store the final output
            $output="";

            //store the user's model objec from $this->model_objs
            $user_obj=$this->model_objs["user_obj"];

            //store the `load_type` index from $_POST variable
            $load_type=isset($_POST["load_type"]) ? $_POST["load_type"] : null;

            $all_users=array();

          

            //default value for total user is `0`
            $total_users = 0;

            //fetch total number of users
            $fetch_total_user_no=$user_obj->select();

            if($fetch_total_user_no["status"] == 1 && $fetch_total_user_no["num_rows"] > 0){

                //override the $total_users value 
                $total_users = $fetch_total_user_no["num_rows"];
            }
                

            //store the `page_no` index from $_POST variable
            $page_no= $_POST["page_no"];
            
            //limit to show user per page
            $limit=20;
            
            //calculate the offset using $page_no and $limit
            $offset= ($page_no - 1) * $limit;
            
            //calculate total number of pages
            $total_pages=ceil($total_users / $limit);
        
            if($page_no > $total_pages || $page_no == 0){
                
                //throw the error message if user tried to set $page_no variable from URL
                $output  .= "
                    <div>
                        <h5>Page doesn't exist</h5>
                    </div>
                ";

            }else{

                //fetch all users from users table
                $fetch_all_users=$user_obj->select(array(
                    "column_name"=>"
                        users.user_id,
                        users.user_name,
                        users.user_email,
                        users.user_role,
                        users.user_joining_date
                    ",
                    "order"=>array(
                        "column"=>"users.user_id",
                        "type"=>"DESC"
                    ),
                    "limit"=>"{$offset}, {$limit}"
                ));
                
                if($fetch_all_users["status"] == 1 && $fetch_all_users["num_rows"] > 0){

                    $text =  ($offset + 1)."-".($offset + $fetch_all_users["num_rows"])." out of {$total_users}";
                    
                    $output .= "
                        <div class='user-list__result'>
                            <p class='user-list__result-txt'>
                                {$text}
                            </p>
                        </div>
                    ";

                    //set $all_user value to fetched information
                    $all_users=$fetch_all_users["fetch_all"];

                    //Run a lop for fetching total posts and user_files
                    foreach($all_users as $index=>$single_user){ 

                        //fetch total posts using a private functions `fetch_total_posts`
                        $all_users[$index]['total_posts']=$this->fetch_total_posts($single_user["user_id"]);
                        
                        //fetch user files using a private functions `fetch_user_file`
                        $all_users[$index]['ufile_info']["profile_img"]=$this->fetch_user_files($single_user["user_id"], "profile_img");
                    }
                }

                $output .= $this->print_all_user_table($all_users);

                if($total_users > $limit){

                    $output .= "
                        <div class='user-list__pagination'>
                            <ul class='user-list__page-list'>
                        
                    ";

                        if($page_no > 1){

                            $prev_page_no=($page_no - 1);

                            $output .= "
                                <li class='user-list__page-item'>
                                    <a class='user-list__page-link' href='{$this->config->domain("users/{$this->user_info['user_name']}/dashboard?admin_options=users&page_no={$prev_page_no}")}'>
                                        <i class='fa fa-angle-left'></i>
                                    </a>
                                </li>
                            ";
                        }

                        for($i=1; $i <= $total_pages; $i++){

                            $link_active = ($page_no == $i) ? "user-list__page-link--active" : "";
        
                            $output .= "
                                <li class='user-list__page-item'>
                                    <a class='user-list__page-link {$link_active}' href='{$this->config->domain("users/{$this->user_info['user_name']}/dashboard?admin_options=users&page_no={$i}")}'>
                                        {$i}
                                    </a>
                                </li>
                            ";
                        }
                            

                        if($page_no < $total_pages){

                            $next_page_no=($page_no + 1);

                            $output .= "
                                <li class='user-list__page-item'>
                                    <a class='user-list__page-link' href='{$this->config->domain("users/{$this->user_info['user_name']}/dashboard?admin_options=users&page_no={$next_page_no}")}'>
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
            }

            echo $output;

        }
     

        //use the function to search from the all users
        public function search_user()
        {

            //validate the $_POST variable
            $_POST = filter_var_array($_POST,FILTER_SANITIZE_STRING);

            //store the final ouptput
            $output = "";

            //store the `search_query` index from $_POST variable
            $search_query=$_POST["search_query"];

            //store the user's model objec from $this->model_objs
            $user_obj=$this->model_objs["user_obj"];

            //store all the fetch results
            $all_results=array();

            //fetch result using $user_obj
            $fetch_result=$user_obj->select(array(
                "column_name"=>"
                    users.user_id,
                    users.user_name,
                    users.user_email,
                    users.user_role,
                    users.user_joining_date
                ",
                "order"=>array(
                    "column"=>"users.user_id",
                    "type"=>"DESC"
                ),
                "where"=>"users.user_name LIKE '%{$search_query}%' OR users.user_email LIKE '%{$search_query}%' OR users.user_role LIKE '%{$search_query}%'"
            ));

            if($fetch_result["status"] == 1 && $fetch_result["num_rows"] > 0){

                //set $all_results to fetched value
                $all_results = $fetch_result["fetch_all"];
    
                //Run a lop for fetching total posts and user_files
                foreach($all_results as $index=>$result){ 

                    //fetch total posts using a private functions `fetch_total_posts`
                    $all_results[$index]['total_posts']=$this->fetch_total_posts($result["user_id"]);
                    
                    //fetch user files using a private functions `fetch_user_file`
                    $all_results[$index]['ufile_info']["profile_img"]=$this->fetch_user_files($result["user_id"], "profile_img");
                }

                $output .= $this->print_all_user_table($all_results);

                
            }else{

                $output .= "
                    <div>
                        <h5>No results found</h5>
                    </div>
                ";

            }

            echo $output;

        }
        

        //user the function to change the user role
        public function change_the_user_role()
        {

            //validate the $_POST variable
            $_POST = filter_var_array($_POST,FILTER_SANITIZE_STRING);

            //store the `user_id` index from $_POST variable
            $user_id=$_POST["user_id"];
            
            //fetch user information
            $user_info=$this->fetch_user_info($user_id);

            //store the `user_role` index from $_POST variable
            $user_role=$_POST["user_role"];

            $output = array();

             //store the user's model objec from $this->model_objs
             $user_obj=$this->model_objs["user_obj"];

            
             $update_user_role=$user_obj->update(array(
                 "fields"=>array(
                     "user_role"=>"{$user_role}"
                 ),
                 "where"=>"users.user_id={$user_id}"
            ));

            if($update_user_role["status"] == 1 && $update_user_role["affected_rows"] == 1){


                if($user_role == "admin"){

                    $mail_msg = "
                        <div>
                            <p>
                                Dear <b>{$user_info['user_fname']} {$user_info['user_lname']}</b>, <br>

                                I am here to notify you that your role on <a href='{$this->config->domain()}' target='_blank'>lobster</a> was updated from creator to admin. 
                                Now , you are considered as an admin of <a href='{$this->config->domain()}' target='_blank'>lobster</a>.  
                                As an admin you can view  all the user lists, catagories, total analytics  etc.
                                <br>
                                <br>
                                I hope, being an admin, you’ll never misuse your power and follow our terms & condition.
                                <br>
                                Sincerely
                                <br>
                                <b>{$this->user_info['user_fname']} {$this->user_info['user_lname']}</b> (An admin of <a href='{$this->config->domain()}' target='_blank'>lobster</a>)
                            </p>
                        </div> 
                    ";

                }elseif($user_role == "creator"){

                    $mail_msg = "
                        <div>
                            <p>
                                Dear <b>{$user_info['user_fname']} {$user_info['user_lname']}</b>, <br>

                                I am here to notify you that your role on <a href='{$this->config->domain()}' target='_blank'>lobster</a>  was updated from admin to creator. 
                                Now , you are considered as a creator of <a href='{$this->config->domain()}' target='_blank'>lobster</a>.  
                                As a creator you can view all common options just like a normal user can.
                                <br>
                                <br>
                                I hope, being a creator, you’ll follow our <a href='#' target='_blank'>terms</a> & <a href='#' target='_blank'>condition</a>.
                                <br>
                                Sincerely
                                <br>
                                <b>{$this->user_info['user_fname']} {$this->user_info['user_lname']}</b> (An admin of <a href='{$this->config->domain()}' target='_blank'>lobster</a>)
                            </p>
                        </div> 
                    ";
                }
            
                //After updating the user role, let's send an email to the user
                $send_mail=$this->functions->send_mail($this->mail,array(
                    "receiver"=>"{$user_info["user_email"]}",
                    "receiver_name"=>"{$user_info["user_fname"]} {$user_info["user_fname"]}",
                    "subject"=>"Congratulation",
                    "body"=>"{$mail_msg}",
                    "alt_body"=>"{$mail_msg}"
                ));
        
                if(!$send_mail){

                    $output = array(
                        "error_status" => 100,
                    );

                }else{

                    $output = array(
                        "error_status" => 0,
                    );
                }
                
            }else{

                $output = array(
                    "error_status" => 1,
                    "errors"=>$update_user_role
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
