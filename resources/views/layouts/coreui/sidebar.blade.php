<!-- Sidebar Brand -->
<div class="sidebar-brand">
    <div class="sidebar-brand-full">
        <img src="{{ url($setting->path_logo) }}" height="32" alt="{{ $setting->nama_perusahaan }}">
        <!--<span class="sidebar-brand-text">{{ $setting->nama_perusahaan }}</span> -->
    </div>
    <div class="sidebar-brand-minimized">
        <img src="{{ url($setting->path_logo) }}" height="24" alt="{{ $setting->nama_perusahaan }}">
    </div>
</div>

<!-- Navigation -->
<ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}" data-nav-text="Dashboard">
            <i class="nav-icon cil-speedometer"></i>
            <span class="nav-text">Dashboard</span>
        </a>
    </li>

    @if (auth()->user()->level == 1)
    <!-- MASTER Section -->
    <li class="nav-title">Master</li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('kategori.index') }}" data-nav-text="Kategori">
            <i class="nav-icon cil-grid"></i>
            <span class="nav-text">Kategori</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('produk.index') }}" data-nav-text="Produk">
            <i class="nav-icon cil-layers"></i>
            <span class="nav-text">Produk</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('promo.index') }}" data-nav-text="Promo">
            <i class="nav-icon cil-tags"></i>
            <span class="nav-text">Promo</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('kas.index') }}" data-nav-text="Kas">
            <i class="nav-icon cil-wallet"></i>
            <span class="nav-text">Kas</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('member.index') }}" data-nav-text="Member">
            <i class="nav-icon cil-contact"></i>
            <span class="nav-text">Member</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('supplier.index') }}" data-nav-text="Supplier">
            <i class="nav-icon cil-truck"></i>
            <span class="nav-text">Supplier</span>
        </a>
    </li>

    <!-- TRANSAKSI Section -->
    <li class="nav-title">Transaksi</li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('pengeluaran.index') }}" data-nav-text="Pengeluaran">
            <i class="nav-icon cil-money"></i>
            <span class="nav-text">Pengeluaran</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('pembelian.index') }}" data-nav-text="Pembelian">
            <i class="nav-icon cil-arrow-circle-bottom"></i>
            <span class="nav-text">Pembelian</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('penjualan.index') }}" data-nav-text="Penjualan">
            <i class="nav-icon cil-arrow-circle-top"></i>
            <span class="nav-text">Penjualan</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('transaksi.index') }}" data-nav-text="Transaksi Aktif">
            <i class="nav-icon cil-basket"></i>
            <span class="nav-text">Transaksi Aktif</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('transaksi.baru') }}" data-nav-text="Transaksi Baru">
            <i class="nav-icon cil-plus"></i>
            <span class="nav-text">Transaksi Baru</span>
        </a>
    </li>

    <!-- REPORT Section -->
    <li class="nav-title">Report</li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('laporan.index') }}" data-nav-text="Laporan">
            <i class="nav-icon cil-description"></i>
            <span class="nav-text">Laporan</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('catatan.index') }}" data-nav-text="Catatan">
            <i class="nav-icon cil-notes"></i>
            <span class="nav-text">Catatan</span>
        </a>
    </li>
    <!-- SYSTEM Section -->
    <li class="nav-title">System</li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('user.index') }}" data-nav-text="User">
            <i class="nav-icon cil-people"></i>
            <span class="nav-text">User</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('setting.index') }}" data-nav-text="Pengaturan">
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
    <a class="nav-link" href="{{ route('transaksi.index') }}" data-nav-text="Transaksi Aktif">
        <i class="nav-icon cil-basket"></i>
        <span class="nav-text">Transaksi Aktif</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('transaksi.baru') }}" data-nav-text="Transaksi Baru">
        <i class="nav-icon cil-plus"></i>
        <span class="nav-text">Transaksi Baru</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('catatan.index') }}" data-nav-text="Catatan">
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