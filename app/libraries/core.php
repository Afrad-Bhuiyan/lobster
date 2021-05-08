<?php

    class core{

        public function __construct(){
            
            if(!empty($_GET)){

                if(isset($_GET["url"])){
                    
                    $url=filter_var($_GET["url"],FILTER_SANITIZE_URL);
                    $url=rtrim($_GET["url"],"/");
                    $url=explode("/",$url);
                    
                    $ctrl=(isset($url[0])) ? strtolower($url[0]) : "Pages";
                    $method=(isset($url[1])) ? strtolower($url[1]) : "index";
                    $param=(isset($url[2])) ? array_slice($url,2) : "";

                    if(file_exists("app/controllers/{$ctrl}.php")){

                        include "app/controllers/{$ctrl}.php";
                        $ctrl=new $ctrl;

                        if(method_exists($ctrl,$method)){
                        
                            $ctrl->$method($param);
    
                        }else{

                            $pages_obj=$this->pages_obj();
                            $pages_obj->not_found();
                        }

                    }else{

                        $pages_obj=$this->pages_obj();

                        if(method_exists($pages_obj,$ctrl)){

                            $pages_obj->$ctrl();
                            
                        }else{

                            $pages_obj->not_found("not_found");
                        }
                    }

                }elseif(isset($_GET["url2"])){

                    $url=filter_var($_GET["url2"],FILTER_SANITIZE_URL);
                    $url=rtrim($_GET["url2"],"/");
                    $url=explode("/",$url);
                    
                    $ctrl=$url[0];
                    $username=(isset($url[1])) ? $url[1] : "";
                    $method=(isset($url[2])) ? $url[2] : "index";

                    if(!empty($username)){

                        include "app/controllers/{$ctrl}.php";
                        $ctrl_obj=new $ctrl($username,$method);

                        if(method_exists($ctrl_obj,$method)){

                            $ctrl_obj->$method($username);
                            
                        }else{

                            $pages_obj=$this->pages_obj();
                            $pages_obj->not_found();
                        }

                    }else{

                        $pages_obj=$this->pages_obj();
                        $pages_obj->not_found();
                    }
                }
            
            }else{
                
                //show the home page
                $pages_obj=$this->pages_obj();
                $pages_obj->index();

            }
        }

        public function pages_obj(){

            include "app/controllers/pages.php";
            return new pages;
        }

    }

?>