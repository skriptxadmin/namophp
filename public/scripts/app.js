jQuery.validator.setDefaults({
  errorClass: "text-danger",
  errorElement: "small",

  highlight: function (element, errorClass, validClass) {
    const element$ = jQuery(element);
    element$.attr("aria-invalid", true);
    element$.next(".hint-text").hide();
    element$.addClass("is-invalid");
    element$.closest(".form-group").addClass("text-danger");
  },

  unhighlight: function (element, errorClass, validClass) {
    const element$ = jQuery(element);
    element$.removeAttr("aria-invalid");
    element$.next(".hint-text").show();
    element$.removeClass("is-invalid");
    element$.closest(".form-group").removeClass("text-danger");
  },

  errorPlacement: function (error, element) {
    const element$ = jQuery(element);

    // Check if it's a Select2 element
    if (element$.hasClass("select2-hidden-accessible")) {
      // Insert error after the visible Select2 container
      error.insertAfter(element$.next(".select2"));
    } else if (element$.attr("type") === "checkbox") {
      // Place error after the checkbox label container
      error.insertAfter(element$.closest("label"));
    } else if(element$.closest(".input-group").length){
      error.insertAfter(element$.closest(".input-group"));

    } else {
      error.insertAfter(element$);
    }
  },
});

jQuery.ajaxSetup({
  headers: {
    "X-Csrf-Token": jQuery("meta[name='csrf-token']").attr("content"),
  },
});

jQuery(document).on("ajaxSend", function (event, request, settings) {
  const spinner = _.get(settings, "spinner", true);
  if (spinner) {
    jQuery("#ajax-spinner").removeClass("dn").addClass("flex"); // show loader
  }
  if (settings.endpoint) {
    settings.url =
      _.trimEnd(window.appLocals.ajax, "/") +
      "/" +
      _.trimStart(settings.endpoint, "/");
  }
  if (settings.form$) {
    const fieldset$ = settings.form$.find("fieldset");
    if (fieldset$) {
      fieldset$.attr("disabled", true);
    }
  }
});

jQuery(document).on("ajaxSuccess", function (event, request, settings) {
  if (!settings.endpoint) {
    return;
  }
  // const message = _.get(request, "responseJSON.message");
  // if (message) {
  //   window.showToaster(message, "success");
  // }
  const redirect = _.get(request, "responseJSON.redirect");
  if (redirect) {
    window.location.href = redirect;
  }
});

jQuery(document).on("ajaxError", function (event, request, settings) {
  if (!settings.endpoint) {
    return;
  }

  const errorsList = _.get(request, "responseJSON.errors") ||
    _.get(request, "responseJSON.error") || [
      "Something went wrong. Contact administrator",
    ];

  window.showToaster(errorsList, "error");
});

jQuery(document).on("ajaxComplete", function (event, request, settings) {
  jQuery("#ajax-spinner").removeClass("flex").addClass("dn"); // show loader
  if (settings.form$) {
    const fieldset$ = settings.form$.find("fieldset");
    if (fieldset$) {
      fieldset$.attr("disabled", false);
    }
  }
});

jQuery(document).on("click", "header nav.nav-primary a.logout", function () {
  const instance = window.showConfirm("Are you sure you want to log out");
  instance.then(function (result) {
    if (!result) {
      return;
    }
    jQuery.ajax({
      endpoint: "/user/logout",
      method: "POST",
      data: {},
      success: function () {},
    });
  });
});

jQuery(document).on("click", ".password-toggler", function () {
  const input$ = jQuery(this).closest("div").find("input[type=password]");
  if (!input$) {
    return;
  }
  input$.attr("type", "text");

  setTimeout(() => {
    input$.attr("type", "password");
  }, 5000);
});

window.secureAjax = function (options) {
  if (!window.appLocals.production) {
    jQuery.ajax(options);
    return;
  }
  if (window.appLocals.isUserLoggedIn) {
    jQuery.ajax(options);
    return;
  }
  grecaptcha.ready(function () {
    grecaptcha
      .execute(window.appLocals.recaptchaSiteKey, { action: "submit" })
      .then(function (token) {
        options.headers = options.headers || {};
        options.headers["X-Recaptcha-Token"] = token;
        jQuery.ajax(options);
      });
  });
};

window.showToaster = function (messages, type = "error", duration = 4000) {

     

  // Normalize into array
  if (!Array.isArray(messages)) messages = [messages];

  // Combine into single message with line breaks
  const combinedMsg = messages.join("\n");

  // Create one toast item
  const $item = $('<div class="toaster-item"></div>')
    .addClass(type)
    .html(combinedMsg.replace(/\n/g, "<br>")); // preserve line breaks

  const toast$ = jQuery("#appToast").find(".toast");

   toast$.removeClass("text-bg-danger");
    toast$.removeClass("text-bg-success");
    toast$.removeClass("text-bg-info");

  toast$.find(".toast-body").html($item);

  if (type == "error") {
    toast$.addClass("text-bg-danger");
  }
  if (type == "success") {
    toast$.addClass("text-bg-success");
  }
  if (type == "info") {
    toast$.addClass("text-bg-info");
  }
  toast$.addClass("show");

  // auto-remove after duration
  setTimeout(() => {
    toast$.removeClass("show");

    toast$.find(".toast-body").html("");
  }, duration);
};

window.showConfirm = function (message, title = "Confirm", isAlert=false) {
  return new Promise((resolve) => {
    const modal$ = jQuery("#confirmModal");
    modal$.find(".modal-title").html(title);
    modal$.find(".modal-body").html(message);
    if(isAlert){
      modal$.find(".modal-footer").hide();
    }
    const instance = bootstrap.Modal.getOrCreateInstance(modal$[0],{backdrop:'static', 'keyboard':false});
    instance.show();
   return modal$.off('click').on('click', '.btn-respond',function(){
      const result = JSON.parse(jQuery(this).attr('data-result'));
      instance.hide();
      return resolve(result);
    });
  });
};
