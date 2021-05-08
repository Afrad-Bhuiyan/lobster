<?php 
    include "app/views/dashboard/includes/header.dash.php";

    
    // echo "<pre>";
    // print_r($user_info);
    // echo "</pre>";
?>
    
    <div class="elements__acc-sett acc-sett">
        <div class="container acc-sett__container">
            <div class="row acc-sett__row">
                <div class="col-12 col-lg-8 acc-sett__col">
                    <div class="acc-sett__col-wrap">
                        <div class="acc-sett__col-head">
                            <h3 class="acc-sett__title acc-sett_title--main">
                                Account Settings
                            </h3>
                        </div>

                        <div class="acc-sett__col-body">
                            <div class="row acc-sett__opt acc-sett__opt--profile">
                                <div class="col-12">
                                    <h4 class="acc-sett__opt-title">Profile Picture</h4>
                                    <p class="acc-sett__opt-subtitle">
                                        Your profile picture will appear where your posts are presented on Lobster, like next to your posts and comments. 
                                    </p>

                                    <div class="acc-sett__opt-content">
                                        <div class="acc-sett__opt-case acc-sett__opt-case--profile">
                                            <?php 
                                                if($profile_img !== null){

                                                    echo "<img class='acc-sett__img acc-sett__img--profile' src='{$config->domain("app/uploads/users/profile/{$profile_img['name']}-md.{$profile_img['ext']}")}' width='{$profile_img['dimension']['md']['width']}' height='{$profile_img['dimension']['md']['height']}' alt='{$user_info['user_name']}'/>";

                                                }
                                            ?>
                                        </div>  

                                        <div class="acc-sett__opt-info">
                                            <p class="acc-sett__opt-desc">
                                                It’s recommended to use a picture that’s at least <strong>200 x 200 pixels</strong> and 1MB or less. Use a PNG, JPG or JPEG file.
                                            </p>

                                            <div class="acc-sett__opt-action">
                                                <form class="acc-sett__form acc-sett__form--profile">
                                                    <?php 
                                                        if($profile_img !== null){
                                                        
                                                            if($profile_img["status"] == 0){

                                                                echo "
                                                                    <button class='acc-sett__btn acc-sett__btn--action acc-sett__btn--upload' type='button' data-upload_action='profile'>
                                                                        <i class='fa fa-upload acc-sett__btn-icon'></i>
                                                                        <span class='acc-sett__btn-text'>Upload</span>
                                                                    </button>
                                                                ";

                                                            }elseif($profile_img["status"] == 1){

                                                                echo "
                                                                    <button class='acc-sett__btn acc-sett__btn--action  acc-sett__btn--change' type='button'  data-change_action='profile'>
                                                                        <i class='fa fa-edit acc-sett__btn-icon'></i>
                                                                        <span class='acc-sett__btn-text'>change</span>
                                                                    </button>
                                                                ";
                                                            }
                                                        }
                                                    
                                                    ?>
                                                    <input class="acc-sett__form-input acc-sett__form-input--file" type="file" name="profile_img">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row acc-sett__opt acc-sett__opt--banner">
                                <div class="col-12">
                                    <h4 class="acc-sett__opt-title">
                                        Profile Banner
                                    </h4>
                                    <p class="acc-sett__opt-subtitle">
                                        This image will appear across the top of your profile 
                                    </p>

                                    <div class="acc-sett__opt-content">
                                        <div class="acc-sett__opt-case acc-sett__opt-case--banner">
                                            <?php 

                                                if($bg_img !== null){

                                                    echo "<img class='acc-sett__img acc-sett__img--banner' src='{$config->domain("app/uploads/users/bg/{$bg_img['name']}-sm.{$bg_img['ext']}")}' alt='{$user_info['user_name']} placeholder background Image' width='{$bg_img['dimension']['sm']['width']}' height='{$bg_img['dimension']['sm']['height']}'/>";
                                                }
                                                
                                            
                                            ?>
                                        </div>  

                                        <div class="acc-sett__opt-info">
                                            <p class="acc-sett__opt-desc">
                                                For the profile Banner, use an image that’s at least <strong>1400 x 350</strong> pixels and 2MB or less
                                            </p>

                                            <div class="acc-sett__opt-action">
                                                <form class="acc-sett__form acc-sett__form--banner">
                                                    <?php 
                                                        if($bg_img !== null){
                                                        
                                                            if($bg_img["status"] == 0){

                                                                echo "
                                                                    <button class='acc-sett__btn acc-sett__btn--action acc-sett__btn--upload' type='button' data-upload_action='banner'>
                                                                        <i class='fa fa-upload acc-sett__btn-icon'></i>
                                                                        <span class='acc-sett__btn-text'>Upload</span>
                                                                    </button>
                                                                ";

                                                            }elseif($bg_img["status"] == 1){

                                                                echo "
                                                                    <button class='acc-sett__btn acc-sett__btn--action  acc-sett__btn--change' type='button'  data-change_action='banner'>
                                                                        <i class='fa fa-edit acc-sett__btn-icon'></i>
                                                                        <span class='acc-sett__btn-text'>change</span>
                                                                    </button>
                                                                ";
                                                            }
                                                        }
                                                    ?>
                                                    <input class="acc-sett__form-input acc-sett__form-input--file" type="file" name="bg_img">
                                                </form>                                             
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>                           
                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </div>

<?php
    include "app/views/dashboard/includes/footer.dash.php";
?>