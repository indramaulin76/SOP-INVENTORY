<ul class="sidebar-menu">
    <!-- Dashboard -->
    <li>
        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            📊 Dashboard
        </a>
    </li>

    <!-- Input Data Menu (Admin and Superadmin only) -->
    @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
    <li>
        <a class="menu-item menu-toggle {{ request()->routeIs('input-data.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'inputDataMenu')">
            ➕ Input Data
        </a>
        <ul class="submenu {{ request()->routeIs('input-data.*') ? 'active' : '' }}" id="inputDataMenu">
            <li><a href="{{ route('input-data.barang') }}" class="submenu-item {{ request()->routeIs('input-data.barang') ? 'active' : '' }}">📦 Input Data Barang</a></li>
            <li><a href="{{ route('input-data.supplier') }}" class="submenu-item {{ request()->routeIs('input-data.supplier') ? 'active' : '' }}">🏢 Input Data Supplier</a></li>
            <li><a href="{{ route('input-data.customer') }}" class="submenu-item {{ request()->routeIs('input-data.customer') ? 'active' : '' }}">👥 Input Data Customer</a></li>
            <li><a href="{{ route('input-data.saldo') }}" class="submenu-item {{ request()->routeIs('input-data.saldo') ? 'active' : '' }}">💰 Input Saldo Awal</a></li>
        </ul>
    </li>
    @endif

    <!-- Input Barang Masuk Menu -->
    <li>
        <a class="menu-item menu-toggle {{ request()->routeIs('input-barang.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'inputBarangMenu')">
            📥 Input Barang Masuk
        </a>
        <ul class="submenu {{ request()->routeIs('input-barang.*') ? 'active' : '' }}" id="inputBarangMenu">
            <li><a href="{{ route('input-barang.pembelian-bahan-baku') }}" class="submenu-item {{ request()->routeIs('input-barang.pembelian-bahan-baku') ? 'active' : '' }}">Pembelian Bahan Baku</a></li>
            <li><a href="{{ route('input-barang.barang-dalam-proses') }}" class="submenu-item {{ request()->routeIs('input-barang.barang-dalam-proses') ? 'active' : '' }}">Barang Dalam Proses</a></li>
            <li><a href="{{ route('input-barang.barang-jadi') }}" class="submenu-item {{ request()->routeIs('input-barang.barang-jadi') ? 'active' : '' }}">Barang Jadi</a></li>
        </ul>
    </li>

    <!-- Input Barang Keluar Menu -->
    <li>
        <a class="menu-item menu-toggle {{ request()->routeIs('output-barang.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'outputBarangMenu')">
            📤 Input Barang Keluar
        </a>
        <ul class="submenu {{ request()->routeIs('output-barang.*') ? 'active' : '' }}" id="outputBarangMenu">
            <li><a href="{{ route('output-barang.penjualan-barang-jadi') }}" class="submenu-item {{ request()->routeIs('output-barang.penjualan-barang-jadi') ? 'active' : '' }}">Penjualan Barang Jadi</a></li>
            <li><a href="{{ route('output-barang.pemakaian-bahan-baku') }}" class="submenu-item {{ request()->routeIs('output-barang.pemakaian-bahan-baku') ? 'active' : '' }}">Pemakaian Bahan Baku</a></li>
            <li><a href="{{ route('output-barang.pemakaian-barang-dalam-proses') }}" class="submenu-item {{ request()->routeIs('output-barang.pemakaian-barang-dalam-proses') ? 'active' : '' }}">Pemakaian Barang Dalam Proses</a></li>
        </ul>
    </li>

    <!-- Report Menu (Admin and Pimpinan only) -->
    @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
    <li>
        <a class="menu-item menu-toggle {{ request()->routeIs('report.*') ? 'active' : '' }}" 
           onclick="toggleSubmenu(event, 'reportMenu')">
            📊 Laporan
        </a>
        <ul class="submenu {{ request()->routeIs('report.*') ? 'active' : '' }}" id="reportMenu">
            <li><a href="{{ route('report.laporan-pembelian-bahan-baku') }}" class="submenu-item {{ request()->routeIs('report.laporan-pembelian-bahan-baku') ? 'active' : '' }}">Laporan Pembelian Bahan Baku</a></li>
            <li><a href="{{ route('report.laporan-barang-dalam-proses') }}" class="submenu-item {{ request()->routeIs('report.laporan-barang-dalam-proses') ? 'active' : '' }}">Laporan Barang Dalam Proses</a></li>
            <li><a href="{{ route('report.laporan-barang-jadi') }}" class="submenu-item {{ request()->routeIs('report.laporan-barang-jadi') ? 'active' : '' }}">Laporan Barang Jadi</a></li>
            <li><a href="{{ route('report.laporan-penjualan') }}" class="submenu-item {{ request()->routeIs('report.laporan-penjualan') ? 'active' : '' }}">Laporan Penjualan</a></li>
            <li><a href="{{ route('report.laporan-pemakaian-bahan-baku') }}" class="submenu-item {{ request()->routeIs('report.laporan-pemakaian-bahan-baku') ? 'active' : '' }}">Laporan Pemakaian Bahan Baku</a></li>
            <li><a href="{{ route('report.laporan-pemakaian-barang-dalam-proses') }}" class="submenu-item {{ request()->routeIs('report.laporan-pemakaian-barang-dalam-proses') ? 'active' : '' }}">Laporan Pemakaian Barang Dalam Proses</a></li>
            <li><a href="{{ route('report.laporan-data-barang') }}" class="submenu-item {{ request()->routeIs('report.laporan-data-barang') ? 'active' : '' }}">Laporan Data Barang</a></li>
            <li><a href="{{ route('report.laporan-data-customer') }}" class="submenu-item {{ request()->routeIs('report.laporan-data-customer') ? 'active' : '' }}">Laporan Data Customer</a></li>
            <li><a href="{{ route('report.laporan-data-supplier') }}" class="submenu-item {{ request()->routeIs('report.laporan-data-supplier') ? 'active' : '' }}">Laporan Data Supplier</a></li>
            <li><a href="{{ route('report.kartu-stock') }}" class="submenu-item {{ request()->routeIs('report.kartu-stock') ? 'active' : '' }}">Kartu Stock</a></li>
            <li><a href="{{ route('report.laporan-stock-akhir') }}" class="submenu-item {{ request()->routeIs('report.laporan-stock-akhir') ? 'active' : '' }}">Laporan Stock Akhir</a></li>
            <li><a href="{{ route('report.laporan-stock-opname') }}" class="submenu-item {{ request()->routeIs('report.laporan-stock-opname') ? 'active' : '' }}">Laporan Stock Opname</a></li>
        </ul>
    </li>
    @endif

    <!-- User Management Menu (Admin and Pimpinan only) -->
    @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
    <li>
        <a href="{{ route('users.index') }}" class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            👥 Manajemen User
        </a>
    </li>
    @endif
</ul>
