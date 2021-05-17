<?php 

class functions{

    //load css  & js scripts dynamically
    public function load_scripts(array $param)
    {

        foreach($param["request_uri"] as $index=>$request_uri){

            if("/lobster{$request_uri}" == $_SERVER["REQUEST_URI"]){

                //Generating CSS file
                if($param["script_type"] == "css"){

                    if(isset($param["comment"])){

                        return "
                            <!--{$param['comment']}-->\r\n
                            <link rel='stylesheet' href='{$param["script_url"]}' type='text/css' media='all'>\r\n
                        ";
                        
                    }else{
                        
                        return "
                            <link rel='stylesheet' href='{$param["script_url"]}' type='text/css' media='all'>\r\n
                        ";
                    }
                }
                
                //Generating Js file
                if($param["script_type"] == "js"){

                    if(isset($param["comment"])){
        
                        return "
                            <!--{$param['comment']}-->\r\n
                            <script src='{$param["script_url"]}'></script>\r\n
                        ";
        
                    }else{
        
                        return "
                            <script src='{$param["script_url"]}'></script>\r\n
                        ";
                    }                
                }
            }
        }
        
    }//load_scripts

    //Send mail
    public function send_mail($mail, array $param)
    {

        // Set mailer to use SMTP
        $mail->isSMTP();  

        // Specify main and backup SMTP servers                
        $mail->Host = "smtp.gmail.com";  

        //Enable SMTP authentication
        $mail->SMTPAuth = true;   

        //SMTP username
        $mail->Username = "getwebsquad@gmail.com"; 

        //SMTP password
        $mail->Password = "01778414604-Gmail"; 
         
        //Enable TLS encryption, `ssl` also accepted
        $mail->SMTPSecure = "tls"; 

        // TCP port to connect to                          
        $mail->Port = 587;     
        
        //set from E-mail Address
        $mail->setFrom("getwebsquad@gmail.com", "LOBSTER");

        //First clear all Recipients E-mail Address
        $mail->clearAllRecipients();

        //add receiver E-mail Address
        if(isset($param['receiver_name'])){

            $mail->addAddress($param['receiver'], $param['receiver_name']);

        }else{

            $mail->addAddress($param['receiver']);
        }

        //Add a reply to E-mail Address
        $mail->addReplyTo("noreply@getwebsquad.com","No Reply");

        //Set email format to HTML
        $mail->isHTML(true);                                
        
        //Add E-mail Subject
        $mail->Subject = $param['subject'];

        //Add E-mail Body
        $mail->Body    = $param["body"];

        //Add E-mail alt body
        $mail->AltBody = $param["alt_body"];


        //add attachments
        if(isset($param["attachment"])){

            $mail->addAttachment($param["attachment"]);        
        }
        
         //Finally Send the email address
        if(!$mail->send()) {
    
            return false;
        
        } else {
    
            return true;
        }

    }//sendmail()

    //Cut out words from a string
    public function short_str_word($str,$limit)
    {
        
        // return implode(" ",$str_short);
        $output = "";
    
        $str_array=explode(" ",$str);

        $str_length=count($str_array);

        if($str_length > $limit){

            $str_short=array_slice($str_array,0,$limit);
            
            $output=implode(" ",$str_short). "...";

        }else{

            $output = $str;
        }

        return $output;


    }//short_str_word

    //genrate random string
    public function generate_random_str($param)
    {

        $string="abcdefghijklmnopqrstuvwxyz$-_ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $generate_string=substr(str_shuffle($string),0,$param["length"]);

        $model_obj=$param["model_obj"];
    
        $output=$model_obj->select(array(
            "where"=>"{$param['column']}='{$generate_string}'"
        ));

        $num_rows=$output["num_rows"];

        if($output["status"] == 1){

            while($num_rows > 0){

                $generate_string=substr(str_shuffle($string),0,$param["length"]);
                $output=$model_obj->select(array(
                    "table_name"=>"{$param['table_name']}",
                    "where"=>"{$param['column']}='{$generate_string}'"
                ));

                $num_rows=$output["num_rows"];
            }

            return filter_var($generate_string,FILTER_SANITIZE_URL);
           
        
        }else{

            echo $output["error"];
            die();

        }
    }//generate_random_str

