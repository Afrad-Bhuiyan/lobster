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

    //use the function fetch unread notificatios in every 5 minitues
    function get_unread_notifications(){

        $.ajax({
            url:`${domain()}ajax_pages/get_unread_notifications`,
            method:"POST",
            data:{
                title_tag:$("title").text()
            },
            dataType:"json",
            success:function(response){

                if(response.error_status == 0){

                    //set the title tag
                    $("title").text(response.title_tag);

                    //set the bell button with new badge
                    $(".ph-nav__dropdown-toggle--nf").html(response.bell_btn);

                }

               // console.log(response);
            }
        })
    }

     //use the object to show modals
     const modal={

        modal_content:{

            post_delete:{
                modal_class:"modal--commentDelete",
                modal_title:"Delete Warning",
                modal_text:`You are about to delete a public comment from your post. Are you sure to proceed with the action? This action can't be undone`,
                modal_false_btn_text:`Cancel`,
                modal_true_btn_text:`Yes. Procced`,
                onTrue:function(){},
                onFalse:function(){}
            },
        },
    
        open:function(modal_name,options = null){

            let selected_modal=this.modal_content[modal_name];

            if(options !== null){

                selected_modal = Object.assign(selected_modal,options);
            }

            const html = `
                <div class="modal fade ${selected_modal.modal_class}">
                    <div class="modal-dialog modal-dialog-centered modal__dialog">
                        <div class="modal-content modal__content">
                            <div class="modal__content-wrap">
                                <div class="modal__head">
                                    <div class="modal__icon"></div>
                                    <h3 class="modal__title">
                                        ${selected_modal.modal_title}
                                    </h3>
                                </div>

                                <div class="modal__body">
                                    <p class="modal__text">
                                        ${selected_modal.modal_text}
                                    </p>
                                </div>
                                
                                <div class="modal__footer">
                                    <button class="modal__btn modal__btn--false modal__btn--cancel" type="button">
                                        <span class="modal__btn-text">
                                            ${selected_modal.modal_false_btn_text}
                                        </span>
                                    </button>

                                    <button class="modal__btn modal__btn--true modal__btn--proceed" type="button">
                                        <span class="modal__btn-text">
                                            ${selected_modal.modal_true_btn_text}
                                        </span>
                                    </button>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            `
            $(".modal").remove();

            $("body").append(html);

            setTimeout(() => {

                $(`.${selected_modal.modal_class}`).modal({
                    backdrop:"static",
                    keyboard:true
                });

                $(`.${selected_modal.modal_class}`).modal('show');
                
                $(`.${selected_modal.modal_class} .modal__btn--true`).on("click", selected_modal.onTrue);
                
                $(`.${selected_modal.modal_class} .modal__btn--false`).on("click",selected_modal.onFalse);

            }, 50);
        }

    }
    

    /*=========================
    Code for Header part starts
    ==========================*/

    setInterval(get_unread_notifications,5000);

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



    //store all the dropdown toggles
    const dd_toggles=$(".ph-nav__dropdown-toggle");

    dd_toggles.on("click",function(){

        //store the clicked dropdwon toggle
        const dd_toggle = $(this);

        //store clicked closest dropdwon content
        const dd_content=dd_toggle.parent().find(".ph-nav__dropdown-content");

        //store dropdown content wrapper element
        const dd_content_wrap=dd_toggle.parent().find(".ph-nav__dropdown-content-wrap");

        //store options
        const obj={
            
            open:function(){

                //store the object
                const that=this;
                
                //remove any existing  .document-wrap elemenet
                $(".document-wrap").remove();

                //append a .document-wrap element into the body
                $(".ph").append(`<div class="document-wrap"></div>`);

                if(!dd_content.hasClass("ph-nav__dropdown-content--block")){

                    //add the block class in dropdown options
                    dd_content.addClass("ph-nav__dropdown-content--block");
                    
                    setTimeout(function(){
                        
                        //add the show class in dropdown options
                        dd_content.addClass("ph-nav__dropdown-content--show");

                        //call the close function from object
                        $(".document-wrap").on("click",that.close);
                    
                    },50);
                }
            },

            close:function(){

                //remove the .documen-wrap first
                $(".document-wrap").remove();
                
                if(dd_content.hasClass("ph-nav__dropdown-content--show")){
                    
                    //remove the show from options
                    dd_content.removeClass("ph-nav__dropdown-content--show");
                    
                    setTimeout(function(){
                        
                        //remove the block class from options
                        dd_content.removeClass("ph-nav__dropdown-content--block");

                    },155);
                }  
            }
        }

        //call the open function to show the popup
        obj.open();
        
        if(dd_toggle.hasClass("ph-nav__dropdown-toggle--nf")){

            $.ajax({
                url:`${domain()}/ajax_pages/fetch_notifications`,
                method:"POST",
                beforeSend:function(){

                    dd_content_wrap.append(`
                        <div class="loading-spinner loading-spinner--nf">
                            <div class="loading-spinner__circle loading-spinner__circle--nf">

                            </div>
                        </div>
                    `);

                },
                success:function(response){

                    //dd_content_wrap.html(" ");

                    dd_content_wrap.html(response);

                    get_unread_notifications();
                }
            })

           
           
        
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

        /**
         * All Functions Starts
         */

            //use the funtion to add events after 
            //loading comments part fully
            function add_events_after_loading_comments(){
                
                $(document).on("focusin focusout keyup",".sp-comm__form-input--textarea",function(e){
            
                    //store all the form fields
                    const form_fields = $(this).parents("form").find(".sp-comm__form-field");

                    //store the current textarea's form field
                    const current_field = $(this).parents(".sp-comm__form-field");

                    //store the existing comment button from the current form
                    const comment_btn = current_field.parents("form").find(".sp-comm__form-btn--comment");

                    if(e.type == "focusin"){

                        form_fields.each(function(index,form_field){
                            
                            if($(form_field).hasClass("sp-comm__form-field--textarea")){
                                
                                //add focused class in textarea's form field
                                $(form_field).addClass('sp-comm__form-field--focused');
                                
                            }else if($(form_field).hasClass("sp-comm__form-field--actionBtns sp-comm__form-field--hide")){
                                
                                //remove hide class from actionBtns form fields
                                $(form_field).removeClass('sp-comm__form-field--hide');
                            }
                        });
                    }

                    if(e.type == "focusout"){

                        //remove focused class form field
                        current_field.removeClass("sp-comm__form-field--focused");
                    }

                    if(e.type == "keyup"){
                    
                        if($(this).val() !== ""){
                        
                            //comment box is not empty, lets remove the disabled class from comment button
                            comment_btn.removeClass("sp-comm__form-btn--disabled");
                            
                        }else{
                            
                            //comment box is empty Now add the disabled class in comment button
                            comment_btn.addClass("sp-comm__form-btn--disabled");
                        }
                    }

                });

                $(document).on("click",".sp-comm__form-btn--comment",function(e){

                    //prevent the default behaviour
                    e.preventDefault();

                    //store the comment form
                    const comment_form = $(this).parents("form");

                    //store the comment button
                    const comment_btn = $(this)

                    //convert all comment from's data into an array
                    const comment_form_data = comment_form.serializeArray();

                    //store all the data to send to the server side
                    const data = {
                        form_type:"",
                        form_data:{}
                    };
                    
                    if(comment_form.hasClass("sp-comm__form--primary")){

                        //override the form_type in data variable
                        data.form_type = "primary";
                        
                    }else if(comment_form.hasClass("sp-comm__form--secondary")){
                        
                        //override the form_type in data variable
                        data.form_type = "secondary";
                    }

                    //conver the comment_form_data into an object
                    for(let i=0; i < comment_form_data.length; i++){
                            
                        data.form_data[comment_form_data[i].name] = comment_form_data[i].value;
                        
                    }
        
                    $.ajax({
                        
                        url:`${domain()}ajax_pages?class=single&method=add_comment`,
                        method:"POST",
                        data:data,
                        beforeSend:function(){
                           comment_btn.addClass("sp-comm__form-btn--loading");
                        },
                        dataType:"json",
                        success:function(response){

                           comment_btn.removeClass("sp-comm__form-btn--loading");

                            if(response.error_status == 1){
                                
                                comment_form.trigger("reset");
                                
                                alert("Please login first");
                                
                            }else if(response.error_status == 100){
                                
                                comment_form.trigger("reset");

                                alert("something went wrong. please try again");
                                
                                console.log(response);

                            }else if(response.error_status == 0){
                                
                                load_comments();

                            }
                        }
                    });





                    
        

                });

                $(document).on("click",".sp-comm__form-btn--cancel",function(e){

                    const form = $(this).parents("form");
                    
                    if(form.hasClass("sp-comm__form--primary")){

                        const actionBtns_field = form.find(".sp-comm__form-field--actionBtns");

                        if(!actionBtns_field.hasClass("sp-comm__form-field--hid")){

                            actionBtns_field.addClass("sp-comm__form-field--hide");
                        }
                    
                    }else if(form.hasClass("sp-comm__form--secondary")){

                        form.remove();

                    }
                });

                $(document).on("click",".sp-single-comm__btn--reply",function(e){

                    const current_reply_btn = $(this);

                    const data = {
                        comment_id: $(this).data("comment_id"),
                        reply_type:$(this).data("reply_type") 
                    }

                    if(current_reply_btn.data("reply_id")){

                        //append reply_id while repling a comment reply
                        data.reply_id = current_reply_btn.data("reply_id");
                    }

                    $.ajax({
                        url:`${domain()}ajax_pages?class=single&method=append_reply_form`,
                        method:"POST",
                        data:data,
                        dataType:"json",
                        success:function(response){

                            if(response.error_status == 1){

                                alert("Please login first");

                            }else if(response.error_status == 0){

                                $(".sp-comm__form--secondary").remove();

                                current_reply_btn.parents(".sp-single-comm__ratings").after(response.html);
                            }

                        // console.log(response);
                    
                        }
                    });

                

                

                });
            
                $(document).on("click",".sp-single-comm__btn--viewReply",function(e){

                    //store the current view replise buttons
                    const viewReply_btn=$(this);
                    
                    //store the container where all replise will be apended
                    const replies_container = viewReply_btn.parent().find(".sp-single-comm__areplies");

                    const obj = {

                        init:function(){

                            if(viewReply_btn.data("first_clicked")){
                            
                                $.ajax({
                                    url:`${domain()}ajax_pages?class=single&method=fetch_comment_replies`,
                                    method:"POST",
                                    data:{
                                        comment_id:viewReply_btn.data("comment_id")
                                    },
                                    beforeSend:function(){

                                        $(".loading-spinner--viewReplies").remove();

                                        viewReply_btn.after(`
                                            <div class='loading-spinner loading-spinner--viewReplies'>
                                                <div class='loading-spinner__circle loading-spinner__circle--viewReplies'>
                                                </div>
                                            </div>
                                        `);
                                    },
                                    dataType:"json",
                                    success:function(response){

                                        if(response.error_status == 1){

                                            alert("something went went wrong. Please try again");

                                            console.log(response);

                                        }else if(response.error_status == 0){   

                                            $(".loading-spinner--viewReplies").remove();

                                            replies_container.html(" ").append(response.html);

                                            replies_container.data("total_replies",response.total_response);

                                            replies_container.collapse("show").on("shown.bs.collapse",obj.show);

                                            viewReply_btn.data("first_clicked", false);
                                        }
                                    }
                                });
                                
                            }else{

                                replies_container.collapse("toggle").on("shown.bs.collapse",obj.show).on("hidden.bs.collapse",obj.hide);
                        
                            }
                        
                        },

                        show:function(e){

                            const total_replies = replies_container.data("total_replies");

                            const viewReply_btn_txt=(total_replies > 1) ? `Hide ${total_replies} replies` : `Hide ${total_replies} reply`;

                            viewReply_btn.find("i").removeClass("fa-caret-down").addClass("fa-caret-up");

                            viewReply_btn.find("span").text(viewReply_btn_txt);
                        },
                        hide:function(e){
                            
                            const total_replies = replies_container.data("total_replies");

                            const viewReply_btn_txt=(total_replies > 1) ? `View ${total_replies} replies` : `View ${total_replies} reply`;
                            
                            viewReply_btn.find("i").removeClass("fa-caret-up").addClass("fa-caret-down");

                            viewReply_btn.find("span").text(viewReply_btn_txt);
                        }
                    }

                    //call the method1 to initialize
                    obj.init();

                });

                $(document).on("click",".sp-single-comm__btn--rate",function(e){

                    const data={
                        rate_action:$(this).hasClass("sp-single-comm__btn--like") ? "like" : "dislike", 
                        rate_for:$(this).data("rate_for"),
                        rate_for_id:$(this).data("rate_for_id")
                    }
    
                    $.ajax({
                        url:`${domain()}ajax_pages?class=single&method=add_rate`,
                        method:"POST",
                        data:data,
                        dataType:"json",
                        success:function(response){


                            if(response.error_status == 1){

                                alert("Please login first");

                            
                            }else if(response.error_status == 100){

                                alert("something went wrong. please try again");

                            }else if(response.error_status == 0){

                                load_comments();

                            }

                           // console.log(response);
                        }
                    });
                   
                



                });


                $(document).on("click",".sp-single-comm__btn--ddToggle",function(){


                    //store dropdown optios element
                    const dropdown_opts = $(this).parent().find(".sp-single-comm__dropdown-opts");
                            
                    const obj={
                    
                        open:function(){

                            //store the object
                            const that=this;
                            
                            //remove any existing  .document-wrap elemenet
                            $(".document-wrap").remove();

                            //append a .document-wrap element into the body
                            $("body").append(`<div class="document-wrap"></div>`);

                            if(!dropdown_opts.hasClass("sp-single-comm__dropdown-opts--show")){

                                //add the block class in dropdown options
                                dropdown_opts.addClass("sp-single-comm__dropdown-opts--block");
                                
                                setTimeout(function(){
                                    
                                    //add the show class in dropdown options
                                    dropdown_opts.addClass("sp-single-comm__dropdown-opts--show");

                                    //call the close function from object
                                    $(".document-wrap").on("click",that.close);
                                
                                },50);
                            }
                        },

                        close:function(){

                            //remove the .documen-wrap first
                            $(".document-wrap").remove();
                            
                            if(dropdown_opts.hasClass("sp-single-comm__dropdown-opts--show")){
                                
                                //remove the show from options
                                dropdown_opts.removeClass("sp-single-comm__dropdown-opts--show");
                                
                                setTimeout(function(){
                                    
                                    //remove the block class from options
                                    dropdown_opts.removeClass("sp-single-comm__dropdown-opts--block");

                                },155);
                            }  
                        }
                    }

                    // //call the open function to show the popup
                    obj.open();

                });

                $(document).on("click",".sp-single-comm__btn--delete",function(e){
                    
                    //store the data to send to the serverside
                    const data={
                        delete:$(this).data("delete"),
                        delete_id:$(this).data("delete_id")
                    }

                    modal.open("post_delete",{
            
                        onTrue:function(){
        
                            //store the true button
                            const trueBtn = $(this);
                        
                            //store the selected modal
                            const selected_modal = trueBtn.parents(".modal");
                            
                            $.ajax({
                                url:`${domain()}ajax_pages?class=single&method=delete_comments`,
                                method:"POST",
                                data:data,
                                dataType:"json",
                                beforeSend:function(){
                                    trueBtn.find("span").text("Procced...")
                                },
                                success:function(response){

                                    if(response.error_status == 1){

                                        trueBtn.addClass("modal__btn--error").find("span").text("Error. Try again");
                                        
                                        setTimeout(function(){

                                            //call the modal function to hide
                                            selected_modal.modal("hide");
                        
                                            selected_modal.on("hidden.bs.modal",function(e){
                        
                                                //permanantly remove the modal from the dom
                                                $(this).remove();
                                                
                                                console.log(response);
                                            });

                                        },500);
                                    
                                    }else if(response.error_status == 0){

                                        trueBtn.find("span").text("Deleted Successfully");
                                        
                                        setTimeout(function(){

                                            //call the modal function to hide
                                            selected_modal.modal("hide");
                        
                                            selected_modal.on("hidden.bs.modal",function(e){
                        
                                                //permanantly remove the modal from the dom
                                                $(this).remove();
                                                
                                                load_comments();
                                            });

                                        },150);
                                    }

                                    console.log(response)
                                }
                            })
        
    
                        },

                        onFalse:function(){
        
                            //store the true button
                            const falsBtn = $(this);
                            
                            //store the selected modal
                            const selected_modal = falsBtn.parents(".modal");
                            
                            //call the modal function to hide
                            selected_modal.modal("hide");
        
                            selected_modal.on("hidden.bs.modal",function(e){
        
                                //permanantly remove the modal from the dom
                                $(this).remove();
                            });
                        }
                    });

                });

                $(document).on("click",".sp-content__btn--rating",function(e){

                    const data={
                        rate_action:$(this).hasClass("sp-content__btn--like") ? "like" : "dislike", 
                        rate_for:$(this).data("rate_for"),
                        rate_for_id:$(this).data("rate_for_id")
                    }

                    $.ajax({
                        url:`${domain()}ajax_pages?class=single&method=add_rate`,
                        method:"POST",
                        data:data,
                        dataType:"json",
                        success:function(response){


                            if(response.error_status == 1){

                                alert("Please login first");

                            
                            }else if(response.error_status == 100){

                                alert("something went wrong. please try again");

                            }else if(response.error_status == 0){

                                load_post_rate_btns();
                            }

                           console.log(response);
                        }
                    });
                   
              
                


                });

                $(document).on("click",".sp-content__btn--save",function(e){

                    $.ajax({
                        url:`${domain()}ajax_pages?class=single&method=add_to_save_list`,
                        method:"POST",
                        data:{
                            post_id:$(this).data("post_id")
                        },
                        dataType:"json",
                        success:function(response){

                            if(response.error_status == 1){

                                alert("Please login first");
                                
                            }else if(response.error_status == 100){
                                
                                alert("Somthing went wrong. Please try again");
                                console.log(response);

                            }else if(response.error_status == 0){

                                load_post_save_btn();
                            }

                            console.log(response);
                        }
                    });
                });
                
            }

            //load all the comments from server
            function load_comments(){

                $.ajax({
                    url:`${domain()}ajax_pages?class=single&method=load_comments`,
                    method:"POST",
                    data:{
                        post_link:post_link
                    },
                    dataType:"json",
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

                        if(response.error_status == 1){

                            alert("error found");
                            console.log(response.errors)


                        }else if(response.error_status == 0){

                            $(".loading-spinner--comment").remove();

                            $(".sp-comm").remove();

                            $(".main-wrap__col-wrap--sp").append(response.html);

                        //    console.log(response);
                        }
                    }
                })
            };

             //load post ratings such as `like` and `dislike` from server
            function load_post_rate_btns(){
                
                $.ajax({
                    url:`${domain()}ajax_pages?class=single&method=load_post_rate_btns`,
                    method:"POST",
                    data:{
                        post_link:post_link
                    },
                    dataType:"json",
                    success:function(response){

                        if(response){

                            $(".sp-content__meta-list-item--like").html("").append(response.like_btn);

                            $(".sp-content__meta-list-item--dislike").html("").append(response.dislike_btn);
                        }

                       // console.log(response);
                    }
                })
            }
    
            //load the save post button with pressed class
            function load_post_save_btn(){

                $.ajax({
                    url:`${domain()}ajax_pages?class=single&method=load_post_save_btn`,
                    method:"POST",
                    data:{
                        post_link:post_link
                    },
                    dataType:"json",
                    success:function(response){

                        if(response){

                            $(".sp-content__meta-list-item--save").html("").append(response.save_btn);
                        }
                    }
                })
            }

            //================================

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

        /**
         * All Functions Ends
         */


        //Here you get an array of all the query string passed in URL
        const query_strings= new URLSearchParams(window.location.search);
        
        //Here you will store the post link
        var post_link="";

        //first check if the object has `v` paramter
        if(query_strings.has("v")){
            
            //Get `v` query string and store it in post_link
            post_link=query_strings.get("v");
        }
    

       
        // //load post ratings such as `like` and `dislike`
        load_post_rate_btns();

        // //load the save post button
        load_post_save_btn();

        // //load total subscribers and subscirbe button
        // load_total_subs_and_btn(post_link);

        //first load all the comments
        load_comments();
    
        setTimeout(add_events_after_loading_comments,1000);

        //first load all posts in sidebar
        // load_posts({
        //     catagory_id:0,
        //     load_position:"sidebar",
        //     post_link:post_link,
        // });

    
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

        $(".sidebar-lg__filter-btn--action").on("click",function(){

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