<?php 
    $config=new config;

    $functions=new functions;

    $user_info=(isset($data["common_info"]["user_info"])) ? $data["common_info"]["user_info"] : null;
                
    $profile_img=($user_info !== null) ? $user_info["ufile_info"]["profile_img"] : null;

    $bg_img=($user_info !== null) ? $user_info["ufile_info"]["bg_img"] : null;
        
    $total_unread_nf=(isset($data["common_info"]["total_unread_nf"])) ? $data["common_info"]["total_unread_nf"] : null;

    $dashboard_uri="/users/{$user_info['user_name']}/dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (isset($data["title_tag"])) ? $data["title_tag"] : "set the title tag"; ?></title>
    <!--Google Fonts-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"> 
    <!--Font Aweseme-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type='text/css' media='all'>
    <!--Boootstrap css-->
    <link rel="stylesheet" href="<?php echo "{$config->domain('assets/css/bootstrap.min.css')}" ?>" type='text/css' media='all'>
    
    <?php 

        $stylesheet_url="";
        
        if(isset($_GET["posts"]) && !empty($_GET["posts"])){

            $stylesheet_url = $config->domain("assets/css/posts.min.css");
            
        }elseif(isset($_GET["settings"]) && !empty($_GET["settings"])){

            $stylesheet_url = $config->domain("assets/css/settings.min.css");
            
        }elseif(isset($_GET["admin_options"]) && !empty($_GET["admin_options"])){

            $stylesheet_url= $config->domain("assets/css/admin_opt.min.css");


        }else{

            $stylesheet_url = $config->domain("assets/css/dashboard.min.css");

        }
        
        echo ' <link rel="stylesheet" href="'. $stylesheet_url.'" type="text/css" media="all">'. "\r\n";
 
    
    ?>
    
    
    <!--Jquery Js-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--1">
            <div class="main-wrap__sidebar sidebar">
                <div class="sidebar__wrap">
                    <div class="sidebar__close">
                        <button class="sidebar__btn sidebar__btn--close" type="button">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                
                    <div class="sidebar__user-info">
                        <?php 
                            if($profile_img !== null){            
                                echo "<img class='sidebar__img sidebar__img--user' src='{$config->domain("app/uploads/users/profile/{$profile_img['name']}-md.{$profile_img['ext']}")}' alt='{$user_info['user_name']}' width='{$profile_img['dimension']['md']['width']}' height='{$profile_img['dimension']['md']['height']}'/>";
                            }
                        ?>
                        <h4 class="sidebar__title sidebar__title--username">
                            <a class="sidebar__link sidebar__link--username" href="<?php echo $config->domain("users/{$user_info['user_name']}") ?>" target="_blank">
                                <?php 
                                    echo $user_info["user_name"];
                                ?>
                            </a>
                        </h4>
                        <span class="sidebar__subtitle sidebar__subtitle--role">
                            <?php
                                echo $user_info["user_role"];
                            ?>
                        </span>
                    </div>

                    <div class="sidebar__options">
                        <div class="sidebar__option sidebar__option--dashboard">
                            <div class="sidebar__option-head">
                                <i class="fa fa-bar-chart sidebar__option-icon"></i> 
                                <h5 class="sidebar__option-title">Dashboard</h5>
                            </div>

                            <ul class="sidebar__option-list">
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard") ?>">
                                        Overview
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="sidebar__option sidebar__option--posts">
                            <div class="sidebar__option-head">
                                <i class="fa fa-tasks sidebar__option-icon"></i> 
                                <h5 class="sidebar__option-title">Posts</h5>
                            </div>

                            <ul class="sidebar__option-list">
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard?posts=myposts&filter=all") ?>">
                                        My Posts
                                    </a>
                                </li>
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard?posts=publish") ?>">
                                        Publish a post
                                    </a>
                                </li>
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard?posts=saved") ?>">
                                        Saved Posts
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="sidebar__option sidebar__option--settings">
                            <div class="sidebar__option-head">
                                <i class="fa fa-cog sidebar__option-icon"></i> 
                                <h5 class="sidebar__option-title">Settings</h5>
                            </div>

                            <ul class="sidebar__option-list">
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard?settings=account") ?>">
                                        Account
                                    </a>
                                </li>
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard?settings=security") ?>">
                                        Security
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <?php if($user_info["user_role"] == "admin"): ?>
                        <div class="sidebar__option sidebar__option--admin-opt">
                            <div class="sidebar__option-head">
                                <i class="fa fa-user-plus sidebar__option-icon"></i> 
                                <h5 class="sidebar__option-title">Admin Options</h5>
                            </div>

                            <ul class="sidebar__option-list">
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard?admin_options=users") ?>">
                                        Users
                                    </a>
                                </li>
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard?admin_options=catagories") ?>">
                                       Catagories
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <div class="sidebar__option sidebar__option--advance">
                            <div class="sidebar__option-head">
                                <i class="fa fa-th-large sidebar__option-icon"></i> 
                                <h5 class="sidebar__option-title">Advance</h5>
                            </div>

                            <ul class="sidebar__option-list">
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="#">
                                        Subscribers
                                    </a>
                                </li>
                                <li class="sidebar__option-item">
                                    <a class="sidebar__link sidebar__link--option" href="#">
                                        Subscriptions
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div><!--Sidebar-->

            <div class="main-wrap__elements elements">
                <nav class="elements__navbar navbar">
                    <a class="navbar__link navbar__link--logo" href="<?php echo $config->domain(); ?>">
                        <img class="navbar__img navbar__img--logo" src="<?php echo $config->domain("assets/img/lobster-logo-poppins.png"); ?>" alt="">
                    </a>

                    <div class="navbar__toggle">
                        <button class="navbar__btn navbar__btn--bars" type="button">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>

                    <div class="navbar__action">
                        <div class="navbar__action-part navbar__action-part--1">
                            <a class="navbar__link navbar__link--home" role="button">
                                <i class="fa fa-home"></i>
                            </a>

                            <a class="navbar__link navbar__link--plus" role="button">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>

                        <div class="navbar__action-part navbar__action-part--2">
                            <div class="navbar__dropdown navbar__dropdown--nf">
                                <div class="navbar__dropdown-toggle navbar__dropdown-toggle--nf" data-to_user_id="<?php echo $user_info["user_id"]; ?>">
                                    <a class="navbar__link navbar__link--bell" role="button">
                                        <i class="fa fa-bell"></i>
                                        <?php 
                                            if($total_unread_nf !== null){

                                                echo ($total_unread_nf > 9) ? "<span>9+</span>" : "<span>{$total_unread_nf}</span>";
                                            }
                                        ?>
                                    </a>
                                </div>

                                <div class="navbar__dropdown-content navbar__dropdown-content--nf">
                                    <div class="navbar__dropdown-content-wrap navbar__dropdown-content-wrap--nf">
                                    
                                    </div>
                                </div>
                            </div>

                            <div class="navbar__dropdown navbar__dropdown--menu">
                                <div class="navbar__dropdown-toggle navbar__dropdown-toggle--menu">
                                    <?php 
                                        if($profile_img !== null){

                                            echo "<img class='navbar__img navbar__img--loggeduser' src='{$config->domain("app/uploads/users/profile/{$profile_img['name']}-sm.{$profile_img['ext']}")}' alt='{$user_info['user_name']}' width='{$profile_img['dimension']['sm']['width']}' height='{$profile_img['dimension']['sm']['height']}'/>";
                                        }
                                    ?>
                                </div>
                                <div class="navbar__dropdown-content navbar__dropdown-content--menu">
                                    <div class="navbar__dropdown-content-wrap navbar__dropdown-content-wrap--menu">
                                        <div class="navbar__dropdown-header navbar__dropdown-header--menu">
                                            <h6>
                                                <?php echo $user_info["user_name"] ?>
                                            </h6>

                                            <span>
                                                <?php echo $user_info["user_role"] ?>
                                            </span>
                                        </div>

                                        <div class="navbar__dropdown-body navbar__dropdown-body--menu">
                                            <ul class="navbar__dropdown-list">
                                                <li class="navbar__dropdown-item">
                                                    <a class="navbar__link navbar__link--dropdown" href="<?php echo $config->domain("users/{$user_info["user_name"]}"); ?>">
                                                        <i class="fa fa-user"></i>
                                                        <span>My Profile</span>
                                                    </a>
                                                </li>

                                                <li class="navbar__dropdown-item">
                                                    <a class="navbar__link navbar__link--dropdown" href="<?php echo $config->domain('accounts/logout'); ?>">
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
                    </div>
                </nav>
     


