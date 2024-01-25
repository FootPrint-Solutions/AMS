<!-- Sidebar -->
{{-- @dd(session('menu')); --}}

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main Menu</span>
                </li>

                {{-- Dashboard --}}
                <li class="{{ $title == 'Dashboard | ' . config('app.name') ? 'active' : '' }}">
                    <a href="/"><i class="fas fa-dashboard"></i> <span>Dashboard</span></a>
                </li>

                {{--  --}}
                @foreach (session('menu') as $menu)
                    <li class="submenu 
                        @if (isset($active) && $active === $menu['id'])
                            active
                        @endif
                    ">
                        <a href="#"><i class="{{ $menu['icon'] }}"></i> <span> {{ $menu['name'] }} </span>
                            @if (count($menu['menus']) > 0)
                                <span class="menu-arrow"></span>
                            @endif
                        </a>

                        <ul>
                            @foreach ($menu['menus'] as $menu_child)
                                <li><a href="{{ $menu_child['url'] }}">{{ $menu_child['name'] }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
