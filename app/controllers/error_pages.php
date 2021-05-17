<?php 
  
    class error_pages extends controller{

        public function __construct()
        { 


        }

        public function error_404()
        {

          $this->view("pages/error_404");
        }
       
    }


    
?>