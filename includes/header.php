<?php
if (!isset($page_title)) $page_title = site_name();
if (!isset($page_description)) $page_description = get_setting('meta_description', '');
$_pc = primary_color();
$_sc = secondary_color();
$_sn = site_name();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= e($page_title) ?> | <?= e($_sn) ?></title>
    <meta name="description" content="<?= e($page_description) ?>">
    <meta name="keywords" content="<?= e(get_setting('meta_keywords', '')) ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="<?= e($_sn) ?>">

    <!-- OG -->
    <meta property="og:title" content="<?= e($page_title) ?> | <?= e($_sn) ?>">
    <meta property="og:description" content="<?= e($page_description) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e($_sn) ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($page_title) ?>">

    <!-- PWA -->
    <link rel="manifest" href="/pwa/manifest.json">
    <meta name="theme-color" content="<?= $_pc ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config={theme:{extend:{colors:{primary:'<?= $_pc ?>',secondary:'<?= $_sc ?>',dark:'#0B1120'},fontFamily:{heading:['Outfit','sans-serif'],body:['DM Sans','sans-serif']}}}}
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..700;1,9..40,300..700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- AOS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

    <style>
    :root{--pc:<?=$_pc?>;--sc:<?=$_sc?>}
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'DM Sans',sans-serif;-webkit-font-smoothing:antialiased;overflow-x:hidden}
    h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Outfit',sans-serif}

    /* Gradient text */
    .grad-text{background:linear-gradient(135deg,var(--pc),#10B981);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

    /* Glass */
    .glass{background:rgba(255,255,255,.75);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.3)}
    .glass-dark{background:rgba(11,17,32,.85);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.06)}

    /* Scrollbar */
    ::-webkit-scrollbar{width:5px}
    ::-webkit-scrollbar-thumb{background:var(--pc);border-radius:9px}

    /* Animations */
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
    .float-anim{animation:float 3s ease-in-out infinite}

    /* Card hover */
    .card-lift{transition:all .35s cubic-bezier(.165,.84,.44,1)}
    .card-lift:hover{transform:translateY(-5px);box-shadow:0 20px 50px -12px rgba(13,107,63,.15)}

    /* Buttons */
    .btn-primary{background:linear-gradient(135deg,var(--pc),#10B981);color:#fff;transition:all .3s}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 25px -5px rgba(13,107,63,.35)}
    .btn-outline{border:2px solid var(--pc);color:var(--pc);transition:all .3s}
    .btn-outline:hover{background:var(--pc);color:#fff}

    /* Blob */
    .blob{position:absolute;border-radius:50%;filter:blur(80px);opacity:.12;z-index:0;pointer-events:none}

    /* Nav */
    .nav-solid{background:rgba(255,255,255,.96);backdrop-filter:blur(16px);box-shadow:0 1px 15px rgba(0,0,0,.05)}

    /* Mobile menu */
    .mob-menu{transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1)}
    .mob-menu.open{transform:translateX(0)}

    /* Icon box hover */
    .icon-box{background:linear-gradient(135deg,rgba(13,107,63,.07),rgba(16,185,129,.07));transition:all .3s}
    .icon-box:hover{background:linear-gradient(135deg,var(--pc),#10B981);color:#fff}

    /* Data table */
    .dtable th{background:#F8FAFC;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#64748B;white-space:nowrap}
    .dtable td{white-space:nowrap}
    .dtable tbody tr:hover td{background:#F8FAFC}

    /* Form inputs */
    .finput{transition:all .2s}
    .finput:focus{border-color:var(--pc);box-shadow:0 0 0 3px rgba(13,107,63,.1);outline:none}

    /* Sidebar link */
    .slink{transition:all .2s;border-left:3px solid transparent}
    .slink:hover,.slink.active{background:rgba(13,107,63,.06);border-left-color:var(--pc);color:var(--pc)}
    .slink-dark{transition:all .15s;border-left:3px solid transparent}
    .slink-dark:hover,.slink-dark.active{background:rgba(255,255,255,.05);border-left-color:var(--pc);color:#fff}

    /* Range slider */
    input[type=range]{-webkit-appearance:none;height:6px;border-radius:3px;background:#E2E8F0;outline:none}
    input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:var(--pc);cursor:pointer;box-shadow:0 2px 8px rgba(13,107,63,.3)}

    /* Loader */
    .spinner{border:3px solid #e5e7eb;border-top:3px solid var(--pc);border-radius:50%;width:20px;height:20px;animation:spin .7s linear infinite;display:inline-block}
    @keyframes spin{to{transform:rotate(360deg)}}

    /* Print */
    @media print{.no-print{display:none!important}body{font-size:11px}}
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">
