<!-- Sidebar -->
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



                {{-- Master Data --}}
                <li
                    class="submenu {{ $title == 'Company | ' . config('app.name') || $title == 'Customer | ' . config('app.name') . '' || $title == 'Vehicle | ' . config('app.name') . '' || $title == 'Battery | ' . config('app.name') . '' ? 'active' : '' }}">
                    <a href="#"
                        class="{{ $title == 'Company | ' . config('app.name') || $title == 'Customer | ' . config('app.name') . '' || $title == 'Vehicle | ' . config('app.name') . '' || $title == 'Battery | ' . config('app.name') . '' ? 'active subdrop' : '' }}"><i
                            class="fa-solid fa-book"></i> <span> Master Data </span> <span
                            class="menu-arrow"></span></a>
                    <ul
                        style="{{ $title == 'Company | ' . config('app.name') || $title == 'Customer | ' . config('app.name') . '' || $title == 'Vehicle | ' . config('app.name') . '' || $title == 'Battery | ' . config('app.name') . '' ? 'display: block;' : '' }}">
                        <li>
                            <a href="/company"
                                class="{{ $title == 'Company | ' . config('app.name') ? 'active' : '' }}">Company</a>
                        </li>
                        <li>
                            <a href="/customer"
                                class="{{ $title == 'Customer | ' . config('app.name') ? 'active' : '' }}">
                                Customer
                            </a>
                        </li>
                        <li><a href="/vehicle"
                                class="{{ $title == 'Vehicle | ' . config('app.name') ? 'active' : '' }}">Vehicle</a>
                        </li>
                        <li><a href="/battery"
                                class="{{ $title == 'Battery | ' . config('app.name') ? 'active' : '' }}">Battery</a>
                        </li>
                    </ul>
                </li>


                {{-- Orders --}}
                <li class="submenu {{ $active == 'Orders' ? 'active' : '' }}">
                    <a href="#"><i class="fa-solid fa-receipt"></i> <span> Orders </span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="/quotation">Quick Quotation</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