    //Get any time in ago format
    public function get_time_in_ago($past_date_and_time)
    {

        date_default_timezone_set("Asia/Dhaka");

        $pass_date_time_stmp=strtotime($past_date_and_time);
        $current_timestmp=strtotime(date("d-F-Y h:i:sA"));

        $diff_time=$current_timestmp-$pass_date_time_stmp;
       
        
        //check if the previous year is leap year
        $is_prev_year_leap_year=date("L", mktime(0,0,0,2,0,date("Y")-1));

        $days_in_prev_year = ($is_prev_year_leap_year) ? 366 : 365;

        $days_in_prev_month=date("t", mktime(0,0,0, date("n") - 1));
     
        
        /**
         * 1 min = 60 sec
         * 1 hours= 60 min / 3600sec
         * 1 day = 24 hours / 86400sec
         * 1 week = 7 days / (86400sec * 7)==6,04,800
         * 1 month = 30 day / (86400sec * 30)==25,9200sec
         * 1 year = 365 day / (86400sec * 365)==3,15,36000sec
         */

        $one_minute_in_sec=60;
        $one_hour_in_sec=3600;
        $one_day_in_sec=86400;
        $one_week_in_sec=($one_day_in_sec * 7);
        $one_month_in_sec=($one_day_in_sec *  $days_in_prev_month);
        $one_year_in_sec=($one_day_in_sec * $days_in_prev_year);
     

        if($diff_time < $one_minute_in_sec){

            return "few seconds ago <br>";

        }elseif($diff_time >= $one_minute_in_sec && $diff_time < $one_hour_in_sec){
        

            if($diff_time < ($one_minute_in_sec * 2)){
                
                //1 minute ago
                return floor($diff_time/$one_minute_in_sec) . " minute ago";
                
            }else{

                //plural minutes ago
                return floor($diff_time/$one_minute_in_sec) . " minutes ago";

            }
            
        }elseif($diff_time >= $one_hour_in_sec && $diff_time < $one_day_in_sec){

            if($diff_time < ($one_hour_in_sec * 2)){
                
                //1 hour ago
                return floor($diff_time/$one_hour_in_sec) . " hour ago";
                
            }else{

                //plural hours ago
                return floor($diff_time/$one_hour_in_sec) . " hours ago";

            }

        }elseif($diff_time >= $one_day_in_sec && $diff_time < $one_week_in_sec){
            
            if($diff_time < ($one_day_in_sec * 2)){
                
                //1 day ago
                return floor($diff_time/$one_day_in_sec) . " day ago";
                
            }else{

                //plural days ago
                return floor($diff_time/$one_day_in_sec) . " days ago";

            }

        }elseif($diff_time >= $one_week_in_sec && $diff_time < $one_month_in_sec){

            if($diff_time < ($one_week_in_sec * 2)){
                
                //1 week ago
                return floor($diff_time/$one_week_in_sec) . " week ago";
                
            }else{

                //plural weeks ago
                return floor($diff_time/$one_week_in_sec) . " weeks ago";

            }
            
        }elseif($diff_time >= $one_month_in_sec && $diff_time < $one_year_in_sec){

            if($diff_time < ($one_month_in_sec * 2)){
                
                //1 month ago
                return floor($diff_time/$one_month_in_sec) . " month ago";
                
            }else{

                //plural months ago
                return floor($diff_time/$one_month_in_sec) . " months ago";
                
            }
            

        }elseif($diff_time >= $one_year_in_sec){

            if($diff_time < ($one_year_in_sec * 2)){
                
                //1 year ago
                return floor($diff_time/$one_year_in_sec) . " year ago";
                
            }else{

                //plural years ago
                return floor($diff_time/$one_year_in_sec) . " years ago";
                
            }
        }
    
    }


    //Get numbers in short form such as 1000=1K 1000000=1M
    public function number_format_short($n, $precision = 1)
    {

        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';

        } else if ($n < 900000) {

            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';

        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';

        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';

        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ( $precision > 0 ) {
            $dotzero = '.' . str_repeat( '0', $precision );
            $n_format = str_replace( $dotzero, '', $n_format );
        }

        return $n_format . $suffix;
    }

    //Check if user logged in or not
    public function if_user_logged_in()
    {

        if(!empty($_SESSION)){

            if(isset($_SESSION["user_id"]) && isset($_SESSION["user_type"])){
                
                session_abort();
                return true;
                
            }else{

                session_abort();
                return false;   
            }

        }else{

            session_abort();
            return false;
        }

        
    }

    //Resize and upload any image
    public function resize_upload_img($param)
    {
        /*
            $param variable=array(
                "width"=>100,
                "height"=>100,
                "img_url"=>$_FILES["filename"]["tmp_name"][0],
                "img_upload_location"=>""
            )

         */

        //images tmp_name
        $img_name=$param["img_url"];

        //Get all the information about the Image
        $img_info=getimagesize($param["img_url"]);

        //Image's Original width & height
        $original_width=$img_info[0];
        $original_height=$img_info[1];
        
        // width & height given while calling the function
        $resized_width=$param["width"];
        $resized_height=$param["height"];
        
        //Get the location where the img has to be uploaded
        $img_upload_location=$param["img_upload_location"];

        //First create a blank black image
        $blank_img=imagecreatetruecolor($resized_width,$resized_height);
    
        if($img_info["mime"] == "image/jpeg"){

            $src_img=imagecreatefromjpeg($img_name);

        }elseif($img_info["mime"] == "image/png"){

            $src_img=imagecreatefrompng($img_name);
        }

        imagecopyresized($blank_img, $src_img, 0,0,0,0, $resized_width, $resized_height, $original_width, $original_height);
        
        if($img_info["mime"] == "image/jpeg"){

            return imagejpeg($blank_img, $img_upload_location, 90);

        }elseif($img_info["mime"] == "image/png"){

            return imagepng($blank_img, $img_upload_location, 90);

        }
    }

    public function check_user_verified_email(){
        
        echo "Hellow";
        
    }

     //Redirecting not found page
    public function not_found()
    {
        
        include "app/controllers/pages.php";
        $page_obj=new pages;
        $page_obj->not_found();
    }//not_found


    //use the function to get `error_pages` controller's object
    public function error_pages(){

        //include the `error_pages` controller
        include "app/controllers/error_pages.php";

        //create the object of `error_pages`
        $error_pages_obj=new error_pages;

        //Return the output
        return $error_pages_obj;

    }
}


?>
