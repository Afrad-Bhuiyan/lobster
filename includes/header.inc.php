
<?php 
    $config=new config;
    $functions=new functions;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (isset($data["title_tag"])) ? $data["title_tag"] : "set your title tag" ?></title>
    <!--Google Fonts-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"> 
    <!--Font Aweseme-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type='text/css' media='all'>
    <!--Boootstrap css-->
    <link rel="stylesheet" href="<?php echo "{$config->domain('assets/css/bootstrap.min.css')}" ?>" type='text/css' media='all'>
    <?php 



        if($_SERVER["REQUEST_URI"] == "/lobster/" || $_SERVER["REQUEST_URI"] == "/lobster"){

            echo "
                <!--Style css-->
                <link rel='stylesheet' href='{$config->domain('assets/css/style.min.css')}' type='text/css' media='all'>\r\n
            ";

        }elseif(isset($_GET["v"])){
                    
            echo "
                <!--Single posts css-->
                <link rel='stylesheet' href='{$config->domain('assets/css/single_post.min.css')}' type='text/css' media='all'>\r\n
            ";

        }else if(isset($_GET["search_query"])){
            
            echo "
                <!--Result css-->
                <link rel='stylesheet' href='{$config->domain('assets/css/result.min.css')}' type='text/css' media='all'>\r\n
            ";

        }else if(isset($_GET["url1"])){

            if(
                $_GET["url1"] == "accounts/login" || 
                $_GET["url1"] == "accounts/signup"|| 
                $_GET["url1"] == "accounts/forgot_password"||
                $_GET["url1"] == "accounts/reset_password"
            ){

                echo "
                    <!--Acount css-->
                    <link rel='stylesheet' href='{$config->domain('assets/css/accounts.min.css')}' type='text/css' media='all'>\r\n
                ";
            }
        
        
        }else if(isset($_GET["url2"])){

            echo "
                <!--Owl Carousel css-->
                <link rel='stylesheet' href='{$config->domain('assets/css/owl.carousel.min.css')}' type='text/css' media='all'>\r\n
                
                <!--Owl Carousel default css-->
                <link rel='stylesheet' href='{$config->domain('assets/css/owl.theme.default.min.css')}' type='text/css' media='all'>\r\n
                
                <!--Profile css-->
                <link rel='stylesheet' href='{$config->domain('assets/css/profile.min.css')}' type='text/css' media='all'>\r\n
            ";
        }
    ?>

    <!--Jquery Js-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="<?php echo $config->domain("assets/js/jquery.autocomplete.min.js") ?>"></script>

