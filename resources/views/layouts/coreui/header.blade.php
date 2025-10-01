<!-- Hamburger Menu Button -->
<button class="header-toggler" type="button">
    <i class="cil-menu"></i>
</button>

<!-- Header Navigation -->
<ul class="header-nav" style="height: 56px !important; display: flex !important; align-items: center !important; margin: 0 0 0 auto !important; padding: 0 !important;">
    <!-- User Menu -->
    <li class="header-nav-item dropdown" style="height: 56px !important; display: flex !important; align-items: center !important; margin: 0 !important; padding: 0 !important;">
        <a class="header-nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="height: 56px !important; padding: 0 0.75rem !important; display: flex !important; align-items: center !important; justify-content: flex-start !important; text-decoration: none !important; margin: 0 !important; line-height: 1 !important; vertical-align: middle !important;">
            @php
                $user = auth()->user();
                $avatar = $user && !empty($user->foto) ? url($user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=5856d6&color=fff';
            @endphp
            <img class="rounded-circle img-profil" src="{{ $avatar }}" alt="{{ auth()->user()->name }}" width="32" height="32" style="vertical-align: middle !important; margin: 0 8px 0 0 !important; display: inline-block !important;">
            <span class="d-none d-md-inline" style="vertical-align: middle !important; line-height: 1 !important; margin: 0 !important; display: inline-block !important;">{{ auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><h6 class="dropdown-header">{{ auth()->user()->name }}</h6></li>
            <li><small class="dropdown-text text-muted px-3">{{ auth()->user()->email }}</small></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('user.profil') }}">
                    <i class="cil-user me-2"></i> Profile
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('setting.index') }}">
                    <i class="cil-settings me-2"></i> Settings
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="cil-account-logout me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </li>
</ul>
