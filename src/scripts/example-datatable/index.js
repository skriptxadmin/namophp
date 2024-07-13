const { isEmpty } = require("lodash");
import { RenderDatatable } from "./render-datatable";
jQuery(function(){

    const page$ = jQuery(document).find("body.page.super-administrator.coordinators");

    if(isEmpty(page$)){

        return;
    }

    RenderDatatable();
})