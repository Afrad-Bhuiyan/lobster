 $(function(){

    const script_type=$("#main-js").data("type");

    //catch the function dynamically
    function domain(){

        let output;
        
        if(location.hostname !== "projects.afradbhuiyan.com"){

            //set the output variable  to `http://localhost/lobster/`
            output=`${location.origin}/lobster/`;
            

        }else{

            //set the output variable  to `https||http://projects.afradbhuiyan.com/lobster/`
            output=`${location.origin}/lobster/`;
        }

        return output;
        
    }

    //function for loading catagory wise post in home and single blog page
    function load_posts(param){
        /*
            param={
                catagory_id:0, (required)
                load_position:"sidebar",(required)
                post_link:post_link (required for single blog page),
                remove_spinner_after:150(optional parameter)
            }
        */
        const load_position=param.load_position

        const remove_spinner_after=param.remove_spinner_after

        $.ajax({
            url:`${domain()}ajax_pages/load_posts`,
            method:"POST",
            data:param,
            dataType:"json",
            beforeSend:function(){

                if(load_position == "sidebar"){

                    $(".loading-spinner--sidebar").remove();
                    $(".sidebar-lg__content").append(`
                        <div class="loading-spinner loading-spinner--sidebar">
                            <div class="loading-spinner__circle loading-spinner__circle--sidebar"></div>
                        </div>
                    `);

                }else if(load_position == "home"){

                    $(".loading-spinner--home").remove();

                    $(".main-wrap__container--post").append(`
                        <div class="loading-spinner loading-spinner--home">
                            <div class="loading-spinner__circle loading-spinner__circle--home">
                            </div>
                        </div>
                    `);
                }
            },
            success:function(response){

                if(response){

                    if(load_position == "sidebar"){

                        $(".sidebar-lg__sp").remove();
    
                        $(".sidebar-lg__content").append(response.html);

                        if(remove_spinner_after){

                            setTimeout(function(){

                                $(".loading-spinner--sidebar").remove();

                            },remove_spinner_after);
                          
                        }else{

                            $(".loading-spinner--sidebar").remove();
                        }
    
                    }else if(load_position == "home"){
                        
                        $(".main-wrap__row").remove(); 

                        $(".main-wrap__container--post").append(response.html);

                        if(remove_spinner_after){

                            setTimeout(function(){

                                $(".loading-spinner--home").remove();

                            },remove_spinner_after);
                          
                        }else{

                            $(".loading-spinner--home").remove();
                        }

                      
                    }
                }

               // console.log(response);
            }
        });
    }

    //update the title tag
    function update_title_tag_and_bell(param){

        const to_user_id=param.to_user_id;

        $.ajax({
            url:`${domain()}ajax_pages/update_title_tag_and_bell`,
            method:"POST",
            data:{
                to_user_id:to_user_id,
                title_tag:$("title").text()
            },
            dataType:"json",
            success:function(response){

                if(response){
                    //set the title tag
                    $("title").text(response.title_tag);

                    //romove the previous bell button with badge
                    $(".ph-nav__btn--bell").remove(); 

                    //set the bell button with new badge
                    $(".ph-nav__dropdown-toggle--nf").append(response.bell_icon);
                }
            }
        });
    }

    //call the function when a popup message needs to be appeared
    function show_popup_msg(param){
        /*
            param={
                text:"<strong>Success :</strong>\r\n <span>2 posts deleted</span>",
                position_class:"popup-msg--top-right",
                type_class:"popup-msg--success"
            }
         */

        const msg_template=`
            <div class="popup-msg ${param.position_class} ${param.type_class}">
                <div class="popup-msg__wrap">
                    <div class="popup-msg__content">
                        <div class="popup-msg__icon"></div>

                        <div class="popup-msg__text">
                            ${param.text}
                        </div>

                        <button class="popup-msg__btn popup-msg__btn--cross" type="button">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        //first remove popup-msg
        $(".popup-msg").remove();
        
        $("body").append(msg_template);
        
        //appear the popup msg with transition
        $(".popup-msg").fadeIn(500);
        
        //remove the messge when clicks on popup cross button
        $(document).on("click",".popup-msg__btn--cross",function(){
            $(this).parents(".popup-msg").remove();
        });
        
        //Remove the message after 3s
        setTimeout(function(){

            $(".popup-msg").fadeOut(1000,function(){

                //first remove popup-msg
                if($(".popup-msg").length > 0){

                    $(".popup-msg").remove();
                }
            });

        },3000)

    }

    /*=========================
    Code for Header part starts
    ==========================*/

    //store the search form
    const search_form=$(".ph-nav__form--search");

    //store the search input
    const search_input=$(".ph-nav__form-input--search");

    //store submit button
    const search_submit_btn=$(".ph-nav__btn--form-submit");
    
    search_submit_btn.on("click",function(e){

        e.preventDefault();

        if(search_input.val() !== ""){

            search_form.submit();
        }        
    });

    
    //store the small search box
    const search_box=$(".ph-nav__search-box");

    const search_sm_btn=$(".ph-nav__btn--search-sm");
    
    search_sm_btn.on("click",function(e){

        if(!search_box.hasClass("ph-nav__search-box--show")){

            //add .show class in small search box
            search_box.addClass("ph-nav__search-box--show");
            
        }else{
            
            //remove .show class from small search box
            search_box.removeClass("ph-nav__search-box--show");
        }
    });

    //store the arrow button form closing the small form
    const from_close_btn=$(".ph-nav__btn--form-close");

    from_close_btn.on("click",function(){

        if(search_box.hasClass("ph-nav__search-box--show")){

            //remove .show class in small search box
            search_box.removeClass("ph-nav__search-box--show");
        }
    });

    /* 
        Jquery plugin for adding the autocomplet feture
        plugin link: https://github.com/devbridge/jQuery-Autocomplete 
    */
    $(".ph-nav__form-input--search").autocomplete({

        serviceUrl:`${domain()}result`,
        type:"POST",
        dataType:"json",
        paramName:"search_query",
        width:"100%",
        appendTo:".ph-nav__form-field--search",
        onSelect:function(selected_suggestion){

            search_form.submit();
        },
        transformResult:function(response){

            return {
                suggestions:$.map(response, function(entry){

                   return {
                        value: entry.post_title
                    }
                })
            }
        }
    });



    const dropdown_toggle=$(".ph-nav__dropdown-toggle");

    dropdown_toggle.on("click",function(){

        if($(this).hasClass("ph-nav__dropdown-toggle--nf")){

            const that=$(this);

            //Remove the previously opened dropdown content
            if($(".ph-nav__dropdown-content--menu").hasClass("ph-nav__dropdown-content--block")){

                $(".ph-nav__dropdown-content--menu").removeClass("ph-nav__dropdown-content--block");

                $(".ph-nav__dropdown-content--menu").removeClass("ph-nav__dropdown-content--show");
            }

            const dropdown_content=$(".ph-nav__dropdown-content--nf");

            if(!dropdown_content.hasClass("ph-nav__dropdown-content--block")){
                
                //Appear the dropdown content
                dropdown_content.addClass("ph-nav__dropdown-content--block");
                
                setTimeout(function(){

                    dropdown_content.addClass("ph-nav__dropdown-content--show");

                    $.ajax({
                        url:`${domain()}ajax_pages/load_notifications`,
                        method:"POST",
                        data:{
                            to_user_id:that.data("to_user_id")
                        },
                        beforeSend:function(){

                            $(".loading-spinner").remove();

                            $(".ph-nav__dropdown-content-wrap--nf").append(`
                                <div class="loading-spinner loading-spinner--nf">
                                    <div class="loading-spinner__circle loading-spinner__circle--nf">

                                    </div>
                                </div>
                            `)
                        
                        },
                        success:function(response){

                            //remove the dropdown body
                            $(".ph-nav__dropdown-content--nf").find(".ph-nav__dropdown-body--nf").remove();
                            
                            //remove the dropdown footer z=
                            $(".ph-nav__dropdown-content--nf").find(".ph-nav__dropdown-footer").remove();

                            //remove the loading spinner
                            $(".loading-spinner").remove();

                            //append all notification in dropdown-content-wrap
                            $(".ph-nav__dropdown-content-wrap--nf").append(response);

                            //update title tag and notification badge
                            update_title_tag_and_bell({
                                to_user_id:that.data("to_user_id"),
                            });

                          // console.log(response);
                        }
                    })
    
                },50);

            }else{

                //Disappear the dropdown content
                dropdown_content.removeClass("ph-nav__dropdown-content--show");

                setTimeout(function(){

                    dropdown_content.removeClass("ph-nav__dropdown-content--block");
                
                },155);

            }

        }else if($(this).hasClass("ph-nav__dropdown-toggle--menu")){

            //Remove the previously opened dropdown content
            if($(".ph-nav__dropdown-content--nf").hasClass("ph-nav__dropdown-content--block")){

                $(".ph-nav__dropdown-content--nf").removeClass("ph-nav__dropdown-content--block");

                $(".ph-nav__dropdown-content--nf").removeClass("ph-nav__dropdown-content--show");
            }

            const dropdown_content=$(".ph-nav__dropdown-content--menu");

            if(!dropdown_content.hasClass("ph-nav__dropdown-content--block")){

                //Appear the dropdown content
                dropdown_content.addClass("ph-nav__dropdown-content--block");
                
                setTimeout(function(){

                    dropdown_content.addClass("ph-nav__dropdown-content--show");
    
                },50);

            }else{

                //Disappear the dropdown content
                dropdown_content.removeClass("ph-nav__dropdown-content--show");

                setTimeout(function(){

                    dropdown_content.removeClass("ph-nav__dropdown-content--block");
                
                },155);
            }
        }

    });

    /*======================
    Code for Header part ends
    ========================*/

    if(script_type == "home"){


        //first load all posts in home page
        // load_posts({
        //     catagory_id:0,
        //     load_position:"home"
        // });
    
        /* Add functionality in catagory carousel */

        const track=document.querySelector(".ph-filter__slider-track")

        //store the first element in carousel
        const track_first_btn=track.firstElementChild;

        //store the last element in carousel
        const track_last_btn=track.lastElementChild;

        //observer options
        const observer_options={
            root:document.querySelector(".ph-filter__slider-wrap"),
            rootMargin:"1px",
            threshold:1
        }
        
         //observing first element in the track
        const track_first_btn_obs=new IntersectionObserver(function(entries){
    
            entries.forEach(function(entry,index){
    
                if(entry.intersectionRatio < 1){
    
                    $(".ph-filter__btn--prev").addClass("ph-filter__btn--block");
                    
                }else{

                    $(".ph-filter__btn--prev").removeClass("ph-filter__btn--block");
                }
            });
    
        },observer_options)
        
        //Start observing the first btn in Track
        track_first_btn_obs.observe(track_first_btn);
    
        //observing last element in the track
        const track_last_btn_obs=new IntersectionObserver(function(entries,trackLastBtnObs){
            
            entries.forEach(function(entry,index){

                if(entry.intersectionRatio < 1){
    
                    $(".ph-filter__btn--next").addClass("ph-filter__btn--block");
                    
                }else{

                    $(".ph-filter__btn--next").removeClass("ph-filter__btn--block");
                }
            })
    
        },observer_options)
    
        //Start observing the Last btn in Track
        track_last_btn_obs.observe(track_last_btn);
    
        //moving the track when clicks on next/prev btn
        let a=0;

        $(".ph-filter__btn--ctrl").click(function(){

            const total_cat_btns = $(".ph-filter__btn--cat").length;
            const track_width = $(".ph-filter__slider-track").outerWidth();
            const moving_num=track_width/total_cat_btns;
    
            if($(this).hasClass("ph-filter__btn--next")){
                a++;
                $(".ph-filter__slider-track").css({
                    transform:`translateX(-${moving_num * a}px)`
                });
            }
                
            if($(this).hasClass("ph-filter__btn--prev")){

                a--;
                $(".ph-filter__slider-track").css({
                    transform:`translateX(-${moving_num * a}px)`
                });

            }
        });

        /* Add active and fetching catagory wise posts */

        $(".ph-filter__btn--cat").on("click",function(){

            const that=$(this)
            
            //first remove the active class from filter button
            $(".ph-filter__btn--cat.ph-filter__btn--active").removeClass("ph-filter__btn--active")
            
            //Now add the active class on which button is clicked
            $(this).addClass("ph-filter__btn--active");

            //load catagory wise posts
            load_posts({
                catagory_id:that.data("cat_id"),
                load_position:"home",
                remove_spinner_after:150
            });

        });



        
    }else if(script_type == "single_post"){

        //Here you get an array of all the query string passed in URL
        const query_strings= new URLSearchParams(window.location.search);
        
        //Here you will store the post link
        var post_link="";

        //first check if the object has `v` paramter
        if(query_strings.has("v")){
            
            //Get `v` query string and store it in post_link
            post_link=query_strings.get("v");
        }
    
        //load all the comments from server
        function load_comments(post_link){

            $.ajax({
                url:`${domain()}ajax_posts/load_comments`,
                method:"POST",
                data:{
                    post_link:post_link
                },
                beforeSend:function(){
                    
                    $(".loading-spinner--comment").remove();

                    $(".main-wrap__col-wrap--sp").append(`
                        <div class="loading-spinner loading-spinner--comment">
                            <div class="loading-spinner__circle loading-spinner__circle--comment">
                                
                            </div>
                        </div>
                    `);
                },
                success:function(response){
    
                    $(".sp-comm").remove();

                    $(".loading-spinner--comment").remove();

                    $(".main-wrap__col-wrap--sp").append(response);
                }
            })
        };

        //load post ratings such as `like` and `dislike` from server
        function load_post_ratings(post_link){
            
            $.ajax({
                url:`${domain()}ajax_posts/load_post_rating`,
                method:"POST",
                data:{
                    post_link:post_link
                },
                dataType:"json",
                success:function(response){

                    if(response){

                        $(".sp-content__meta-btn--rating").remove();
                        
                        $(".sp-content__meta-item--like").append(response.like_btn);

                        $(".sp-content__meta-item--dislike").append(response.dislike_btn);

                        //console.log(response);
                    }
                
                }
            })
        }

        //load the save post button with pressed class
        function load_save_post_btn(post_link){

            $.ajax({
                url:`${domain()}ajax_posts/load_save_post_btn`,
                method:"POST",
                data:{
                    post_link:post_link
                },
                dataType:"json",
                success:function(response){
                    
                    if(response.error == 0){

                        $(".sp-content__meta-btn--save").remove();

                        $(".sp-content__meta-item--save").append(response.save_btn);

                    }else if(response.error == 1){

                        console.log(response);
                    }

                   // console.log(response);
                }
            })
        }   

        //load total subscribers and subscribe button
        function load_total_subs_and_btn(post_link){

            $.ajax({
                url:`${domain()}ajax_posts/load_total_subs_and_btn`,
                method:"POST",
                data:{
                    post_link:post_link
                },
                dataType:"json",
                success:function(response){

                    if(response){

                       $(".sp-content__auth-subs").text("").text(response.total_sub);
                        
                       $(".sp-content__btn--subscribe").remove();

                       $(".sp-content__auth-side--right").append(response.sub_btn)
                    }
            
                    //console.log(response);
                }
            })
            
        }

        //load post ratings such as `like` and `dislike`
        load_post_ratings(post_link);

        //load the save post button
        load_save_post_btn(post_link);

        //load total subscribers and subscirbe button
        load_total_subs_and_btn(post_link);

        //first load all the comments
        load_comments(post_link);

        //first load all posts in sidebar
        load_posts({
            catagory_id:0,
            load_position:"sidebar",
            post_link:post_link,
        });


        /**
         * Adding functionality in
         * in post catagories filter
         */
        const track=document.querySelector(".sidebar-lg__filter-track");

        //store the first element in carousel
        const track_first_btn=track.firstElementChild;

        //store the last element in carousel
        const track_last_btn=track.lastElementChild;

        //observer options
        const observer_options={
            root:document.querySelector(".sidebar-lg__filter-wrap"),
            rootMargin:"1px",
            threshold:1
        }
        
         //observing first element in the track
        const track_first_btn_obs=new IntersectionObserver(function(entries){
    
            entries.forEach(function(entry,index){
    
                if(entry.intersectionRatio < 1){
    
                    $(".sidebar-lg__filter-btn--prev").addClass("sidebar-lg__filter-btn--block");
                    
                }else{
                    
                    $(".sidebar-lg__filter-btn--prev").removeClass("sidebar-lg__filter-btn--block");
        
                }
            });
    
        },observer_options)
        
        //Start observing the first btn in Track
        track_first_btn_obs.observe(track_first_btn);
    
        //observing last element in the track
        const track_last_btn_obs=new IntersectionObserver(function(entries,trackLastBtnObs){
            
            entries.forEach(function(entry,index){

                
                if(entry.intersectionRatio < 1){
    
                    $(".sidebar-lg__filter-btn--next").addClass("sidebar-lg__filter-btn--block");
                    
                }else{
                    
                    $(".sidebar-lg__filter-btn--next").removeClass("sidebar-lg__filter-btn--block");
        
                }
            })
    
        },observer_options)
    
        //Start observing the Last btn in Track
        track_last_btn_obs.observe(track_last_btn);
    
        //moving the track when clicks on next/prev btn
        let a=0;

        $(".sidebar-lg__filter-btn--action").click(function(){

            const total_cat_btns = $(".sidebar-lg__filter-btn--cat").length;
            const track_width = $(".sidebar-lg__filter-track").outerWidth();
            const moving_num=track_width/total_cat_btns;
    
            if($(this).hasClass("sidebar-lg__filter-btn--next")){
                a++;
                $(".sidebar-lg__filter-track").css({
                    transform:`translateX(-${moving_num * a}px)`
                });
            }
                
            if($(this).hasClass("sidebar-lg__filter-btn--prev")){
                a--;

                $(".sidebar-lg__filter-track").css({
                    transform:`translateX(-${moving_num * a}px)`
                });

            }
        });

        //call the function while adding a comment
        function add_comment(e){

            const that=$(this);
    
            let form_data_ary=$(this).parents("form").serializeArray();
    
            let data={};
    
            for(let i=0; i < form_data_ary.length; i++){
                
                data[form_data_ary[i].name]= form_data_ary[i].value
            }
    
            if($(this).parents("form").hasClass("sp-comm__form--primary")){
    
                data.comment_type="primary_comment";
    
            }else if($(this).parents("form").hasClass("sp-comm__form--secondary")){
    
                data.comment_type="secondary_comment";
            }

            $.ajax({
                
                url:`${domain()}ajax_posts/add_comment`,
                method:"POST",
                data:data,
                beforeSend:function(){
                    that.addClass("sp-comm__form-btn--loading");
                },
                success:function(response){

                    if(response == 1){

                        load_comments(post_link);

                    }else if(response == 0){

                        that.removeClass("sp-comm__form-btn--loading");

                        alert("something went wrong. Please try again");

                    }else if(response == 10){

                        that.removeClass("sp-comm__form-btn--loading");

                        alert("Please login first");
                    }

                    console.log(response);
    
                }
            });
    
        }

        //call the function when focusin & focusout on commment box
        function function_for_textarea(e){
        
            if(e.type == "focusin"){

                $(this).parents(".sp-comm__form-side--top").addClass("sp-comm__form-side--focused");
                
                if($(this).parents("form").hasClass("sp-comm__form--primary")){

                    if($(".sp-comm__form-side--bottom").hasClass("sp-comm__form-side--hide")){

                        $(".sp-comm__form-side--bottom").removeClass("sp-comm__form-side--hide")
                    }
                }
            }
            
            if(e.type == "focusout"){
                
                $(this).parents(".sp-comm__form-side--top").removeClass("sp-comm__form-side--focused");
            }

            if(e.type == "keyup"){

                if($(this).val() !== ""){

                    if($(this).parents("form").find(".sp-comm__form-btn--comment").hasClass("sp-comm__form-btn--disabled")){
                        
                        $(this).parents("form").find(".sp-comm__form-btn--comment").removeClass("sp-comm__form-btn--disabled")
                    }

                }else{
                    
                    if(!$(this).parents("form").find(".sp-comm__form-btn--comment").hasClass("sp-comm__form-btn--disabled")){
                        
                        $(this).parents("form").find(".sp-comm__form-btn--comment").addClass("sp-comm__form-btn--disabled")
                    }

                }
            }
            
        }

        //call the function when clicks on form's cancel button
        function function_for_cancel_btn(e) {

            if($(this).parents("form").hasClass("sp-comm__form--primary")){

                if(!$(this).parents(".sp-comm__form-side--bottom").hasClass("sp-comm__form-side--hide")){

                    //Remove the buttons if we clicked on primary form's cancel button
                    $(this).parents(".sp-comm__form-side--bottom").addClass("sp-comm__form-side--hide")
                }

            }else if($(this).parents("form").hasClass("sp-comm__form--secondary")){
                
                //remove the entire secondary form
                $(this).parents(".sp-comm__form--secondary").remove();

            }
        }

        function function_for_reply_btn() {

            const that=$(this);

            $.ajax({
                url:`${domain()}ajax_posts/append_reply_form`,
                method:"POST",
                data:{
                    comment_id:that.data("comment_id")
                },
                dataType:"json",
                success:function(response){

                    if(response.error == 1){

                        //first remove any existing secondary form
                        $(".sp-comm__form--secondary").remove();
                        
                        //append the form in reply button's parent element
                        that.parent().append(response.form)
                    }

                    if(response.error == 0){

                        alert("Please login first");
                        
                    }
        
                }
            });
        }

        //call the function when clicks on dropdown delete button
        function show_comment_delete_popup(e){

            const that=$(this);

            let data_attr="";

            if(that.data("comment_type") == "primary_comment"){

                data_attr=`data-comment_id="${that.data("comment_id")}"`

            }else if(that.data("comment_type") == "secondary_comment"){

                data_attr=`data-cr_id="${that.data("cr_id")}"`

            }
        
            const output=`
                <div class="modal fade modal--delete modal--comment-delete">
                    <div class="modal-dialog modal-dialog-centered modal__dialog">
                        <div class="modal-content modal__content">
                            <div class="modal__content-wrap">
                                <div class="modal__head">
                                    <div class="modal__icon modal__icon--warning">
                                        <i class="fa fa-exclamation"></i>
                                    </div>
                                    <h3 class="modal__title">Delete Warning</h3>
                                </div>
            
                                <div class="modal__body">
                                    <p>
                                        You are about to delete a public comment from your post. Are you sure to proceed with the action? This action can't be undone
                                    </p>
                                </div>
            
                                <div class="modal__footer">
                                    <button class="modal__btn modal__btn--cancel" type="button">
                                        <span class="">Cancel</span>
                                    </button>
            
                                    <button class="modal__btn modal__btn--proceed" type="button" data-comment_type="${that.data("comment_type")}" ${data_attr}>
                                        <span>Yes. Procced</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            //first delete any existing modal
            $(".modal").remove();
            
            //append the modal
            $("body").append(output);

            //show the modal
            $(".modal--comment-delete").modal("show");
        }


        //comment form's textarea
        $(document).on("focusin focusout keyup",".sp-comm__form-input-textarea", function_for_textarea)

        //comment form's cancel button
        $(document).on("click",".sp-comm__form-btn--cancel", function_for_cancel_btn)

        //Appear the form when clicks on reply button
        $(document).on("click",".sp-single-comm__btn--reply",function_for_reply_btn);

        //comment form's comment button
        $(document).on("click",".sp-comm__form-btn--comment", add_comment);

        //comment form's delete button
        $(document).on("click",".sp-single-comm__dropdown-link--delete",show_comment_delete_popup);

        //Disappear the comment delete popup when click's on cancel button
        $(document).on("click",".modal--comment-delete .modal__btn--cancel",function(e){
            
            $(".modal--comment-delete").modal("hide");

            //`hidden.bs.modal` is an event which will be called when the modal will hide properly
            $(".modal--comment-delete").on("hidden.bs.modal",function(e){
                
                //remove the html when modal is hide properly
                $(".modal--comment-delete").remove();
            });
            
        });
        
        //Send a request to server to delete the comment when click's on proceed button
        $(document).on("click",".modal--comment-delete .modal__btn--proceed",function(e){

            const that=$(this);
        
            //store the data-comment_type attr's value
            const comment_type = that.data("comment_type");

            //store the data which will be sent to server side
            const data={
                comment_type:comment_type
            };

            if(comment_type == "primary_comment"){

                data.comment_id=that.data("comment_id");
                
            }else if(comment_type == "secondary_comment"){
                
                data.cr_id=that.data("cr_id");
            }
                
            $.ajax({
                url:`${domain()}ajax_posts/delete_comment_and_replies`,
                method:"POST",
                data:data,
                beforeSend:function(){

                    that.find("span").text("Proceeding...")

                },
                success:function(response){

                    if(response == 1){

                        that.find("span").text("Got it...!"); 

                        setTimeout(function(){

                            that.find("span").text("Yes. Proceed"); 

                            $(".modal--comment-delete").modal("hide");

                            load_comments(post_link);

                        },1000);
                        
                    }else if(response == 0){

                        that.find("span").text("Error. Try again"); 

                        setTimeout(function(){

                            that.find("span").text("Yes. Proceed"); 

                            $(".modal--comment-delete").modal("hide");

                            load_comments(post_link);

                        },1000);

                    }else{

                        console.log(response);
                    }

                }
            })
           
            
            
        });

        //send a request to server to rate (`like` or `dislike`) the post 
        $(document).on("click",".sp-content__meta-btn--rating",function(){

            const that=$(this);

            const data={
                post_id:that.data("post_id")
            };

            let pr_action="";

            if(that.hasClass("sp-content__meta-btn--like")){

                pr_action="like";
                
            }else if(that.hasClass("sp-content__meta-btn--dislike")){
                
                pr_action="dislike";
            }

            data.pr_action=pr_action;

            $.ajax({
                url:`${domain()}ajax_posts/post_rating`,
                method:"POST",
                data:data,
                dataType:"json",
                success:function(response){

                    if(response.error == 0){
                        
                        load_post_ratings(post_link);

                        //show popup message at the bottom
                        if(response.pr_action){

                            const msg_param={
                                position_class:"popup-msg--bottom-left",
                                type_class:`popup-msg--rating popup-msg--${pr_action}`
                            }

                            if(pr_action == "like"){

                                msg_param.text="<span>You liked the post</span>";
                                
                            }else if(pr_action == "dislike"){
                                
                                msg_param.text="<span>You disliked the post</span>";
                            }
    
                            show_popup_msg(msg_param);
                        }

                    }else if(response.error == 1){

                        
                        alert("something went wrong");


                    }else if(response.error == 100){

                        alert("Please login first");

                    }

                    //console.log(response);
                }
            })
            
         

        });

        $(document).on("click",".sp-content__meta-btn--save",function(){
            
            const that=$(this);

            $.ajax({
                url:`${domain()}ajax_posts/save_posts`,
                method:"POST",
                data:{
                    post_id:that.data("post_id")
                },
                dataType:"json",
                success:function(response){
                    
                    if(response.error == 0){

                        load_save_post_btn(post_link);

                        if(response.action == "save"){

                            const msg_param={
                                position_class:"popup-msg--top-right",
                                type_class:`popup-msg--success`,
                                text:"<span>Add to save posts</span>"
                            }
                           
                            show_popup_msg(msg_param)
                        }

                    }else if(response.error == 1){


                        alert('something went wrong');

                        console.log(response);

                    }else if(response.error == 100){

                        alert("Please login first");
                    
                    }

                }
            })

        
        });

        //subscriber
        $(document).on("click",".sp-content__btn--subscribe",function(){

            const that=$(this);

            $.ajax({
                url:`${domain()}ajax_posts/subscribe`,
                method:"POST",
                data:{
                    sub_owner:that.data("sub_owner")
                },
                dataType:"json",
                success:function(response){

                    if(response.error == 0){    

                        load_total_subs_and_btn(post_link);

                        if(response.action == "subscribe"){

                            const msg_param={
                                position_class:"popup-msg--bottom-left",
                                type_class:`popup-msg--rating popup-msg--sub`,
                                text:"<span>Add to Subscribed list</span>"
                            }
                           
                            show_popup_msg(msg_param);

                        }

                    }else if(response.error == 1){

                        alert(response.error_msg);

                        console.log(response.error_msg);

                    }else if(response.error == 100){

                        alert("Please login first");
                    }

                    console.log(response);
                }
            })

        });

         /**
         * add active class in filter catagory buttons
         */

        $(".sidebar-lg__filter-btn--cat").on("click",function(){

            const that=$(this);

            //first remove active class from catagory
            $(".sidebar-lg__filter-btn--cat.sidebar-lg__filter-btn--active").removeClass("sidebar-lg__filter-btn--active");
            
            //add active class on which user clicked
            $(this).addClass("sidebar-lg__filter-btn--active");

            load_posts({
                catagory_id:that.data("cat_id"),
                load_position:"sidebar",
                post_link:post_link,
                remove_spinner_after:150
            });
        });
        

    }else if(script_type == "login"){

        //store all the from input
        const form_input=$(".ac-form__input").not(".ac-form__input--remember");

        form_input.on("focusin focusout",function(e){

            if(e.type == "focusin"){
                
                //add a css class to the border-color when focusin
                $(this).parent().addClass("ac-form__field--focused");

                $(this).parent().removeClass("ac-form__field--error");
            
            }else if(e.type == "focusout"){
                
                //remove the css class when focusout
                $(this).parent().removeClass("ac-form__field--focused");
            }
        });
    
        //store the login button
        const login_btn=$(".ac-form__btn--login");

        //validate and submit the form
        login_btn.click(function(e){

            e.preventDefault();

            let form_data=$(".ac-form--login").serializeArray();
            
            let form_data_obj={};

            for(let i=0; i < form_data.length; i++){

                form_data_obj[form_data[i].name]=form_data[i].value;
            }

            //console.log(form_data_obj);

            $.ajax({
                url:`${domain()}ajax_accounts/login`,
                method:"POST",
                data: form_data_obj,
                dataType:"json",
                beforeSend:function(){

                    $(".loading-spinner--login").remove();

                    $(".main-wrap__col-wrap--login").append(`
                        <div class="loading-spinner loading-spinner--login">
                            <div class="loading-spinner__circle loading-spinner__circle--login">

                            </div>
                        </div>
                    `);
                },
                success:function(response){
                    

                    //remove the loading circle from the front of the form
                    $(".loading-spinner--login").remove();

                    //remove the error msg from input's bottom
                    $(".ac-form__msg--error").remove();
                    
                    //remove the red border from input field
                    $(".ac-form__field--error").removeClass("ac-form__field--error");
                    
                    let response_type=typeof response;

                    if(response == 1){

                        $(".ac-form--login").trigger("reset");

                        window.location.href=domain();

                    }else if( response_type == "object"){

                        for(const key in response){

                            $(`${response[key].target}`).append(`${response[key].error_msg}`);
                            
                            $(`${response[key].target}`).find(".ac-form__field").addClass("ac-form__field--error");
                        }

                    }else{
                        
                        alert("Failed to login. Please try again");

                        //console.log(response);
                    }

                    //console.log(response);
                }
            });




        });

      
        //store the eye button
        const eye_btn=$(".ac-form__btn--eye");
        
        eye_btn.on("click",function(){

            if(!$(this).hasClass("ac-form__btn--active")){

                $(this).addClass("ac-form__btn--active");

                $(this).find("i").remove();

                $(this).append(`<i class='fa fa-eye'></i>`);

                $(this).prev("input").attr("type","text");

            }else{

                $(this).removeClass("ac-form__btn--active");

                $(this).find("i").remove();

                $(this).append(`<i class='fa fa-eye-slash'></i>`);
                
                $(this).prev("input").attr("type","password");
            }
        });




        
    }else if(script_type == "signup"){

        //store all the from input
        const form_input=$(".ac-form__input");

        form_input.on("focusin focusout",function(e){

            if(e.type == "focusin"){
                
                //add a css class to the border-color when focusin
                $(this).parent().addClass("ac-form__field--focused");
                
            }else if(e.type == "focusout"){
                
                //remove the css class when focusout
                $(this).parent().removeClass("ac-form__field--focused");
            }
        });
        

        //validating signup form while typing
        form_input.on("keyup",function(){

            //store input element in which user is typing
            const current_input=$(this);

            if(current_input.val() !== ""){

                //store the data serverside data
                const request_data={
                    input_value: current_input.val(),
                    name_attr:current_input.attr("name"),
                }


                if(current_input.attr("name")=="con-pass"){

                    //send create pssword value for checking create and confirm passwords are same
                    request_data.cre_pass=$(".ac-form__input--cre-pass").val()
                }


                $.ajax({
                    url:`${domain()}ajax_accounts/signup_form_validate`,
                    type:"POST",
                    data:request_data,
                    dataType:"json",
                    success:function(response){ 

                        if(response){

                            //Before printing any error message. we have to remove them
                            current_input.parents(".ac-form__col").find(".ac-form__msg").remove();
                            
                            //Before printing any error message, we have to remove all the input-check
                            current_input.parents(".ac-form__col").find(".ac-form__input-check").remove();

                            response_type=typeof response;

                            if(response_type == "object"){

                                for(const key in response){
                                     
                                    current_input.parent().append(`
                                        <div class="ac-form__input-check ac-form__input-check--invalid"></div>
                                    `)

                                    current_input.parents(".ac-form__col").append(response[key].error_msg)
                                }
                                //setting data-valid='0' when we got an error;
                                current_input.attr("data-valid","0");
                       
                            }else if(response == 1){

                               current_input.parent().append(`
                                        <div class="ac-form__input-check ac-form__input-check--valid"></div>
                                `)

                                //setting data-valid='1' when we got no error;
                                current_input.attr("data-valid","1");
                            
                            }

                            //counting the invalid inputs numbers
                            const invalid_inputs_num=$("[data-valid='0']").length;

                            if(invalid_inputs_num == 0){

                                //remove the disabled class from submit button if everything is OK
                                $(".ac-form__btn--signup").removeClass("ac-form__btn--disabled");
                                
                            }else{
                                
                                //Add the disabled class in submit button if the form gets invalid agains
                                $(".ac-form__btn--signup").addClass("ac-form__btn--disabled");
                        

                            }
                        }

                        //console.log(response);

                    }
                })

            }else{

                
                 //Remove the .error-msg if the user makes the input empty after typing somthing
                current_input.parents(".ac-form__col").find(".ac-form__msg").remove();
                
                //Remove the ..checking-reloader if the user makes the input empty after typing somthing
                current_input.parents(".ac-form__col").find(".ac-form__input-check").remove();
            }
        })

        //store the signup button
        const signup_btn=$(".ac-form__btn--signup");

    
        //Submiting the form when clicks on submit btn
        signup_btn.on("click",function(e){

            //preven the default behaviour
            e.preventDefault();

            //check each input is validated properly
            if($("[data-valid='0']").length == 0){
                
                let form_data=$(".ac-form--signup").serializeArray();
                
                let form_data_obj={};

                //Converting the form_data array into an object
                for(let i=0; i < form_data.length; i++){

                    form_data_obj[form_data[i].name]=form_data[i].value
                }

                $.ajax({
                    url:`${domain()}ajax_accounts/create_account`,
                    type:"POST",
                    data:form_data_obj,
                    beforeSend:function(){

                        $(".loading-spinner--signup").remove();

                        $(".main-wrap__col-wrap--signup").append(`
                            <div class="loading-spinner loading-spinner--signup">
                                <div class="loading-spinner__circle loading-spinner__circle--signup">

                                </div>
                            </div>
                        `);

                    },
                    success:function(response){

                        if(response == 1){

                            $("ac-form ac-form--signup").trigger("reset");

                            $(".loading-spinner--signup").remove();

                            window.location.href=domain();
                            
                        }else if(response == 0){
                            
                            $("ac-form ac-form--signup").trigger("reset");

                            $(".loading-spinner--signup").remove();

                            alert("Account created successfully.Verification E-mail Was not sent");
                        }

                        console.log(response);
                    }
                })
            }
        });
         
    }else if(script_type == "forgot_password"){

        //store all the from input
        const form_input=$(".ac-form__input").not(".ac-form__input--remember");

        form_input.on("focusin focusout",function(e){

            if(e.type == "focusin"){
                
                //add a css class to the border-color when focusin
                $(this).parent().addClass("ac-form__field--focused");

                $(this).parent().removeClass("ac-form__field--error");
            
            }else if(e.type == "focusout"){
                
                //remove the css class when focusout
                $(this).parent().removeClass("ac-form__field--focused");
            }
        });


        //store the forgot password form
        const form_fpw=$(".ac-form--fpw");

        //store the recovery button
        const recovery_btn=$(".ac-form__btn--recovery");

        //store the recovery button
        const loginpage_btn=$(".ac-form__btn--loginpage");

        loginpage_btn.on("click",function(e){

            window.location.href=`${domain()}accounts/login`
        });

        recovery_btn.on("click",function(e){
            e.preventDefault();

            //collect form data from fpw form
            const fpw_form_data=form_fpw.serializeArray();

            //store all the data for sending to the server side
            const data={}
            
            for(let i=0; i < fpw_form_data.length; i++){

                data[fpw_form_data[i].name]=fpw_form_data[i].value;
            }

            $.ajax({
                url:`${domain()}ajax_accounts/forgot_password`,
                method:"POST",
                data:data,
                dataType:"json",
                beforeSend:function(){

                    $(".loading-spinner").remove();

                    $(".main-wrap__col-wrap").append(`
                        <div class="loading-spinner loading-spinner--fpw">
                            <div class="loading-spinner__circle loading-spinner__circle--fpw">
                            </div>
                        </div>
                    `);

                },
                success:function(response){

                    $(".ac-form__msg--error").remove();
                
                    if(response.error_status == 1){
                        
                        $(".loading-spinner").remove();

                        $.each(response.errors,function(index,value){
                            $(`${value.target}`).append(`${value.error_msg}`);
                            
                            $(`${value.target}`).find(".ac-form__field").addClass("ac-form__field--error");
                            
                        });
                    
                    }else if(response.error_status == 100){
                        
                        $(".loading-spinner").remove();

                        form_fpw.trigger("reset");

                        alert("Token was not inserted");
                        
                        
                    }else if(response.error_status == 200){
                        
                        $(".loading-spinner").remove();

                        form_fpw.trigger("reset");

                        alert("Token created and inserted successfully. Mail was not sent");
                        

                    }else if(response.error_status == 0){

                        $(".loading-spinner").remove();

                        form_fpw.trigger("reset");

                        const msg_param={
                            position_class:"popup-msg--top-right",
                            type_class:`popup-msg--success`,
                            text:"<span>Check you inbox</span>"
                        }
                       
                        show_popup_msg(msg_param)
                    }


                    //console.log(response);

                }
            })

        });


        
    }else if(script_type == "reset_password"){

        //store all the from input
        const form_input=$(".ac-form__input").not(".ac-form__input--remember");

        form_input.on("focusin focusout",function(e){

            if(e.type == "focusin"){
                
                //add a css class to the border-color when focusin
                $(this).parent().addClass("ac-form__field--focused");

                $(this).parent().removeClass("ac-form__field--error");
            
            }else if(e.type == "focusout"){
                
                //remove the css class when focusout
                $(this).parent().removeClass("ac-form__field--focused");
            }
        });

        
        const resetpwd_form=$(".ac-form--resetpwd");
        
        const resetpwd_btn=$(".ac-form__btn--resetpwd");

        resetpwd_btn.on("click",function(e){

            //prevent the default behaviour
            e.preventDefault();

            //store all the data
            const resetpwd_form_data=resetpwd_form.serializeArray();

            const data={};

            for(let i=0; i < resetpwd_form_data.length; i++){

                data[resetpwd_form_data[i].name]=resetpwd_form_data[i].value;
            }

            $.ajax({

                url:`${domain()}ajax_accounts/reset_password`,
                method:"POST",
                data:data,
                dataType:"json",
                beforeSend:function(){

                    $(".loading-spinner").remove();

                    $(".main-wrap__col-wrap").append(`
                        <div class="loading-spinner loading-spinner--resetpwd">
                            <div class="loading-spinner__circle loading-spinner__circle--resetpwd">
                            </div>
                        </div>
                    `);

                },
                success:function(response){

                    $(".ac-form__msg--error").remove();

                    if(response.error_status == 1){

                        $(".loading-spinner").remove();

                        for(const key in response.errors){

                            $(`${response.errors[key].target}`).append(`${response.errors[key].error_msg}`);
                            
                            $(`${response.errors[key].target}`).find(".ac-form__field").addClass("ac-form__field--error");
                        }

                    }else if(response.error_status == 0){

                        $(".loading-spinner").remove();

                        resetpwd_form.trigger("reset");

                        const msg_param={
                            position_class:"popup-msg--top-right",
                            type_class:`popup-msg--success`,
                            text:"<span>Password Changed Successfully</span>"
                        }
                       
                        show_popup_msg(msg_param)

                        setTimeout(function(){

                            window.location.href=`${domain()}accounts/login`

                        },2000)

                    }
                
                    //console.log(response);
                }
                
            })
        });
        




         //store the eye button
         const eye_btn=$(".ac-form__btn--eye");
        
         eye_btn.on("click",function(){
 
             if(!$(this).hasClass("ac-form__btn--active")){
 
                 $(this).addClass("ac-form__btn--active");
 
                 $(this).find("i").remove();
 
                 $(this).append(`<i class='fa fa-eye'></i>`);
 
                 $(this).prev("input").attr("type","text");
 
             }else{
 
                 $(this).removeClass("ac-form__btn--active");
 
                 $(this).find("i").remove();
 
                 $(this).append(`<i class='fa fa-eye-slash'></i>`);
                 
                 $(this).prev("input").attr("type","password");
             }
         });

    }else if(script_type == "profile_page"){

     
        $(".profile-area__carousel--recent").owlCarousel({
            dots:true,
            nav:false,
            margin:15,
            navText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
            navElement:"button",
            navContainer:".owl-carousel",
            responsiveClass:true,
            responsive:{
                0:{
                    items:2,
                },

                768:{
                    items:3,
                },

                992:{
                    items:4,
                }
            }
        });
    }









});