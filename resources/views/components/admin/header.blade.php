<div class="top_nav">
    <div class="nav_menu d-flex align-items-center justify-content-between px-3">

        <!-- Left: Menu Toggle -->
        <div class="nav toggle">
            <a id="menu_toggle" class="text-dark">
                <i class="fa fa-bars"></i>
            </a>
        </div>

        <!-- Right: User Dropdown -->
        <nav class="nav navbar-nav">
            @php
                // Generate initials from user name
                $nameParts = explode(' ', trim(auth()->user()->name));
                $initials = count($nameParts) > 1
                    ? strtoupper($nameParts[0][0] . $nameParts[1][0])
                    : strtoupper(substr($nameParts[0], 0, 2));
            @endphp

            <ul class="navbar-right d-flex align-items-center mb-0">
                <li class="nav-item dropdown">
                    
                    <!-- Profile Trigger -->
                    <a href="#" 
                       class="nav-link dropdown-toggle d-flex align-items-center" 
                       id="navbarDropdown" 
                       role="button" 
                       data-toggle="dropdown" 
                       aria-haspopup="true" 
                       aria-expanded="false">

                        <!-- User Profile Photo OR Initials Fallback -->
                        <div class="me-2">
                            <img src="{{ auth()->user()->profile_photo_url }}" 
                                 alt="User Avatar" 
                                 class="rounded-circle" 
                                 style="width:40px; height:40px; object-fit:cover;"
                                 onerror="this.style.display='none'; document.getElementById('userInitials').style.display='flex';">

                            <!-- Initials Fallback -->
                            <div id="userInitials"
                                style="display:none; width:40px; height:40px; border-radius:50%; 
                                       background-color:#0d6efd; color:#fff; font-weight:bold; 
                                       align-items:center; justify-content:center; text-transform:uppercase;">
                                {{ $initials }}
                            </div>
                        </div>

                        <!-- User Name & Department -->
                        <div>
                            <strong>{{ ucfirst(auth()->user()->name) }}</strong>
                            @if(auth()->user()->department?->name)
                                <small class="d-block text-muted">
                                    (Department: {{ auth()->user()->department->name }})
                                </small>
                            @endif
                        </div>
                    </a>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="navbarDropdown">
                        {{-- Future: Add Profile, Settings, Help, etc. --}}
                        {{-- <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="fa fa-user me-2"></i> Profile
                        </a> --}}

                        {{-- Logout Form --}}
                        <form id="logout-form" action="{{ url('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                        <a class="dropdown-item text-danger" href="{{ url('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out me-2"></i> Log Out
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>
