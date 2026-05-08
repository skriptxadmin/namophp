jQuery(function(){
    const form$ = jQuery("form.forgot-password");
    form$.validate({
        rules: {
            username: {
                required: true
            },
         
        },
        submitHandler: function(form, event){
            const data = {
                username: form$.find("#username").val(),
              
            }
            jQuery.ajax({
                form$:form$,
                method:"POST",
                endpoint:"guest/forgot-password",
                data:data,
                success:function(){

                }
            })
        }
    })
})