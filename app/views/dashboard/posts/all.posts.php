
<?php 
    include "app/views/dashboard/includes/header.dash.php";

    $user_post_meta=(isset($data["user_post_meta"])) ? $data["user_post_meta"] : null;

    $total_posts=(isset($data["total_posts"])) ? $data["total_posts"] : null;
    

    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";
?>

    <div class="elements__my-posts my-posts" data-load_posts="<?php echo ($total_posts > 0) ? 1 : 0; ?>"> 
        <?php 
            if($total_posts > 0):
        ?>
        <div class="container my-posts__container my-posts__container--post"> 
            <div class="my-posts__head d-flex">
                 <h3 class="my-posts__title">My Posts</h3>
            </div>

            <div class="row my-posts__topbar-row">
                <div class="col-12 col-md-4 my-posts__topbar-col my-posts__topbar-col--link">
                    <ul class="my-posts__topbar-list">
                        <li class="my-posts__topbar-item">
                            <a class="my-posts__topbar-link" href="<?php echo $config->domain("users/{$_SESSION['user_name']}/dashboard?posts=myposts&filter=all"); ?>">
                                <?php 
                                    echo "All ({$user_post_meta['all_posts_num']})";
                                ?>
                            </a>
                        </li>

                        <li class="my-posts__topbar-item">
                            <a class="my-posts__topbar-link"href="<?php echo $config->domain("users/{$_SESSION['user_name']}/dashboard?posts=myposts&filter=published"); ?>">
                                <?php 
                                    echo "Published ({$user_post_meta['published_posts_num']})";
                                ?>
                            </a>
                        </li>

                        <li class="my-posts__topbar-item">
                            <a class="my-posts__topbar-link" href="<?php echo $config->domain("users/{$_SESSION['user_name']}/dashboard?posts=myposts&filter=draft"); ?>">
                                <?php 
                                    echo "Draft ({$user_post_meta['draft_posts_num']})";
                                ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-6 col-md-4 my-posts__topbar-col my-posts__topbar-col--filter">
                    <div class="my-posts__select-box">
                        <select class="my-posts__select-input my-posts__select-input--topbar-filter" name="name">
                            <option value="">Filter Posts</option>
                            <option value="most_read_posts">Most Read posts</option>
                            <option value="most_liked_posts">Most liked posts</option>
                            <option value="most_disliked_posts">Most disliked posts</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-6 col-md-4 my-posts__topbar-col my-posts__topbar-col--filter">
                    <form class="my-posts__form my-posts__form--search">
                        <div class="my-posts__form-field my-posts__form-field--search">
                            <input class="my-posts__input my-posts__input--search" type="text" name="" id=""placeholder="Search...">
                        </div>

                        <div class="my-posts__form-field my-posts__form-field--submit">
                            <button class="my-posts__btn my-posts__btn--search" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <button class="my-posts__btn my-posts__btn--delete my-posts__btn--hide" type="button">
                        <span>Delete</span>
                    </button>
                </div>
            </div><!--TopBar-->
            
            <div class="row my-posts__post my-posts__post--row">
                <div class="col-12 my-posts__post-col">
                </div>
            </div>
                    
        </div>
        <?php else: ?>
            <!--No Posts found-->
            <div class="container my-posts__container my-posts__container--msg">
                <div class="my-posts__msg my-posts__msg--noposts">
                    <h4 class="my-posts__msg-title my-posts__msg-title--noposts">You havan't pulished any post yet</h4>
                        <a class="my-posts__btn my-posts__btn--lets-pub" href="" role="button">
                            Let's publish a post
                        </a>
                    </h4>
                </div>
            </div>
        <?php endif;?>
    </div>
<?php 
       include "app/views/dashboard/includes/footer.dash.php";

?>