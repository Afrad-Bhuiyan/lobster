
<?php 
      include "app/views/dashboard/includes/header.dash.php";

      $catagories=isset($data["catagories"]) ? $data["catagories"] : null;

    //   echo "<pre>";
    //   print_r($data);
    //   echo "</pre>";

?>
    <!--pp==publish post-->
    <div class="container pp-sec">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6 mx-auto pp-sec__wrap">

                <div class="row pp-sec__main-title">
                    <div class="col-12">
                        <h1>Publish a Post</h1>
                    </div>
                </div>

                <form class="col-wrap full-width pp-sec__form pp-sec__form--publish">

                    <div class="row pp-sec__form-row pp-sec__form-row--dropzone">

                        <input class="pp-sec__input pp-sec__input--file" type="file" name="post_img"/>

                        <div class="col-12 pp-sec__form-col pp-sec__form-col--1">

                            <div class="pp-sec__form-label">
                                <p>The recommanded dimension is <span>870x580 Pixel</span></p>
                            </div>

                            <div class="pp-sec__dropzone">
                                <img class="pp-sec__dropzone-img" src="<?php echo $config->domain("assets/img/post-placeholder.jpg"); ?>" alt="">
                                
                                <div class="pp-sec__dropzone-cap">
                                    <i class="fa fa-upload"></i>
                                    <span>Upload an Image</span>
                                </div>

                                <button class="pp-sec__form-btn pp-sec__form-btn--close pp-sec__form-btn--hide" type="button">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row pp-sec__form-row pp-sec__form-row--title">

                        <div class="col-12 pp-sec__form-col pp-sec__form-col--1">

                            <div class="pp-sec__form-label pp-sec__form-label--title">
                                <p><span class="typed-letter">0</span>/100</p>
                            </div>

                            <div class="pp-sec__input-wrap pp-sec__input-wrap--text">
                                <input class="pp-sec__input pp-sec__input--text" type="text" name="post_title" placeholder="Post Title" autocomplete="off">
                            </div>
                        </div>

                    </div>

                    <div class="row pp-sec__form-row pp-sec__form-row--catagory">
                        <div class="col-12 print_error_msg pp-sec__form-col pp-sec__form-col--1">
                            <div class="pp-sec__input-wrap pp-sec__input-wrap--select">                                
                                <select class="pp-sec__input pp-sec__input--select" name="post_category">
                                    <option value="">Select a category</option>
                                    <?php 

                                        if($catagories !== null){

                                            foreach($catagories as $cat_index=>$catagory){

                                                echo "
                                                    <option value='{$catagory['cat_id']}'>
                                                        {$catagory['cat_name']}
                                                    </option>
                                                ";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row pp-sec__form-row pp-sec__form-row--visibility">
                        <div class="col-12 pp-sec__form-col pp-sec__form-col--1">
                            <div class="pp-sec__input-wrap pp-sec__input-wrap--select">                                
                                <select class="pp-sec__input pp-sec__input--select" name="post_visibility">
                                    <option value="">Select post status</option>
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row pp-sec__form-row pp-sec__form-row--editor">
                        <div class="col-12 pp-sec__form-col pp-sec__form-col--1">
                            <div id="editor"></div>
                        </div>
                    </div>

                    <div class="row save-btn pp-sec__form-row pp-sec__form-row--publish">
                        <div class="col-12 pp-sec__form-col pp-sec__form-col--1">
                            <button class="btn btn-primary btn-full pp-sec__form-btn pp-sec__form-btn--publish" type="submit">Publish</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php 
        include "app/views/dashboard/includes/footer.dash.php";

?>