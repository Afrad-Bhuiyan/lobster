<?php
    include "includes/header.inc.php";

    // echo "<pre>";
    // print_r($_GET);
    // echo "</pre>";
?>

    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--fpw">
            <div class="container main-wrap__container main-wrap__container--fpw">
                <div class="row main-wrap__row main-wrap__row--fpw">
                    <div class="col-10 col-md-6 col-xl-5 main-wrap__col main-wrap__col--fpw">
                        <div class="main-wrap__col-wrap main-wrap__col-wrap--fpw">
                            <a class="main-wrap__link main-wrap__link--logo" href="<?php echo $config->domain(); ?>">
                                <img class="main-wrap__img main-wrap__img--logo"  src="<?php echo $config->domain("assets/img/lobster-logo-poppins.png") ?>" alt="">
                            </a>
                        
                            <form class="main-wrap__form ac-form ac-form--fpw"> 
                                <div class="ac-form__head ac-form__head--fpw">
                                    <h2 class="ac-form__title ac-form__title--fpw" >
                                        Forgot Password?
                                    </h2> 

                                    <p class="ac-form__subtitle ac-form__subtitle--fpw">
                                       Enter your E-mail address or Username associated with your account. we'll send you an E-mail with a recovery link
                                    </p> 
                                </div>
                                
                                <div class="ac-form__body ac-form__body--fpw">
                                    <div class="row ac-form__row ac-form__row--uname-email">
                                        <div class="col-12 ac-form__col ac-form__col--uname-email">
                                            <div class="ac-form__field ac-form__field--uname-email">
                                                <input class="ac-form__input ac-form__input--uname-email" type="text" name="uname-email" placeholder="Email or Username" autocomplete="off">
                                            </div>
                                        </div>
                                    </div><!--row-username-->

                                    <div class="row ac-form__row ac-form__row--fpw">
                                        <div class="col-12 ac-form__col ac-form__col--fpw">
                                           <button class="ac-form__btn ac-form__btn--recovery" type="submit">
                                                <i class="fa fa-envelope ac-form__btn-icon"></i>    
                                                <span class="ac-form__btn-text">Get the recovery link</span>
                                            </button>

                                            <button class="ac-form__btn ac-form__btn--loginpage" type="button">
                                                <i class="fa fa-user ac-form__btn-icon"></i>    
                                                <span class="ac-form__btn-text">Return to Login page</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php
    include "includes/footer.inc.php"
?>