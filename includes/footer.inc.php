
     <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <?php 
       
       $data_type="";

       if($_SERVER["REQUEST_URI"] == "/lobster/" || $_SERVER["REQUEST_URI"] == "/lobster"){

            $data_type="home";

       }elseif(isset($_GET["url1"]) && $_GET["url1"] == "posts"){
           
            $data_type="single_post";
            
        }elseif(isset($_GET["url1"]) && $_GET["url1"] == "accounts/login"){
            
            $data_type="login";
            
        }elseif(isset($_GET["url1"]) && $_GET["url1"] == "accounts/signup"){
            
            $data_type="signup";

       }elseif(isset($_GET["url1"]) && $_GET["url1"] == "accounts/forgot_password"){

            $data_type="forgot_password";


       }elseif(isset($_GET["url1"]) && $_GET["url1"] == "accounts/reset_password"){

            $data_type="reset_password";

       }elseif(isset($_GET["url2"])){

            $data_type="profile_page";

            echo "
                <!--Owl Carousel js-->
                <script src='{$config->domain("assets/js/owl.carousel.min.js")}'></script>\r\n
            ";
       }

    ?>
    
    <script id="main-js" src="<?php echo $config->domain("assets/js/main.js") ?>" data-type="<?php echo $data_type ?>"></script>
</body>
</html>