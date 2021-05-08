<?php 
    include "includes/header.inc.php";
?>


    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--spsb">
            <div class="container main-wrap__container main-wrap__container--spsb">
                <?php 
                    $user_info=(isset($data["common_info"]["user_info"])) ? $data["common_info"]["user_info"] : null;
                    $post=$data["post"];
                    $catagories=(isset($data["catagories"])) ? $data["catagories"] : null;

                    // echo "<pre>";
                    // print_r($data);
                    // echo "</pre>";
                ?>

                <div class="row main-wrap__row main-wrap__row--spsb">
                    <div class="col-12 col-lg-8  main-wrap__col  main-wrap__col--sp">
                        <div class="main-wrap__col-wrap main-wrap__col-wrap--sp">
                            <div class="sp-content">
                                <div class="sp-content__thumb">
                                    <img src="<?php echo $config->domain("app/uploads/posts/{$post['pfile_name']}-lg.{$post['pfile_ext']}") ?>" alt="">
                                </div>
                                
                                <div class="sp-content__title">
                                    <h2><?php echo $post['post_title'] ?></h2>
                                </div>

                                <div class="sp-content__meta-wrap">
                                    <div class="sp-content__meta-side sp-content__meta-side--left">
                                        <div class="sp-content__meta-item sp-content__meta-item--read">
                                            <span>56,632 read</span>
                                        </div>

                                        <div class="sp-content__meta-item sp-content__meta-item--date">
                                            <span>
                                                <?php 
                                                    $date_time=explode("_",$post['post_date']);
                                                    //Print the date only
                                                    echo $date_time[0];
                                                ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="sp-content__meta-side sp-content__meta-side--right">
                                        <div class="sp-content__meta-item sp-content__meta-item--like" title="Like">
                                            <!--like button will be append-->
                                        </div>

                                        <div class="sp-content__meta-item sp-content__meta-item--dislike">
                                            <!--Dislike button will be append-->
                                        </div>

                                        <div class="sp-content__meta-item sp-content__meta-item--save">
                                            <!--save button will be append-->
                                        </div>
                                    </div>
                                </div>

                                <div class="sp-content__auth-wrap">
                                    <div class="sp-content__auth-side sp-content__auth-side--left">
                                        <?php 
                                        
                                            if($post["ufile_name"] == "false" || $post["ufile_ext"] == "false"){

                                                echo "
                                                    <img class='sp-content__auth-img' src='{$config->domain('app/uploads/users/user-placeholder-img.png')}' alt='{$post['user_name']}'s placeholder Profile picture on lobster'>
                                                ";

                                            }else{

                                                echo "
                                                    <img class='sp-content__auth-img' src='{$config->domain("app/uploads/users/{$post['ufile_name']}-sm.{$post['ufile_ext']}")}' alt='{$post['user_name']}'s Profile picture on lobster'>
                                                ";
                                            }

                                        ?>
                                        
                                        <div class="sp-content__auth-text">
                                            <a class="sp-content__auth-name" href="#"><?php echo $post['user_name'] ?></a>
                                            <br>
                                            <span class="sp-content__auth-subs">
                                                <!--Total subscriber will append here-->
                                            </span>
                                        </div>
                                    </div>

                                    <div class="sp-content__auth-side sp-content__auth-side--right">
                                        <!--Subscribe button will appear here-->
                                    </div>
                                </div>

                                <div class="sp-content__desc">
                                    <?php 
                                        echo $post['post_content'];
                                    ?>
                                </div>
                            </div><!--sp-content-->

                            <div class="sidebar-sm d-block d-lg-none">
                            

                            </div><!--Sidebar-sm-->
                        </div>
                    </div><!--Single Posts-->

                    <div class="col-12 col-lg-4 d-none d-lg-block main-wrap__col main-wrap__col--sb">
                        <div class="main-wrap__col-wrap main-wrap__col-wrap--sb">

                            <div class="sidebar-lg">
                                <div class="sidebar-lg__filters">
                                    <button class="sidebar-lg__filter-btn sidebar-lg__filter-btn--action sidebar-lg__filter-btn--prev" type="button">
                                        <i class="fa fa-angle-left"></i>
                                    </button>

                                    <div class="sidebar-lg__filter-wrap">
                                        <div class="sidebar-lg__filter-track">
                                            <button class="sidebar-lg__filter-btn sidebar-lg__filter-btn--cat sidebar-lg__filter-btn--active" type="button"  data-cat_id='0'>All</button>
                                            <?php 
                                                if($catagories !== null){
                                                    foreach($catagories as $cat_index=>$cat){
                                                        echo "
                                                            <button class='sidebar-lg__filter-btn sidebar-lg__filter-btn--cat' type='button' data-cat_id='{$cat['cat_id']}'>{$cat['cat_name']}</button>
                                                        ";
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <button class="sidebar-lg__filter-btn sidebar-lg__filter-btn--action sidebar-lg__filter-btn--next" type="button">
                                        <i class="fa fa-angle-right"></i>
                                    </button>
                                </div>
                                
                                 <div class="sidebar-lg__content">
                                    <!--All content will be appended-->
                                </div>
                            </div>
                        </div>
                    </div><!--Sidebar-->
                </div>
            </div>
        </section>
    </main>
<?php 
    include "includes/footer.inc.php"
?>