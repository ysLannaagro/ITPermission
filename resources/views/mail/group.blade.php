@extends('layouts.app-master')

<style>
    .set_center {
        text-align: center;
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

        <div class="row g-3 mb-2 align-items-center justify-content-between">
            <div class="col-auto">
                <div class="row">
                    <div class="pull-right col-auto">
                        <a class="btn btn-secondary" href="{{ route('mail.index') }}">Back</a>
                    </div>
                    <h3 class="col">{{ __('กลุ่มของ') }} -> {{ $mail->email }}</h3>
                </div>
            </div>
            {{-- <div class="col-auto">
                <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" method="GET" id="frm_search">
                    <input type="search" class="form-control form-control-dark" id="txt_search" name="txt_search" 
                    placeholder="Search..." aria-label="Search" value="{{ $txt_search }}">
                </form>
            </div><!--//col-auto--> --}}
        </div><!--//row onclick="getURL($key);" -->
    
        <nav class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
            <a class="flex-sm-fill text-sm-center nav-link active" data-bs-toggle="tab" href="#" role="tab"
                aria-controls="orders-all" aria-selected="true"></a>
        </nav>             
        
        <div class="tab-content" id="orders-table-tab-content">                
            <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">
                    <form method="POST" action="{{ route('mail.group', $mail->id) }}">
                        @csrf
                        <table class="table app-table-hover mb-0 text-left" id="myTable">
                            <thead>
                                <tr style="text-align: center">
                                    <th class="cell" rowspan="2" style="width:5%">No.</th>
                                    <th class="cell" colspan="{{ $max_col }}">ลำดับขั้น</th>
                                    <th class="cell" rowspan="2" style="width:5%"></th>
                                </tr>
                                <tr style="text-align: center">
                                    {{-- @for($i=0; $i<$max_col; $i++)
                                        <th class="cell" style="width:{{ 90/$max_col }}%">{{ $i+1 }}</th>
                                    @endfor --}}
                                    @for($i=$max_col; $i>0; $i--)
                                        <th class="cell" style="width:{{ 90/$max_col }}%">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>                            
                                @php
                                    $row = 0;
                                    $sort = 0;
                                    $count_row = 0;
                                @endphp
                                @foreach ($set_group as $key=>$value) 
                                    @php
                                        $c_row = 0; 
                                    @endphp 
                                    @foreach ($value as $key1=>$value1)                    
                                        <tr>  
                                            @if($c_row==0)                              
                                                <td class="cell" style="text-align: center;" rowspan="{{ count($set_group[$key]) }}">{{ ++$row }}</td> 
                                            @endif
                                            @php
                                                $loop = 0; 
                                            @endphp    
                                            @for($i=$max_col; $i>0; $i--)    
                                                @if(!empty($set_group[$key][$key1][$loop]))
                                                    <td class="cell @if($chk_duplicate[$set_group[$key][$key1][$loop]]>1) bg-danger text-white @endif">
                                                        {{ $gm_all[$set_group[$key][$key1][$loop]] }}
                                                    </td>
                                                @else
                                                    <td class="cell" style="text-align: left;"></td>
                                                @endif                                        
                                                @php
                                                    $loop++; 
                                                @endphp
                                            @endfor
                                            @if($c_row==0)
                                                <td class="cell" style="text-align: left;" rowspan="{{ count($set_group[$key]) }}">
                                                    <a class="text-danger" href="{{ route('mail.group_del',$row_id[$key]) }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                                                        </svg>
                                                    </a>
                                                </td> 
                                            @endif 
                                        </tr> 
                                        @php
                                            $c_row++; 
                                        @endphp                                     
                                    @endforeach                                                                          
                                    @php
                                        $count_row = $count_row+$c_row; 
                                    @endphp 
                                @endforeach
                                <tr>                                                     
                                    <td class="cell" style="text-align: center;">{{ ++$row }}</td> 
                                    <td class="cell" colspan="{{ $max_col+1 }}">
                                        <select name="to_detail[{{ $sort }}]" id="to_detail[{{ $sort }}]" class="form-select" onchange="myFunction({{ $sort }},{{ $row }},{{ $max_col }},{{ $count_row }})">
                                            <option value="">ไม่ระบุ</option>
                                            @foreach($group_add as $key)
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
                                    @for($i=$max_col; $i>0; $i--) 
                                        <td class="cell"></td>  
                                    @endfor                                    
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function myFunction(sort,row,col,count_row) {            
            // console.log(document.getElementsByClassName("form-select")[sort].value);
            if(document.getElementsByClassName("form-select")[sort].value){
                var table = document.getElementById("myTable");
                // นับว่ามี select กี่ตัว ไว้ใช้ตอนวน loop ตัดค่าออก
                var  titleElement = document.getElementsByClassName("form-select");  
                let c_s = titleElement.length;

                var  txt_all = <?php echo json_encode($gmr_all); ?>; 
                var data_select = [];
                for (let i = 0; i < c_s; i++) {
                    var chk_v = document.getElementsByClassName("form-select")[i].value;
                    if(chk_v){
                        console.log(chk_v);
                        data_select[chk_v] =1;
                        // console.log(txt_all['up'][chk_v]); 
                        // var chk_up = txt_all['up'][chk_v];
                        // while (chk_up) {
                        //     for(const [key, value] of Object.entries(txt_all['up'][chk_v])){
                        //         console.log('k->'+key+' v->'+value);
                        //         data_select[key] = 1;
                        //     }
                            
                        // }
                        // // console.log(txt_all['down'][chk_v]);
                        // if(txt_all['up'][chk_v]) { 
                        //     for(const [key, value] of Object.entries(txt_all['down'][chk_v])){
                        //         console.log('k->'+key+' v->'+value);
                        //         data_select[key] = 1;
                        //     }
                        // }
                        // // for(var k in txt_all) {
                        // //     console.log(k, txt_all['up'][chk_v]);
                        // // } 
                    }
                }
             
                console.log(data_select);
                if(document.getElementsByClassName("form-select")[(c_s-1)].value){
                    var obj = <?php echo json_encode($group_add); ?>;
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
                        var table_row = table.insertRow(count_row+3);
                        // var cell = [];
                        // //  = table_row.insertCell(0);
                        // for (let i = 0; i < col; i++) {
                        //     cell[(i+1)] = table_row.insertCell(i);
                        // }

                        // cell[1].innerHTML = row+1;
                        // cell[1].className = 'set_center';
                        // var select1 = '<select name="to_detail['+(sort+1)+']" id="to_detail['+(sort+1)+']" class="form-select" onchange="myFunction('+(sort+1)+','+(row+1)+','+col+')">';
                        // // var select2 ='<option value="">ไม่ระบุ</option>';
                        // // console.log(select2)
                        // var select3 ='</select>';
                        // cell[2].colSpan = (col+1);
                        // cell[2].innerHTML = select1+select2+select3;

                        var cell1 = table_row.insertCell(0);
                        var cell2 = table_row.insertCell(1);
                        var select1 = '<select name="to_detail['+(sort+1)+']" id="to_detail['+(sort+1)+']" class="form-select" onchange="myFunction('+(sort+1)+','+(row+1)+','+col+','+(count_row+1)+')">';
                        // // var select2 ='<option value="">ไม่ระบุ</option>';
                        // // console.log(select2)
                        var select3 ='</select>';
                        // cell[2].colSpan = (col+1);
                        // cell[2].innerHTML = select1+select2+select3;

                        cell1.innerHTML = row+1;
                        cell1.className = 'set_center';
                        cell2.colSpan = (col+1);
                        cell2.innerHTML = select1+select2+select3;
                    }
                }
            }
        }
    </script>
@endsection