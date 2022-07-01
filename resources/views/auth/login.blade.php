@extends('layouts.auth-master')

@section('content')
    <div class="d-flex justify-content-center mt-4" style="width: 100%">
        <form method="post" action="{{ route('login.perform') }}">
            
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <img class="mb-2" src="{{ asset('images/download.png') }}" alt="" width="72" height="72">
            
            <h1 class="h3 mb-3 fw-normal">Login</h1>

            @include('layouts.partials.messages')

            <div class="form-group form-floating mb-3">
                <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Username" required="required" autofocus>
                <label for="floatingName">Email or Username</label>
                @if ($errors->has('username'))
                    <span class="text-danger text-left">{{ $errors->first('username') }}</span>
                @endif
            </div>
            
            <div class="form-group form-floating mb-3">
                <input type="password" class="form-control" name="password" value="{{ old('password') }}" placeholder="Password" required="required">
                <label for="floatingPassword">Password</label>
                @if ($errors->has('password'))
                    <span class="text-danger text-left">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
            
            {{-- @include('auth.partials.copy') --}}
        </form>
    </div>
@endsection