jQuery(function(){
    const form$ = jQuery("form.register");
    form$.validate({
        rules: {
            username: {
                required: true
            },
            email:{
                required: true,
                email:true
            },
            fullname:{
                required: true,
                pattern:/^[a-zA-Z\s]*$/
            },
            mobile:{
                required: true,
                pattern:/^[0-9]*$/
            }
        },
        submitHandler: function(form, event){
            const data = {
                username: form$.find("#username").val(),
                email: form$.find("#email").val(),
                mobile: form$.find("#mobile").val(),
                fullname: form$.find("#fullname").val()
            }
            jQuery.ajax({
                form$:form$,
                method:"POST",
                endpoint:"guest/register",
                data:data,
                success:function(){

                }
            })
        }
    })
})