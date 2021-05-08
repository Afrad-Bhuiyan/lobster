<?php 

    class controller{

        public function view($view_name,$data=null){
            
            include "app/views/{$view_name}.php";
        }
        
        public function model($model_name){

            include "app/models/{$model_name}.php";
            return new $model_name;
        }
    }

?>