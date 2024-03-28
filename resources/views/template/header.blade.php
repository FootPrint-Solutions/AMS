<div class="header">
    {{-- Logo --}}
    <div class="header-left">
        <a href="/" class="logo">
            <h1><img src="/img/logos/128x128.png"> AMS</h1>
        </a>

        <a href="/" class="logo logo-small">
            <h3>AMS</h3>
        </a>
    </div>

    <div class="menu-toggle">
        <a href="javascript:void(0);" id="toggle_btn">
            <i class="fas fa-bars"></i>
        </a>
    </div>

    {{-- Search Bar --}}
    <div class="top-nav-search">
        <form>
            <input type="text" class="form-control" placeholder="Search here">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    {{-- Mobile Menu Toggle --}}
    <a class="mobile_btn" id="mobile_btn">
        <i class="fas fa-bars"></i>
    </a>

    {{-- Right Menu --}}
    <ul class="nav user-menu">
        {{-- Notifications --}}
        <li class="nav-item dropdown noti-dropdown me-2">
            {{-- Notification Logo --}}
            <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown">
                <img src="{{ asset('/img/icons/header-icon-05.svg') }}" alt="">
            </a>

            {{-- Notification Dropdown Menu --}}
            <div class="dropdown-menu notifications">
                {{-- Header --}}
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifications</span>
                    <a href="javascript:void(0)" class="clear-noti"> Clear All </a>
                </div>

                {{-- List of Notifications --}}
                <div class="noti-content">
                    <ul class="notification-list">
                        <li class="notification-message">
                            <a href="#">
                                <div class="media d-flex">
                                    {{-- Profile Picture --}}
                                    <span class="avatar avatar-sm flex-shrink-0">
                                        <img class="rounded-circle" alt="User Image"
                                            src="{{ asset('/img/profiles/default_profile.png') }}">
                                    </span>

                                    {{-- Info --}}
                                    <div class="media-body flex-grow-1">
                                        <p class="noti-details"><span class="noti-title">Example Notifications</p>
                                        <p class="noti-time"><span class="notification-time">4 mins ago</span></p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Footer --}}
                <div class="topnav-dropdown-footer">
                    <a href="#">View all Notifications</a>
                </div>
            </div>
        </li>

        {{-- Zoom --}}
        <li class="nav-item zoom-screen me-2">
            <a href="#" class="nav-link header-nav-list win-maximize">
                <img src="{{ asset('/img/icons/header-icon-04.svg') }}" alt="">
            </a>
        </li>

        {{-- User Menu --}}
        <li class="nav-item dropdown has-arrow new-user-menus">
            {{-- User --}}
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <div class="user-img">
                    {{-- Profile Picture --}}
                    @if (is_null(auth()->user()->image) || empty(auth()->user()->image))
                        <img class="rounded-circle" alt="User Image"
                            src="{{ asset('/img/profiles/default_profile.png') }}">
                    @else
                        <img class="rounded-circle" alt="User Image"
                            src="{{ asset('storage/image/profile/' . auth()->user()->image) }}">
                    @endif

                    {{-- Name & Role --}}
                    <div class="user-text">
                        @auth
                            <h6>{{ Auth::user()->name }}</h6>
                            <p class="text-muted mb-0">Administrator</p>
                        @endauth
                    </div>
                </div>
            </a>

            {{-- User Dropdown Menu --}}
            <div class="dropdown-menu">
                {{-- Header --}}
                <div class="user-header">
                    <div class="avatar avatar-sm">
                        @if (is_null(auth()->user()->image) || empty(auth()->user()->image))
                            <img class="rounded-circle" alt="User Image"
                                src="{{ asset('/img/profiles/default_profile.png') }}">
                        @else
                            <img class="rounded-circle" alt="User Image"
                                src="{{ asset('storage/image/profile/' . auth()->user()->image) }}">
                        @endif
                    </div>

                    <div class="user-text">
                        @auth
                            <h6>{{ Auth::user()->name }}</h6>
                            <p class="text-muted mb-0">Administrator</p>
                        @endauth
                    </div>
                </div>

                {{-- Profile --}}
                <a class="dropdown-item" href="/profile">My Profile</a>

                {{-- Logout --}}
                <a class="dropdown-item" href="/logout">Logout</a>
            </div>
        </li>

    </ul>
    {{-- End of Right Menu --}}

</div>
