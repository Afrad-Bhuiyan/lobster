<?php 

/*
 * 1. The class `ajax_users _posts` will be used to store all the function and variable
 *    fo dasboards post options
 * 
 * 2. Only POST Reqeust will be acceptable.
 */

class ajax_users_settings{

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


        //use the function to check $password  matchs with the db password
        private function check_pass_match_with_current($password)
        {

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

    /**
     * =========================
     * All Public functions ends
     * =========================
     */
  
}



?>
