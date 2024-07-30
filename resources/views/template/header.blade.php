<div class="header">
    {{-- Logo --}}
    <div class="header-left" style="box-shadow: 0px 0px 0px rgba(0, 0, 0, 0)">
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
    {{-- <div class="top-nav-search">
        <form>
            <input type="text" class="form-control" placeholder="Search here">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div> --}}

    {{-- Mobile Menu Toggle --}}
    <a class="mobile_btn" id="mobile_btn">
        <svg width="90" height="90" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_f_83_1133)">
                <rect x="20" y="20" width="50" height="50" rx="7" fill="#60D3AA" fill-opacity="0.2" />
            </g>
            <rect x="48.4" y="28" width="13.6" height="13.6" rx="2" fill="#092C4C" />
            <rect x="28" y="28" width="13.6" height="13.6" rx="2" fill="#092C4C" />
            <rect x="28" y="48.4" width="13.6" height="13.6" rx="2" fill="#092C4C" />
            <rect x="48.4" y="48.4" width="13.6" height="13.6" rx="2" fill="#092C4C" />
            <defs>
                <filter id="filter0_f_83_1133" x="0" y="0" width="90" height="90" filterUnits="userSpaceOnUse"
                    color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                    <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                    <feGaussianBlur stdDeviation="10" result="effect1_foregroundBlur_83_1133" />
                </filter>
            </defs>
        </svg>
    </a>

    {{-- Right Menu --}}
    <ul class="nav user-menu">
        {{-- Notifications --}}
        <li class="nav-item dropdown noti-dropdown me-2">
            {{-- Notification Logo --}}
            {{-- <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown">
                <img src="{{ asset('/img/icons/header-icon-05.svg') }}" alt="">
            </a> --}}

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
                <i class="fa fa-expand"></i>
            </a>
        </li>

        {{-- User Menu --}}
        <li class="nav-item dropdown has-arrow new-user-menus">
            {{-- User --}}
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <div class="d-none d-lg-block">
                    <div class="user-img mt-4   ">
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
                </div>


                {{-- mobile version --}}
                <div class="user-img d-block d-md-none">
                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="50" height="50" rx="7" fill="#EFEFEF" />
                        <rect x="4" y="4" width="42" height="42" rx="7"
                            fill="url(#pattern0_83_1075)" />
                        <defs>
                            <pattern id="pattern0_83_1075" patternContentUnits="objectBoundingBox" width="1"
                                height="1">
                                <use xlink:href="#image0_83_1075" transform="scale(0.0078125)" />
                            </pattern>
                            <image id="image0_83_1075" width="128" height="128"
                                xlink:href="data:image/jpeg;base64,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" />
                        </defs>
                    </svg>
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
