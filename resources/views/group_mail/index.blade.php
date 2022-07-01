@extends('layouts.app-master')

{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<style>
body{
	/* padding-top:50px; */
	background-color:#34495e;
}

.hiddenRow {
    padding: 0 !important;
}
</style>

@section('content')
    <div class="bg-light p-5 rounded">
        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('success') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row g-4 mb-2 align-items-center justify-content-between">
            <div class="col-auto">
                <div class="row">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0">จัดการกลุ่ม</h1>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="row g-2 justify-content-start justify-content-md-end align-items-center">	
                    <div class="col-auto">
                        <form class="row mb-3 mb-lg-0 me-lg-3" method="GET" id="frm_search" action="{{ route('group_mail.index') }}">
                            <input type="search" class="form-control form-control-dark col" id="txt_search" name="txt_search" 
                            placeholder="Search..." aria-label="Search" value="{{ Session::get('txt_search') }}" >
                            <select class="form-select col" name="set_col" id="set_col">
                                <option value="">..Public mail..</option>
                                <option value="public" @if(Session::get('set_col')=='public') selected @endif>Public mail</option>
                                <option value="private" @if(Session::get('set_col')=='private') selected @endif>Private mail</option>
                            </select>
                            <input type="hidden" id="page_main" name="page_main" value="1"/>
                            <button type="submit" class="btn btn-success col-auto">
                                {{ __('Search') }}
                            </button>
                        </form>
                    </div>
                    <div class="col-auto">						    
                        {{-- <a class="btn btn-info" href="{{ route('group_mail_import') }}">
                            Upload Excel
                        </a> --}}
                        <a class="btn btn-success" href="{{ route('group_mail.create') }}">
                            Add
                        </a>
                    </div>
                </div><!--//row-->
            </div><!--//col-auto-->
        </div><!--//row onclick="getURL($key);" -->
    
        <nav class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
            <a class="flex-sm-fill text-sm-center nav-link active" data-bs-toggle="tab" href="#" role="tab"
                aria-controls="orders-all" aria-selected="true"></a>
        </nav>  
        
        <div class="tab-content" id="orders-table-tab-content">                
            <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">
                    <div class="accordion">
                        @php
                            function outputString($key,$value,$c_level,$gm_all_show, $row_id, $num_gm, $num_m, $html=''){
                                // $html = '-----------------------';
                                //$row_id -> 1.2.3
                                $collapse_1 = 'det_'.($c_level-1).'_'; 
                                $collapse_2 = 'det_'.$c_level.'_';   
                                $row_num = 0;
                                $html .= '<tr>
                                        <td colspan="12" class="hiddenRow">
                                            <div class="accordian-body collapse" id="'.$collapse_1.$key.'"> 
                                                <table class="table table-info">
                                                    <thead>
                                                        <tr>
                                                            <th class="cell" style="width:5%"></th>
                                                            <th class="cell" style="width:5%">No.</th>
                                                            <th class="cell" style="width:45%">Name</th>
                                                            <th class="cell" style="width:10%">Public mail</th>
                                                            <th class="cell" colspan="2" style="width:10%">Manage</th>
                                                            <th class="cell" style="width:25%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>';                            
                                                        foreach ($value as $key1 => $value1){     

                                                            $html .= '<tr>
                                                                <td class="cell" style="vertical-align:middle;text-align: center;" ';
                                                                if(is_array($value1))   $html .= 'data-toggle="collapse" data-bs-toggle="collapse" href="#'.$collapse_2.$key1.'" aria-expanded="true" aria-controls="'.$collapse_2.$key1.'" ';
                                                                $html .= '>';
                                                                if(is_array($value1)){
                                                                    $html .= '<a class="text-primary" href="#" title="show">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                                            </svg>
                                                                        </a>';
                                                                }
                                                                $html .= '</td>
                                                                <td class="cell" style="text-align: center;">'.$row_id.'.'.++$row_num.'</td>  
                                                                <td class="cell" style="text-align: left;">'.$gm_all_show['id'][$key1].'</td> 
                                                                <td class="cell" style="text-align:center;">
                                                                    <input gm_id="'.$key1.'" class="form-check-input set_public" type="checkbox" 
                                                                    value="" id="set_column['.$key1.']"';
                                                                    if($gm_all_show['set'][$key1]==1) $html .= ' checked ';
                                                                    $html .= ' onchange="handleChange(event, '.$key1.')">                                        
                                                                </td> 
                                                                <td class="cell" style="text-align: center;">
                                                                    <div class="col-auto mx-2">
                                                                        <a class="text-primary" href="'.route('group_mail.edit', $key1).'" title="แก้ไข">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-tools" viewBox="0 0 16 16">
                                                                                <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3c0-.269-.035-.53-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814L1 0Zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708ZM3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026L3 11Z"/>
                                                                            </svg>
                                                                        </a>
                                                                    </div> 
                                                                </td>
                                                                <td class="cell" style="text-align: center;">                                              
                                                                    <div class="col-auto mx-2">';
                                                                    if(empty($num_gm[$key1]) && empty($num_m[$key1]) && empty($num_f[$key1])){// route('gm_del', $key1) url('/group_mail/destroy/'.$key1.'?txt_search='.$txt_search.'&set_col='.$set_col)
                                                                        $html .='<a class="text-danger" href="'.route('gm_del', $key1).'" title="ลบ">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                                                        </svg>
                                                                                    </a>';
                                                                    } 
                                                                    $html .='</div>                                                
                                                                </td>  
                                                                <td class="cell" style="text-align: center;">
                                                                    <div class="row justify-content-around">
                                                                        <div class="col-auto mx-2">
                                                                            <a class="text-info" href="'.route('group_mail.to_manage', $key1).'" title="การจัดกลุ่ม">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-diagram-2-fill" viewBox="0 0 16 16">
                                                                                    <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H11a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 7h2.5V6A1.5 1.5 0 0 1 6 4.5v-1zm-3 8A1.5 1.5 0 0 1 4.5 10h1A1.5 1.5 0 0 1 7 11.5v1A1.5 1.5 0 0 1 5.5 14h-1A1.5 1.5 0 0 1 3 12.5v-1zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 9 12.5v-1z"/>
                                                                                </svg>
                                                                            </a>
                                                                        </div> 
                                                                        <div class="col-auto mx-2">
                                                                            <a class="text-secondary" href="'.route('group_mail.to_mail', $key1).'" title="รายชื่อ E-mail">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                                                                                    <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                                                                                </svg>
                                                                            </a>
                                                                        </div> 
                                                                        <div class="col-auto mx-2">
                                                                            <a class="text-secondary" href="'.route('group_mail.to_folder', $key1).'" title="สิทธิ์การใช้ Folder">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-folder2" viewBox="0 0 16 16">
                                                                                    <path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5v-9zM2.5 3a.5.5 0 0 0-.5.5V6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5zM14 7H2v5.5a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5V7z"/>
                                                                                </svg>
                                                                            </a>
                                                                        </div> 
                                                                    </div>                         
                                                                </td>
                                                            </tr>';
                                                            if(is_array($value1)) {
                                                                // $c_level++;
                                                                $row_id_new = $row_id.'.'.$row_num;
                                                                $html .= outputString($key1,$value1,$c_level+1,$gm_all_show, $row_id_new, $num_gm, $num_m);
                                                            } 
                                                        }
                                                        $html .='</tbody>
                                                </table>
                                            </div> 
                                        </td>
                                    </tr>';
                                                             
                                
                                return $html;
                            }
                        @endphp   
                        
                        <table class="table app-table-hover mb-0 text-left table-bordered">
                            <thead>
                                <tr style="text-align: center">
                                    <th class="cell" style="width:5%"></th>
                                    <th class="cell" style="width:5%">No.</th>
                                    <th class="cell" style="width:45%">Name</th>
                                    <th class="cell" style="width:10%">Public mail</th>
                                    <th class="cell" colspan="2" style="width:10%">Manage</th>
                                    <th class="cell" style="width:25%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i=0;
                                    $level = 0;
                                @endphp
                                @foreach ($to_return as $key=>$value)
                                    <tr>
                                        <td class="cell" style="vertical-align:middle;text-align: center;" @if(is_array($value)) data-toggle="collapse" data-bs-toggle="collapse" href="#det_0_{{ $key }}"  
                                        aria-expanded="true" aria-controls="det_0_{{ $key }}" @endif>
                                            @if(is_array($value))
                                                <a class="text-primary" href="#" title="show">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="cell" style="text-align: center;">{{ ++$i }}</td>  
                                        <td class="cell" style="text-align: left;">{{ $gm_all_show['id'][$key] }}</td> 
                                        <td class="cell" style="text-align:center;">
                                            <input gm_id="{{ $key }}" class="form-check-input set_public" type="checkbox" 
                                            value="" id="set_column[{{ $key }}]" @if($gm_all_show['set'][$key]==1) checked @endif onchange="handleChange(event, {{ $key }})">                                        
                                        </td> 
                                        <td class="cell" style="text-align: center;">
                                            <div class="col-auto mx-2">{{-- route('group_mail.edit', $key) url('group_mail/'.$key.'/edit?txt_search='.$txt_search.'&set_col='.$set_col) --}}
                                                <a class="text-primary" href="{{ route('group_mail.edit', $key) }}" title="แก้ไข">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-tools" viewBox="0 0 16 16">
                                                        <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3c0-.269-.035-.53-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814L1 0Zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708ZM3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026L3 11Z"/>
                                                    </svg>
                                                </a>
                                            </div> 
                                        </td>
                                        <td class="cell" style="text-align: center;">                                              
                                            <div class="col-auto mx-2"> 
                                                @if(empty($num_gm[$key]) && empty($num_m[$key]) && empty($num_f[$key])){{-- url('/group_mail/destroy/'.$key.'?txt_search='.$txt_search.'&set_col='.$set_col) --}}
                                                    <a class="text-danger" href="{{ route('gm_del', $key) }}" title="ลบ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                        </svg>
                                                    </a>
                                                @endif 
                                            </div>                                                
                                        </td>  
                                        <td class="cell" style="text-align: center;">
                                            <div class="row justify-content-around">
                                                <div class="col-auto mx-2">{{-- url('group_mail/to_manage/'.$key.'?txt_search='.$txt_search.'&set_col='.$set_col) --}}
                                                    <a class="text-info" href="{{ route('group_mail.to_manage', $key) }}" title="การจัดกลุ่ม">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-diagram-2-fill" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H11a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 7h2.5V6A1.5 1.5 0 0 1 6 4.5v-1zm-3 8A1.5 1.5 0 0 1 4.5 10h1A1.5 1.5 0 0 1 7 11.5v1A1.5 1.5 0 0 1 5.5 14h-1A1.5 1.5 0 0 1 3 12.5v-1zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 9 12.5v-1z"/>
                                                        </svg>
                                                    </a>
                                                </div> 
                                                <div class="col-auto mx-2">{{-- url('group_mail/to_mail/'.$key.'?txt_search='.$txt_search.'&set_col='.$set_col) --}}
                                                    <a class="text-secondary" href="{{ route('group_mail.to_mail', $key) }}" title="รายชื่อ E-mail">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                                                            <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                                                        </svg>
                                                    </a>
                                                </div> 
                                                <div class="col-auto mx-2">{{-- url('group_mail/to_folder/'.$key.'?txt_search='.$txt_search.'&set_col='.$set_col) --}}
                                                    <a class="text-secondary" href="{{ route('group_mail.to_folder', $key) }}" title="สิทธิ์การใช้ Folder">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-folder2" viewBox="0 0 16 16">
                                                            <path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5v-9zM2.5 3a.5.5 0 0 0-.5.5V6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5zM14 7H2v5.5a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5V7z"/>
                                                        </svg>
                                                    </a>
                                                </div> 
                                            </div>                         
                                        </td>
                                    </tr>
                                    @if(is_array($value))
                                        {{-- ใช้ {{  }} ไม่ได้ --}}
                                        <?php echo outputString($key,$value,$level+1,$gm_all_show, $i, $num_gm, $num_m); ?>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>                
                </div>                 
            </div>
        </div>
    </div>
        
    {{-- เพื่อให้ $.get() ใช้งานได้ต้องมี jquery-1.7.1.min.js ก่อนด้วย http://code.jquery.com/jquery-1.7.1.min.js--}}
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/jquery-1.7.1.min.js') }}"></script>
    <script type="text/javascript">
    // <script>
        // var input = document.getElementById("txt_search");
        // input.addEventListener("keypress", function(event) {
        //   if (event.key === "Enter") {
        //     event.preventDefault();
        //     frm_search.submit();
        //   }
        // });

        function handleChange(e, id) {
            const {checked} = e.target;
            if(checked){
                chk = 'check';
            }else{
                chk = 'not';
            }
            console.log(chk);
            var tex_search = document.getElementById("txt_search").value;
            var set_col = document.getElementById("set_col").value;
            // console.log(tex_search);

            // var link_url = window.location.pathname+"/chk_public?id="+id+"&chk="+chk+"&txt_search="+tex_search+"&set_col="+set_col;
            var link_url = window.location.pathname+"/chk_public?id="+id+"&chk="+chk;
            console.log(link_url);
            // console.log(window.location.pathname );
            // window.location = window.location.pathname+link_url;


            $.get(link_url,function (data) {
                alert(data);
            });
        }

        // $(document).ready(function () {
            // $("set_public").on("change", function () {
            //     var id = $(this).attr("gm_id");
            //     console.log(id);
            //     var chk_status = document.getElementById("set_column["+id+"]").checked;
            //     var need=0;
            //     if(chk_status===true) need=1;
            //     alert(need);

            //     $.get("/group_mail/chk_public?id="+id+"&need="+need,function (data) {
            //         alert(data);
            //     });
            // });
        // });
    </script>
@endsection