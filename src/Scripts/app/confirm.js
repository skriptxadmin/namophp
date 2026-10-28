
window.showConfirm = function (message, title = "Confirm", isAlert = false) {
  return new Promise((resolve) => {
    const modal$ = jQuery("#confirmModal");
    modal$.find(".modal-title").html(title);
    modal$.find(".modal-body").html(message);
    if (isAlert) {
      modal$.find(".modal-footer").hide();
    }
    const instance = bootstrap.Modal.getOrCreateInstance(modal$[0], {
      backdrop: "static",
      keyboard: false,
    });
    instance.show();
    return modal$.off("click").on("click", ".btn-respond", function () {
      const result = JSON.parse(jQuery(this).attr("data-result"));
      return resolve(result);
    });
  });
};

window.hideConfirm = function () {
  const modal$ = jQuery("#confirmModal");

  const instance = bootstrap.Modal.getOrCreateInstance(modal$[0]);

  instance.hide();
};

