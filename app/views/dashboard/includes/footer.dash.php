            </div><!--elements-->
        </section>
    </main>
    <script>
        /*=====Global Functions=====*/
    
        //get the post request url
        function post_request_url(){

            let output;

            if(location.hostname !== "projects.afradbhuiyan.com"){

                output=`${location.origin}/lobster/<?php echo "ajax_users/{$user_info['user_name']}"; ?>`;

            }else{
                output=`${location.origin}/lobster/<?php echo "ajax_users/{$user_info['user_name']}"; ?>`;
            }

            return output;
        }

        //get the user's profile link
        function user_profile_link(){

            let output;

            if(location.hostname !== "projects.afradbhuiyan.com"){

                output=`${location.origin}/lobster/<?php echo "users/{$user_info['user_name']}/"; ?>`;

            }else{
                output=`${location.origin}/lobster/<?php echo "users/{$user_info['user_name']}/"; ?>`;
            }

            return output;
        }

        //Catching the domain dynamically
        function domain(){

            let output;

            if(location.hostname !== "projects.afradbhuiyan.com"){

                output=`${location.origin}/lobster/`;

            }else{
                output=`${location.origin}/lobster/`;
            }

            return output;

        }

        //Copy any text to the clipboard
        function copy(str_value = null){

            if(str_value == null){

                return false;
            }

            //create a virtual input element for selecting
            const inputElement=document.createElement("input");

            //set the input element's value to str_value
            inputElement.setAttribute("value",str_value);

            //append the input element into the body
            document.body.appendChild(inputElement);

            //select input element's value
            inputElement.select();

            //Copy input element's value
            document.execCommand("copy");

            //Finially Remove the input element from body
            inputElement.parentNode.removeChild(inputElement);

            return true;
        };


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
                url:`${post_request_url()}/get_unread_notifications`,
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
                        $(".navbar__dropdown-toggle--nf").html(response.bell_btn);

                    }

                    //console.log(response);
                }
            })
        }

        //use the function to add a modal
        function show_popup_modal(modal_name = null){

            const modal_content={
                role_change:{
                    modal_class:"modal--roleChange",
                    modal_title:"Attention",
                    modal_text:`
                        You are about to delete a public comment from your post. Are you sure to proceed with the action? This action can't be undone
                    `,
                    modal_false_btn_text:`Cancel`,
                    modal_true_btn_text:`Yes. Procced`
                }
            }

            const modal=`
                <div class="modal fade ${modal_content[modal_name].modal_class}">
                    <div class="modal-dialog modal-dialog-centered modal__dialog">
                        <div class="modal-content modal__content">
                            <div class="modal__content-wrap">
                                <div class="modal__head">
                                    <div class="modal__icon"></div>
                                    <h3 class="modal__title">
                                        ${modal_content[modal_name].modal_title}
                                    </h3>
                                </div>

                                <div class="modal__body">
                                    <p class="modal__text">
                                        ${modal_content[modal_name].modal_text}
                                    </p>
                                </div>
                                
                                <div class="modal__footer">
                                    <button class="modal__btn modal__btn--false modal__btn--cancel" type="button">
                                        <span class="modal__btn-text">
                                            ${modal_content[modal_name].modal_false_btn_text}
                                        </span>
                                    </button>

                                    <button class="modal__btn modal__btn--true modal__btn--proceed" type="button">
                                        <span class="modal__btn-text">
                                            ${modal_content[modal_name].modal_true_btn_text}
                                        </span>
                                    </button>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            `;

            $(".modal").remove();
            
            $("body").append(modal);

            $(`.${modal_content[modal_name].modal_class}`).modal("show");
            
            $(`
                .${modal_content[modal_name].modal_class} .modal__btn--true,
                .${modal_content[modal_name].modal_class} .modal__btn--false
            `).on("click",function(){

                $(`.${modal_content[modal_name].modal_class}`).modal("hide");

                if($(this).hasClass("modal__btn--true")){

                    return true;
                    
                }else if($(this).hasClass("modal__btn--false")){
                    
                    return false;
                }

            });
        }

        const modal={

            modal_content:{

                role_change:{
                    modal_class:"modal--roleChange",
                    modal_title:"Attention",
                    modal_text:`
                        You are about to delete a public comment from your post. Are you sure to proceed with the action? This action can't be undone
                    `,
                    modal_false_btn_text:`Cancel`,
                    modal_true_btn_text:`Yes. Procced`,
                    onTrue:function(){},
                    onFalse:function(){}
                    
                },

                post_delete:{
                    modal_class:"modal--postDelete",
                    modal_title:"Delete Warning",
                    modal_text:``,
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

    </script>

    <script>
        $(function(){
            
            /*=====common functions=====*/
            const current_url=window.location.href;
            const dashboard_options=$(".sidebar__link--option");
         
        
            dashboard_options.each(function(index,option){

                if(option.href == current_url){
                    
                    option.classList.add("sidebar__link--active");
                }
            })

            /*===JS for sidebar starts===*/

            //store the sidebar
            const sidebar=$(".sidebar");
            
            //store the navbar toggle button
            const nav_toggle=$(".navbar__toggle");

            //store the sidebar close button
            const sidebar_close=$(".sidebar__btn--close");
        
            nav_toggle.on("click",function(){

                if(!sidebar.hasClass("sidebar--block")){

                    //make the sidebar display block
                    sidebar.addClass("sidebar--block");
                    
                    setTimeout(() => {

                        //Finally show the sidebar
                        sidebar.addClass("sidebar--show");
                        
                    }, 50);
                }
            });

            sidebar_close.on("click",function(e){

                if(sidebar.addClass("sidebar--show")){

                    //First remove the show class
                    sidebar.removeClass("sidebar--show");
                
                    setTimeout(() => {

                        //Finaly remove the  block class
                        sidebar.removeClass("sidebar--block");
                        
                    }, 255);
                }
            });

            /*===JS for Sidebar ends===*/


            /*===JS for Header starts===*/

            //store all the dropdown toggles
            const dropdown_toggles=$(".navbar__dropdown-toggle");

        
            dropdown_toggles.on("click",function(){

                //store the clicked dropdwon toggle
                const dd_toggle = $(this);

                //store clicked closest dropdwon content
                const dd_content=dd_toggle.parent().find(".navbar__dropdown-content");

                //store dropdown content wrapper element
                const dd_content_wrap=dd_toggle.parent().find(".navbar__dropdown-content-wrap");

                //store options
                const obj={
                    
                    open:function(){

                        //store the object
                        const that=this;
                        
                        //remove any existing  .document-wrap elemenet
                        $(".document-wrap").remove();

                        //append a .document-wrap element into the body
                        $(".navbar").append(`<div class="document-wrap"></div>`);

                        if(!dd_content.hasClass("navbar__dropdown-content--block")){

                            //add the block class in dropdown options
                            dd_content.addClass("navbar__dropdown-content--block");
                            
                            setTimeout(function(){
                                
                                //add the show class in dropdown options
                                dd_content.addClass("navbar__dropdown-content--show");

                                //call the close function from object
                                $(".document-wrap").on("click",that.close);
                            
                            },50);
                        }
                    },

                    close:function(){

                        //remove the .documen-wrap first
                        $(".document-wrap").remove();
                        
                        if(dd_content.hasClass("navbar__dropdown-content--show")){
                            
                            //remove the show from options
                            dd_content.removeClass("navbar__dropdown-content--show");
                            
                            setTimeout(function(){
                                
                                //remove the block class from options
                                dd_content.removeClass("navbar__dropdown-content--block");

                            },155);
                        }  
                    }
                }

                //call the open function to show the popup
               obj.open();

                if(dd_toggle.hasClass("navbar__dropdown-toggle--nf")){



                    $.ajax({
                        url:`${post_request_url()}/fetch_notifications`,
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

                            dd_content_wrap.html(response);

                            get_unread_notifications();

                            console.log(response);
                        }
                    });
                }
                
        
            });

            setInterval(get_unread_notifications, 5000);

            /*===JS for Header ends===*/
    
            
            //copy any string using data-copy selector attribute
            const data_copy_elem=document.querySelectorAll("[data-copy]");

            data_copy_elem.forEach(function(element,index){
                
                element.addEventListener("click",function(){

                    const that=this;
                    const value_for_copy=this.dataset.copy;
                   
                   if(copy(value_for_copy)){

                        this.innerText="Copied";
                        
                        setTimeout(() => {
                            that.innerText="Copy"
                        }, 1000);
                   }
                })
            })
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    
    <?php 

        if(isset($_GET["option"])){

            if($_GET["option"] == "posts"){
                 
                $post_type="";

                if($_GET["sub_option"] == "my_posts"){

                    $post_type = "myposts";


                }elseif($_GET["sub_option"] == "publish"){

                    $post_type = "publish";

                    echo '<script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/ckeditor.js"></script>'."\r\n";

                
                }elseif($_GET["sub_option"] == "edit"){

                    $post_type = "edit";

                    echo '<script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/ckeditor.js"></script>'."\r\n";

                
                }elseif($_GET["sub_option"] == "saved"){

                    $post_type = "saved";

                }

                echo "
                    <script id='post-js' src='{$config->domain('assets/js/dashboard/posts.js')}' data-post_type='{$post_type}'></script>\r\n
                ";

            }elseif($_GET["option"] == "settings"){

                $setting_type="";

                if($_GET["sub_option"] == "account"){
                    
                    $setting_type="account";
    
                }elseif($_GET["sub_option"] == "security"){
                    
                    $setting_type="security";
                }

                echo "
                    <script id='settings-js' src='{$config->domain('assets/js/dashboard/settings.js')}' data-setting_type='{$setting_type}'></script>\r\n
                ";  
                
            }elseif($_GET["option"] == "admin_options"){

                $option_type="";

                if($_GET["sub_option"] == "users"){

                    $option_type = "users";

                }elseif($_GET["sub_option"] == "catagories"){

                    $option_type = "catagories";
                }

                echo "
                    <script id='admin-opt-js' src='{$config->domain('assets/js/dashboard/admin_options.js')}' data-option_type='{$option_type}'></script>\r\n
                ";
            }
        }

       
    ?>

</body>
</html>
