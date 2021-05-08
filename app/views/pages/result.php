<?php 
    include "includes/header.inc.php";

    $results=(isset($data["results"])) ? $data["results"] : null;
?>

    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--result">
            <div class="container main-wrap__container main-wrap__container--result">
                <div class="row main-wrap__row main-wrap__row--result">
                    <div class="col-12 col-md-10  mx-auto main-wrap__col main-wrap__col--result">
                        <?php 
                            if($results !== null):
                        ?>
                            <div class="main-title">
                                <h3 class="main-title__h">
                                    <span>Results for</span>
                                    <span>"<?php echo $data['search_query'] ?>"</span>
                                </h3>
                            </div>
                        
                            <div class="result-info">
                                <span class="result-info__total">
                                    <?php 
                                    
                                    echo ($data['total_results'] > 1) ? "{$data['total_results']} results found" : "{$data['total_results']} result found";
                                    ?>
                                
                                </span>
                            </div>

                        <?php 
                                foreach($results as $result_index=>$result): 

                                    $post_date=str_replace(", ", "-", $result['post_date']);
                                    $post_date=str_replace(" ","-",$post_date);
                                    $post_date_formated=str_replace("_"," ",$post_date);
                
                        ?>
                            <div class="row search-result search-result--row">
                                <div class="col-12 search-result__col search-result__col-1">
                                    <div class="search-result__wrap">
                                        <a class="search-result__link search-result__link--postimg" href="<?php echo $config->domain("posts?v={$result['post_link']}") ?>">
                                            <img class="search-result__img search-result__img--post"  src="<?php echo $config->domain("app/uploads/posts/{$result['pfile_name']}-md.{$result['pfile_ext']}") ?>" alt="">
                                        </a>
                                        
                                        <div class="search-result__content">
                                            <h4 class="search-result__title search-result__title--post">
                                                <a class="search-result__link search-result__link--posttitle" href="<?php echo $config->domain("posts?v={$result['post_link']}") ?>">
                                                    <?php echo $result["post_title"] ?>
                                                </a>
                                            </h4>

                                            <div class="search-result__meta">
                                                <ul class="search-result__meta-list">
                                                    <li class="search-result__meta-item search-result__meta-item--read">
                                                        <?php echo "{$result["post_read"]} read" ?>
                                                    </li>

                                                    <li class="search-result__meta-item search-result__meta-item--time">
                                                        <?php 
                                                            echo $functions->get_time_in_ago($post_date_formated);
                                                        ?>
                                                    </li>
                                                </ul>
                                            </div>
                        
                                            <div class="search-result__user-info">
                                                <img class="search-result__img search-result__img--user" src="<?php echo $config->domain("app/uploads/users/{$result['ufile_name']}-md.{$result['ufile_ext']}") ?>" alt="">
                                                <a class="search-result__link search-result__link--username" href="<?php echo $config->domain("users/{$result['user_name']}") ?>">
                                                    <?php echo $result["user_name"] ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        <?php 
                                endforeach;
                            else:

                                echo "
                                    <div class='result-msg'>
                                        <h3>
                                            <i class='fa fa-exclamation-circle'></i>
                                            <span>No results found</span>
                                        </h3>
                                    </div>
                                ";
                            endif; 
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </main>


   
<?php 
    include "includes/footer.inc.php"
?>