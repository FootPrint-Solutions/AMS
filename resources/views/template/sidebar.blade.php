<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                {{-- Menu Title --}}
                <li class="menu-title">
                    <span> Main Menu </span>
                </li>

                {{-- Menu --}}
                @foreach (session('menu') as $menu)
                    @php
                        $isActive = isset($active) && $active === $menu['id'];
                    @endphp

                    <li
                        class="@if (count($menu['menus']) > 0) submenu @endif @if ($isActive) active @endif">
                        <a href="{{ $menu['url'] }}">
                            <i class="{{ $menu['icon'] }}"></i>
                            <span> {{ $menu['name'] }} </span>
                            @if (count($menu['menus']) > 0)
                                <span class="menu-arrow"></span>
                            @endif
                        </a>

                        <ul style="@if ($isActive) display: block; @endif">
                            @foreach ($menu['menus'] as $menu_child)
                                @php
                                    $active_child = isset($active) && $active === $menu_child['id'];
                                @endphp
                                <li>
                                    <a href="{{ $menu_child['url'] }}"
                                        class="@if ($active_child) active @endif">
                                        {{ $menu_child['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