</head>
<body>


        
        <?php


            $total_unread_nf=(isset($data["common_info"]["total_unread_nf"])) ? $data["common_info"]["total_unread_nf"] : null;

            if(
                $_SERVER["REQUEST_URI"] == "/lobster/" ||
                $_SERVER["REQUEST_URI"] == "/lobster" ||
                isset($_GET["v"]) ||
                isset($_GET["search_query"]) ||
                isset($_GET["url2"])
            ):

            /**
             * [a-zA-Z0-9.]
             */
                $user_info=(isset($data["common_info"]["user_info"])) ? $data["common_info"]["user_info"] : null;

                $nf_info=(isset($data["common_info"]["nf_info"])) ? $data["common_info"]["nf_info"] : null;
                
                $profile_img=($user_info !== null) ? $user_info["ufile_info"]["profile_img"] : null;
        
                if($user_info !== null && $user_info["user_email_status"] == "unverified"){

                    $msg_text="";
                    $msg_type=($user_info["user_type"] == "new") ? "success" : "info";

                    if($user_info["user_type"] == "new"){

                        $msg_text .= "
                            <p class='msg__text'>
                                <i class='fa fa-exclamation-circle msg__icon'></i> Your account has been created successfully. Check your inbox A verification mail has been sent to <span>{$user_info['user_email']}</span>. Didn't receive the E-mail? <a class='msg__link msg__link--resend' role='button' data-verification_email='resend'>Resend</a>
                            </p> 
                        ";

                    }else{

                        $msg_text .= "
                            <p class='msg__text'>
                                <i class='fa fa-exclamation-circle msg__icon'></i> Your E-mail address haven't been verified yet. Check your inbox, A verification mail was sent to <span>{$user_info['user_email']}</span>. Didn't you recevie the mail? <a class='msg__link msg__link--resend' role='button' data-verification_email='resend'>Resend</a> 
                            </p> 
                        ";

                    }

                    echo "
                        <div class='msg msg--{$msg_type}'>
                            <div class='container msg__container'>
                                <div class='col-12 col-lg-10 mx-auto'>
                                    <div class='msg__content'>
                                        {$msg_text}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ";
                }

        ?>
    
            <header class="ph">
                <nav class="ph-nav ph__nav">
                    <div class="container-fluid ph-nav__container">
                            <div class="ph-nav__logo">
                                <a href="<?php echo $config->domain(); ?>">
                                    <img src="<?php echo $config->domain("assets/img/lobster-logo-poppins.png") ?>" alt="Logo">
                                </a>
                            </div>
                            
                            <div class="ph-nav__search-box">
                                <form class="ph-nav__form ph-nav__form--search" action="<?php echo $config->domain("result"); ?>">
                                    <div class="ph-nav__form-wrap">
                                        <div class="ph-nav__form-close d-block d-md-none">
                                            <button class="ph-nav__btn ph-nav__btn--form-close"  type="button">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>
                                        </div>

                                        <div class="ph-nav__form-field-wrap">
                                            <div class="ph-nav__form-field ph-nav__form-field--search">
                                                <input class="ph-nav__form-input ph-nav__form-input--search" type="text" name="search_query" placeholder="Search..." autocomplete="off">
                                            </div>

                                            <div class="ph-nav__form-field ph-nav__form-field--submit">
                                                <button class="ph-nav__btn ph-nav__btn--form-submit" type="submit">
                                                        <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>


                        <div class="ph-nav__acts">
                            <div class="ph-nav__act ph-nav__act--1 d-block d-md-none">
                                <button  class="ph-nav__btn ph-nav__btn--search-sm" type="button">
                                    <i class="fa fa-search ph-nav__form-btn-icon"></i>
                                </button>
                            </div>

                            <?php if($user_info !== null): ?>
                            <div class="ph-nav__act ph-nav__act--2">
                                <div class="ph-nav__dropdown ph-nav__dropdown--nf">
                                    <div class="ph-nav__dropdown-toggle ph-nav__dropdown-toggle--nf">
                                        <button class="ph-nav__btn ph-nav__btn--bell" type="button">
                                            <i class="fa fa-bell ph-nav__btn-icon"></i>
                                            <?php 
                                                if($nf_info["unread"] > 0){

                                                    //store the badge text according t unread status
                                                    $badge_txt=($nf_info["unread"] > 9) ? "9+" : $nf_info["unread"]; 
                    
                                                    echo "
                                                        <span class='ph-nav__badge ph-nav__badge--nf'>
                                                            {$badge_txt}
                                                        </span>
                                                    ";
                                                }
                                            
                                            ?>
                                        </button>
                                    </div>

                                    <div class='ph-nav__dropdown-content ph-nav__dropdown-content--nf'>
                                        <div class='ph-nav__dropdown-content-wrap ph-nav__dropdown-content-wrap--nf'>
                                         
                                        </div>
                                    </div>
                                </div>

                                <div class="ph-nav__dropdown ph-nav__dropdown--menu">
                                    <div class="ph-nav__dropdown-toggle ph-nav__dropdown-toggle--menu">
                                        <?php 
                                            if($profile_img !== null){
                                                
                                                echo "<img class='ph-nav__dropdown-img--profileimg' src='{$config->domain("app/uploads/users/profile/{$profile_img['name']}-sm.{$profile_img['ext']}")}' alt='{$user_info['user_name']}' width='{$profile_img['dimension']['sm']['width']}' height='{$profile_img['dimension']['sm']['height']}'/>";
                                            }
                                        ?>
                                    </div>

                                    <div class="ph-nav__dropdown-content ph-nav__dropdown-content--menu">
                                        <div class="ph-nav__dropdown-content-wrap ph-nav__dropdown-content-wrap--menu">
                                            <div class="ph-nav__dropdown-header ph-nav__dropdown-header--menu">
                                                <h6>
                                                    <?php 
                                                        echo $user_info["user_name"]; 
                                                    ?>
                                                </h6>
                                                
                                                <span>
                                                    <?php
                                                        echo $user_info["user_role"]; 
                                                            
                                                    ?>
                                                </span>
                                            </div>
                                        
                                            <div class="ph-nav__dropdown-body ph-nav__dropdown-body--menu">
                                                <ul class="ph-nav__dropdown-list">
                                                    <li class="ph-nav__dropdown-item">
                                                        <a class="ph-nav__dropdown-link" href="<?php echo $config->domain("users/{$user_info["user_name"]}/dashboard"); ?>">
                                                            <i class="fa fa-bar-chart"></i>
                                                            <span>Dashboard</span>
                                                        </a>
                                                    </li>
                                                    
                                                    <li  class="ph-nav__dropdown-item">
                                                        <a class="ph-nav__dropdown-link" href="<?php echo $config->domain("users/{$user_info["user_name"]}"); ?>">
                                                            <i class="fa fa-user"></i>
                                                            <span>My Profile</span>
                                                        </a>
                                                    </li>

                                                    <li class="ph-nav__dropdown-item">
                                                        <a class="ph-nav__dropdown-link" href="<?php echo $config->domain('accounts/logout'); ?>">
                                                            <i class="fa fa-sign-out"></i>
                                                            <span>Logout</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php elseif($user_info == null): ?>
                            <div class="ph-nav__act ph-nav__act--2">
                                <a class="ph-nav__btn ph-nav__btn--login" href="<?php echo $config->domain("accounts/login") ?>" title="Login">
                                    <i class="fa fa-user"></i>
                                    <span>Login</span>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </nav>
                
                <?php if($_SERVER["REQUEST_URI"] == "/lobster/" || $_SERVER["REQUEST_URI"] == "/lobster"):?>
                <div class="ph-filter ph__filter">
                    <div class="container ph-filter__container">
                        <div class="ph-filter__slider">
                            <button class="ph-filter__btn ph-filter__btn--ctrl ph-filter__btn--prev" type="button">
                                <i class="fa fa-angle-left"></i>
                            </button>

                            <div class="ph-filter__slider-wrap">
                                <div class="ph-filter__slider-track">
                                    <button class="ph-filter__btn ph-filter__btn--cat ph-filter__btn--active" type="button" data-cat_id="0">All</button>
                                    
                                    <?php
                                        if(isset($data["catagories"])){

                                            foreach($data["catagories"] as $cat_index=>$cat){
                                                echo "
                                                    <button class='ph-filter__btn ph-filter__btn--cat' type='button' data-cat_id='{$cat['cat_id']}'>{$cat['cat_name']}</button>    
                                                ";
                                            }
                                        } 
                                    ?>
                                </div>
                            </div>
                            <button class="ph-filter__btn ph-filter__btn--ctrl ph-filter__btn--next" type="button">
                                <i class="fa fa-angle-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </header>
        <?php
            endif;
        

            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
        ?>