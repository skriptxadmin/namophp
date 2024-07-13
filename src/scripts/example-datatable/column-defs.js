export const ColumnDefs = [
    {
        title: 'Your Profile',
        data:"to_profile.fullname",
        targets: 0,
        "bSortable": false,
        
        render: function(data, type, row, meta){
            let html$ = `<div><a href="${row.to_profile.view_url}">${row.to_profile.fullname}</a></div>`;
            html$ += `<div><small>${row.to_profile.age}, ${row.to_profile.gender}, ${row.to_profile.maritalstatus}</small></div>`;
           
            return html$;
        }
    },
    {
        title: 'Other Profile',
        data:"from_profile.fullname",

        targets: 1,
        "bSortable": false,
        render: function(data, type, row, meta){
            let html$ = `<div><a href="${row.from_profile.view_url}">${row.from_profile.fullname}</a></div>`;
            html$ += `<div><small>${row.from_profile.age}, ${row.from_profile.gender}, ${row.from_profile.maritalstatus}</small></div>`;
            return html$;
        }
    },
    {
        title: 'Status',
        data:"accepted_at",
        targets: 2,
        "bSortable": false,
        render: function(data, type, row, meta){
            if(!data){
                return 'pending';
            }
            return 'accepted';
        }
    },
    {
        title: 'Created At',
        data:"created_at",
        targets: 3,
        "bSortable": false
    },
    
    {
        title: 'Action',
        data:"created_at",
        targets: 4,
        "bSortable": false,
        render: function(data, type, row, meta){
            if(!row.accepted_at){
                
           return `<button class="btn btn-sm btn-primary btn-icon btn-action" data-action="accept">Accept
           </button>`;
            }
           return `<button class="btn btn-sm btn-danger btn-icon btn-action" data-action="revoke">Revoke
           </button>`
        }
    },
]