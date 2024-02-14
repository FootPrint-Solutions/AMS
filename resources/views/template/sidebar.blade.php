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
                                    $active_child_menu = isset($active_child) && $active_child === $menu_child['id'];
                                @endphp

                                <li @if (!empty(session('submenu')[$menu_child['id']])) class="submenu" @endif>
                                    <a href="{{ $menu_child['url'] }}"
                                        class="@if ($active_child_menu) active @endif">
                                        {{ $menu_child['name'] }}

                                        @if (!empty(session('submenu')[$menu_child['id']]))
                                            <span class="menu-arrow"></span>
                                        @endif
                                    </a>

                                    @if (!empty(session('submenu')[$menu_child['id']]))
                                        <ul>
                                            <li>
                                                <a href="{{ $menu_child['url'] }}">
                                                    {{ $menu_child['name'] }}
                                                </a>
                                            </li>

                                            @foreach (session('submenu')[$menu_child['id']] as $submenu)
                                                <li>
                                                    <a href="{{ $submenu['url'] }}">
                                                        {{ $submenu['name'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
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
