<?php

    class core{

        //store config class object
        private $config;
    
        //store functions class object
        private $functions;

        //store the `pages` controller object
        private $pages;
        
        public function __construct(){
            
            //set $this->config to config class object
            $this->config=new config;

            //set $this->functions to functions class object
            $this->functions=new functions;



            if(!empty($_GET)){
                
                //first validate the $_GET variable
                $_GET = filter_var_array($_GET, FILTER_SANITIZE_URL);
                
                //store the current url
                $url = "";

                if(isset($_GET["url1"])){

                    /**
                     * $_GET["url1"] will be used to call all 
                     * the controller apart form the users controller
                     */
                    $url = $_GET["url1"];
                    
                }elseif(isset($_GET["url2"])){

                    /**
                     * $_GET["url2"] will be used to call only 
                     * the`users` controller
                     */
                    $url = $_GET["url2"];
                    
                }


                //trim the extra `/` from the right side
                $url = rtrim($url,"/");

                //sperate string where `/` found and get the `ctrl`, `method`, `param` in an array  
                $url = explode("/",$url);
        

                if(isset($_GET["url1"])){

                    //store the controller(first index is the controller) form the $url
                    $ctrl=(isset($url[0])) ? strtolower($url[0]) : null;

                    //store the method(second index is the method) form the $url
                    $method=(isset($url[1])) ? strtolower($url[1]) : "index";

                    //store the parameters(third index is the parameters) form the $url
                    $param=(isset($url[2])) ? array_slice($url,2) : null;


                    if(file_exists("app/controllers/{$ctrl}.php")){

                        /**
                         * user requested to an existing controller,
                         * Now  include the controller and called the method 
                         */ 
                     
                        //include the controller
                        include "app/controllers/{$ctrl}.php";
                        
                        //create an object of the controlle
                        $ctrl = new $ctrl($method);

                        //check method exists or not in the controller
                        if(method_exists($ctrl,$method)){

                            //if exist call the method from the controller
                            $ctrl->$method($param);
    
                        }else{

                            //if method doesn't exist, show the 404 error page
                            $this->functions->error_pages()->error_404();
                        }

                    }else{

                        /**
                         * user is trying to access a method from 
                         * pages controller
                         */

                          
                        //set $this->pages to pages controller object
                        $this->pages=$this->pages();

                        //store the method from URL
                        $page_method = $ctrl;
                

                        //check method exists or not in pages controller
                        if(method_exists($this->pages,$page_method)){
                            
                            //call the method from pages controller
                            $this->pages->$page_method();
                            
                        }else{

                           //if method doesn't exist, show the 404 error page
                           $this->functions->error_pages()->error_404();
                
                        }
                    }

                }elseif(isset($_GET["url2"])){

                    //store the controller(first index is the controller) form the $url
                    $ctrl=(isset($url[0])) ? strtolower($url[0]) : null;

                    //store the method(second index is the method) form the $url
                    $user_name=(isset($url[1])) ? strtolower($url[1]) : null;

                    //store the method(second index is the method) form the $url
                    $method=(isset($url[2])) ? strtolower($url[2]) : "index";

                    //store the parameters(third index is the parameters) form the $url
                    $param=(isset($url[2])) ? array_slice($url,3) : null;


                    if(!empty($user_name)){

                        include "app/controllers/{$ctrl}.php";

                        $ctrl_obj=new $ctrl($user_name,$method);

                        if(method_exists($ctrl_obj,$method)){

                            $ctrl_obj->$method($param);
                            
                        }else{
                                    
                            //if method doesn't exist, show the 404 error page
                            $this->functions->error_pages()->error_404();
                        
                        }

                    }else{

                        //if method doesn't exist, show the 404 error page
                        $this->functions->error_pages()->error_404();
                   
                    }

                }

            }else{

                //set $this->pages to pages controller object
                $this->pages = $this->pages();
            
                //show the home page
                $this->pages->index();

            }
            
        }

        //use the function to get `pages` controller's object
        private function pages(){
            include "app/controllers/pages.php";
            return new pages;
        }
    
    }

?>