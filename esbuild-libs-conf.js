// build/libs.js

module.exports = [
    {
        name: 'core',
        css: [
            'node_modules/bootstrap/dist/css/bootstrap.css'
        ],
        js: [
            'node_modules/jquery/dist/jquery.js',
            'node_modules/bootstrap/dist/js/bootstrap.bundle.js',
            'node_modules/moment/min/moment.min.js',
            'node_modules/lodash/lodash.js'
        ]
    },

    {
        name: 'validation',
        js: [
            'node_modules/jquery-validation/dist/jquery.validate.js',
            'node_modules/jquery-validation/dist/additional-methods.js'
        ]
    },

    {
        name: 'datatables',
        css: [
            'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.css'
        ],
        js: [
            'node_modules/datatables.net/js/dataTables.js',
            'node_modules/datatables.net-bs5/js/dataTables.bootstrap5.js'
        ]
    },

    {
        name: 'datepicker',
        css: [
            'node_modules/vanillajs-datepicker/dist/css/datepicker.css',
            'node_modules/vanillajs-datepicker/dist/css/datepicker-bs5.css'
        ],
        js: [
            'node_modules/vanillajs-datepicker/dist/js/datepicker-full.js'
        ]
    },

    {
        name: 'lightbox',
        css: [
            'node_modules/simplelightbox/dist/simple-lightbox.css'
        ],
        js: [
            'node_modules/simplelightbox/dist/simple-lightbox.js'
        ]
    },

    {
        name: 'placeholder',
        css: [
            'node_modules/placeholder-loading/dist/css/placeholder-loading.css'
        ],
        js: []
    },
    {
        name:'select2',
        css:[
            'node_modules/select2/dist/css/select2.css',
            'node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.css'
        ],
        js:[
            'node_modules/select2/dist/js/select2.js'
        ]
    },
    {
        name:"easyqrcode",
        js:[
            "node_modules/easyqrcodejs/dist/easy.qrcode.min.js"
        ]
    }
];