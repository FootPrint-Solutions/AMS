<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main Menu</span>
                </li>

                {{-- Dashboard --}}
                <li>
                    <a href="/"><i class="fas fa-dashboard"></i> <span>Dashboard</span></a>
                </li>

                {{-- Master Data --}}
                <li class="submenu">
                    <a href="#"><i class="fa-solid fa-book"></i> <span> Master Data </span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="/company">Company</a></li>
                        <li><a href="/customer">Customer</a></li>
                        <li><a href="/vehicle">Vehicle</a></li>
                        <li><a href="/battery">Battery</a></li>
                    </ul>
                </li>

                {{-- Orders --}}
                <li class="submenu">
                    <a href="#"><i class="fa-solid fa-receipt"></i> <span> Orders </span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="/quotation">Quick Quotation</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->