jQuery(function(){
    const form$ = jQuery("form.set-password");
    form$.validate({
        rules: {
            username: {
                required: true
            },
             otp: {
                required: true
            },
             password: {
                required: true
            },
             cpassword: {
                required: true,
                equalTo:"#password"
            },
         
        },
        submitHandler: function(form, event){
            const data = {
                username: form$.find("#username").val(),
                otp: form$.find("#otp").val(),
                password: form$.find("#password").val(),
                cpassword: form$.find("#cpassword").val(),
              
            }
            jQuery.ajax({
                form$:form$,
                method:"POST",
                endpoint:"guest/set-password",
                data:data,
                success:function(){

                }
            })
        }
    })
})