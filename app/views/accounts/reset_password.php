<?php
    include "includes/header.inc.php";

    // echo "<pre>";
    // print_r($_GET);
    // echo "</pre>";
?>

    <main class="main-wrap">
        <section class="main-wrap__sec main-wrap__sec--resetpwd">
            <div class="container main-wrap__container main-wrap__container--resetpwd">
                <div class="row main-wrap__row main-wrap__row--resetpwd">
                    <div class="col-10 col-md-6 col-xl-5 main-wrap__col main-wrap__col--resetpwd">
                        <div class="main-wrap__col-wrap main-wrap__col-wrap--resetpwd">
                            <a class="main-wrap__link main-wrap__link--logo" href="<?php echo $config->domain(); ?>">
                                <img class="main-wrap__img main-wrap__img--logo"  src="<?php echo $config->domain("assets/img/lobster-logo-poppins.png") ?>" alt="">
                            </a>
                            
                            <form class="main-wrap__form ac-form ac-form--resetpwd">
                                <div class="ac-form__head ac-form__head--resetpwd">
                                    <h2  class="ac-form__title ac-form__title--resetpwd">
                                        Reset Password
                                    </h2> 

                                    <div class="ac-form__info">
                                        <p class="ac-form__subtitle ac-form__subtitle--resetpwd">
                                            In order to protect your account, make sure your password meets these requirments
                                        </p>
                                        
                                        <ul class="ac-form__info-list">
                                            <li class="ac-form__info-item">
                                                Minimum length must be 8
                                            </li>

                                            <li class="ac-form__info-item">
                                                Maximum length must be 20
                                            </li>

                                            <li class="ac-form__info-item">
                                                Must contain at least a single charecter from each group <br> <strong>(a-z|A-Z)(0-9)(!@#$%^&*()\-_.)</strong>
                                            </li>
                                        </ul>
                                    </div> 
                                </div>
                                
                                <div class="ac-form__body ac-form__body--resetpwd">
                                    <div class="row ac-form__row ac-form__row--cre-pass">
                                        <div class="col-12 ac-form__col ac-form__col--cre-pass">
                                            <div class="ac-form__field ac-form__field--cre-pass">
                                                <input type="hidden" name="selector" value="<?php echo $_GET["selector"]; ?>">
                                                <input class="ac-form__input ac-form__input--cre-pass" type="password" name="cre-pass" placeholder="Create a password" autocomplete="off">
                                                <button class="ac-form__btn  ac-form__btn--eye" type="button">
                                                    <i class="fa fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div><!--row-create-password-->

                                    <div class="row ac-form__row ac-form__row--con-pass">
                                        <div class="col-12 ac-form__col ac-form__col--con-pass">
                                            <div class="ac-form__field ac-form__field--con-pass">
                                                <input class="ac-form__input ac-form__input--con-pass" type="password" name="con-pass" placeholder="Confirm the password" autocomplete="off">
                                                <button class="ac-form__btn ac-form__btn--eye" type="button">
                                                    <i class="fa fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div><!--row-confirm-password-->

                                    
                                    <div class="row ac-form__row ac-form__row--resetpwd">
                                        <div class="col-12 ac-form__col ac-form__col--resetpwd">
                                           <button class="ac-form__btn ac-form__btn--resetpwd" type="submit">
                                                Reset Password
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