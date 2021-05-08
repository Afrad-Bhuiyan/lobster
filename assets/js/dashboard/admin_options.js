$(function(){

    const option_type=document.querySelector("#admin-opt-js").dataset.option_type;

   

    if(option_type == "users"){

        //Here you get an array of all the query string passed in URL
        const query_strings= new URLSearchParams(window.location.search);

        let page_no=1;

        //first check if the object has `filter` paramter
        if(query_strings.has("page_no")){
            
            //Get `filter` query string and store it in filter
            page_no=query_strings.get("page_no");
        }


        //use the function to add event to user role option
        //when all users list is load properly
        function add_event_to_opt(){

            //store role change circle check boxs
            const role_options=$(".user-list__opt");

            $(document).on("click",".user-list__opt",function(){
                
                //store user role change form
                const user_role_form =  $(this).parents("form");

                //selected the option's input
                const radio_input = $(this).find(".user-list__radio");
            
                if(!radio_input.prop("checked")){

                    modal.open("role_change",{

                        onTrue:function(){

                            //store the true button for which the function  is excuting
                            const true_btn=$(this);
                            
                            //store the selected modal
                            const selected_modal=true_btn.parents(".modal");

                            //collect user role form's data
                            const form_data=user_role_form.serializeArray();

                            //store the value to send to the server side
                            const data = {};

                            //Run a loop to convert the array into an object
                            for(let i=0; i < form_data.length; i++){
                                
                                 data[form_data[i].name] = form_data[i].value;
                            }

                            //override the user role to clicked radio input's value
                            data.user_role = radio_input.val(); 

                            $.ajax({
                                url:`${post_request_url()}?class=admin_options&method=change_the_user_role`,
                                method:"POST",
                                data:data,
                                dataType:"json",
                                beforeSend:function(){

                                    true_btn.find("span").text("Procceding...")

                                },
                                success:function(response){
                    
                                    if(response.error_status == 1){

                                        true_btn.addClass("modal__btn--error");
                                        
                                        true_btn.find("span").text("Error. Try again");

                                        setTimeout(() => {

                                            //show the selected modal using modal() method of bootstrap
                                            selected_modal.modal("hide");
                                            
                                            //being hidden 100% let's remove the entire html from DOM
                                            selected_modal.on("hidden.bs.modal",function(){

                                                $(this).remove();

                                            });
                                            
                                        }, 2000);

                                    }else if(response.error_status == 100){
                                        
                                        true_btn.addClass("modal__btn--error");
                                        
                                        true_btn.find("span").text("Error. Try again");

                                        setTimeout(() => {

                                            //show the selected modal using modal() method of bootstrap
                                            selected_modal.modal("hide");
                                            
                                            //being hidden 100% let's remove the entire html from DOM
                                            selected_modal.on("hidden.bs.modal",function(){

                                                $(this).remove();

                                            });
                                            
                                        }, 2000);


                                    }else if(response.error_status == 0){

                                        selected_modal.modal("hide");
                                        
                                        //being hidden 100% let's remove the entire html from DOM
                                        selected_modal.on("hidden.bs.modal",function(){

                                            $(this).remove();

                                            load_all_users();
                                        });
                                    }

                                    //console.log(response);
                                
                                }
                            });

            


                         

                         

                        },
                        
                        onFalse:function(){

                            //store the true button for which the function  is excuting
                            const true_btn=$(this);

                            //store the selected modal
                            const selected_modal=true_btn.parents(".modal");

                            //show the selected modal using modal() method of bootstrap
                            selected_modal.modal("hide");
                            
                            //being hidden 100% let's remove the entire html from DOM
                            selected_modal.on("hidden.bs.modal",function(){
                                $(this).remove();
                            });
                        
                        }

                    })  
                }
            });

        }


        //use the functions to load all users
        function load_all_users(){
            
            $.ajax({
                url:`${post_request_url()}?class=admin_options&method=load_all_users`,
                method:"POST",
                data:{
                    page_no:page_no
                },
                success:function(response){
    
                    $(".user-list__col-body").html(" ");
                    
                    $(".user-list__col-body").append(response);
    
                    add_event_to_opt();
                }
                
            });
        }

        //call the `load_all_users`
        load_all_users();

        //store the top search bar
        const search_form=$(".user-list__form--search");

        search_form.on("submit",function(e){
            e.preventDefault();
        });

        //store the form search input
        const search_input=$(".user-list__form-input--search");

        search_input.on("focusin focusout keyup",function(e){

            if(e.type == "focusin"){

                //add focused class on form field
                $(this).parent().addClass("user-list__form-field--focused");
                
            }else if(e.type == "focusout"){
                
                //remove focused class on form field
                $(this).parent().removeClass("user-list__form-field--focused");

            }else if(e.type == "keyup"){

                //store the search query
                const search_query=$(this).val();

                if(search_query !== ""){

                    $.ajax({
                        url:`${post_request_url()}?class=admin_options&method=search_user`,
                        method:"POST",
                        data:{
                            search_query:search_query
                        },
                        success:function(response){

                            $(".user-list__col-body").html(" ");
                    
                            $(".user-list__col-body").append(response);
                        }
                        
                    });

                }else{

                    load_all_users();
                }
            }

        });
      
     

        

        



       
    }else if(option_type == "catagories"){

     

       

    }
    
    
   


});//ready functions