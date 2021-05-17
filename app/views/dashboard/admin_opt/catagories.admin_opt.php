<?php 
    include "app/views/dashboard/includes/header.dash.php";
?>
    
    <div class="elements__cat-list cat-list">
        <div class="container cat-list__container">
            <div class="row cat-list__row">
                <div class="col-12 col-md-10 col-lg-8 cat-list__col">
                    <div class="cat-list__col-wrap">
                        <div class="cat-list__col-head">
                            <h3 class="cat-list__title cat-list__title--main">
                                All Catagories 
                            </h3>
                        </div>

                        <div class="cat-list__col-body">
                            <div class="cat-list__topBar">
                                <button class="cat-list__btn cat-list__btn--add" type="button" title="Add a catagory">
                                    <i class="fa fa-plus"></i>
                                    <span>Add a catagory</span>
                                </button>
                            </div>

                            <div class="cat-list__tbl-wrap">
                                <table class="cat-list__tbl">
                                    <thead class="cat-list__thead">
                                        <tr class="cat-list__tr cat-list__tr--thead">
                                            <th class="cat-list__th cat-list__th--catId">
                                                <div class="cat-list__th-wrap">
                                                    <span class="cat-list__txt cat-list__txt--th">
                                                        ID
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="cat-list__th cat-list__th--catName">
                                                <div class="cat-list__th-wrap">
                                                    <span class="cat-list__txt cat-list__txt--th">
                                                        Catagory (10)
                                                    </span>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="cat-list__tbody">
                                        <?php for($i=1; $i <= 10; $i++): ?>
                                        <tr class="cat-list__tr cat-list__tr--tbody">
                                            <td class="cat-list__td cat-list__td--catId">
                                                <div class="cat-list__td-wrap">
                                                    <span class="cat-list__txt cat-list__txt--td">
                                                       1
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="cat-list__td cat-list__td--catName">
                                                <div class="cat-list__td-wrap">
                                                    <div class="cat-list__catName">
                                                        <span class="cat-list__txt cat-list__txt--td">
                                                              Web development (10)
                                                        </span>
                                                    </div>

                                                    <div class="cat-list__dropdown cat-list__dropdwon--catAct">
                                                        <button class="cat-list__btn cat-list__btn--dropdown-toggle">
                                                            <svg height="1.3rem" viewBox="-192 0 512 512" width="1.3rem" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="m128 256c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0"/>
                                                                <path d="m128 64c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0"/>
                                                                <path d="m128 448c0 35.347656-28.652344 64-64 64s-64-28.652344-64-64 28.652344-64 64-64 64 28.652344 64 64zm0 0"/>
                                                            </svg>
                                                        </button>

                                                        <?php 
                                                            if($i == 2):
                                                        ?>
                                                        <div class="cat-list__opts">                                                                
                                                            <ul class="cat-list__opt-list">
                                                                <li class="cat-list__opt-item">
                                                                    <a class="cat-list__opt-link" role="button">
                                                                        <i class="fa fa-pencil"></i>
                                                                        <span>Rename</span>
                                                                    </a>
                                                                </li>

                                                                <li class="cat-list__opt-item">
                                                                    <a class="cat-list__opt-link" role="button">
                                                                        <i class="fa fa-trash"></i>
                                                                        <span>Delete</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endfor; ?>
                                    </tbody>    
                                </table>
                            </div>               
                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </div>

<?php
    include "app/views/dashboard/includes/footer.dash.php";
?>