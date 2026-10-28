jQuery(document).on("click", "a.logout", function () {
  const confirmInstance = window.showConfirm(
    "Are you sure you want to log out",
  );
  confirmInstance.then(function (result) {
    if (!result) {
      return;
    }
    window.secureAjax({
      endpoint: "/user/logout",
      method: "POST",
      data: {},
      success: function () {
        window.hideConfirm();
      },
    });
  });
});