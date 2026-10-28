
window.showToaster = function (messages, type = "error", duration = 3000) {
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
