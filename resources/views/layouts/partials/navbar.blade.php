<header class="p-3 bg-dark text-white">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
          <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"/></svg>
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          @auth
            <li><a href="{{ route('home.index') }}" class="nav-link px-2 text-secondary">Home</a></li> 
            {{-- <li>
                <a class="dropdown-toggle nav-link px-2 text-white" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                    Mail
                </a>
                <ul class="dropdown-menu bg-dark" aria-labelledby="user-dropdown-toggle">
                  <li><a href="{{ route('group_mail.index') }}" class="nav-link px-2 text-white">Set Group</a></li>
                  <li><a href="{{ route('mail.index') }}" class="nav-link px-2 text-white">Set Mail</a></li>
                </ul> 
            </li>          --}}
            <li><a href="{{ route('group_mail.index') }}" class="nav-link px-2 text-white">Group</a></li>
            {{-- <li><a href="{{ route('mail.index') }}" class="nav-link px-2 text-white">Set Mail</a></li>
            <li><a href="{{ route('folder.index') }}" class="nav-link px-2 text-white">Drive P</a></li> --}}
            <li>
                <a class="dropdown-toggle nav-link px-2 text-white" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                  Setting
                </a>
                <ul class="dropdown-menu bg-dark" aria-labelledby="user-dropdown-toggle">
                  <li><a href="{{ route('mail.index') }}" class="nav-link px-2 text-white">Mail</a></li>
                  <li><a href="{{ route('folder.index') }}" class="nav-link px-2 text-white">Folder</a></li>
                  {{-- <li><a href="{{ route('group_folder.index') }}" class="nav-link px-2 text-white">Group</a></li> --}}
                </ul> 
              {{-- </div>  --}}
            </li>
            <li><a href="{{ route('report.index') }}" class="nav-link px-2 text-white">Report</a></li>
          {{-- <li><a href="#" class="nav-link px-2 text-white">About</a></li> --}}
          @endauth
        </ul>
  
        {{-- <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
          <input type="search" class="form-control form-control-dark" placeholder="Search..." aria-label="Search">
        </form> --}}
  
        @auth
          {{auth()->user()->name}}
          <div class="text-end">
            <a href="{{ route('logout.perform') }}" class="btn btn-outline-light me-2">Logout</a>
            {{-- <a href="{{ route('register.perform') }}" class="btn btn-warning">Sign-up</a> --}}
          </div>
        @endauth
  
        @guest
          <div class="text-end">
            <a href="{{ route('login.perform') }}" class="btn btn-outline-light me-2">Login</a>
            {{-- <a href="{{ route('register.perform') }}" class="btn btn-warning">Sign-up</a> --}}
          </div>
        @endguest
      </div>
    </div>
  </header>