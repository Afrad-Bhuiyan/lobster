
<?php 
      include "app/views/dashboard/includes/header.dash.php";

?>
    <!--pp==publish post-->
    <div class="container ep-sec">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6 mx-auto ep-sec__wrap">

                <div class="row ep-sec__main-title">
                    <div class="col-12">
                        <h1>Edit Post</h1>
                        <?php 
                            
                            $single_post=$data["posts"]["fetch_all"][0];
                            $catagories=$data["catagories"];
                           
                        ?>
                    </div>
                </div>

                <form class="col-wrap full-width ep-sec__form ep-sec__form--edit">

                    <div class="row ep-sec__form-row ep-sec__form-row--dropzone">

                        <input class="ep-sec__input ep-sec__input--file" type="file" name="post_img_new"/>
                        <input class="ep-sec__input ep-sec__input--post-img-old" type="hidden" name="post_img_old" value="<?php echo $single_post["pfile_name"]; ?>|<?php echo $single_post["pfile_ext"]; ?>"/>
                        <input class="ep-sec__input ep-sec__input--post-link" type="hidden" name="post_id" value="<?php echo $single_post["post_id"] ?>">
                        <div class="col-12 ep-sec__form-col ep-sec__form-col--1">

                            <div class="ep-sec__form-label">
                                <p>The recommanded dimension is <span>870x580 Pixel</span></p>
                            </div>

                            <div class="ep-sec__dropzone">
                                <img class="ep-sec__dropzone-img" src="<?php echo $config->domain("app/uploads/posts/{$single_post['pfile_name']}-lg.{$single_post['pfile_ext']}"); ?>" alt="<?php echo $single_post["post_title"] ?>">
                                
                                <div class="ep-sec__dropzone-cap ep-sec__dropzone-cap--hide">
                                    <i class="fa fa-upload"></i>
                                    <span>Upload an Image</span>
                                </div>

                                <button class="ep-sec__form-btn ep-sec__form-btn--close" type="button">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row ep-sec__form-row ep-sec__form-row--title">

                        <div class="col-12 ep-sec__form-col ep-sec__form-col--1">
                            <div class="ep-sec__form-label ep-sec__form-label--title">
                                <p><span class="typed-letter"><?php echo strlen($single_post["post_title"] ); ?></span>/100</p>
                            </div>

                            <div class="ep-sec__input-wrap ep-sec__input-wrap--text">
                                <input class="ep-sec__input ep-sec__input--text ep-sec__input--title" type="text" name="post_title" placeholder="Post Title" value="<?php echo $single_post["post_title"] ?>" autocomplete="off">
                            </div>
                        </div>

                    </div>

                    <div class="row ep-sec__form-row ep-sec__form-row--catagory">
                        <div class="col-12 print_error_msg ep-sec__form-col ep-sec__form-col--1">
                            <div class="ep-sec__input-wrap ep-sec__input-wrap--select">                                
                                <select class="ep-sec__input ep-sec__input--select" name="post_category">
                                    <option value="">Select a category</option>
                                    <?php 

                                        if($catagories > 0){

                                            foreach($catagories as $key=>$catagory){

                                                if($catagory["cat_id"] == $single_post["cat_id"]){
                                                    echo "
                                                        <option selected value='{$catagory['cat_id']}'>
                                                            {$catagory['cat_name']}
                                                        </option>
                                                    ";

                                                }else{
                                                    echo "
                                                        <option value='{$catagory['cat_id']}'>
                                                            {$catagory['cat_name']}
                                                        </option>
                                                    ";
                                                }
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row ep-sec__form-row ep-sec__form-row--visibility">
                        <div class="col-12 ep-sec__form-col ep-sec__form-col--1">
                            <div class="ep-sec__input-wrap ep-sec__input-wrap--select">                                
                                <select class="ep-sec__input ep-sec__input--select" name="post_visibility">
                                    <option value="">Select post status</option>
                                    <?php 
                                    
                                        $post_status_pub="";
                                        $post_status_draft="";

                                        if($single_post["post_status"] == "published"){

                                            $post_status_pub = "selected";

                                        }elseif($single_post["post_status"] == "draft"){

                                            $post_status_draft = "selected";

                                        }
                                    
                                    ?>
                                    <option <?php echo $post_status_pub ?> value="published">Published</option>
                                    <option <?php echo $post_status_draft ?> value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row ep-sec__form-row ep-sec__form-row--editor">
                        <div class="col-12 ep-sec__form-col ep-sec__form-col--1">
                            <div id="editor"><?php echo $single_post["post_content"] ?></div>
                        </div>
                    </div>

                    <div class="row save-btn ep-sec__form-row ep-sec__form-row--publish">
                        <div class="col-12 ep-sec__form-col ep-sec__form-col--1">
                            <button class="btn btn-primary btn-full ep-sec__form-btn ep-sec__form-btn--save" type="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php 
        include "app/views/dashboard/includes/footer.dash.php";

?>