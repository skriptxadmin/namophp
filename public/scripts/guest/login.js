jQuery(function(){
    const form$ = jQuery("form.login");
    form$.validate({
        rules: {
            username: {
                required: true
            },
            password:{
                required: true
            }
        },
        submitHandler: function(form, event){
            const data = {
                username: form$.find("#username").val(),
                password: form$.find("#password").val()
            }
            jQuery.ajax({
                form$:form$,
                method:"POST",
                endpoint:"guest/login",
                data:data,
                success:function(){

                }
            })
        }
    })
})