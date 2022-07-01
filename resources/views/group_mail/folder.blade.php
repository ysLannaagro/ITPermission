@extends('layouts.app-master')
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script> --}}

@section('content')
    <div class="app-content p-md-4">
        <div class="app-container-xl">
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

            <div class="row g-3 mb-2 align-items-center justify-content-between">
                <div class="col-auto">
                    <div class="row">
                        <div class="pull-right col-auto">{{-- url('group_mail?txt_search='.$txt_search.'&set_col='.$set_col) --}}
                            <a class="btn btn-secondary" href="{{ route('group_mail.index') }}">Back</a>
                        </div>
                        <h3 class="col">{{ __('Folder ในกลุ่ม') }} -> {{ $gm->name }}</h3>
                    </div>
                </div>
                <div class="col-auto">
                    <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" method="GET" id="frm_search">
                        <input type="search" class="form-control form-control-dark" id="txt_folder" name="txt_folder" 
                        placeholder="Search..." aria-label="Search" value="{{ Session::get('txt_folder') }}">
                        <input type="hidden" id="page_main" name="page_main" value="1"/>
                    </form>
                </div><!--//col-auto-->
            </div><!--//row onclick="getURL($key);" -->
        
            <nav class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
                <a class="flex-sm-fill text-sm-center nav-link active" data-bs-toggle="tab" href="#" role="tab"
                    aria-controls="orders-all" aria-selected="true"></a>
            </nav>  

            <div class="row g-3 mb-2 bg-secondary">
                <h3 class="col">{{ __('Folder ในกลุ่ม') }} -> {{ $gm->name }}</h3>                    
            </div>
            
            <div class="tab-content" id="orders-table-tab-content"  style="overflow: auto; max-height: 300px;">                
                <div class="app-card app-card-orders-table shadow-sm mb-5">
                    <div class="app-card-body">
                        <form method="POST" action="{{ route('group_mail.folder', $gm->id) }}">
                            @csrf
                            <table class="table app-table-hover mb-0 text-left" id="myTable">
                                <thead>
                                    <tr>
                                        <th class="cell" style="width:5%">No.</th>
                                        <th class="cell" style="width:30%">Name</th>
                                        <th class="cell" style="width:20%">Full</th>
                                        <th class="cell" style="width:40%">Read</th>
                                        <th class="cell" style="width:5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $row = 0;
                                        $sort = 0;
                                    @endphp
                                    @foreach ($fig as $kid=>$vid)                    
                                        <tr>                                
                                            <td class="cell">{{ ++$row }}</td> 
                                            <td class="cell">{{ $folder[$fig[$kid]['f_id']]['name'] }}</td>
                                            <td class="cell">
                                                <input full_id="{{ $fig[$kid]['f_id'] }}" class="form-check-input set_full" type="checkbox" 
                                                    value="" id="set_full[{{ $kid }}]"
                                                    @if($fig[$kid]['full']==1) checked @endif onchange="handleChange(event, {{ $kid }}, 'full')">
                                            </td>
                                            <td class="cell">
                                                <input read_id="{{ $fig[$kid]['f_id'] }}" class="form-check-input set_read" type="checkbox" 
                                                    value="" id="set_read[{{ $kid }}]"
                                                    @if($fig[$kid]['read']==1) checked @endif onchange="handleChange(event, {{ $kid }}, 'read')">
                                            </td>
                                            <td class="cell">
                                                <a class="text-danger" href="{{ route('group_mail.folder_del',$kid) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>    
                                    @endforeach                       
                                    <tr>                                                     
                                        <td class="cell">{{ ++$row }}</td> 
                                        <td class="cell" colspan="4">
                                            {{-- ใช้ไม่ได้ มันจะส่งแต่ค่า value ไม่ส่ง id มา หาวิธีไม่เจอ
                                                <input class="form-control" list="answers[{{ $sort }}]" id="to_detail[{{ $sort }}]" name="answer" 
                                                placeholder="Type to search..." onchange="myFunction({{ $sort }},{{ $row }})">
                                            <datalist id="answers[{{ $sort }}]">
                                                @foreach($folder_add as $key)
                                                    <option id="{{ $key->id }}" data-value="{{ $key->id }}">{{ $key->name }}</option>
                                                @endforeach
                                            </datalist>
                                            <input type="hidden" name="answer" id="to_detail{{ $sort }}-hidden"> --}}

                                            <select name="to_detail[{{ $sort }}]" id="to_detail[{{ $sort }}]" 
                                            class="form-select mdb-select" onchange="myFunction({{ $sort }},{{ $row }})" 
                                            data-live-search="true">
                                                <option value="">ไม่ระบุ</option>
                                                @foreach($folder_add as $key)
                                                    <option value="{{ $key->id }}">{{ $key->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="cell"></td>  
                                        <td class="cell">
                                            <div class="form-group row mb-2">
                                                <div class="col-md-6 offset-md-2">
                                                    <button type="submit" class="btn btn-success">
                                                        {{ __('Save') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cell"></td>
                                        <td class="cell"></td>
                                        <td class="cell"></td>                                          
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div><!--//app-card-body-->		
                </div><!--//app-card-->                  
            </div><!--//tab-content-->

            @if(!empty($to_in_group))
                @foreach($to_in_group as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $key1 => $value1)
                            @if(is_array($value))
                                @foreach($value1 as $key2 => $value2)
                                    <div class="row g-3 mb-2 mt-2">
                                        <h3 class="col">{{ __('Folder ในกลุ่ม') }} -> {{ $gm_name[$value2] }}</h3>                    
                                    </div>
                                    <div class="tab-content" id="orders-table-tab-content" style="overflow: auto; max-height: 300px;">                
                                        <div class="app-card app-card-orders-table shadow-sm mb-5">                    
                                            <div class="app-card-body">
                                                <table class="table app-table-hover mb-0 text-left" id="myTable" border="1">
                                                    <thead>
                                                        <tr>
                                                            <th class="cell" style="width:5%">No.</th>
                                                            <th class="cell" style="width:30%">Name</th>
                                                            <th class="cell" style="width:20%">Full</th>
                                                            <th class="cell" style="width:40%">Read</th>
                                                            <th class="cell" style="width:5%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $row = 0;
                                                            $sort = 0;
                                                        @endphp
                                                        @if(!empty($fig_other[$value2]))
                                                            @foreach ($fig_other[$value2] as $kid=>$vid)                    
                                                                <tr>                                
                                                                    <td class="cell">{{ ++$row }}</td> 
                                                                    <td class="cell">{{ $folder[$vid]['name'] }}</td>
                                                                    <td class="cell">
                                                                        <input full_id="{{ $vid }}" class="form-check-input set_full" type="checkbox" 
                                                                            value="" id="set_full[{{ $kid }}]"
                                                                            @if($fig_all[$kid]['full']==1) checked @endif onchange="handleChange(event, {{ $kid }}, 'full')">
                                                                    </td>
                                                                    <td class="cell">
                                                                        <input read_id="{{ $vid }}" class="form-check-input set_read" type="checkbox" 
                                                                            value="" id="set_read[{{ $kid }}]"
                                                                            @if($fig_all[$kid]['read']==1) checked @endif onchange="handleChange(event, {{ $kid }}, 'read')">
                                                                    </td>
                                                                    <td class="cell">
                                                                        <a class="text-danger" href="{{ route('group_mail.folder_del',$kid) }}">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                                                                            </svg>
                                                                        </a>
                                                                    </td>
                                                                </tr>    
                                                            @endforeach 
                                                        @endif     
                                                    </tbody>
                                                </table>
                                            </div><!--//app-card-body-->		
                                        </div><!--//app-card-->                  
                                    </div><!--//tab-content-->
                                @endforeach
                            @endif    
                        @endforeach
                    @endif
                @endforeach
            @endif

        </div><!--//container-fluid-->
    </div><!--//app-content--> 
    {{-- <script>
        $( document ).ready(function() {
            $('.mdb-select').selectpicker();
        });
    </script> --}}
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/jquery-1.7.1.min.js') }}"></script> 
    <script>        
        var input = document.getElementById("txt_folder");
        input.addEventListener("keypress", function(event) {
          if (event.key === "Enter") {
            event.preventDefault();
            frm_search.submit();
          }
        });
        
        function myFunction(sort,row) {         
            // console.log(document.getElementsByClassName("form-select")[sort].value);
            if(document.getElementsByClassName("form-select")[sort].value){
                var table = document.getElementById("myTable");
                // let numb = table.rows.length-2;
                // for (let i = numb; i > sort+1; i--) {
                //     table.deleteRow(i);
                // }

                // นับว่ามี select กี่ตัว ไว้ใช้ตอนวน loop ตัดค่าออก
                var  titleElement = document.getElementsByClassName("form-select");  
                let c_s = titleElement.length;
                // var  txt_old = document.getElementsByClassName("old_main");  
                var  txt_old = <?php echo json_encode($fig); ?>;
                // let txt_old_main = txt_old.length;
                // console.log('count='+c_s);
                // console.log(txt_old_main);
                // console.log(txt_old);

                var data_select = [];
                for (let i = 0; i < c_s; i++) {
                    if(document.getElementsByClassName("form-select")[i].value){
                        // var  test = document.getElementsByClassName("form-select")[i].value;
                        // data_select.push(test);
                        console.log(document.getElementsByClassName("form-select")[i].value);
                        data_select[document.getElementsByClassName("form-select")[i].value] =1;
                    }
                }
                for(const [key, value] of Object.entries(txt_old)){
                    data_select[value] = 1;
                }
                // for (let i = 0; i < txt_old_main; i++) {
                //     if(txt_old[i].value){
                //         // var  test = document.getElementsByClassName("form-select")[i].value;
                //         // data_select.push(test);
                //         data_select[txt_old[i].value] =1;
                //     }
                // }
                // console.log(data_select);
                if(document.getElementsByClassName("form-select")[(c_s-1)].value){
                    var obj = <?php echo json_encode($folder_add); ?>;
                    // console.log(obj);
                    var select2 ='<option value="">ไม่ระบุ</option>';
                    for(let key in obj){                    
                        // console.log(data_select[key]);                       
                        if(!data_select[obj[key].id]){
                            // console.log(key+'->'+obj[key].id+'->'+obj[key].email); 
                            select2 += '<option value="'+obj[key].id+'">'+obj[key].name+'</option>';
                        }
                    }
                    if(select2 !=='<option value="">ไม่ระบุ</option>'){
                        var table_row = table.insertRow(row+1);
                        var cell1 = table_row.insertCell(0);
                        var cell2 = table_row.insertCell(1);

                        cell1.innerHTML = row+1;
                        var select1 = '<select name="to_detail['+(sort+1)+']" id="to_detail['+(sort+1)+']" class="form-select" onchange="myFunction('+(sort+1)+','+(row+1)+')">';
                        // var select2 ='<option value="">ไม่ระบุ</option>';
                        // console.log(select2)
                        var select3 ='</select>';
                        cell2.colSpan = "4";
                        cell2.innerHTML = select1+select2+select3;
                    }
                }
            }
        }

        function handleChange(e, id, type) {
            const {checked} = e.target;
            if(checked){
                chk = 'check';
            }else{
                chk = 'not';
            }
            console.log(chk);
            // console.log(tex_search);
            var str = window.location.href;
            var str_1 = str.split("/").slice(0, -2).join("/")
            // var link_url = window.location.pathname+"/chk_public?id="+id+"&chk="+chk;
            var link_url = str_1+"/chk_folder?id="+id+"&chk="+chk+"&type="+type;
            console.log(str_1);
            console.log(link_url);
            // console.log(window.location.pathname );
            // window.location = window.location.pathname+link_url;

            $.get(link_url,function (data) {
                alert(data);
            });
        }
        
    </script>

@endsection
