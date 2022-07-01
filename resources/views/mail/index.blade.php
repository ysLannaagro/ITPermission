@extends('layouts.app-master')

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
                <h1 class="app-page-title mb-0">จัดการ Mail</h1>
            </div>
            <div class="col-auto">
                <div class="page-utilities">
                    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">	
                        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" method="GET" id="frm_search">
                            <input type="search" class="form-control form-control-dark" id="txt_search" name="txt_search" 
                            placeholder="Search..." aria-label="Search" value="{{ $txt_search }}">
                            {{-- <input type="button" class="btn btn-success" value="Search" onClick="this.form.action='{{ route('wh_rm.index') }}'; submit()"> Name/LACO Name/E-Mail --}}
                        </form>	
                        <div class="col-auto">				    
                            <a class="btn btn-dark" href="{{ route('mail_import_view') }}">
                                Upload Excel
                            </a>
                            <a class="btn btn-success" href="{{ route('mail.create') }}">
                                Add
                            </a>
                        </div>
                    </div><!--//row-->
                </div><!--//table-utilities-->
            </div><!--//col-auto-->
        </div><!--//row onclick="getURL($key);" -->
    
        <nav class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
            <a class="flex-sm-fill text-sm-center nav-link active" data-bs-toggle="tab" href="#" role="tab"
                aria-controls="orders-all" aria-selected="true"></a>
        </nav>             
        
        <div class="tab-content" id="orders-table-tab-content">                
            <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">
                    <table class="table app-table-hover mb-0 text-left">
                        <thead>
                            <tr style="text-align: center">
                                <th class="cell" style="width:5%">No.</th>
                                <th class="cell" style="width:25%">Name</th>
                                <th class="cell" style="width:5%">LACO Name</th>
                                <th class="cell" style="width:25%">E-Mail</th>
                                <th class="cell" colspan="2" style="width:25%">Manage</th>
                                <th class="cell" style="width:15%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $check_date = ""; 
                            @endphp
                            @foreach ($mail as $key)                    
                                <tr @if($key->status==0) class="text-danger" @endif>                                
                                    <td class="cell" style="text-align: center;">{{ $loop->iteration }}</td>  
                                    <td class="cell" style="text-align: left;">{{ $key->name }}</td>   
                                    <td class="cell" style="text-align: left;">{{ strtoupper($key->pre_name) }}</td>   
                                    <td class="cell" style="text-align: left;">{{ $key->email }}</td>  
                                    {{-- @if($key->status==1) --}}
                                        <td class="cell" style="text-align: center;">
                                            {{-- <div class="row"> --}}
                                                <div class="col-auto mx-1">
                                                    <a class="btn btn-primary" href="{{ route('mail.edit', $key->id) }}">แก้ไข</a>
                                                </div>                         
                                            </td>
                                            <td class="cell" style="text-align: center;">
                                                {{-- @if(empty($mig[$key->id])) --}}
                                                    <div class="col-auto mx-1">
                                                        <a class="btn btn-danger" href="{{ route('mail_del', $key->id) }}">ลบ</a>
                                                    </div> 
                                                {{-- @endif  --}}
                                            </td>
                                            <td class="cell" style="text-align: center;">
                                                <div class="col-auto mx-1">
                                                    <a class="btn btn-info" href="{{ route('mail.to_group', $key->id) }}">จัดกลุ่ม Mail</a>
                                                </div> 
                                            {{-- </div>                          --}}
                                        </td>  
                                    {{-- @else
                                        <td class="cell" style="text-align: center;">
                                            <div class="row">
                                                <div class="col-auto mx-2">
                                                    <a class="btn btn-secondary" href="{{ route('mail.to_use', $key->id) }}">To Use</a>
                                                </div> 
                                            </div>                         
                                        </td>  
                                    @endif --}}
                                </tr>    
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        var input = document.getElementById("txt_search");
        input.addEventListener("keypress", function(event) {
          if (event.key === "Enter") {
            event.preventDefault();
            frm_search.submit();
          }
        });
    </script>
@endsection