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
                <h1 class="app-page-title mb-0">Folder</h1>
            </div>
            <div class="col-auto">
                <div class="page-utilities">
                    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">	
                        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" method="GET" id="frm_search">
                            <input type="search" class="form-control form-control-dark" id="txt_search" name="txt_search" 
                            placeholder="Search..." aria-label="Search" value="{{ Session::get('txt_folder_search') }}">
                            {{-- <input type="button" class="btn btn-success" value="Search" onClick="this.form.action='{{ route('wh_rm.index') }}'; submit()"> Name/LACO Name/E-Mail --}}
                            <input type="hidden" id="page_main" name="page_main" value="1"/>
                        </form>	
                        <div class="col-auto">				    
                            <a class="btn btn-dark" href="{{ route('folder_import') }}">
                                Upload Excel
                            </a>
                            <a class="btn btn-success" href="{{ route('folder.create') }}">
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
                            <tr>
                                <th class="cell" style="width:5%">No.</th>
                                <th class="cell" style="width:25%">Name</th>
                                <th class="cell" style="width:10%"></th>
                                <th class="cell" colspan="2" style="width:10%">Manage</th>                                
                                <th class="cell" style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $check_date = ""; 
                            @endphp
                            @foreach ($folder as $key)                    
                                <tr @if($key->status==0) class="text-danger" @endif>                                
                                    <td class="cell" style="text-align: center;">{{ $loop->iteration }}</td>  
                                    <td class="cell" style="text-align: left;">{{ $key->name }}</td>
                                    <td class="cell" style="text-align: center;">
                                        <div class="col-auto mx-1">
                                            <a class="btn btn-info" href="{{ route('folder.to_group', $key->id) }}">จัดกลุ่ม</a>
                                        </div> 
                                    </td>     
                                    <td class="cell" style="text-align: center;">
                                        <div class="col-auto mx-1">
                                            {{-- <a class="btn btn-primary" href="{{ route('folder.edit', $key->id) }}">แก้ไข</a> --}}
                                            <a class="text-primary" href="{{ route('folder.edit', $key->id) }}" title="แก้ไข">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-tools" viewBox="0 0 16 16">
                                                    <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3c0-.269-.035-.53-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814L1 0Zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708ZM3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026L3 11Z"/>
                                                </svg>
                                            </a>
                                        </div>                         
                                    </td>
                                    <td class="cell" style="text-align: center;">
                                        @if(empty($fig[$key->id]))
                                            <div class="col-auto mx-1">
                                                {{-- <a class="btn btn-danger" href="{{ route('folder_del', $key->id) }}">ลบ</a> --}}
                                                <a class="text-danger" href="{{ route('folder_del', $key->id) }}" title="ลบ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                    </svg>
                                                </a>
                                            </div> 
                                        @endif 
                                    </td>  
                                    <td class="cell"></td>                                  
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