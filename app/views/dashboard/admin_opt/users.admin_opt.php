<?php 
    include "app/views/dashboard/includes/header.dash.php";
?>
    
    <div class="elements__user-list user-list">
        <div class="container user-list__container">
            <div class="row user-list__row">
                <div class="col-12 user-list__col">
                    <div class="user-list__col-wrap">
                        <div class="user-list__col-head">
                            <h3 class="user-list__title user-list__title--main">
                                All Users List 
                            </h3>

                            <div class="user-list__search-box">
                                <form class="user-list__form user-list__form--search">
                                    <div class="user-list__form-field">
                                        <input class="user-list__form-input user-list__form-input--search" type="text" name="" placeholder="Search...">
                                        <button class="user-list__btn user-list__btn--search" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>  
                
                        <div class="user-list__col-body">


                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </div>

<?php
    include "app/views/dashboard/includes/footer.dash.php";
?>