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
