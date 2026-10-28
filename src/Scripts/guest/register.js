jQuery(function(){
    const form$ = jQuery("form.register");
    form$.validate({
        rules: {
          
            fullname:{
                required: true,
                minlength:3,
                maxlength:40,
                pattern:/^[a-zA-Z\s]*$/
            },
            mobile:{
                required: true,
                pattern:/^[6-9][0-9]{9}$/
            }
        },
        submitHandler: function(form, event){
            const data = {
                mobile: form$.find("#mobile").val(),
                fullname: form$.find("#fullname").val()
            }

            window.secureAjax({
                form$:form$,
                method:"POST",
                endpoint:"guest/register",
                data:data,
                success:function(res){
                    const username = _.get(res, 'username', '');
                    if(_.isEmpty(username)) return;
                    const otp = _.get(res, 'otp','');
                    let url = `${window.appLocals.url}/guest/set-password/${username}`;
                    if(otp){
                        url += `?otp=${otp}`;
                    }
                    window.location.href=url;
                }
            })
        }
    })
})