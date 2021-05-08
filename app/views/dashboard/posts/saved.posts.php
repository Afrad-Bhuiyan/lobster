
<?php 
    include "app/views/dashboard/includes/header.dash.php";

    $saved_posts=isset($data["saved_posts"]) ? $data["saved_posts"] : null;
    $total_saved_posts=isset($data["total_saved_posts"]) ? $data["total_saved_posts"] : null;

    // echo "<pre>";
    // print_r($saved_posts);
    // echo "</pre>";

?>

    <div class="elements__saved-posts saved-posts">
        <div class="container saved-posts__container">
            <?php 
                  if($saved_posts !== null):
            ?>
            <div class="row saved-posts__row saved-posts__row--main">
                <div class="col-12 col-lg-10 saved-posts__col saved-posts__col--main">
                    <div class="saved-posts__col-wrap">
                        <div class="saved-posts__head">
                            <h3 class="saved-posts__title saved-posts__title--main">
                                Saved Posts
                            </h3>
                            <?php 

                                if($total_saved_posts !== null ){

                                    echo "
                                        <div class='saved-posts__total'>
                                            <p>
                                                <strong class='saved-posts__total-num'>{$total_saved_posts}</strong> 
                                                <span>Saved Posts</span>
                                            </p>
                                        </div>
                                    ";

                                }
                            ?>
                            
                        </div>

                        <?php foreach($saved_posts as $sp_index=>$sp): ?>
                        <div class="saved-posts__single-row">

                            <a class="saved-posts__link saved-posts__link--postimg" href="<?php echo $config->domain("posts?v={$sp['post_link']}") ?>" title="<?php echo $sp['post_title']; ?>">
                                <img class="saved-posts__img saved-posts__img--post" src="<?php echo $config->domain("app/uploads/posts/{$sp['pfile_name']}-md.{$sp['pfile_ext']}") ?>" alt="">
                            </a>
                            
                            <div class="saved-posts__content">
                                <h5 class="saved-posts__title saved-posts__title--post">
                                    <a class="saved-posts__link saved-posts__link--postitle" href="<?php echo $config->domain("posts?v={$sp['post_link']}") ?>" title="<?php echo $sp['post_title']; ?>">
                                        <?php
                                            echo $sp["post_title"];
                                        ?>
                                    </a>
                                </h5>

                                <div class="saved-posts__user-info">
                                    <img class="saved-posts__img saved-posts__img--user" src="<?php echo $config->domain("app/uploads/users/{$sp['ufile_name']}-sm.{$sp['ufile_ext']}") ?>" alt="<?php echo $sp['user_name']; ?>">
                                   
                                    <a class="saved-posts__link saved-posts__link--username"  href="<?php echo $config->domain("users/{$sp['user_name']}") ?>" title="<?php echo $sp['user_name']; ?>">
                                        <?php 
                                            echo $sp['user_name'];
                                        ?>
                                    </a>
                                </div>
                            
                            </div>

                            <div class="saved-posts__action">
                                <button class="saved-posts__btn saved-posts__btn--action">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>

                                <div class="saved-posts__dropdown">
                                    <!--Dropdown-->
                                    <ul class="saved-posts__dropdown-list">
                                        <li class="saved-posts__dropdown-item">
                                            <a class="saved-posts__dropdown-link saved-posts__dropdown-link--remove" role="button" data-sp_id="<?php echo $sp['sp_id']; ?>">
                                                <i class="fa fa-trash"></i>
                                                <span>Remove</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>

                <div class="row saved-posts__row saved-posts__row--noposts">
                    <div class="col-12 col-lg-6 saved-posts__col saved-posts__col--noposts">
                        <div class="saved-posts__noposts">
                            <h5 class="saved-posts__title saved-posts__title--noposts">
                                All Saved post will appear here
                            </h5>
                            
                            <a class="saved-posts__btn saved-posts__btn--browse" href="<?php echo $config->domain(); ?>" target="_blank" role="button">
                                Browse Posts
                            </a>
                        </div>
                    </div>
                </div>
            
            <?php endif; ?>
        </div>
    </div>

<?php
    include "app/views/dashboard/includes/footer.dash.php";
?>