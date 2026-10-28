
window.dtActionRow1 = function (data, type, row) {
  return `
                <button class="btn btn-sm btn-edit text-warning">
                    ${window.appLocals.svgs.pencil}
                </button>
                <button class="btn btn-sm text-danger btn-delete">
                    ${window.appLocals.svgs.trash}
                </button>
            `;
};

window.dtAjax = function (endpoint, args = {}) {
  return function (data, callback, settings) {
    if (!_.isEmpty(args)) {
      data = { ...data, ...args };
    }
    window.secureAjax({
      endpoint: `${endpoint}`,
      method: "GET",
      data: data,
      success: function (res) {
        callback({
          draw: data.draw,
          recordsTotal: res.recordsTotal,
          recordsFiltered: res.recordsFiltered,
          data: res.data,
        });
      },
      error: function () {
        callback({
          draw: data.draw,
          recordsTotal: 0,
          recordsFiltered: 0,
          data: [],
        });
      },
    });
  };
};

