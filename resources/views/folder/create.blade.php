@extends('layouts.app-master')

@section('content')
    <div class="app-content p-md-4">
        <div class="app-container-xl">
            <div class="page-heading row">        
                <div class="pull-right col-auto">
                    <a class="btn btn-success" href="{{ route('folder.index') }}">Back</a>
                </div>
                <h3 class="col">{{ __('Folder') }} -> Create</h3>
            </div>
        
            <nav class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
                <a class="flex-sm-fill text-sm-center nav-link active" data-bs-toggle="tab" href="#" role="tab"
                    aria-controls="orders-all" aria-selected="true"></a>
            </nav> 
            @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('error') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif 
            
            <div class="tab-content" id="orders-table-tab-content">                
                <div class="app-card app-card-orders-table shadow-sm mb-5">
                    <div class="app-card-body">
                        <form method="POST" action="{{ route('folder.store') }}">
                            @csrf
                            <div class="form-group row mb-2">
                                <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                </div>
                            </div>  

                            {{-- <div class="form-group row mb-2">
                                <label for="pre_name" class="col-md-2 col-form-label text-md-right">{{ __('LACO Name') }}</label>

                                <div class="col-md-6">
                                    <input id="pre_name" type="text" class="form-control @error('pre_name') is-invalid @enderror"
                                        name="pre_name" value="{{ old('pre_name') }}" autocomplete="pre_name" autofocus style="text-transform:uppercase" />
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="email" class="col-md-2 col-form-label text-md-right">{{ __('E-Mail') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                </div>
                            </div>                             --}}

                            <div class="form-group row mb-2">
                                <div class="col-md-6 offset-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div><!--//app-card-body-->		
                </div><!--//app-card-->                  
            </div><!--//tab-content-->
        </div><!--//container-fluid-->
    </div><!--//app-content-->        
@endsection
