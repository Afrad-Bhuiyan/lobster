<?php
    include "includes/header.inc.php";

    // echo "<pre>";
    // print_r($_GET);
    // echo "</pre>";
?>

    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--login">
            <div class="container main-wrap__container main-wrap__container--login">
                <div class="row main-wrap__row main-wrap__row--login">
                    <div class="col-10 col-md-6 col-xl-5 main-wrap__col main-wrap__col--login">
                        <div class="main-wrap__col-wrap main-wrap__col-wrap--login">
                            <a class="main-wrap__link main-wrap__link--logo" href="<?php echo $config->domain(); ?>">
                                <img class="main-wrap__img main-wrap__img--logo"  src="<?php echo $config->domain("assets/img/lobster-logo-poppins.png") ?>" alt="">
                            </a>
                            
                            <form class="main-wrap__form ac-form ac-form--login" action="#">
                                <div class="ac-form__head ac-form__head--login">
                                    <h2 class="ac-form__title ac-form__title--login">
                                        Login
                                    </h2> 

                                    <p class="ac-form__subtitle ac-form__subtitle--login">
                                        Don't have an account? Lets's <a class="ac-form__link ac-form__link--login" href="<?php echo $config->domain('accounts/signup'); ?>">create one</a>
                                    </p> 
                                </div>
                                
                                <div class="ac-form__body ac-form__body--login">
                                    <div class="row ac-form__row ac-form__row--uname-email">
                                        <div class="col-12 ac-form__col ac-form__col--uname-email">
                                            <div class="ac-form__field ac-form__field--uname-email">
                                                <input class="ac-form__input ac-form__input--uname-email" type="text" name="uname-email" placeholder="Email or Username" autocomplete="off">
                                            </div>
                                            
                                        </div>
                                    </div><!--row-username-->

                                    <div class="row ac-form__row ac-form__row--password">
                                        <div class="col-12 ac-form__col ac-form__col--password">
                                            <div class="ac-form__field ac-form__field--password">
                                                <input class="ac-form__input ac-form__input--password" type="password" name="password" placeholder="Password" autocomplete="off">
                                                <button class="ac-form__btn  ac-form__btn--eye" type="button">
                                                    <i class="fa fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div><!--row-password-->

                                    <div class="row ac-form__row ac-form__row--refpw">
                                        <div class="col-12 ac-form__col ac-form__col--refpw">
                                            <div class="ac-form__col-wrap ac-form__col-wrap--refpw">
                                                <input class="ac-form__input ac-form__input--remember" type="checkbox" name="" id="input-remember">
                                                <label class="ac-form__label ac-form__label--remember" for="input-remember"></label>
                                                <span class="ac-form__label-text">Remember Me</span>
                                            </div>

                                            <a class="ac-form__link ac-form__link--fpw" href="<?php echo $config->domain("accounts/forgot_password") ?>">Forgot Password?</a>
                                        </div>
                                    </div>
                                    
                                    <div class="row ac-form__row ac-form__row--login">
                                        <div class="col-12 ac-form__col ac-form__col--login">
                                           <button class="ac-form__btn ac-form__btn--login" type="submit">
                                                Login
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