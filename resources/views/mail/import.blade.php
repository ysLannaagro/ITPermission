@extends('layouts.app-master')

@section('content')
<div class="app-content p-md-4">
	<header class="ex-header bg-gray">
        <div class="app-container">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
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
            <div class="row justify-content-between">
                <div class="col-auto my-4">  
                    <div class="page-heading row">        
                        <div class="pull-right col-auto">
                            <a class="btn btn-secondary" href="{{ route('mail.index') }}">Back</a>
                        </div>                       
                        <h3 class="col">{{ __('จัดการ Mail') }} -> Import</h3>
                    </div>          
                </div> <!-- end of col -->           
            </div> <!-- end of row -->
        </div> <!-- end of container -->

    </header> <!-- end of ex-header -->  
   
    <nav class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
        <a class="flex-sm-fill text-sm-center nav-link active" data-bs-toggle="tab" href="#" role="tab"
            aria-controls="orders-all" aria-selected="true"></a>
    </nav>

    <div class="app-container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    
                    {{-- <div class="card-header">Questions - Import</div> --}}
                    <div class="card-body">                        
                        <div class="row g-4 settings-section">
	                
                            <div class="col-6 col-md-6">
                                <div class="app-card app-card-settings shadow-sm p-4">
                                    
                                    <div class="app-card-body">
                                        <form action="{{ route('mail_import') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
                                                <h3 class="col">{{ __('รายชื่อ Mail') }}</h3>
                                                <div class="custom-file text-left">
                                                    <input type="file" name="file_upload" class="custom-file-input" id="file_upload">
                                                </div>
                                            
                                                <br>
                                                <button class="btn btn-success">Import</button>
                                                {{-- <a class="btn btn-warning" href="{{ route('quiz_export') }}">Export</a> --}}
                                                <div class="col-auto" style="float: right; margin-right: 10%; width:50%">
                                                    <a href="{{ asset('assets/file/mail.xlsx') }}">ตัวอย่างไฟล์..</a>
                                                </div>
                                                <p class="text-danger">{{ __('** หากมี LACO name หรือ Email ซ้ำ จะเป็นการ updae **') }}</p>
                                            </div>
                                        </form>  
                                    </div><!--//app-card-body-->
                                    
                                </div><!--//app-card-->
                            </div>

                            <div class="col-6 col-md-6">
                                <div class="app-card app-card-settings shadow-sm p-4">
                                    
                                    <div class="app-card-body">
                                        <form action="{{ route('mail_import_relation') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
                                                <h3 class="col">{{ __('ความสัมพันธ์ระหว่างกลุ่มกับเมลล์') }}</h3>
                                                <div class="custom-file text-left">
                                                    <input type="file" name="file_upload" class="custom-file-input" id="file_upload">
                                                </div>
                                            
                                                <br>
                                                <button class="btn btn-success">Import</button>
                                                {{-- <a class="btn btn-warning" href="{{ route('quiz_export') }}">Export</a> --}}
                                                <div class="col-auto" style="float: right; margin-right: 10%; width:50%">
                                                    <a href="{{ asset('assets/file/gm.xlsx') }}">ตัวอย่างไฟล์..</a>
                                                </div>
                                                <p class="text-danger">{{ __('** หากมีค่าซ้ำเดิมจะไม่ทำงาน **') }}</p>
                                            </div>
                                        </form>  
                                    </div><!--//app-card-body-->
                                    
                                </div><!--//app-card-->
                            </div>
                        </div><!--//row-->

                        {{-- <hr class="my-4">
                        <div class="row g-4 settings-section">
                            <h1 class="app-page-title">ตัวอย่างสำหรับการ Import</h1>
                            <img src="{{asset('/pic/quiz.jpg')}}"  class="photo" width="100" height="250" data-toggle="modal" data-target="#exampleModal">
                        </div><!--//row-->  --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection