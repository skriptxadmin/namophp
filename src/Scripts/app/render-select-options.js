window.renderSelectOptions = function (element, options, text = "") {
  const valueTextOptionTpl = jQuery("#valueTextOptionTpl").html();
  element.html(_.template(valueTextOptionTpl)({ options, text }));
  return true;
};
