jQuery(function () {
  const form$ = jQuery("form.forgot-password");
  form$.validate({
    rules: {
      username: {
        required: true,
      },
    },
    submitHandler: function (form, event) {
      const data = {
        mobile: form$.find("#username").val(),
      };
      window.secureAjax({
        form$: form$,
        method: "POST",
        endpoint: "guest/forgot-password",
        data: data,
        success: function (res) {
          const username = _.get(res, "username", "");
          if (_.isEmpty(username)) return;
          const otp = _.get(res, "otp", "");
          let url = `${window.appLocals.url}/guest/set-password/${username}`;
          if (otp) {
            url += `?otp=${otp}`;
          }
          window.location.href = url;
        },
      });
    },
  });
});
