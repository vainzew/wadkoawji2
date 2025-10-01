<?php

function format_uang ($angka) {
    return number_format($angka, 0, ',', '.');
}

function terbilang ($angka) {
    $angka = abs($angka);
    $baca  = array('', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');
    $terbilang = '';

    if ($angka < 12) { // 0 - 11
        $terbilang = ' ' . $baca[$angka];
    } elseif ($angka < 20) { // 12 - 19
        $terbilang = terbilang($angka -10) . ' belas';
    } elseif ($angka < 100) { // 20 - 99
        $terbilang = terbilang($angka / 10) . ' puluh' . terbilang($angka % 10);
    } elseif ($angka < 200) { // 100 - 199
        $terbilang = ' seratus' . terbilang($angka -100);
    } elseif ($angka < 1000) { // 200 - 999
        $terbilang = terbilang($angka / 100) . ' ratus' . terbilang($angka % 100);
    } elseif ($angka < 2000) { // 1.000 - 1.999
        $terbilang = ' seribu' . terbilang($angka -1000);
    } elseif ($angka < 1000000) { // 2.000 - 999.999
        $terbilang = terbilang($angka / 1000) . ' ribu' . terbilang($angka % 1000);
    } elseif ($angka < 1000000000) { // 1000000 - 999.999.990
        $terbilang = terbilang($angka / 1000000) . ' juta' . terbilang($angka % 1000000);
    }

    return $terbilang;
}

function tanggal_indonesia($tgl, $tampil_hari = true)
{
    $nama_hari  = array(
        'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'
    );
    $nama_bulan = array(1 =>
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );

    $tahun   = substr($tgl, 0, 4);
    $bulan   = $nama_bulan[(int) substr($tgl, 5, 2)];
    $tanggal = substr($tgl, 8, 2);
    $text    = '';

    if ($tampil_hari) {
        $urutan_hari = date('w', mktime(0,0,0, substr($tgl, 5, 2), $tanggal, $tahun));
        $hari        = $nama_hari[$urutan_hari];
        $text       .= "$hari, $tanggal $bulan $tahun";
    } else {
        $text       .= "$tanggal $bulan $tahun";
    }
    
    return $text; 
}

function tambah_nol_didepan($value, $threshold = null)
{
    return sprintf("%0". $threshold . "s", $value);
}

function generateHardwareId()
{
    // PERFORMANCE OPTIMIZATION: Cache hardware ID in session (doesn't change during session)
    // This prevents expensive exec() calls on every request
    if (session()->has('cached_hardware_id')) {
        return session('cached_hardware_id');
    }

    // Generate a unique hardware ID based on system information
    // This ID should be different on each PC/server to prevent unauthorized copying
    $serverInfo = [
        php_uname('n'), // hostname (unique per machine)
        php_uname('s'), // OS name
        php_uname('m'), // machine type (architecture)
        php_uname('r'), // OS release version
        $_SERVER['DOCUMENT_ROOT'] ?? 'unknown', // installation path
        $_SERVER['SERVER_ADDR'] ?? 'no-ip', // Server IP address
    ];

    // Only try to get MAC address once per session (expensive operation)
    // This is cached to avoid repeated exec() calls
    $macAddress = @exec('getmac 2>&1') ?: @exec('ifconfig 2>&1') ?: 'no-mac';
    $serverInfo[] = $macAddress;

    // Create deterministic hardware ID that changes per machine
    $hardwareId = md5(implode('|', array_filter($serverInfo)));
    
    // Cache in session for performance (hardware doesn't change during session)
    session(['cached_hardware_id' => $hardwareId]);
    
    return $hardwareId;
}

function generateInstallationId()
{
    $installFile = storage_path('app/installation.lock');

    // Always return the same ID if file exists
    if (file_exists($installFile)) {
        return trim(file_get_contents($installFile));
    }

    // Generate unique installation ID (one-time only)
    $installId = md5(
        generateHardwareId() .
        ($_SERVER['DOCUMENT_ROOT'] ?? '') .
        php_uname('n') . // hostname
        'static-salt-' . php_uname('s') // Static components only
    );

    // Save permanently
    if (!is_dir(dirname($installFile))) {
        mkdir(dirname($installFile), 0755, true);
    }

    file_put_contents($installFile, $installId);
    return $installId;
}

function checkActivationStatus()
{
    // Use cache to avoid repeated file I/O and server calls
    $cacheKey = 'activation_status_' . generateHardwareId();
    $cachedStatus = cache($cacheKey);
    
    // Return cached status if available and not expired (5 minutes)
    if ($cachedStatus !== null) {
        return $cachedStatus;
    }
    
    $activationFile = storage_path('app/activation.json');

    if (!file_exists($activationFile)) {
        $result = ['status' => 'not_activated', 'message' => 'Application not activated'];
        cache([$cacheKey => $result], now()->addMinutes(5));
        return $result;
    }

    $data = json_decode(file_get_contents($activationFile), true);

    if (!$data || !isset($data['activation_code']) || !isset($data['hardware_id'])) {
        $result = ['status' => 'invalid', 'message' => 'Invalid activation data'];
        cache([$cacheKey => $result], now()->addMinutes(5));
        return $result;
    }

    $currentHardwareId = generateHardwareId();
    if ($data['hardware_id'] !== $currentHardwareId) {
        $result = ['status' => 'hardware_mismatch', 'message' => 'Hardware mismatch detected'];
        cache([$cacheKey => $result], now()->addMinutes(5));
        return $result;
    }

    // Check installation ID if exists
    if (isset($data['installation_id'])) {
        $currentInstallationId = generateInstallationId();
        if ($data['installation_id'] !== $currentInstallationId) {
            $result = ['status' => 'installation_mismatch', 'message' => 'Installation ID mismatch - possible cloned installation'];
            cache([$cacheKey => $result], now()->addMinutes(5));
            return $result;
        }
    }

    // Verify with server (only once per 30 minutes to reduce server calls)
    $serverCacheKey = 'server_verification_' . $data['activation_code'];
    $serverResult = cache($serverCacheKey);
    
    if ($serverResult === null) {
        $serverResult = verifyActivationWithServer($data['activation_code'], $currentHardwareId);
        // Cache server verification for 30 minutes
        cache([$serverCacheKey => $serverResult], now()->addMinutes(30));
    }

    if ($serverResult['status'] === 'success') {
        $result = ['status' => 'active', 'message' => 'Application activated', 'data' => $data];
        cache([$cacheKey => $result], now()->addMinutes(5));
        return $result;
    }

    cache([$cacheKey => $serverResult], now()->addMinutes(5));
    return $serverResult;
}

function verifyActivationWithServer($activationCode, $hardwareId)
{
    // Use a shorter timeout for faster failure
    $serverUrl = env('ACTIVATION_SERVER_URL', 'http://localhost:8001');

    $data = [
        'activation_code' => $activationCode,
        'hardware_id' => $hardwareId
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $serverUrl . '/api/activation/verify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Connection: close'  // Add connection close header
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  // Reduced from 10 to 5 seconds
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);  // Reduced from 5 to 3 seconds
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);  // Force fresh connection
    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);  // Prevent connection reuse

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // If there's a connection error, return a cached success if available
    if ($error) {
        $fallbackCacheKey = 'activation_fallback_' . $activationCode;
        $fallbackData = cache($fallbackCacheKey);
        
        if ($fallbackData && $fallbackData['status'] === 'success') {
            return $fallbackData;
        }
        
        return ['status' => 'error', 'message' => 'Cannot connect to activation server'];
    }

    $result = json_decode($response, true);

    if ($httpCode === 200 && $result && $result['status'] === 'success') {
        $successResult = ['status' => 'success', 'message' => 'Activation verified', 'data' => $result['data']];
        // Cache successful verification for fallback
        cache(['activation_fallback_' . $activationCode => $successResult], now()->addHours(24));
        return $successResult;
    }

    return ['status' => 'error', 'message' => $result['message'] ?? 'Verification failed'];
}

