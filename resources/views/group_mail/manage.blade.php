@extends('layouts.app-master')

@section('content')
    <div class="app-content p-md-4">
        <div class="app-container-xl">
            <div class="page-heading row">        
                <div class="pull-right col-auto">{{-- url('group_mail?txt_search='.$txt_search.'&set_col='.$set_col) --}}
                    <a class="btn btn-secondary" href="{{ route('group_mail.index') }}">Back</a>
                </div>
                <h3 class="col">{{ __('จัดการกลุ่ม') }} -> {{ $gm->name }} (กลุ่มในสังกัด)</h3>
            </div>

        
            <nav class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
                <a class="flex-sm-fill text-sm-center nav-link active" data-bs-toggle="tab" href="#" role="tab"
                    aria-controls="orders-all" aria-selected="true"></a>
            </nav>  
            
            <div class="tab-content" id="orders-table-tab-content">                
                <div class="app-card app-card-orders-table shadow-sm mb-5">
                    <div class="app-card-body">
                        <form method="POST" action="{{ route('group_mail.manage', $gm->id) }}">
                            @csrf
                            <table class="table app-table-hover mb-0 text-left" id="myTable">
                                <thead>
                                    <tr style="text-align: center">
                                        <th class="cell" style="width:20%">No.</th>
                                        <th class="cell" style="width:70%">Name</th>
                                        <th class="cell" style="width:10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $row = 0;
                                        $sort = 0;
                                    @endphp
                                    {{-- <tr>                                
                                        <td class="cell">{{ $sort }}</td>  
                                        <td class="cell">
                                            {{ $gm->name }}
                                            @foreach($old_main as $key => $value)
                                                <input class="old_main" type="hidden" value="{{ $value }}" id="old_main[{{ $key }}]" name ="old_main[{{ $key }}]" >
                                            @endforeach                                            
                                        </td>  
                                    </tr> --}}
                                    @if(count($to_show)>0)
                                        @foreach ($to_show as $kid=>$vid)                    
                                            <tr>                                
                                                <td class="cell">{{ ++$row }}</td>  
                                                <td class="cell">
                                                    {{ $gm_status[$to_show[$kid]['detail']]['name'] }}
                                                    {{-- <select name="to_detail[{{ $sort }}]" id="to_detail[{{ $sort }}]" class="form-select" required onchange="myFunction({{ $sort }})">
                                                        <option value="">ไม่ระบุ</option>
                                                        @foreach($level[$sort+1] as $key)
                                                            <option value="{{ $key->id }}" @if($to_show[$kid]==$key->id) selected @endif>{{ $key->name }}</option>
                                                        @endforeach
                                                    </select> --}}
                                                </td>
                                                <td class="cell">
                                                    @if(empty($chk_mail[$to_show[$kid]['detail']]))
                                                        <a class="text-danger" href="{{ route('group_mail.manage_del', $to_show[$kid]['id']) }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                                                            </svg>
                                                        </a>  
                                                    @endif  
                                                </td> 
                                            </tr>   
                                        @endforeach
                                    @endif       
                                    @if(count($level[$sort])>0)
                                        <tr>                                                     
                                            <td class="cell">{{ ++$row }}</td> 
                                            <td class="cell" colspan="2">
                                                <select name="to_detail[{{ $sort }}]" id="to_detail[{{ $sort }}]" class="form-select" onchange="myFunction({{ $sort }}, {{ $row }})">
                                                    <option value="">ไม่ระบุ</option>
                                                    @foreach($level[$sort] as $key)
                                                        <option value="{{ $key->id }}">{{ $key->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endif
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
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div><!--//app-card-body-->		
                </div><!--//app-card-->                  
            </div><!--//tab-content-->
        </div><!--//container-fluid-->
    </div><!--//app-content-->   
    <script>
        function myFunction(sort, nrow) {
            // console.log(document.getElementsByClassName("form-select")[sort].value);
            if(document.getElementsByClassName("form-select")[sort].value){
                var table = document.getElementById("myTable");
                // let numb = table.rows.length-2;
                // // console.log('l='+numb);
                // for (let i = numb; i > sort+1; i--) {
                //     // console.log('i='+i);
                //     table.deleteRow(i);
                // }

                // นับว่ามี select กี่ตัว ไว้ใช้ตอนวน loop ตัดค่าออก
                var  titleElement = document.getElementsByClassName("form-select");  
                let c_s = titleElement.length;
                var  txt_old = document.getElementsByClassName("old_main");  
                let txt_old_main = txt_old.length;
                var to_notin = <?php echo json_encode($to_notin); ?>;          
                console.log(to_notin);

                var data_select = [];
                // console.log(document.getElementsByClassName("form-select")[i].value);
                for (let i = 0; i < c_s; i++) {

                    if(document.getElementsByClassName("form-select")[i].value){
                        // var  test = document.getElementsByClassName("form-select")[i].value;
                        // data_select.push(test);
                        data_select[document.getElementsByClassName("form-select")[i].value] =1;
                    }
                }
                for(const [key, value] of Object.entries(to_notin)){
                    data_select[value] = 1;
                }
                for (let i = 0; i < txt_old_main; i++) {
                    if(document.getElementsByClassName("old_main")[i].value){
                        // var  test = document.getElementsByClassName("form-select")[i].value;
                        // data_select.push(test);
                        data_select[document.getElementsByClassName("old_main")[i].value] =1;
                    }
                }
                // console.log(data_select);
                if(document.getElementsByClassName("form-select")[(c_s-1)].value){
                    var obj = <?php echo json_encode($gm_status); ?>;
                    // console.log(obj);
                    var select2 ='<option value="">ไม่ระบุ</option>';
                    for(let key in obj){
                        if(!data_select[key]){
                            select2 += '<option value="'+key+'">'+obj[key].name+'</option>';
                        }
                    }
                    
                    if(select2 !=='<option value="">ไม่ระบุ</option>'){
                        var row = table.insertRow(nrow+1);
                        var cell1 = row.insertCell(0);
                        var cell2 = row.insertCell(1);
                        // var cell3 = row.insertCell(2);

                        cell1.innerHTML = sort+2;
                        var select1 = '<select name="to_detail['+(sort+1)+']" id="to_detail['+(sort+1)+']" class="form-select" onchange="myFunction('+(sort+1)+','+(nrow+1)+')">';
                        // var select2 ='<option value="">ไม่ระบุ</option>';
                        // console.log(select2)
                        var select3 ='</select>';
                        cell2.colSpan = "2";
                        cell2.innerHTML = select1+select2+select3;
                    } 
                }          
            }
        }
            
    </script>     
@endsection
