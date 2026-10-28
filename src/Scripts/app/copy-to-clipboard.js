jQuery(function () {
  const btnCopyToClipboard = jQuery(".btn-copy-to-clipboard");

  btnCopyToClipboard.on("click", function () {
    const text = jQuery(this).attr("data-copy-text");
    navigator.clipboard
      .writeText(text)
      .then(() => {
        window.showToaster("Copied", "success");
      })
      .catch((err) => {
        window.showToaster("Copy Failed", "error");
      });
  });
});