<?php
    include "includes/header.inc.php";

    // echo "<pre>"
    // print_r($data);
    // echo "</pre>";

    // print_r($_GET);
;
?>

    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--profile">
            <div class="container main-wrap__container">
                <div class="main-wrap__profile-area profile-area">
                    <div class="profile-area__banner">
                        <img class="profile-area__img profile-area__img--bg" src="<?php echo $config->domain("app/uploads/users/bg/lobster-default-bg.jpg") ?>" alt="">
                    </div>
                    
                    <div class="profile-area__info">
                        <div class="profile-area__info-side profile-area__info-side--left">
                            <div class="dummy-img dummy-img--profile-main dummy-img--lightblue">
                                <span class="dummy-img__text">a</span>
                            </div>
                            <!-- <img class="profile-area__img profile-area__img--profile" src="<?php //echo $config->domain("assets/img/afrad-bhuiyan.jpg") ?>" alt=""> -->
                            
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
                        <ul class="profile-area__bar-list profile-area__bar-list--nav"> 
                            <li class="profile-area__bar-item profile-area__bar-item--nav">
                                <a class="profile-area__link profile-area__link--bar" href="#">
                                    Home
                                </a>
                            </li>

                            <li class="profile-area__bar-item profile-area__bar-item--nav">
                                <a class="profile-area__link profile-area__link--bar profile-area__link--active" href="#">
                                    Posts
                                </a>
                            </li>
                            
                            <li class="profile-area__bar-item profile-area__bar-item--nav">
                                <a class="profile-area__link profile-area__link--bar" href="#">
                                    About
                                </a>
                            </li>
                        </ul>

                        <form class="profile-area__form profile-area__form--search" action="">
                            <div class="profile-area__form-wrap">
                                <input class="profile-area__form-input profile-area__form-input--search" type="text" name="" placeholder="Search...">
                                <button class="profile-area__btn profile-area__btn--search" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="profile-area__content profile-area__content--dynamic">
                        <div class="profile-area__posts profile-area__posts--recent">
                            <h5 class="profile-area__title profile-area__title--recent">
                                Recent Posts
                            </h5>

                            <div class="owl-carousel profile-area__carousel profile-area__carousel--recent">
                                <?php for($a=1; $a < 20; $a++): ?>
                                <div class="profile-area__post profile-area__post--recent">
                                    <div class="profile-area__post-wrap">
                                        <div class='profile-area__post-thumb'>
                                            <a class="profile-area__link profile-area__link--postimg" href='#'>
                                                <img class="profile-area__img profile-area__img--post" src="<?php echo $config->domain("assets/img/post-1.jpg") ?>" alt="">
                                            </a>
                                        </div>

                                        <div class="profile-area__post-info">
                                            <h6 class="profile-area__post-title">
                                                <a class="profile-area__link profile-area__link--posttitle" href="">
                                                    Lorem ipsum dolor sit amet consectetur...
                                                </a>
                                            </h6>
                                            <div class="profile-area__post-meta">
                                                <span class="profile-area__post-view">0 read</span>
                                                <i class="fa fa-circle profile-area__icon profile-area__icon--circle"></i>
                                                <span class="profile-area__time">5 hours ago</span>
                                            </div>
                                            <div class="profile-area__post-author">  
                                                <img class="profile-area__img profile-area__img--user" src="<?php echo $config->domain("assets/img/afrad-bhuiyan.jpg") ?>" alt="">
                                                <a class="profile-area__link profile-area__link--uname" href="">
                                                    afradbhuiyan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>


<?php 
    include "includes/footer.inc.php"
?>