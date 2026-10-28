const modalBlocker$ = jQuery("#modalBlocker");
const modalBlockerOptions = {
  backdrop: "static",
  keyboard: false,
};
const modalBlockerInstance = new bootstrap.Modal(
  modalBlocker$[0],
  modalBlockerOptions,
);

let blockerCount = 0;
let blockerReady = false;
let blockerHidePending = false;

modalBlocker$.on("shown.bs.modal", function () {
  blockerReady = true;

  if (blockerHidePending && blockerCount === 0) {
    blockerHidePending = false;
    modalBlockerInstance.hide();
  }
});

modalBlocker$.on("hidden.bs.modal", function () {
  blockerReady = false;
});


jQuery.ajaxSetup({
  headers: {
    "X-Csrf-Token": jQuery("meta[name='csrf-token']").attr("content"),
  },
});

jQuery(document).on("ajaxSend", function (event, request, settings) {
  const blocker = _.get(settings, "blocker", true);
  if (blocker) {
    showBlocker();
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
  const message = _.get(request, "responseJSON.message");
  if (message) {
    window.showToaster(message, "success");
  }
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
  const blocker = _.get(settings, "blocker", true);
  if (blocker) {
    hideBlocker();
  }
  if (settings.form$) {
    const fieldset$ = settings.form$.find("fieldset");
    if (fieldset$) {
      fieldset$.attr("disabled", false);
    }
  }
});

function showBlocker() {
  blockerCount++;
  if (blockerCount === 1) {
    blockerHidePending = false;
    blockerReady = false;

    modalBlockerInstance.show();
  }
}

function hideBlocker() {
  blockerCount--;

  if (blockerCount <= 0) {
    blockerCount = 0;

    if (blockerReady) {
      modalBlockerInstance.hide();
    } else {
      // AJAX finished before Bootstrap finished showing it
      blockerHidePending = true;
    }
  }
}


window.secureAjax = function (options) {
  if (options.endpoint) {
    options.url =
      _.trimEnd(window.appLocals.ajax, "/") +
      "/" +
      _.trimStart(options.endpoint, "/");
  }
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