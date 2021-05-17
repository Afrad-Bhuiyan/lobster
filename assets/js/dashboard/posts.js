$(function(){

    const post_type=document.querySelector("#post-js").dataset.post_type;

    if(post_type == "myposts"){

        //Here you get an array of all the query string passed in URL
        const query_strings= new URLSearchParams(window.location.search);
        
        //Here you will store the filter query string
        var filter="";

        //Default page number for my posts
        var page=1;

        //first check if the object has `filter` paramter
        if(query_strings.has("filter")){
            
            //Get `filter` query string and store it in filter
            filter=query_strings.get("filter");
        }

        //first check if the object has `filter` paramter
        if(query_strings.has("page")){
            
            //Get `filter` query string and store it in filter
            page=query_strings.get("page");
        }


        //use the function to add events after printing my posts table
        function add_events(){
            
            //store class name of dropdown toggle button's
            const ddToggle_btn_class=".my-posts__btn--ddToggle";
            
            //select the button using jquery
            const ddToggle_btn=$(ddToggle_btn_class);

            $(document).on("click",ddToggle_btn_class,function(){

                //store dropdown optios element
                const dropdown_opts = $(".my-posts__dropdown-opts");
                
                //store the current dropdown options element
                var current_dropdown_opts = $(this).parent().find(".my-posts__dropdown-opts");
            
                const obj={
                
                    open:function(){

                        //store the object
                        const that=this;
                        
                        //remove any existing  .document-wrap elemenet
                        $(".document-wrap").remove();

                        //append a .document-wrap element into the body
                        $("body").append(`<div class="document-wrap"></div>`);

                        if(!current_dropdown_opts.hasClass("my-posts__dropdown-opts--show")){

                            //add the block class in dropdown options
                            current_dropdown_opts.addClass("my-posts__dropdown-opts--block");
                            
                            setTimeout(function(){
                                
                                //add the show class in dropdown options
                                current_dropdown_opts.addClass("my-posts__dropdown-opts--show");

                                //call the close function from object
                                $(".document-wrap").on("click",that.close);
                            
                            },50);
                        }
                    },

                    close:function(){

                        //remove the .documen-wrap first
                        $(".document-wrap").remove();
                        
                        if(current_dropdown_opts.hasClass("my-posts__dropdown-opts--show")){
                            
                            //remove the show from options
                            current_dropdown_opts.removeClass("my-posts__dropdown-opts--show");
                            
                            setTimeout(function(){
                                
                                //remove the block class from options
                                current_dropdown_opts.removeClass("my-posts__dropdown-opts--block");

                            },155);
                        }  
                    }
                }

                //call the open function to show the popup
                obj.open();

            });


            //select the checkbox labels
            const checkbox_labels=$(".my-posts__label");

            $(document).on("click",".my-posts__label",function(){

                //store the clicked checkbox label
                const current_checkbox_label=$(this);
                
                //store the clicked label's closest input checkbox
                const current_input_checkbox=$(this).parent().find(".my-posts__input--checkbox");

                //store current input's tr apart from tr--the
                const checkbox_tr=$(this).parents(".my-posts__tr--tbody");
                
            
                //store all the single input checkbox
                const single_inputs = $(".my-posts__input--single");

                if(!current_input_checkbox.prop("checked")){

                    //make input checkbox checked
                    current_input_checkbox.prop("checked",true);

                    if(checkbox_tr.length == 1){

                        //add a css class to highlight the checkbox_tr
                        checkbox_tr.addClass("my-posts__tr--selected");
                    }
                    

                }else{
                    
                    //make the input checkbox unchecked
                    current_input_checkbox.prop("checked",false);

                    if(checkbox_tr.length == 1){

                        //remove the class that was added to highlight the checkbox_tr
                        checkbox_tr.removeClass("my-posts__tr--selected");
                    }
                }

          
                
                setTimeout(function(){

                    //store all the checked single input checkboxes
                    let checked_inputs = $(".my-posts__input--single:checked");
                    
                    //store all the unchecked single input checkboxes
                    let unchecked_inputs =$(".my-posts__input--single").not(":checked");

                    if(current_checkbox_label.hasClass("my-posts__label--checkAll")){

                        if(current_input_checkbox.prop("checked")){
    
                            unchecked_inputs.each(function(index,input){
                                
                                //make the unchecked inputs checked
                                $(input).prop("checked",true);

                                $(input).parents(".my-posts__tr--tbody").addClass("my-posts__tr--selected");
                                
                            });

                        }else{
                        
                            checked_inputs.each(function(index,input){
    
                                //make the unchecked inputs checked
                                $(input).prop("checked",false);

                                $(input).parents(".my-posts__tr--tbody").removeClass("my-posts__tr--selected");

                            });
                        }

                    }
                    
                    //set the checked_inputs to updated checkd inputs
                    checked_inputs = $(".my-posts__input--single:checked");
                    
                    //set the unchecked_inputs to updated unchecked inputs
                    unchecked_inputs =$(".my-posts__input--single").not(":checked");

                    if(checked_inputs.length > 0){
                        
                        //when a single post is checked then appear the delete button
                        $(".my-posts__btn--delete").removeClass("my-posts__btn--hide");

                        if(checked_inputs.length == single_inputs.length){
                            
                            //condition true means all posts are checked. let's checked the checkAll input
                            $(".my-posts__input--checkAll").prop("checked",true);
                        }
                        
                        
                    }else{
                        
                        //when a single post not is checked then disappear the delete button
                        $(".my-posts__btn--delete").addClass("my-posts__btn--hide");

                        //unchecked the checkAll input
                        $(".my-posts__input--checkAll").prop("checked",false);
                    }
                },50)
            });
            
        }
    
        //Load all logged user posts
        function load_my_posts(){

            $.ajax({
                url:`${post_request_url()}?class=posts&&method=load_my_posts`,
                method:"POST",
                data:{
                    filter:filter,
                    page:page
                },
                success:function(response){

                    //first make the parent element empty
                    $(".my-posts__col-body").html(" ");

                    //append all the HTML in parent element
                    $(".my-posts__col-body").append(response);
            
                }
            });
        }

        


        // //store data-load_post attribute value
        // const load_posts=$(".elements__my-posts").data("load_posts");

        //check if logged in user posted any posts
        if(true){    
            load_my_posts();
        }

        setTimeout(function(){

            add_events();

        },1000)

        //catch the current location
        const current_location=location.href;

        //adding active class in topbar links
        const topbar_links=$(".my-posts__topBar-link");

        $.each(topbar_links,function(index, topbar_link){

            if(topbar_link.href == current_location){

                topbar_link.classList.add("my-posts__topBar-link--active");
            }
        });

        //store topbar dropdown filter
        const topbar_filter=$(".my-posts__input--filter");

        topbar_filter.on("change",function(e){

            const selected_value=$(this).val();

            if(selected_value !== ""){
                $.ajax({
                    url:`${post_request_url()}?class=posts&&method=filter_my_posts`,
                    method:"POST",
                    data:{
                        selected_value:selected_value,
                        filter:filter
                    },
                    success:function(response){

                        //first make the parent element empty
                        $(".my-posts__col-body").html(" ");

                        //append all the HTML in parent element
                        $(".my-posts__col-body").append(response);

                        console.log(response);
                    }
                }); 

            }else{
                
                //load all the post if user chooses `filter posts` option
                load_my_posts();
            }

        });


        //store all the forms in my posts section
        const my_posts_forms=$(".my-posts__form");

        my_posts_forms.each(function(index,form){
            
            $(form).on("submit",function(e){

                //prevent the default behaviour on submitting the form
                e.preventDefault();
            });
        });
        

        //store all inputs in my-posts section
        const form_inputs=$(".my-posts__input");

        form_inputs.on("focusin focusout",function(e){

            if(e.type == "focusin"){

                $(this).parents(".my-posts__form-field").addClass("my-posts__form-field--focused");
                
            }else if(e.type == "focusout"){
                
                $(this).parents(".my-posts__form-field").removeClass("my-posts__form-field--focused");
            }

        });

        //store the topbar search input
        const search_input=$(".my-posts__input--search");

        search_input.on("keyup",function(e){

            //store the search query
            const search_query=$(this).val();

            if(search_query !== ""){

                $.ajax({
                    url:`${post_request_url()}?class=posts&&method=search_my_posts`,
                    method:"POST",
                    data:{
                        search_query:search_query,
                        filter:filter
                    },
                    success:function(response){

                        if(response){

                            //first make the parent element empty
                            $(".my-posts__col-body").html(" ");

                            //append all the HTML in parent element
                            $(".my-posts__col-body").append(response);

                        }
                        
                        console.log(response)
                    }
                });

            }else{

                load_my_posts();
            }

          
            




        });

        //store the posts delete button
        const post_delete_btn=$(".my-posts__btn--delete");

        post_delete_btn.on("click",function(e){

            //store all the checked inputs
            const checked_inputs = $(".my-posts__input--single:checked");

            const dymanic_text = (checked_inputs.length > 1) ? `${checked_inputs.length} posts` : `${checked_inputs.length} post`;
            
            const post_ids = [];

            
            checked_inputs.each(function(index,input){

                post_ids.push($(input).val());
            });

            if(post_ids.length !== 0){

                modal.open("post_delete",{
                
                    modal_text:`
                        You are about to delete <strong>${dymanic_text}</strong>. 
                        Deleting posts will permanantly delete all the comments, notifications, ratings etc. 
                        Are you sure to proceed with the action? This action can't be undone.
                    `,
                    onTrue:function(){
    
                        //store the true button
                        const trueBtn = $(this);
                        
                        //store the selected modal
                        const selected_modal = trueBtn.parents(".modal");
                        
                        $.ajax({
                            url:`${post_request_url()}?class=posts&method=delete_my_posts`,
                            method:"POST",
                            data:{
                                post_ids:post_ids
                            },
                            success:function(response){
    
                                console.log(response)
                            }
                        })
    
    
                        //call the modal function to hide
                        selected_modal.modal("hide");
    
                        selected_modal.on("hidden.bs.modal",function(e){
    
                            //permanantly remove the modal from the dom
                            $(this).remove();
                        });
                
                        console.log("delete posts");
                        
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
                     
                        console.log("don't delete posts");
    
                    }
                });

            }else{

                alert("Please selected at a single post");
            }
        });


    }else if(post_type == "publish"){

        const editor=document.querySelector("#editor");

        let data;

        //Initialize and configer CKeditor
        ClassicEditor.create(editor,{

            removePlugins:["BlockQuote","ImageUpload","MediaEmbed","Indent"],

            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            }
        }).then(function(newEditor){

            data=newEditor;
            
        }).catch(function(error){

            console.log(error);
        });

        const dropzone=document.querySelector(".pp-sec__dropzone");

        const dropzone_input=document.querySelector(".pp-sec__input--file");

        const publish_form=document.querySelector(".pp-sec__form--publish");

        //Preview the image after upload
        function show_post_img(event, files){

            //All dropped files
            const uploaded_files=files;

            //Include dropped files into dropzone_input.files
            if(event == "drop"){
                dropzone_input.files=uploaded_files;
            }

            const form_data=new FormData(publish_form);

            //append all the upload images in FormData obj
            for(let i=0; i < uploaded_files.length; i++){
                
                form_data.append("post_img[]",uploaded_files[i])
            }

            //add an extra key for catching the reqeust
            form_data.append("request","post_img");

            $.ajax({
                url:`${post_request_url()}?class=posts&method=add_the_post`,
                method:"POST",
                data:form_data,
                contentType:false,
                processData:false,
                dataType:"json",
                success:function(response){

                    //before showing the error_msg first remove that
                    $(".pp-sec__form-msg").remove();

                    if(response.error_status == 1){
                        
                        //store all the errors
                        const errors = response.errors;

                        for(const key in errors){

                            //print all the errors
                            $(`${errors[key].target}`).append(`${errors[key].error_msg}`)
                        }

                    }else if(response.error_status == 0){

                        //create FileReader Object
                        const reader=new FileReader();

                        //read the file using the obj
                        reader.readAsDataURL(uploaded_files[0]);
                        
                        //show the img on successfully load the image
                        reader.onload=function(){

                            //show the uplaoded Image
                            $(".pp-sec__dropzone-img").attr(`src`,`${reader.result}`);
                            
                            //Disappear the dropzone caption. we can show it If we hover on dropzone
                            if(!$(".pp-sec__dropzone-cap").hasClass("pp-sec__dropzone-cap--hide")){

                                $(".pp-sec__dropzone-cap").addClass("pp-sec__dropzone-cap--hide")
                            }

                            //Appear uploaded image close button 
                            if($(".pp-sec__form-btn--close").hasClass("pp-sec__form-btn--hide")){

                                $(".pp-sec__form-btn--close").removeClass("pp-sec__form-btn--hide");

                                $(".pp-sec__form-btn--close").on("click",uploaded_img_close);
                            }
                            
                            //remove event from dropzone so that a popup can not appear after clicking on dropzone
                            dropzone.removeEventListener("click",show_file_upload_popup);
                        }
                    }

                    console.log(response);
                }
            });
        }

        function show_file_upload_popup(){

            dropzone_input.click();
        }

        function uploaded_img_close(){

            //First, Make the input[type='file'] null
            dropzone_input.value="";

            //replace the uploaded image with the placholder image
            $(".pp-sec__dropzone-img").attr(`src`,`${domain()}app/uploads/posts/post-placeholder.jpg`);
            
            //Appear the dropzone hover caption
            if($(".pp-sec__dropzone-cap").hasClass("pp-sec__dropzone-cap--hide")){

                $(".pp-sec__dropzone-cap").removeClass("pp-sec__dropzone-cap--hide")
            }

            //Disappear the close button
            if(!$(".pp-sec__form-btn--close").hasClass("pp-sec__form-btn--hide")){

                $(".pp-sec__form-btn--close").addClass("pp-sec__form-btn--hide");

                //Finally, add event to dropzone so that Image can be uploaded
                dropzone.addEventListener("click",show_file_upload_popup);
            }
            
        }

        dropzone.addEventListener("dragover",function(e){
            e.preventDefault();
            this.classList.add("pp-sec__dropzone--focused");
        });

        dropzone.addEventListener("dragleave",function(e){
            e.preventDefault();
            this.classList.remove("pp-sec__dropzone--focused");
        });

        dropzone.addEventListener("click",show_file_upload_popup);

        dropzone_input.addEventListener("change",function(){

            show_post_img("change",this.files);
        })

        dropzone.addEventListener("drop",function(e){
            e.preventDefault();

            this.classList.remove("pp-sec__dropzone--focused");
    
            show_post_img("drop",e.dataTransfer.files)
        });


        //check post_title-length
        const input_title=$(".pp-sec__form-row--title .pp-sec__input--text");

        input_title.on("keyup",function (){
           
            const input_title_length=$(this).val().length;

            if(input_title_length > 100){

                $(".pp-sec__form-label--title").addClass("pp-sec__form-label--error");
                
            }else{ 

                $(".pp-sec__form-label--title").removeClass("pp-sec__form-label--error");
            }

            $(".pp-sec__form-label--title span").text(input_title_length);

        });

        //store the posts publish button
        const form_btn_publish=$(".pp-sec__form-btn--publish");

     
        form_btn_publish.on("click",function(e){
            
            e.preventDefault();

            const form_data=new FormData(publish_form);

            form_data.append("post_desc",data.getData());

            form_data.append("request","post_form");

            $.ajax({
                url:`${post_request_url()}?class=posts&method=add_the_post`,
                method:"POST",
                data:form_data,
                contentType:false,
                processData:false,
                dataType:"json",
                success:function(response){

                    $(".pp-sec__form-msg").remove();
                
                    if(response.error_status == 1){

                         //store all the errors
                         const errors = response.errors;

                         for(const key in errors){
 
                             //print all the errors
                             $(`${errors[key].target}`).append(`${errors[key].error_msg}`)
                         }

                    }else if(response.error_status == 0){

                        $(".pp-sec__form--publish").trigger("reset");

                        //replace the uploaded image with the placholder image
                        $(".pp-sec__dropzone-img").attr(`src`,`${domain()}app/uploads/posts/post-placeholder.jpg`);
                        
                        //Appear the dropzone hover caption
                        if($(".pp-sec__dropzone-cap").hasClass("pp-sec__dropzone-cap--hide")){

                            $(".pp-sec__dropzone-cap").removeClass("pp-sec__dropzone-cap--hide")
                        }

                        //Disappear the close button
                        if(!$(".pp-sec__form-btn--close").hasClass("pp-sec__form-btn--hide")){

                            $(".pp-sec__form-btn--close").addClass("pp-sec__form-btn--hide");
                        }
                        
                        window.location.href=`${user_profile_link()}dashboard/posts?sub_option=my_posts&filter=all`;

                    }


                    console.log(response);
                }
            })
        });
    }else if(post_type == "edit"){

        const editor=document.querySelector("#editor");

        let data;
    
        //Initialize and configer CKeditor
        ClassicEditor.create(editor,{
    
            removePlugins:["BlockQuote","ImageUpload","MediaEmbed","Indent"],
    
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            }
        }).then(function(newEditor){
    
            data=newEditor;
            
        }).catch(function(error){
    
            console.log(error);
        });
    
        const dropzone=document.querySelector(".ep-sec__dropzone");
    
        const dropzone_input=document.querySelector(".ep-sec__input--file");

        const edit_form=document.querySelector(".ep-sec__form--edit");
        
        //Preview the image after upload
        function show_post_img(event, files){
    
            //All dropped files
            const uploaded_files=files;
    
            //Include dropped files into dropzone_input.files
            if(event == "drop"){
                dropzone_input.files=uploaded_files;
            }
    
            const form_data=new FormData(edit_form);
    
            //append all the upload images in FormData obj
            for(let i=0; i < uploaded_files.length; i++){
                
                form_data.append("post_img_new[]",uploaded_files[i])
            }
    
            //add an extra key for catching the reqeust
            form_data.append("request","post_img_new");
    
            $.ajax({
                url:`${post_request_url()}edit_the_post`,
                method:"POST",
                data:form_data,
                contentType:false,
                processData:false,
                dataType:"json",
                success:function(response){
    
                    //before showing the error_msg first remove that

                    $(".ep-sec__form-msg").remove();
    
                    if(response == 1){
    
                        //create FileReader Obj
                        const reader=new FileReader();
    
                        //read the file using the obj
                        reader.readAsDataURL(uploaded_files[0]);
                        
                        //show the img on successfully load the image
                        reader.onload=function(){

                            $(".ep-sec__dropzone-img").attr(`src`,`${reader.result}`);

                            if(!$(".ep-sec__dropzone-cap").hasClass("ep-sec__dropzone-cap--hide")){

                                $(".ep-sec__dropzone-cap").addClass("ep-sec__dropzone-cap--hide")
                            }

                            if($(".ep-sec__form-btn--close").hasClass("ep-sec__form-btn--hide")){

                                $(".ep-sec__form-btn--close").removeClass("ep-sec__form-btn--hide");
                            }   
                                         
                            dropzone.removeEventListener("click",show_file_upload_popup);

                            dropzone.removeEventListener("dragover", dragover_function);
            
                            dropzone.removeEventListener("dragleave",dragleave_function);
            
                            dropzone.removeEventListener("drop",drop_function);
                        }

    
                    }else if(typeof response == "object"){
    
                        //Printing the error msg from obj
                        for(const key in response){

                            $(`${response[key].target}`).append(`${response[key].error_msg}`);
                        }
                    }

                    console.log(response);
                }
            });
        }


        //Call the function when click event triggers over the dropzone
        function show_file_upload_popup(){

            dropzone_input.click();
        }

        //Call the function when dragover event triggers over the dropzone
        function  dragover_function(e){

            e.preventDefault();
            this.classList.add("ep-sec__dropzone--focused");

        }

        //Call the function when dragleave event triggers over the dropzone
        function  dragleave_function(e){
            
            e.preventDefault();
            this.classList.remove("ep-sec__dropzone--focused");
            
        }

        //Call the function when drop event triggers over the dropzone
        function  drop_function(e){

            e.preventDefault();
            this.classList.remove("ep-sec__dropzone--focused");
            show_post_img("drop",e.dataTransfer.files);
            
        }

        dropzone_input.addEventListener("change",function(){
    
            show_post_img("change",this.files);
        })
    
        const close_btn=$(".ep-sec__form-btn--close");

        close_btn.on("click",function(){
            
            //First, Make the input[type='file'] null
            dropzone_input.value="";

            //Make the input[type='hidden'] null
            document.querySelector(".ep-sec__input--post-img-old").value="";

            //replace the uploaded image with the placholder image
            $(".ep-sec__dropzone-img").attr(`src`,`${domain()}app/uploads/posts/post-placeholder.jpg`);
            
            //Appear the dropzone hover caption
            if($(".ep-sec__dropzone-cap").hasClass("ep-sec__dropzone-cap--hide")){

                $(".ep-sec__dropzone-cap").removeClass("ep-sec__dropzone-cap--hide")
            }

            //Disappear the close button
            if(!$(".ep-sec__form-btn--close").hasClass("ep-sec__form-btn--hide")){

                $(".ep-sec__form-btn--close").addClass("ep-sec__form-btn--hide");

                //Finally, add event to dropzone so that Image can be uploaded
                dropzone.addEventListener("click",show_file_upload_popup);

                dropzone.addEventListener("dragover", dragover_function);

                dropzone.addEventListener("dragleave",dragleave_function);

                dropzone.addEventListener("drop",drop_function);
            }
        });
    

        const input_title=$(".ep-sec__input--title");
    
        //check post_title-length
        input_title.on("keyup",function(e){
           
            const title_length=$(this).val().length;
    
            if(title_length > 100){
    
                $(".ep-sec__form-label--title").addClass("ep-sec__form-label--error");
                
            }else{ 
    
                $(".ep-sec__form-label--title").removeClass("ep-sec__form-label--error");
    
            }

            $(".ep-sec__form-label--title span").text(title_length);

        });
    
    
        //submitting the form 
        const save_btn=$(".ep-sec__form-btn--save");

        save_btn.on("click",function(e){
            
            e.preventDefault();
    
            const form_data=new FormData(edit_form);

            form_data.append("post_desc",data.getData());
            form_data.append("request","post_form");
    
            $.ajax({
                url:`${post_request_url()}edit_the_post`,
                method:"POST",
                data:form_data,
                contentType:false,
                processData:false,
                dataType:"json",
                success:function(response){
    
                    $(".ep-sec__form-msg").remove();

                    if(response == 1){

                        $(".ep-sec__form--edit").trigger("reset");
    
                        window.location.href=`${user_profile_link()}dashboard?posts=myposts&filter=all`;

                    }else if(typeof response == "object"){

                        for(const key in response){
    
                            $(`${response[key].target}`).append(`${response[key].error_msg}`)
                        }
                    }

                    console.log(response);
                }
            })
        });
    }else if(post_type == "saved"){
        
        //store the saved posts action button
        const saved_posts_act_btn=$(".saved-posts__btn--action");

        saved_posts_act_btn.on("click",function(e){

            const saved_posts_dropdown=$(this).parent().find(".saved-posts__dropdown");

            if($(".saved-posts__dropdown").not(saved_posts_dropdown).hasClass("saved-posts__dropdown--block")){

                $(".saved-posts__dropdown").removeClass("saved-posts__dropdown--block");

                $(".saved-posts__dropdown").removeClass("saved-posts__dropdown--show");
            }
            if(!saved_posts_dropdown.hasClass("saved-posts__dropdown--block")){

                saved_posts_dropdown.addClass("saved-posts__dropdown--block");
                
                setTimeout(function(){

                    saved_posts_dropdown.addClass("saved-posts__dropdown--show");

                },50)

            }else{

                saved_posts_dropdown.removeClass("saved-posts__dropdown--show");
                
                setTimeout(function(){

                    saved_posts_dropdown.removeClass("saved-posts__dropdown--block");

                },155)

            }
        });

        const dropdown_remove_link=$(".saved-posts__dropdown-link--remove");

        dropdown_remove_link.on("click",function(e){

            //store the saved post id
            const sp_id=$(this).data("sp_id");

            const saved_post_single_row=$(this).parents(".saved-posts__single-row");

       
           
            $.ajax({

                url:`${post_request_url()}delete_saved_posts`,
                method:"POST",
                data:{
                    sp_id:sp_id
                },
                dataType:"json",
                success:function(response){

                    if(response.error == 0){

                        saved_post_single_row.fadeOut(500,function(){

                            if(response.total_saved_posts == 0){

                                window.location.href = window.location.href;

                            }else{

                                $(".saved-posts__total-num").text(response.total_saved_posts);

                            }
                        });
                    
                    }else if(response.error == 1){

                        alert("Couldn't remove the posts. Please try again");

                    }
                    

                    console.log(response);
                }

            });

        });
        
        


    }
});//ready function