function activateApplication($activationCode)
{
    $hardwareId = generateHardwareId();
    $installationId = generateInstallationId(); // Unique per installation
    $serverUrl = env('ACTIVATION_SERVER_URL', 'http://localhost:8001');

    // Try with installation_id first, fallback without it
    $data = [
        'activation_code' => $activationCode,
        'hardware_id' => $hardwareId,
        'client_domain' => $_SERVER['HTTP_HOST'] ?? 'unknown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $serverUrl . '/api/activation/activate');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_error($ch)) {
        curl_close($ch);
        return ['status' => 'error', 'message' => 'Cannot connect to activation server'];
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 200 && $result && $result['status'] === 'success') {
        // Save activation data with installation ID
        $activationData = [
            'activation_code' => $activationCode,
            'hardware_id' => $hardwareId,
            'installation_id' => $installationId,
            'activated_at' => date('Y-m-d H:i:s'),
            'server_response' => $result['data']
        ];

        $activationFile = storage_path('app/activation.json');
        if (!is_dir(dirname($activationFile))) {
            mkdir(dirname($activationFile), 0755, true);
        }

        file_put_contents($activationFile, json_encode($activationData, JSON_PRETTY_PRINT));

        // CRITICAL: Clear all activation-related caches after successful activation
        // This prevents the bug where user gets redirected back to activation after login
        \Illuminate\Support\Facades\Cache::forget('activation_status_' . $hardwareId);
        \Illuminate\Support\Facades\Cache::forget('root_activation_check');
        \Illuminate\Support\Facades\Cache::forget('middleware_activation_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        \Illuminate\Support\Facades\Cache::forget('server_verification_' . $activationCode);

        return ['status' => 'success', 'message' => 'Application activated successfully'];
    }

    return ['status' => 'error', 'message' => $result['message'] ?? 'Activation failed'];
}