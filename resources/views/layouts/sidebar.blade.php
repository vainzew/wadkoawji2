<!-- Sidebar Brand -->
<div class="sidebar-brand">
    <div class="sidebar-brand-full">
        <img src="{{ url($setting->path_logo) }}" height="32" alt="{{ $setting->nama_perusahaan }}">
        <span class="sidebar-brand-text">{{ $setting->nama_perusahaan }}</span>
    </div>
    <div class="sidebar-brand-minimized">
        <img src="{{ url($setting->path_logo) }}" height="24" alt="{{ $setting->nama_perusahaan }}">
    </div>
</div>

<!-- Navigation -->
<ul class="sidebar-nav">
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="nav-icon cil-speedometer"></i>
            <span class="nav-text">Dashboard</span>
        </a>
    </li>

    @if (auth()->user()->level == 1)
    <!-- MASTER Section -->
    <li class="nav-title">Master</li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
            <i class="nav-icon cil-grid"></i>
            <span class="nav-text">Kategori</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
            <i class="nav-icon cil-layers"></i>
            <span class="nav-text">Produk</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('promo.*') ? 'active' : '' }}" href="{{ route('promo.index') }}">
            <i class="nav-icon cil-tags"></i>
            <span class="nav-text">Promo</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
            <i class="nav-icon cil-wallet"></i>
            <span class="nav-text">Kas</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('member.*') ? 'active' : '' }}" href="{{ route('member.index') }}">
            <i class="nav-icon cil-contact"></i>
            <span class="nav-text">Member</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
            <i class="nav-icon cil-truck"></i>
            <span class="nav-text">Supplier</span>
        </a>
    </li>

    <!-- TRANSAKSI Section -->
    <li class="nav-title">Transaksi</li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}" href="{{ route('pengeluaran.index') }}">
            <i class="nav-icon cil-money"></i>
            <span class="nav-text">Pengeluaran</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
            <i class="nav-icon cil-arrow-circle-bottom"></i>
            <span class="nav-text">Pembelian</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
            <i class="nav-icon cil-arrow-circle-top"></i>
            <span class="nav-text">Penjualan</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('transaksi.index') && !session('transaksi_baru_clicked') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">
            <i class="nav-icon cil-basket"></i>
            <span class="nav-text">Transaksi Aktif</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('transaksi.baru') || (request()->routeIs('transaksi.index') && session('transaksi_baru_clicked')) ? 'active' : '' }}" href="{{ route('transaksi.baru') }}">
            <i class="nav-icon cil-plus"></i>
            <span class="nav-text">Transaksi Baru</span>
        </a>
    </li>

    <!-- REPORT Section -->
    <li class="nav-title">Report</li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
            <i class="nav-icon cil-description"></i>
            <span class="nav-text">Laporan</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('catatan.*') ? 'active' : '' }}" href="{{ route('catatan.index') }}">
            <i class="nav-icon cil-notes"></i>
            <span class="nav-text">Catatan</span>
        </a>
    </li>

    <!-- SYSTEM Section -->
    <li class="nav-title">System</li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}">
            <i class="nav-icon cil-people"></i>
            <span class="nav-text">User</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('setting.*') ? 'active' : '' }}" href="{{ route('setting.index') }}">
            <i class="nav-icon cil-settings"></i>
            <span class="nav-text">Pengaturan</span>
        </a>
    </li>
</ul>

<!-- Sidebar Footer with Toggle -->
<div class="sidebar-footer">
    <button class="sidebar-toggler-btn" type="button" id="sidebarToggler">
        <i class="nav-icon cil-arrow-circle-left" id="toggleIcon"></i>
    </button>
</div>

@else
<!-- Cashier Menu -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('transaksi.index') && !session('transaksi_baru_clicked') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">
        <i class="nav-icon cil-basket"></i>
        <span class="nav-text">Transaksi Aktif</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('transaksi.baru') || (request()->routeIs('transaksi.index') && session('transaksi_baru_clicked')) ? 'active' : '' }}" href="{{ route('transaksi.baru') }}">
        <i class="nav-icon cil-plus"></i>
        <span class="nav-text">Transaksi Baru</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('catatan.*') ? 'active' : '' }}" href="{{ route('catatan.index') }}">
        <i class="nav-icon cil-notes"></i>
        <span class="nav-text">Catatan</span>
    </a>
</li>
</ul>

<!-- Sidebar Footer with Toggle -->
<div class="sidebar-footer">
    <button class="sidebar-toggler-btn" type="button" id="sidebarToggler">
        <i class="nav-icon cil-arrow-circle-left" id="toggleIcon"></i>
    </button>
</div>
@endif