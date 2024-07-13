<?php

class Example extends Controller{



    public function get(){


        $items_per_page =  !empty($_POST['length'])?$_POST['length']:$_ENV['ITEMS_PER_PAGE'];

        $pageNumber = !empty($_POST['start'])?($_POST['start']+1):1;
    
        $search = !empty($_POST['search']['value'])?trim($_POST['search']['value']):NULL;
    
 
        $rows = Model::whereIn('column', "{column_value}")
        
        ->paginate($items_per_page, ['*'], 'page', $pageNumber);

        $data= [
            
            "draw" => $_POST['draw'],
            "recordsTotal"=> $rows->total(),
            "recordsFiltered"=> $rows->total(),
            "data"=> $rows->items()
    ];

    $this->json($data);

    return;
    }
}
