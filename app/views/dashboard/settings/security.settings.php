<?php 
    include "app/views/dashboard/includes/header.dash.php";
?>


    <div class="elements__secu-sett secu-sett">
        <div class="container secu-sett__container">
            <div class="row secu-sett__row">
                <div class="col-12 col-lg-8 secu-sett__col">
                    <div class="secu-sett__col-wrap">
                        <div class="secu-sett__head">
                            <h3 class="secu-sett__title">
                                Security Settings
                            </h3>
                        </div>

                        <div class="secu-sett__body">
                            <form class="secu-sett__form secu-sett__form--changePwd" action="">
                                <div class="secu-sett__form-head">
                                    <h4 class="secu-sett__form-title">
                                        Change Password
                                    </h4>
                                    <p class="secu-sett__form-subtitle">
                                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Laudantium pariatur distinctio perferendis ullam veritatis non.
                                    </p>
                                </div>

                                <div class="secu-sett__form-body">
                                    <div class="row secu-sett__form-row row secu-sett__form-row--currPass">
                                        <div class="col-12 col-md-3 col-lg-4 col-xl-3 secu-sett__form-col secu-sett__form-col--label">
                                            <label class="secu-sett__form-label" for="">
                                                <span>Current Password</span>
                                                <strong>:</strong>
                                            </label>
                                        </div>

                                        <div class="col-12 col-md-9 col-lg-8 col-xl-9 secu-sett__form-col secu-sett__form-col--field">
                                            <div class="secu-sett__form-field secu-sett__form-field--currPass">
                                                <div class="secu-sett__form-input-wrap">
                                                    <input tabindex="1" class="secu-sett__form-input" type="password" name="current_password">
                                                    <button class="secu-sett__btn secu-sett__btn--eye" type="button">
                                                        <i class="fa fa-eye-slash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--row current password-->

                                    <div class="row secu-sett__form-row row secu-sett__form-row--newPass">
                                        <div class="col-12 col-md-3 col-lg-4 col-xl-3 secu-sett__form-col secu-sett__form-col--label">
                                            <label class="secu-sett__form-label" for="">
                                                <span>New Password</span>
                                                <strong>:</strong>
                                            </label>
                                        </div>

                                        <div class="col-12 col-md-9 col-lg-8 col-xl-9 secu-sett__form-col secu-sett__form-col--field">
                                            <div class="secu-sett__form-field secu-sett__form-field--newPass">
                                                <div class="secu-sett__form-input-wrap">
                                                    <input  tabindex="2"  class="secu-sett__form-input" type="password" name="new_password">
                                                    <button class="secu-sett__btn secu-sett__btn--eye" type="button">
                                                        <i class="fa fa-eye-slash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--row new password-->

                                    <div class="row secu-sett__form-row row secu-sett__form-row--conPass">
                                        <div class="col-12 col-md-3 col-lg-4 col-xl-3 secu-sett__form-col secu-sett__form-col--label">
                                            <label class="secu-sett__form-label" for="">
                                                <span>Confirm Password</span>
                                                <strong>:</strong>
                                            </label>
                                        </div>

                                        <div class="col-12 col-md-9 col-lg-8 col-xl-9 secu-sett__form-col secu-sett__form-col--field">
                                            <div class="secu-sett__form-field secu-sett__form-field--conPass">
                                                <div class="secu-sett__form-input-wrap">
                                                    <input tabindex="3"  class="secu-sett__form-input" type="password" name="confirm_password">
                                                    
                                                    <button class="secu-sett__btn secu-sett__btn--eye" type="button">
                                                        <i class="fa fa-eye-slash"></i>
                                                    </button>
                                                </div>    
                                            </div>
                                        </div>
                                    </div><!--row confirm password-->

                                    <div class="row secu-sett__form-row row secu-sett__form-row--changePwd">
                                        <div class="col-12 secu-sett__form-col secu-sett__form-col--field">
                                            <div class="secu-sett__form-field secu-sett__form-field--changePwd">
                                                <button tabindex="4" class="secu-sett__btn secu-sett__btn--changePwd" type="submit">
                                                    Change Password
                                                </button>
                                            </div>
                                        </div>
                                    </div><!--row change password-->
                                </div>
                            </form>

                            <form class="secu-sett__form secu-sett__form--deactivation" action="">
                                <div class="secu-sett__form-head">
                                    <h4 class="secu-sett__form-title">
                                        Account Deactivation
                                    </h4>
                                    <p class="secu-sett__form-subtitle">
                                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Laudantium pariatur distinctio perferendis ullam veritatis non.
                                    </p>
                                </div>

                                <div class="secu-sett__form-body">
                                    <div class="row secu-sett__form-row row secu-sett__form-row--deactivation">
                                        <div class="col-12 col-md-3 col-lg-4 col-xl-3 secu-sett__form-col secu-sett__form-col--label">
                                            <label class="secu-sett__form-label" for="">
                                                <span>I am leaving...</span>
                                                <strong>:</strong>
                                            </label>
                                        </div>

                                        <div class="col-12 col-md-9 col-lg-8 col-xl-9 secu-sett__form-col secu-sett__form-col--field">
                                            <div class="secu-sett__form-field secu-sett__form-field--deactivation">
                                                <div class="secu-sett__form-input-wrap secu-sett__form-input-wrap--select">
                                                    <select tabindex="5" class="secu-sett__form-input secu-sett__form-input--select" name="reason">
                                                        <option value="">Choose a reason</option>
                                                        <option value="I didn't like your website">I didn't like your website</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--row current password-->

                                    <div class="row secu-sett__form-row row secu-sett__form-row--deleteAcc">
                                        <div class="col-12 secu-sett__form-col secu-sett__form-col--field">
                                            <div class="secu-sett__form-field secu-sett__form-field--deleteAcc">
                                                <button tabindex="6" class="secu-sett__btn secu-sett__btn--deleteAcc secu-sett__btn--disable" type="button">
                                                    delete the account
                                                </button>
                                            </div>
                                        </div>
                                    </div><!--row delete account-->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php 
    include "app/views/dashboard/includes/footer.dash.php";

?>