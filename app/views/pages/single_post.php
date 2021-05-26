<?php 
    include "includes/header.inc.php";

    $single_post=isset($data["single_post"]) ? $data["single_post"] : null;

    $pfile_info=isset($single_post["pfile_info"]) ? $single_post["pfile_info"] : null;
    
    $catagories=(isset($data["catagories"])) ? $data["catagories"] : null;

    $post_auth_info=isset($data["single_post"]["post_auth_info"]) ? $data["single_post"]["post_auth_info"]: null;

    $post_auth_profile=isset($post_auth_info["ufile_info"]["profile_img"]) ? $post_auth_info["ufile_info"]["profile_img"] : null;

    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";

?>
    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--spsb">
            <div class="container main-wrap__container main-wrap__container--spsb">
                <div class="row main-wrap__row main-wrap__row--spsb">
                    <div class="col-12 col-lg-8  main-wrap__col  main-wrap__col--sp">
                        <div class="main-wrap__col-wrap main-wrap__col-wrap--sp">
                            <div class="sp-content">
                                <?php 
                                    echo "
                                        <img class='sp-content__img sp-content__img--postThumb' src='{$config->domain("app/uploads/posts/{$pfile_info['name']}-lg.{$pfile_info['ext']}")}' alt='' width='{$pfile_info['dimension']['lg']['width']}' height='{$pfile_info['dimension']['lg']['height']}'/>
                                    ";
                                ?>

                                <h2 class="sp-content__title">
                                    <?php echo $single_post['post_title'] ?>
                                </h2>

                                <div class="sp-content__meta">
                                    <ul class="sp-content__meta-list sp-content__meta-list--left">
                                        <li class="sp-content__meta-list-item">
                                            <span class="sp-content__meta-list-txt">
                                                <?php echo  number_format($single_post['total_read']) . " read" ?>
                                            </span>
                                        </li>
                                        
                                        <li class="sp-content__meta-list-item">
                                            <span class="sp-content__meta-list-txt">
                                                <?php 
                                                    $date_time=explode("_",$single_post['post_date']);
                                                    //Print the date only
                                                    echo $date_time[0];
                                                ?>
                                            </span>
                                        </li>
                                    </ul>

                                    <ul class="sp-content__meta-list sp-content__meta-list--right">
                                        <li class="sp-content__meta-list-item sp-content__meta-list-item--like">
                                            <!--Like button will appended-->
                                        </li>
                                        
                                        <li class="sp-content__meta-list-item sp-content__meta-list-item--dislike">
                                            <!--Dislike button will appended-->
                                        
                                        </li>
                                        
                                        <li class="sp-content__meta-list-item sp-content__meta-list-item--save">
                                            <!--Save button will appended-->
                                           
                                        </li>
                                    </ul>
                                </div>

                                <div class="sp-content__auth">
                                    <div class="sp-content__auth-side sp-content__auth-side--left">
                                        <?php 
                                            echo "
                                                <img class='sp-content__img sp-content__img--postAuthor' src='{$config->domain("app/uploads/users/profile/{$post_auth_profile['name']}-sm.{$post_auth_profile['ext']}")}' alt='{$post_auth_info['user_name']}'/>
                                            "
                                        ?>
                                        <div class="sp-content__auth-info">
                                            <a class="sp-content__link sp-content__link--uname" href="<?php echo $config->domain("users/{$post_auth_info['user_name']}") ?>">
                                                afradbhuiyan
                                            </a>
                                            <span class="sp-content__txt sp-content__txt--subs">
                                            </span>
                                        </div>
                                    </div>

                                    <div class="sp-content__auth-side sp-content__auth-side--right">
                                        <!--Subscribe button will be appended-->
                                    </div>
                                </div>

                                <div class="sp-content__desc">
                                    <?php
                                        echo $single_post['post_content'];
                                    ?> 
                                </div>

                            </div><!--sp-content-->

                            <div class="sidebar-sm d-block d-lg-none">
                            

                            </div><!--Sidebar-sm-->

                            <div class="sp-comm">

                            </div><!--sp-comm-->
                        </div>
                    </div><!--Single Posts-->

                    <div class="col-12 col-lg-4 d-none d-lg-block main-wrap__col main-wrap__col--sb">
                        <div class="main-wrap__col-wrap main-wrap__col-wrap--sb">
                            <div class="sidebar-lg">
                                <div class="sidebar-lg__filter">
                                    <button class="sidebar-lg__btn sidebar-lg__btn--ctrl sidebar-lg__btn--prev" type="button">
                                        <i class="fa fa-angle-left"></i>
                                    </button>
                                    <div class="sidebar-lg__filter-wrap">
                                        <div class="sidebar-lg__filter-track">
                                            <?php 
                                                if($catagories !== null){
                                                    foreach($catagories as $cat_index=>$cat){

                                                        $filter_btn_active = ($cat["cat_id"] == 1) ? "sidebar-lg__btn--active" : "";

                                                        echo "
                                                            <button class='sidebar-lg__btn sidebar-lg__btn--cat {$filter_btn_active}' type='button' data-cat_id='{$cat['cat_id']}'>
                                                                {$cat['cat_name']}
                                                            </button>
                                                        ";
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <button class="sidebar-lg__btn sidebar-lg__btn--ctrl sidebar-lg__btn--next" type="button">
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