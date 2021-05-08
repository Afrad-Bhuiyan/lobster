$(function(){

    const post_type=document.querySelector("#post-js").dataset.post_type;

    if(post_type == "myposts"){

        //Here you get an array of all the query string passed in URL
        const query_strings= new URLSearchParams(window.location.search);
        
        //Here you will store the filter query string
        var filter="";

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

        //Load all logged user posts
        function load_my_posts(){

            $.ajax({
                url:`${post_request_url()}load_my_posts`,
                method:"POST",
                data:{
                    filter:filter,
                    page:page
                },
                success:function(response){

                    if(response){

                        //first make the parent element empty
                        $(".my-posts__post-col").html(" ");

                        //append all the HTML in parent element
                        $(".my-posts__post-col").append(response);

                        //console.log(response);
                    }
                }
            });
        }

        //store data-load_post attribute value
        const load_posts=$(".elements__my-posts").data("load_posts");

        //check if logged in user posted any posts
        if(load_posts == 1){    

            load_my_posts();
        }

        //catch the current location
        const current_location=location.href;

        //adding active class in topbar links
        const topbar_links=$(".my-posts__topbar-link");

        $.each(topbar_links,function(index, topbar_link){

            if(topbar_link.href == current_location){

                topbar_link.classList.add("my-posts__topbar-link--active");
            }
        });

        //store topbar dropdown filter
        const topbar_filter=$(".my-posts__select-input--topbar-filter");

        topbar_filter.on("change",function(e){

            const selected_value=$(this).val();

            if(selected_value !== ""){
                $.ajax({
                    url:`${post_request_url()}filter_my_posts`,
                    method:"POST",
                    data:{
                        selected_value:selected_value,
                        filter:filter
                    },
                    success:function(response){

                        //first make the parent element empty
                        $(".my-posts__post-col").html(" ");
                        
                        //append all the HTML in parent element
                        $(".my-posts__post-col").append(response);    

                        //console.log(response);
                    }
                }); 

            }else{
                
                //load all the post if user chooses `filter posts` option
                load_my_posts();
            }

        });

        //prevent the search form to submit
        $(".my-posts__form--search").on("submit",function(e){

            e.preventDefault();
        });
        
        //store the topbar search input
        const topbar_search_input=$(".my-posts__input--search");

        topbar_search_input.on("focusin focusout",function(e){

         
            if(e.type == "focusin"){

                $(this).parents("form").addClass("my-posts__form--focused");
                
            }else if(e.type == "focusout"){
                
                $(this).parents("form").removeClass("my-posts__form--focused");
            }

        });

        topbar_search_input.on("keyup",function(e){

            //store the search query
            const search_query=$(this).val();

            if(search_query !== ""){

                $.ajax({
                    url:`${post_request_url()}search_my_posts`,
                    method:"POST",
                    data:{
                        search_query:search_query,
                        filter:filter
                    },
                    success:function(response){

                        if(response){

                            //first make the parent element empty
                            $(".my-posts__post-col").html(" ");

                            //append all the HTML in parent element
                            $(".my-posts__post-col").append(response);
                        }
                        
                        //console.log(response)
                    }
                });

            }else{

                load_my_posts();
            }

          
            




        });

        $(".post-checkbox label").on("click",function(){


            if($(this).parents(".post-checkbox").hasClass("all")){

                $(this).parents(".post-checkbox").toggleClass("checked");

                if($(this).parents(".post-checkbox").hasClass("checked")){

                    //add class on all .table-row.post element for styling
                    $(".table-row.post").addClass("checked");

                    //click all input's that are not checked for selecting them
                    $(".table-row.post input[type='checkbox']").not(":checked").click();
                    
                    
                }else{
                    
                    ///click all input's that are aleardy checked for deselecting them
                    $(".table-row.post input[type='checkbox']:checked").click();

                    //remove the class .table-row.post element for destyling
                    $(".table-row.post").removeClass("checked");

                }
            }

            
            if($(this).parents(".post-checkbox").hasClass("single")){

                //toggle the .checked class for styling
                $(this).parents(".table-row.post").toggleClass("checked");

                setTimeout(function(){

                    //Total checkbox length
                    const total_post_checkbox_length=$(".post-checkbox.single input[type='checkbox']").length;
                    
                    //Total checked checkbox length
                    const total_checked_post_length=$(".post-checkbox.single input[type='checkbox']:checked").length;
                    
                    if(total_post_checkbox_length == total_checked_post_length){

                        //add the check mark from  all the post checkedbox
                        $(".post-checkbox.all input[type='checkbox']").not(":checked").click();
                    }
                    
                },50)
            }

            
            setTimeout(function(){

                let checked_inputs=$(".post-checkbox.single input[type='checkbox']:checked");

                if(checked_inputs.length > 0){

                    if($(".record-delete-btn").hasClass("d-none")){

                        //appear the .record-delete-btn
                        $(".record-delete-btn").removeClass("d-none")
                        
                    }
                    
                }else{
                    
                    if(!$(".record-delete-btn").hasClass("d-none")){
                        
                        //disappear the .record-delete-btn
                        $(".record-delete-btn").addClass("d-none")
                    }

                    //remove the check mark from  all the post checkedbox
                    $(".post-checkbox.all input[type='checkbox']:checked").click();
                }

            },50)
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
                url:`${post_request_url()}add_the_post`,
                method:"POST",
                data:form_data,
                contentType:false,
                processData:false,
                dataType:"json",
                success:function(response){

                    //before showing the error_msg first remove that
                    $(".pp-sec__form-msg").remove();

                    if(response == 1){

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

                    }else if(typeof response == "object"){

                        //Printing the error msg from obj
                        for(const key in response){

                            $(`${response[key].target}`).append(`${response[key].error_msg}`)
                        }
                    }
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

        const form_btn_publish=$(".pp-sec__form-btn--publish");

        //submitting the form 
        form_btn_publish.on("click",function(e){
            
            e.preventDefault();

            const form_data=new FormData(publish_form);

            form_data.append("post_desc",data.getData());

            form_data.append("request","post_form");

            $.ajax({
                url:`${post_request_url()}add_the_post`,
                method:"POST",
                data:form_data,
                contentType:false,
                processData:false,
                dataType:"json",
                success:function(response){

                    $(".pp-sec__form-msg").remove();

                    if(response == 1){

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