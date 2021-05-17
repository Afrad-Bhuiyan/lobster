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
                                <div class="sp-content__thumb">
                                    <?php 
                                        echo "
                                            <img src='{$config->domain("app/uploads/posts/{$pfile_info['name']}-lg.{$pfile_info['ext']}")}' alt='' width='{$pfile_info['dimension']['lg']['width']}' height='{$pfile_info['dimension']['lg']['height']}'/>
                                        ";
                                    ?>
                                </div>
                                
                                <div class="sp-content__title">
                                    <h2>
                                        <?php echo $single_post['post_title'] ?>
                                    </h2>
                                </div>

                                <div class="sp-content__meta-wrap">
                                    <div class="sp-content__meta-side sp-content__meta-side--left">
                                        <div class="sp-content__meta-item sp-content__meta-item--read">
                                            <span>56,632 read</span>
                                        </div>

                                        <div class="sp-content__meta-item sp-content__meta-item--date">
                                            <span>
                                                <?php 
                                                    $date_time=explode("_",$single_post['post_date']);
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
                                            echo "
                                                <img class='sp-content__auth-img' src='{$config->domain("app/uploads/users/profile/{$post_auth_profile['name']}-sm.{$post_auth_profile['ext']}")}' alt='{$post_auth_info['user_name']}'/>
                                            "
                                        ?>
                                        <div class="sp-content__auth-text">
                                            <a class="sp-content__auth-name" href="<?php echo $config->domain("users/{$post_auth_info['user_name']}") ?>">
                                               afradbhuiyan
                                            </a>
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
                            
                                    <p>
                                        <?php
                                            echo $single_post['post_content']; 
                                        ?> 
                                    </p>
                                </div>
                            </div><!--sp-content-->

                            <div class="sidebar-sm d-block d-lg-none">
                            

                            </div><!--Sidebar-sm-->

                            <div class="sp-comm">
                                <!-- <div class="sp-comm__title">
                                    <h4>
                                        <span>10</span> <strong>comments</strong>
                                    </h4> 
                                </div>

                                <form class="sp-comm__form sp-comm__form--lg sp-comm__form--primary">
                                    <div class="sp-comm__form-side-wrap">
                                        <div class="sp-comm__form-side sp-comm__form-side--top">
                                            <div class="sp-comm__logged-user-info">
                                                <img src="<?php echo $config->domain("app/uploads/users/profile/wVYdSTEJDhZ-sm.jpg") ?>" alt="">
                                            </div>

                                            <div class="sp-comm__form-field sp-comm__form-field--textarea">
                                                <textarea class="sp-comm__form-input sp-comm__form-input-textarea" name="comment_content" placeholder="Write your comment"></textarea>
                                                <input class="sp-comm__form-input sp-com__form-input--hidden" type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                            </div>
                                        </div>

                                        <div class="sp-comm__form-side sp-comm__form-side--bottom sp-comm__form-side--hide">
                                            <div class="sp-comm__form-btn-wrap">
                                                <button class="sp-comm__form-btn sp-comm__form-btn--cancel" type="button">Cancel</button>
                                                <button class="sp-comm__form-btn sp-comm__form-btn--comment sp-comm__form-btn--disabled" type="button">
                                                    <span>Comment</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>     
                                </form>

                                <div class="sp-single-comm sp-single-comm--primary">
                                    <div class="sp-single-comm__userimg sp-single-comm__userimg--primary">
                                        <img  class="sp-content__auth-img" src="<?php echo $config->domain("app/uploads/users/profile/wVYdSTEJDhZ-sm.jpg") ?>" alt="">
                                    </div>

                                    <div class="sp-single-comm__wrap sp-single-comm__wrap--primary">          
                                        <div class="sp-single-comm__content sp-single-comm__content--primary">
                                            <div class="sp-single-comm__username sp-single-comm__username--primary <?php echo  ($comment["user_id"] == $post_author) ? "sp-single-comm__username--admin" : ""; ?>">
                                                <a href="#">afradbhuiyan</a>
                                                <i class="fa fa-circle"></i>
                                                <span>5 hours ago</span>
                                            </div>

                                            <div class="sp-single-comm__body sp-single-comm__body--primary">
                                                <p>
                                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel modi enim veniam, tenetur perspiciatis unde!
                                                </p>
                                            </div>

                                            <div class="sp-single-comm__reply sp-single-comm__reply--primary">
                                                <button class="sp-single-comm__btn sp-single-comm__btn--reply sp-single-comm__btn--primary" type="button" data-comment_id="1">Reply</button>
                                            </div>
                                        </div>

                                        <div class="sp-single-comm sp-single-comm--secondary">
                                            <div class="sp-single-comm__userimg sp-single-comm__userimg--secondary">
                                                <img  class="sp-content__auth-img" src="<?php echo $config->domain("app/uploads/users/profile/wVYdSTEJDhZ-sm.jpg") ?>" alt="">
                                            </div>

                                            <div class="sp-single-comm__content sp-single-comm__content--secondary">
                                                <div class="sp-single-comm__username sp-single-comm__username--secondary">
                                                    <a href="#">
                                                        afradbhuiyan
                                                    </a>
                                                    <i class="fa fa-circle"></i>
                                                    <span>5 hours</span>
                                                </div>

                                                <div class="sp-single-comm__body sp-single-comm__body--secondary">
                                                    <p>
                                                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Perspiciatis consequatur soluta repudiandae inventore vero placeat corrupti minima reiciendis.
                                                    </p>
                                                </div>
                                                <div class="sp-single-comm__reply sp-single-comm__reply--secondary">
                                                    <button class="sp-single-comm__btn sp-single-comm__btn--reply sp-single-comm__btn--secondary" type="button" data-comment_id="<?php echo $comment['comment_id']; ?>">
                                                        Reply
                                                    </button>
                                                </div>
                                            </div>
                                        
                                            <div class="btn-group dropleft sp-single-comm__opt sp-single-comm__opt--secondary">
                                                <button class="sp-single-comm__btn sp-single-comm__btn--opt sp-single-comm__btn--secondary" data-toggle="dropdown" id="dropdown-secondary" type="button" data-offset="10,0">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>

                                                <div class="dropdown-menu sp-single-comm__dropdown sp-single-comm__dropdown--secondary" aria-labelledby="#dropdown-secondary">
                                                    <ul class="sp-single-comm__dropdown-list">
                                                        <li class="sp-single-comm__dropdown-item">
                                                            <a class="sp-single-comm__dropdown-link sp-single-comm__dropdown-link--delete" role="button" data-comment_type="secondary_comment" data-cr_id="<?php echo $comment_reply["cr_id"] ?>">
                                                                <i class="fa fa-trash"></i>
                                                                <span>Delete</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>                              
                                        </div>
                                    </div>
                        
                                    <div class="btn-group dropleft sp-single-comm__opt sp-single-comm__opt--primary">
                                        <button class="sp-single-comm__btn sp-single-comm__btn--opt sp-single-comm__btn--primary" data-toggle="dropdown" id="dropdown-primary" type="button" data-offset="10,0">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>

                                        <div class="dropdown-menu sp-single-comm__dropdown sp-single-comm__dropdown--primary" aria-labelledby="#dropdown-primary">
                                            <ul class="sp-single-comm__dropdown-list">
                                                <li class="sp-single-comm__dropdown-item">
                                                    <a class="sp-single-comm__dropdown-link sp-single-comm__dropdown-link--delete" role="button" data-comment_type="primary_comment" data-comment_id="<?php echo $comment["comment_id"] ?>">
                                                        <i class="fa fa-trash"></i>
                                                        <span>Delete</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div> -->
                            </div><!--sp-comm-->
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