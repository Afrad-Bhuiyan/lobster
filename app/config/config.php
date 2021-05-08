<?php 


    class config{

        public function db_info($param){

            /**
             * $condition==true (for live server)
             * $condition==false (for local server)
             */

            $condition=($_SERVER["SERVER_NAME"]=="localhost" || $_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"]) ? false : true;


            if($param == "db_host"){

                return ($condition) ? "localhost" : "localhost";
            }

            if($param == "db_user"){

                return ($condition) ? "afrad_bhuiyan" : "root";
            }

            if($param == "db_pass"){

                return ($condition) ? "01778414604-db" : "";
            }

            if($param == "db_name"){

                return ($condition) ? "afrad_lobster" : "lobster";
            }
            
        }
        
        public function domain($addtional_path=""){

            //if someone visits by writing localhost
            $condition1= $_SERVER['SERVER_NAME']=="localhost";

            //if someone visits by writing ip address like:192.168.1.42
            $condition2=$_SERVER['SERVER_NAME'] == $_SERVER['SERVER_ADDR']; 

            if($condition1 || $condition2){

                /**
                 * Write local server's URL Information
                 * 
                 * If your project exists directly on your domain/sub-domain 
                 * then @param folder_name=""
                 * OR
                 * If your project exists inside into a sub folder of your domain/sub-domain
                 * then @param folder_name="sub-folder/"
                 */

                $folder_name="lobster/";
                
            }else{
                /**
                 * Write live server's URL Information
                 * 
                 * If your project exists directly on your domain/sub-domain 
                 * then @param folder_name=""
                 * OR
                 * If your project exists inside into a sub folder of your domain/sub-domain
                 * then @param folder_name="sub-folder/"
                 */

                
                $folder_name="lobster/";

            }
            
            return $domain="{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}/{$folder_name}{$addtional_path}";

        }
    }

?>