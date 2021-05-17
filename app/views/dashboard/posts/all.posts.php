
<?php 
    include "app/views/dashboard/includes/header.dash.php";

    $user_post_meta=(isset($data["user_post_meta"])) ? $data["user_post_meta"] : null;

    $total_posts=(isset($data["total_posts"])) ? $data["total_posts"] : null;

    // echo "<pre>";
    // print_r($user_post_meta);
    // echo "</pre>";
?>

    
    <div class="elements__my-posts my-posts">
        <div class="container my-posts__container">
            <div class="row my-posts__row">
                <div class="col-12 my-posts__col">
                    <div class="my-posts__col-wrap">
                        <div class="my-posts__col-head">
                            <h3 class="my-posts__title my-posts__title--main">
                                My posts
                            </h3>
                        
                            <div class="row my-posts__topBar">
                                <div class="col-12 col-md-4 my-posts__topBar-col  my-posts__topBar-col--link">
                                    <ul class="my-posts__topBar-list">
                                        <li class="my-posts__topBar-item">
                                            <a class="my-posts__topBar-link" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard/posts?sub_option=my_posts&filter=all"); ?>">
                                                <?php 
                                                    echo "All ({$user_post_meta['all_posts_num']})";
                                                ?>
                                            </a>
                                        </li>

                                        <li class="my-posts__topBar-item">
                                            <a class="my-posts__topBar-link"href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard/posts?sub_option=my_posts&filter=published"); ?>">
                                                <?php 
                                                    echo "Published ({$user_post_meta['published_posts_num']})";
                                                ?>
                                            </a>
                                        </li>

                                        <li class="my-posts__topBar-item">
                                            <a class="my-posts__topBar-link" href="<?php echo $config->domain("users/{$user_info['user_name']}/dashboard/posts?sub_option=my_posts&filter=draft"); ?>">
                                                <?php 
                                                    echo "Draft ({$user_post_meta['draft_posts_num']})";
                                                ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="col-6 col-md-4 my-posts__topBar-col  my-posts__topBar-col--filter">
                                    <form class="my-posts__form my-posts__form--filter">
                                        <div class="my-posts__form-field">
                                            <div class="my-posts__select-box">
                                                <select class="my-posts__input my-posts__input--filter" name="filter">
                                                    <option value="">Filter Posts</option>
                                                    <option value="most_read_posts">Most Read posts</option>
                                                    <option value="most_liked_posts">Most liked posts</option>
                                                    <option value="most_disliked_posts">Most disliked posts</option>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-6 col-md-4 my-posts__topBar-col  my-posts__topBar-col--seach">
                                    <form class="my-posts__form my-posts__form--search">
                                        <div class="my-posts__form-field">
                                            <input class="my-posts__input my-posts__input--search" type="text" name="" id=""placeholder="Search...">
                                            <button class="my-posts__btn my-posts__btn--search" type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                    <button class="my-posts__btn my-posts__btn--delete my-posts__btn--hide" type="button">
                                        <i class="fa fa-trash my-posts__btn-icon"></i>
                                        <span class="my-posts__btn-text">Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>  

                        <div class="my-posts__col-body">
                          
                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </div>
<?php 
       include "app/views/dashboard/includes/footer.dash.php";

?>