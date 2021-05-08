$(function(){

    const setting_type=document.querySelector("#settings-js").dataset.setting_type;

    if(setting_type == "account"){

        //use the function for loading personal information of logged user's 
        function load_personal_information(){

            $.ajax({
                url:`${post_request_url()}?class=settings&method=load_personal_information`,
                method:"POST",
                success:function(response){
    
                    if(response){

                        $(".acc-sett__opt--pi").remove();

                        $(".acc-sett__col-body").append(response);

                        //console.log(response);
                    }
                }
            });
        }

        //use the function for sending the request to the server side
        function send_request_to_update(form_input){

            const name_attr=form_input.attr("name");

            const input_value=form_input.val();

            $.ajax({
                url:`${post_request_url()}?class=settings&method=update_personal_info`,
                method:"POST",
                data:{
                    name_attr:name_attr,
                    input_value:input_value,
                    action:"update"
                },
                dataType:"json",
                success:function(response){

                    $(".acc-sett__msg--error").remove();
                    
                    if(response.error_status == 0){

                        load_personal_information();

                    }else if(response.error_status == 1){

                        for(const key in response.errors){

                            form_input.parents(".acc-sett__form-col--field").append(response.errors[key].error_msg);
                        }
                    }

                    console.log(response);

                }
            })



        }

        //call the function to show the information
        load_personal_information();

        
        setTimeout(function(){

            $(document).on("submit",".acc-sett__form",function(e){

                e.preventDefault();
            });

            $(document).on("click",".acc-sett__btn--edit",function(){

                //store the currently clicked  edit button
                const current_edit_btn=$(this);

                //store the name attribute from the current input
                const name_attr=$(this).attr("name");

                $.ajax({
                    url:`${post_request_url()}?class=settings&method=update_personal_info`,
                    method:"POST",
                    data:{
                        name_attr:name_attr,
                        action:"edit"
                    },
                    dataType:"json",
                    success:function(response){

                        $(".acc-sett__form-input").not(".acc-sett__form-input--file").remove();

                        $(".acc-sett__msg--error").remove();

                        if(response.error_status == 0){

                            current_edit_btn.closest(".acc-sett__ff-value").addClass("acc-sett__ff-value--hide");

                            current_edit_btn.parents(".acc-sett__form-field").append(response.html);

                            //catch the input element that was appended
                            const form_input=$(".acc-sett__form-input");
            
                            //focuse the input element
                            form_input.focus();
                            
                            //select the input element's value
                            form_input.select();

                            if(name_attr == "user_email"){

                                $(".acc-sett__msg").addClass("acc-sett__msg--hide");
                            }


                            form_input.on("focusout keyup",function(e){

                                //13 is the esc code for enter key
                                const enter_esc_key=13;
                        
                                if(e.type == "focusout"){

                                    if($(this).val() == ""){
                                    
                                        //remove the input wrap element
                                        form_input.parent().remove();

                                        $(".acc-sett__msg--error").remove();

                                        current_edit_btn.closest(".acc-sett__ff-value").removeClass("acc-sett__ff-value--hide");
                                    
                                        $(".acc-sett__msg").removeClass("acc-sett__msg--hide");

                                    }

                                }

                                if(e.type == "keyup"){

                                    if($(this).attr("name") == "user_desc"){
                                        
                                        const desc_length=$(this).val().length;

                                        $(".acc-sett__input-lc span").text(desc_length);
                                
                                        (desc_length > 600) ?  $(".acc-sett__input-lc").addClass("acc-sett__input-lc--error") : $(".acc-sett__input-lc").removeClass("acc-sett__input-lc--error");
                                    
                                    }

                                    if(e.which == enter_esc_key){

                                        if($(this).val() == ""){

                                            //remove the input wrap element
                                            form_input.parent().remove();

                                            $(".acc-sett__msg--error").remove();

                                            current_edit_btn.closest(".acc-sett__ff-value").removeClass("acc-sett__ff-value--hide");

                                            $(".acc-sett__msg").removeClass("acc-sett__msg--hide");

                                        }else{
                                         
                                            send_request_to_update($(this));
                                        }
                                    }
                                }
                            });
                        }
                        
                        //console.log(response);
                    }
                })

        

            });

        },100);


        //show file uplaod popup when clicks on change button
        $(".acc-sett__btn--change").on("click",function(){

            if($(this).data("change_action") == "profile"){

                $("input[name='profile_img']").click();

            }else if($(this).data("change_action") == "banner"){
                
                $("input[name='bg_img']").click();
            }

        });


        //show file uplaod popup when clicks on uplaod button
        $(".acc-sett__btn--upload").on("click",function(e){
            
            if($(this).data("upload_action") == "profile"){

                $("input[name='profile_img']").click();

            }else if($(this).data("upload_action") == "banner"){
                
                $("input[name='bg_img']").click();
            }
        
            //console.log($(this).closest(".acc-sett__form-input--file"));
        });

        //store both action forms
        const action_forms=$(".acc-sett__form--profile, .acc-sett__form--banner");

        //call the function
        action_forms.on("submit",function(e){
            
            //prevent the default behaviour
            e.preventDefault();
        });

        //store all the file inputs
        const file_inputs=$(".acc-sett__form-input--file");

        file_inputs.on("change",function(e){

            //store the changed input's form 
            const current_form=$(this).parents("form").get(0);

            const current_input=$(this);

            const form_data=new FormData(current_form);
            
            form_data.append("name_attr", $(this).attr("name"));

            $.ajax({
                url:`${post_request_url()}?class=settings&method=update_user_files`,
                method:"POST",
                data:form_data,
                contentType:false, 
                processData:false,
                dataType:"json",
                success:function(response){

                    $(".acc-sett__msg--error").remove();

                    if(response.error_status ==0){

                        //create FileReader Obj
                        const reader=new FileReader();

                        //read the file using the obj
                        reader.readAsDataURL(current_input.get(0).files[0]);
                        
                        //show the img on successfully load the image
                        reader.onload=function(){

                            current_input.parents(".acc-sett__opt").find(".acc-sett__img").attr(`src`,`${reader.result}`);  

                            current_input.parents("form").trigger("reset");        
                        }


                    }else if(response.error_status == 1){

                        for(const key in response.errors){

                            current_input.parents(".acc-sett__opt-info").append(response.errors[key].error_msg);
                        }
                    }

                    //console.log(response);
                }
            });
           

        });
      

    }else if(setting_type == "security"){
    
        
        //store all the form_input 
        const form_input=$(".secu-sett__form-input");

        //add a callback function
        form_input.on("focusin focusout",function(e){

            //store the form_input_wrap element
            const form_field=$(this).parents(".secu-sett__form-field");

            if(e.type == "focusin"){
                
                //add .secu-sec__form-input-wrap--focused class in input wrap
                if(!form_field.hasClass("secu-sett__form-field--focused")){

                    form_field.addClass("secu-sett__form-field--focused");

                    form_field.removeClass("secu-sett__form-field--error");
                }
            }

            if(e.type == "focusout"){

                //remove .secu-sec__form-input-wrap--focused class from input wrap
                if(form_field.hasClass("secu-sett__form-field--focused")){

                    form_field.removeClass("secu-sett__form-field--focused")
                }
            }
        });
    
        //store the eye button
        const eye_btn=$(".secu-sett__btn--eye");

        //add a callback function
        eye_btn.on("click",function(e){

            if(!$(this).hasClass("secu-sett__btn--active")){
 
                $(this).addClass("secu-sett__btn--active").find("i").remove();
                
                $(this).append(`<i class='fa fa-eye'></i>`);

                $(this).prev("input").attr("type","text");

            }else{

                $(this).removeClass("secu-sett__btn--active").find("i").remove();

                $(this).append(`<i class='fa fa-eye-slash'></i>`);
                
                $(this).prev("input").attr("type","password");
            }

        });

        //store the select input element
        const form_input_select=$(".secu-sett__form-input--select");

        //store the submit button
        const form_btn_delete=$(".secu-sett__btn--deleteAcc");
        
        //add a callback function
        form_input_select.on("change",function(e){

            if($(this).val() !== ""){
           
                //check if delete has .secu-sec__form-btn--disabled
                if(form_btn_delete.hasClass("secu-sett__btn--disable")){
                    
                    //remove .secu-sec__form-btn--disabled class from delete btn
                    form_btn_delete.removeClass("secu-sett__btn--disable") 
                }

            }else{

                //check if delete does not have .secu-sec__form-btn--disabled
                if(!form_btn_delete.hasClass("secu-sett__btn--disable")){

                    //add .secu-sec__form-btn--disabled class from delete btn
                    form_btn_delete.addClass("secu-sett__btn--disable")
                    
                }
            }
        });

        //store the change password form
        const changePwd_form=$(".secu-sett__form--changePwd");

        //store the change button
        const changePwd_btn=$(".secu-sett__btn--changePwd");

        //add a callback function
        changePwd_btn.on("click", function(e){

            e.preventDefault();   
            
            //collect all the data from change password form
            const form_data=changePwd_form.serializeArray();
            
            //store the all data for sending to the server side
            let data={};

            for(let i=0; i < form_data.length; i++){

                data[form_data[i].name]=form_data[i].value;
            }

            //custom valu adding for validating in the serverside
            data.setting="change";
            
            $.ajax({
                url:`${post_request_url()}?class=settings&method=security_settings`,
                method:"POST",
                data:data,
                dataType:"json",
                success:function(response){

                    $(".secu-sett__form-msg--error").remove();

                    if(response.error_status ==  1){

                        for(const key in response.errors){

                            $(`${response.errors[key].target}`).parent().append(`${response.errors[key].error_msg}`);
                            
                            $(`${response.errors[key].target}`).addClass("secu-sett__form-field--error");
                        }
                    

                    }else if(response.error_status ==  100){

                        show_popup_msg({
                            text:"<strong>Error</strong> : <span>Please try again</span>",
                            position_class:"popup-msg--top-right",
                            type_class:"popup-msg--error"
                        });

                        changePwd_form.trigger("reset");

                    }else if(response.error_status ==  0){

                        show_popup_msg({
                            text:"<strong>Success</strong> : <span>Password Changed</span>",
                            position_class:"popup-msg--top-right",
                            type_class:"popup-msg--success"
                        });
        
                        changePwd_form.trigger("reset");

                        setTimeout(function(){
                            
                            window.location.href=`${domain()}accounts/login`;

                        },2500)
                    }

                    console.log(response);
                    
                }
            });
        
        });
    }
    
    
   


});//ready functio