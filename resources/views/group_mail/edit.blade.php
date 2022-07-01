@extends('layouts.app-master')

@section('content')
    <div class="app-content p-md-4">
        <div class="app-container-xl">            
            <div class="page-heading row">        
                <div class="pull-right col-auto">{{-- route('group_mail.index') url('group_mail?txt_search='.$txt_search.'&set_col='.$set_col)--}}
                    <a class="btn btn-secondary" href="{{ route('group_mail.index') }}">Back</a>
                </div>
                <h3 class="col">{{ __('จัดการกลุ่ม') }} -> Edit</h3>
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
                    <div class="app-card-body">{{--  url('group_mail/'.$gm->id.'?txt_search='.$txt_search.'&set_col='.$set_col)--}}
                        <form method="POST" action="{{ route('group_mail.update', $gm->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row mb-2">
                                <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ $gm->name }}" required autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div> 
                            <div class="form-group row mb-2">
                                <label for="set_column" class="col-md-2 col-form-label text-md-right"></label>

                                <div class="col-md-6">
                                    <input class="form-check-input" type="checkbox" value="1" id="set_column" name="set_column" @if($gm->set_column==1) checked @endif>
                                    <label class="form-check-label" for="set_column">
                                        Public mail
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <div class="col-md-6 offset-md-2">
                                    <button type="submit" class="btn btn-success">
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
