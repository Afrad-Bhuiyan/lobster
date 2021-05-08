<?php
    include "includes/header.inc.php";

    // echo "<pre>";
    // print_r($_GET);
    // echo "</pre>";
?>
    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--signup">
            <div class="container main-wrap__container main-wrap__container--signup">
                <div class="row main-wrap__row main-wrap__row--signup">
                    <div class="col-10 col-md-6 col-xl-5 main-wrap__col main-wrap__col--signup">
                        <div class="main-wrap__col-wrap main-wrap__col-wrap--signup">
                            <a class="main-wrap__link main-wrap__link--logo" href="<?php echo $config->domain(); ?>">
                                <img class="main-wrap__img main-wrap__img--logo"  src="<?php echo $config->domain("assets/img/lobster-logo-poppins.png") ?>" alt="">
                            </a>
                            
                            <form class="main-wrap__form ac-form ac-form--signup" action="#">
                                <div class="ac-form__head ac-form__head--signup">
                                    <h2 class="ac-form__title ac-form__title--signup">
                                        Signup
                                    </h2> 

                                    <p class="ac-form__subtitle ac-form__subtitle--signup">
                                        Already have an account? Lets's <a class="ac-form__link ac-form__link--signup" href="<?php echo $config->domain('accounts/login'); ?>">Login</a>
                                    </p> 
                                </div>
                                
                                <div class="ac-form__body ac-form__body--signup">
                                    <div class="row ac-form__row ac-form__row--fname-lname">

                                        <div class="col-12 col-lg-6 ac-form__col ac-form__col--fname">
                                            <div class="ac-form__field ac-form__field--fname">
                                                <input class="ac-form__input ac-form__input--fname" type="text" name="fname" placeholder="First Name" autocomplete="off" data-valid="0">
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-6 ac-form__col ac-form__col--lname">
                                            <div class="ac-form__field ac-form__field--lname">
                                                <input class="ac-form__input ac-form__input--lname" type="text" name="lname" placeholder="Last Name" autocomplete="off" data-valid="0">
                                            </div>
                                        </div>
                                    </div><!--row-fname-lname-->

                                    <div class="row ac-form__row ac-form__row--username">
                                        <div class="col-12 ac-form__col ac-form__col--username">
                                            
                                            <div class="ac-form__field ac-form__field--username">
                                                <input class="ac-form__input ac-form__input--username" type="text" name="username" placeholder="Choose a username" autocomplete="off" data-valid="0">
                                            </div>
                                        </div>
                                    </div><!--row-username-->
                                
                                    <div class="row ac-form__row ac-form__row--email">
                                        <div class="col-12 ac-form__col ac-form__col--email">
                                            <div class="ac-form__field ac-form__field--email">
                                                <input class="ac-form__input ac-form__input--email" type="text" name="email" placeholder="E-mail Address" autocomplete="off" data-valid="0">
                                            </div>
                                        </div>
                                    </div><!--row-email-->

                                    <div class="row ac-form__row ac-form__row--cre-pass">
                                        <div class="col-12 ac-form__col ac-form__col--cre-pass">                                            
                                            <div class="ac-form__field ac-form__field--cre-pass">
                                                <input class="ac-form__input ac-form__input--cre-pass" type="password" name="cre-pass" placeholder="Create a Password" autocomplete="off" data-valid="0">
                                            </div>
                                        </div>
                                    </div><!--row-cre-pass-->
                                    
                                    <div class="row ac-form__row ac-form__row--con-pass">
                                        <div class="col-12 ac-form__col ac-form__col--con-pass">
                                            <div class="ac-form__field ac-form__field--con-pass">
                                                <input class="ac-form__input ac-form__input--con-pass" type="password" name="con-pass" placeholder="Confirm the Password" autocomplete="off" data-valid="0">
                                            </div>
                                        </div>
                                    </div><!--row-con-pass-->
                                    
                                    <div class="row ac-form__row ac-form__row--signup">
                                        <div class="col-12 ac-form__col ac-form__col--signup">
                                           <button class="ac-form__btn ac-form__btn--signup ac-form__btn--disabled" type="submit">
                                                Sign Up
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