

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

    // Select2
    if (element$.hasClass("select2-hidden-accessible")) {
      error.insertAfter(element$.next(".select2"));

      // Checkbox
    } else if (element$.attr("type") === "checkbox") {
      error.insertAfter(element$.closest("label"));

      // Input inside input-group
    } else if (element$.closest(".input-group").length) {
      error.insertAfter(element$.closest(".input-group"));

      // Default
    } else {
      error.insertAfter(element$);
    }
  },
});
