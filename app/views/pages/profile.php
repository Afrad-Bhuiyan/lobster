<?php 
    include "includes/header.inc.php";

    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";

    //print_r($_GET);

?>

    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--profile">
            <div class="container main-wrap__container">
                <div class="main-wrap__profile-area profile-area">
                    <div class="profile-area__banner">
                        <img class="profile-area__img profile-area__img--bg" src="<?php echo $config->domain("assets/img/bg.jpg") ?>" alt="">
                    </div>
                    
                    <div class="profile-area__info">
                        <div class="profile-area__info-side profile-area__info-side--left">
                            <img class="profile-area__img profile-area__img--profile" src="<?php echo $config->domain("assets/img/afrad-bhuiyan.jpg") ?>" alt="">
                            
                            <div class="profile-area__info-text">
                                <h4 class="profile-area__title profile-area__title--username">afradbhuiyan</h4>
                                <span class="profile-area__text profile-area__text--subs">1.1M subscribers</span>
                            </div>
                        </div>

                        <div class="profile-area__info-side profile-area__info-side--right">
                            <button class="profile-area__btn profile-area__btn--subs" type="button">
                                Subscribe
                            </button>
                        </div>
                    </div>
                    
                    <div class="profile-area__bar profile-area__bar--top">
                        <ul>
                            <li>
                                <a href="#">Home</a>
                            </li>

                            <li>
                                <a href="#">Posts</a>
                            </li>

                            <li>
                                <a href="#">About</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h1><?php echo $data["content"] ?></h1>
                    </div>
                </div>
            </div>
        </section>
    </main>


<?php 
    include "includes/footer.inc.php"
?>