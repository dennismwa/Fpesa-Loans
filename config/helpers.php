<?php
/**
 * Fpesa Loan Platform - Helper Functions
 */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    session_start();
}
require_once __DIR__ . '/database.php';

/* ═══════════════════════════════════════
   CSRF PROTECTION
   ═══════════════════════════════════════ */
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}
function verify_csrf(?string $token = null): bool {
    $token = $token ?? ($_POST['_csrf'] ?? '');
    return !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

/* ═══════════════════════════════════════
   SETTINGS
   ═══════════════════════════════════════ */
function get_all_settings(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    try {
        $db = Database::connect();
        $rows = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
        $cache = [];
        foreach ($rows as $r) $cache[$r['setting_key']] = $r['setting_value'];
    } catch (Exception $e) {
        $cache = [];
    }
    return $cache;
}
function get_setting(string $key, string $default = ''): string {
    $s = get_all_settings();
    return $s[$key] ?? $default;
}
function site_name(): string { return get_setting('site_name', 'Fpesa'); }
function primary_color(): string { return get_setting('primary_color', '#0D6B3F'); }
function secondary_color(): string { return get_setting('secondary_color', '#F59E0B'); }
function app_fee(): float { return (float)get_setting('application_fee', '200'); }

/* ═══════════════════════════════════════
   AUTH
   ═══════════════════════════════════════ */
function is_logged_in(): bool { return !empty($_SESSION['user_id']); }
function is_admin(): bool { return !empty($_SESSION['admin_id']); }
function require_login(): void {
    if (!is_logged_in()) { header('Location: /auth/login.php'); exit; }
}
function require_admin(): void {
    if (!is_admin()) { header('Location: /admin/login.php'); exit; }
}
function current_user(): ?array {
    if (!is_logged_in()) return null;
    static $u = null;
    if ($u) return $u;
    $db = Database::connect();
    $st = $db->prepare("SELECT * FROM users WHERE id = ?");
    $st->execute([$_SESSION['user_id']]);
    $u = $st->fetch() ?: null;
    return $u;
}
function current_admin(): ?array {
    if (!is_admin()) return null;
    static $a = null;
    if ($a) return $a;
    $db = Database::connect();
    $st = $db->prepare("SELECT * FROM admins WHERE id = ?");
    $st->execute([$_SESSION['admin_id']]);
    $a = $st->fetch() ?: null;
    return $a;
}

/* ═══════════════════════════════════════
   SANITIZE
   ═══════════════════════════════════════ */
function e(string $s): string {
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}
function clean(string $s): string { return e($s); }

/* ═══════════════════════════════════════
   FLASH MESSAGES
   ═══════════════════════════════════════ */
function set_flash(string $type, string $msg): void {
    $_SESSION['_flash'] = ['type' => $type, 'msg' => $msg];
}
function get_flash(): ?array {
    $f = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return $f;
}
function render_flash(): string {
    $f = get_flash();
    if (!$f) return '';
    $colors = [
        'success' => 'bg-emerald-50 border-emerald-500 text-emerald-800',
        'error'   => 'bg-red-50 border-red-500 text-red-800',
        'warning' => 'bg-amber-50 border-amber-500 text-amber-800',
        'info'    => 'bg-sky-50 border-sky-500 text-sky-800',
    ];
    $icons = [
        'success' => 'check-circle',
        'error'   => 'alert-circle',
        'warning' => 'alert-triangle',
        'info'    => 'info',
    ];
    $c = $colors[$f['type']] ?? $colors['info'];
    $ic = $icons[$f['type']] ?? 'info';
    return '<div class="flash-msg border-l-4 rounded-lg p-4 mb-6 flex items-center gap-3 ' . $c . '" role="alert">
        <i data-lucide="' . $ic . '" class="w-5 h-5 flex-shrink-0"></i>
        <p class="text-sm font-medium">' . e($f['msg']) . '</p>
        <button onclick="this.parentElement.remove()" class="ml-auto text-current opacity-60 hover:opacity-100"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>';
}

/* ═══════════════════════════════════════
   FORMATTING
   ═══════════════════════════════════════ */
function fmt_money(float $n): string {
    return 'KSH ' . number_format($n, 2);
}
function fmt_date(string $d): string {
    return $d ? date('M d, Y', strtotime($d)) : '—';
}
function fmt_datetime(string $d): string {
    return $d ? date('M d, Y h:i A', strtotime($d)) : '—';
}
function time_ago(string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return fmt_date($dt);
}
// Aliases used across files
function format_money(float $n): string { return fmt_money($n); }
function format_date(string $d): string { return fmt_date($d); }
function format_datetime(string $d): string { return fmt_datetime($d); }

/* ═══════════════════════════════════════
   STATUS BADGE
   ═══════════════════════════════════════ */
function status_badge(string $status): string {
    $map = [
        'pending'      => 'bg-amber-100 text-amber-700',
        'fee_paid'     => 'bg-blue-100 text-blue-700',
        'under_review' => 'bg-indigo-100 text-indigo-700',
        'approved'     => 'bg-emerald-100 text-emerald-700',
        'rejected'     => 'bg-red-100 text-red-700',
        'active'       => 'bg-sky-100 text-sky-700',
        'completed'    => 'bg-emerald-100 text-emerald-700',
        'paid'         => 'bg-emerald-100 text-emerald-700',
        'overdue'      => 'bg-red-100 text-red-700',
        'partial'      => 'bg-amber-100 text-amber-700',
        'disbursed'    => 'bg-blue-100 text-blue-700',
        'closed'       => 'bg-gray-100 text-gray-600',
        'defaulted'    => 'bg-red-100 text-red-700',
        'cancelled'    => 'bg-gray-100 text-gray-600',
        'confirmed'    => 'bg-emerald-100 text-emerald-700',
    ];
    $c = $map[strtolower($status)] ?? 'bg-gray-100 text-gray-600';
    $label = ucfirst(str_replace('_', ' ', $status));
    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide ' . $c . '">' . $label . '</span>';
}

/* ═══════════════════════════════════════
   EMI CALCULATION
   ═══════════════════════════════════════ */
function calculate_emi(float $principal, float $rate, int $months): array {
    $mr = ($rate / 100) / 12;
    if ($mr > 0) {
        $emi = $principal * $mr * pow(1 + $mr, $months) / (pow(1 + $mr, $months) - 1);
    } else {
        $emi = $principal / $months;
    }
    $total = $emi * $months;
    return [
        'emi'           => round($emi, 2),
        'total_payment' => round($total, 2),
        'total_interest' => round($total - $principal, 2),
        'monthly_rate'  => $mr,
    ];
}

function generate_schedule(float $principal, float $rate, int $months, string $start): array {
    $c = calculate_emi($principal, $rate, $months);
    $emi = $c['emi'];
    $mr = $c['monthly_rate'];
    $bal = $principal;
    $schedule = [];
    for ($i = 1; $i <= $months; $i++) {
        $interest = round($bal * $mr, 2);
        $princ = round($emi - $interest, 2);
        if ($i == $months) { $princ = $bal; $emi = $princ + $interest; }
        $bal = round($bal - $princ, 2);
        $due = date('Y-m-d', strtotime("$start +{$i} month"));
        $schedule[] = [
            'no' => $i, 'due_date' => $due,
            'principal' => $princ, 'interest' => $interest,
            'emi' => round($emi, 2), 'balance' => max(0, $bal),
        ];
    }
    return $schedule;
}

/* ═══════════════════════════════════════
   PAGINATION
   ═══════════════════════════════════════ */
function paginate(int $total, int $per = 15, int $current = 1): array {
    $pages = max(1, ceil($total / $per));
    $current = max(1, min($current, $pages));
    $offset = ($current - 1) * $per;
    return compact('total', 'per', 'current', 'pages', 'offset');
}
function render_pagination(array $p, string $url): string {
    if ($p['pages'] <= 1) return '';
    $sep = strpos($url, '?') !== false ? '&' : '?';
    $h = '<nav class="flex items-center justify-center gap-1.5 mt-8">';
    if ($p['current'] > 1)
        $h .= '<a href="' . $url . $sep . 'page=' . ($p['current']-1) . '" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-200 hover:border-primary hover:text-primary transition">&laquo;</a>';
    $start = max(1, $p['current']-2);
    $end = min($p['pages'], $p['current']+2);
    for ($i = $start; $i <= $end; $i++) {
        $act = $i === $p['current'] ? 'bg-primary text-white border-primary' : 'bg-white border-gray-200 hover:border-primary hover:text-primary';
        $h .= '<a href="' . $url . $sep . 'page=' . $i . '" class="px-3 py-2 text-sm rounded-lg border ' . $act . ' transition">' . $i . '</a>';
    }
    if ($p['current'] < $p['pages'])
        $h .= '<a href="' . $url . $sep . 'page=' . ($p['current']+1) . '" class="px-3 py-2 text-sm rounded-lg bg-white border border-gray-200 hover:border-primary hover:text-primary transition">&raquo;</a>';
    $h .= '</nav>';
    return $h;
}

/* ═══════════════════════════════════════
   WALLET
   ═══════════════════════════════════════ */
function get_wallet_balance(int $uid): float {
    $db = Database::connect();
    $st = $db->prepare("SELECT balance FROM wallets WHERE user_id = ?");
    $st->execute([$uid]);
    $r = $st->fetch();
    return $r ? (float)$r['balance'] : 0.0;
}
function wallet_txn(int $uid, string $type, float $amount, string $desc): void {
    $db = Database::connect();
    $db->prepare("INSERT IGNORE INTO wallets (user_id, balance, created_at) VALUES (?, 0, NOW())")->execute([$uid]);
    if ($type === 'credit') {
        $db->prepare("UPDATE wallets SET balance = balance + ?, updated_at = NOW() WHERE user_id = ?")->execute([$amount, $uid]);
    } else {
        $db->prepare("UPDATE wallets SET balance = GREATEST(0, balance - ?), updated_at = NOW() WHERE user_id = ?")->execute([$amount, $uid]);
    }
    $db->prepare("INSERT INTO wallet_transactions (user_id, type, amount, description, created_at) VALUES (?,?,?,?,NOW())")->execute([$uid, $type, $amount, $desc]);
}

/* ═══════════════════════════════════════
   FILE UPLOAD
   ═══════════════════════════════════════ */
function upload_file(array $file, string $dir, array $ext = ['jpg','jpeg','png','pdf','doc','docx']): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $fext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fext, $ext)) return null;
    if ($file['size'] > 10 * 1024 * 1024) return null;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = uniqid('fp_') . '_' . time() . '.' . $fext;
    $path = rtrim($dir, '/') . '/' . $name;
    return move_uploaded_file($file['tmp_name'], $path) ? $name : null;
}

/* ═══════════════════════════════════════
   NOTIFICATIONS & LOGS
   ═══════════════════════════════════════ */
function notify(int $uid, string $title, string $msg, string $type = 'info'): void {
    $db = Database::connect();
    $db->prepare("INSERT INTO notifications (user_id, title, message, type, created_at) VALUES (?,?,?,?,NOW())")->execute([$uid, $title, $msg, $type]);
}
function add_log(string $action, string $details = '', ?int $uid = null): void {
    $db = Database::connect();
    $uid = $uid ?? ($_SESSION['admin_id'] ?? $_SESSION['user_id'] ?? 0);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $db->prepare("INSERT INTO logs (user_id, action, details, ip_address, created_at) VALUES (?,?,?,?,NOW())")->execute([$uid, $action, $details, $ip]);
}

/* ═══════════════════════════════════════
   GENERATE UNIQUE REFERENCE
   ═══════════════════════════════════════ */
function gen_ref(string $prefix = 'FP'): string {
    return $prefix . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}
