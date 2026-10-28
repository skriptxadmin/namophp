window.getQueryParms = function (key='') {
  const params = new URLSearchParams(window.location.search);

  if (key) {
    return params.get(key);
  }

  return Object.fromEntries(params.entries());
};