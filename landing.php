<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/includes/utils.php';

$isLoggedIn = isset($_SESSION['user_id']);

$categoryModel      = new Category();
$productModel       = new Product();
$categories         = $categoryModel->getActiveCategories();
$popular_products   = $productModel->getPopularProducts(8);
$discounted_products= $productModel->getDiscountedProducts(4);

// Stats for social proof
$db            = getDB();
$total_products= intval($db->query("SELECT COUNT(*) FROM products WHERE status=1")->fetchColumn());
$total_users   = intval($db->query("SELECT COUNT(*) FROM users")->fetchColumn());
$total_orders  = intval($db->query("SELECT COUNT(*) FROM orders")->fetchColumn());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceryMart — Fresh Groceries Delivered in 30 Minutes</title>
    <meta name="description" content="Order fresh fruits, vegetables, dairy, bakery items and daily essentials online. Fast delivery, best prices, 100% organic.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --green-dark:   #1B5E20;
            --green-main:   #2E7D32;
            --green-mid:    #388E3C;
            --green-light:  #4CAF50;
            --green-soft:   #E8F5E9;
            --yellow:       #FFC107;
            --orange:       #FF6F00;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; background: #fff; }
        a { text-decoration: none; }

        /* ─── NAVBAR ─────────────────────────────────────────── */
        .lp-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 16px 0;
            transition: all 0.3s;
        }
        .lp-nav.scrolled {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            padding: 10px 0;
        }
        .lp-nav .logo {
            font-size: 22px; font-weight: 800;
            color: #fff; display: flex; align-items: center; gap: 10px;
            transition: color 0.3s;
        }
        .lp-nav.scrolled .logo { color: var(--green-dark); }
        .lp-nav .logo .logo-badge {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--green-light), #66BB6A);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff;
            box-shadow: 0 4px 12px rgba(76,175,80,0.4);
        }
        .nav-links a {
            color: rgba(255,255,255,0.85); font-size: 14px; font-weight: 500;
            margin: 0 14px; transition: color 0.2s;
        }
        .lp-nav.scrolled .nav-links a { color: #444; }
        .nav-links a:hover { color: var(--green-light) !important; }
        .btn-nav-login {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.4);
            color: #fff !important; border-radius: 50px;
            padding: 8px 22px; font-size: 13px; font-weight: 600;
            transition: all 0.2s;
        }
        .lp-nav.scrolled .btn-nav-login {
            background: var(--green-soft); border-color: var(--green-light);
            color: var(--green-dark) !important;
        }
        .btn-nav-login:hover { background: var(--green-light) !important; color: #fff !important; border-color: var(--green-light) !important; }
        .btn-nav-signup {
            background: linear-gradient(135deg, var(--green-main), var(--green-light));
            border: none; color: #fff !important; border-radius: 50px;
            padding: 8px 22px; font-size: 13px; font-weight: 600;
            box-shadow: 0 4px 12px rgba(76,175,80,0.35);
            transition: all 0.2s;
        }
        .btn-nav-signup:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(76,175,80,0.45); }

        /* ─── HERO ───────────────────────────────────────────── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(150deg, #0d2e0d 0%, #1a4a20 35%, #2d6a35 65%, #1e5a28 100%);
            position: relative; overflow: hidden;
            display: flex; align-items: center;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: url('https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=40') center/cover;
            opacity: 0.08;
        }
        /* Glowing orbs */
        .hero-orb {
            position: absolute; border-radius: 50%;
            background: radial-gradient(circle, rgba(76,175,80,0.25) 0%, transparent 70%);
            animation: orbPulse 5s ease-in-out infinite;
        }
        .hero-orb:nth-child(1) { width: 500px; height: 500px; top: -100px; right: -100px; animation-delay: 0s; }
        .hero-orb:nth-child(2) { width: 300px; height: 300px; bottom: -50px; left: 10%; animation-delay: 2s; }
        @keyframes orbPulse { 0%,100%{transform:scale(1)opacity:.8} 50%{transform:scale(1.1);opacity:1} }

        .hero-content { position: relative; z-index: 2; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(76,175,80,0.2); border: 1px solid rgba(76,175,80,0.4);
            border-radius: 50px; padding: 8px 18px; color: #A5D6A7; font-size: 13px; font-weight: 600;
            margin-bottom: 28px;
            animation: fadeSlideIn 0.6s ease forwards;
        }
        .hero-badge .dot { width: 8px; height: 8px; background: #69F0AE; border-radius: 50%; animation: blink 1.5s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        .hero-title {
            font-size: clamp(42px, 6vw, 72px); font-weight: 900;
            color: #fff; line-height: 1.1; letter-spacing: -2px;
            margin-bottom: 24px;
        }
        .hero-title .highlight {
            background: linear-gradient(135deg, #69F0AE, #00E676);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .hero-sub {
            font-size: 18px; color: rgba(255,255,255,0.65);
            line-height: 1.7; max-width: 500px; margin-bottom: 40px;
        }
        .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; }
        .btn-hero-primary {
            background: linear-gradient(135deg, var(--green-light), #00C853);
            border: none; color: #fff; font-weight: 700; font-size: 16px;
            padding: 16px 36px; border-radius: 50px;
            box-shadow: 0 8px 28px rgba(76,175,80,0.4);
            transition: all 0.25s; display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(76,175,80,0.5); color: #fff; }
        .btn-hero-secondary {
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3);
            color: #fff; font-weight: 600; font-size: 16px;
            padding: 16px 36px; border-radius: 50px;
            transition: all 0.25s; display: inline-flex; align-items: center; gap: 10px;
            backdrop-filter: blur(8px);
        }
        .btn-hero-secondary:hover { background: rgba(255,255,255,0.2); color: #fff; }

        /* Hero stats */
        .hero-stats { display: flex; gap: 32px; margin-top: 48px; flex-wrap: wrap; }
        .stat-item .num { font-size: 28px; font-weight: 800; color: #fff; line-height: 1; }
        .stat-item .lbl { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-divider { width: 1px; background: rgba(255,255,255,0.15); }

        /* Hero image side */
        .hero-img-wrap {
            position: relative; z-index: 2;
        }
        .hero-img-main {
            border-radius: 28px; box-shadow: 0 30px 80px rgba(0,0,0,0.5);
            width: 100%; max-height: 520px; object-fit: cover;
            border: 3px solid rgba(255,255,255,0.1);
        }
        .hero-float-card {
            position: absolute; background: rgba(255,255,255,0.97);
            border-radius: 16px; padding: 12px 18px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.15);
            display: flex; align-items: center; gap: 10px;
            backdrop-filter: blur(10px);
            animation: floatCard 3s ease-in-out infinite;
        }
        @keyframes floatCard { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .hero-float-card.card-1 { bottom: 30px; left: -30px; animation-delay: 0s; }
        .hero-float-card.card-2 { top: 40px; right: -20px; animation-delay: 1.5s; }
        .hero-float-card .card-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .hero-float-card .card-text strong { display: block; font-size: 13px; color: #1a1a1a; }
        .hero-float-card .card-text span   { font-size: 11px; color: #888; }

        /* ─── MARQUEE STRIP ──────────────────────────────────── */
        .marquee-strip {
            background: linear-gradient(135deg, var(--green-dark), var(--green-mid));
            padding: 14px 0; overflow: hidden; white-space: nowrap;
        }
        .marquee-inner { display: inline-block; animation: marquee 25s linear infinite; }
        .marquee-inner span { display: inline-block; padding: 0 30px; color: rgba(255,255,255,0.85); font-size: 13px; font-weight: 500; }
        .marquee-inner span i { color: #69F0AE; margin-right: 6px; }
        @keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

        /* ─── SECTION COMMONS ────────────────────────────────── */
        .section-tag {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--green-soft); color: var(--green-dark);
            font-size: 12px; font-weight: 700; letter-spacing: 0.8px;
            text-transform: uppercase; border-radius: 50px; padding: 6px 16px;
            margin-bottom: 14px;
        }
        .section-heading { font-size: clamp(26px, 4vw, 38px); font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; line-height: 1.2; }
        .section-sub { color: #777; font-size: 16px; max-width: 520px; line-height: 1.7; }

        /* ─── CATEGORY CARDS ─────────────────────────────────── */
        .cat-card {
            background: #fff; border-radius: 20px; padding: 24px 16px;
            text-align: center; transition: all 0.25s;
            border: 2px solid transparent; cursor: pointer;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            display: block; color: inherit;
        }
        .cat-card:hover { border-color: var(--green-light); transform: translateY(-6px); box-shadow: 0 12px 32px rgba(76,175,80,0.15); }
        .cat-img-wrap { width: 100%; height: 110px; border-radius: 14px; background: var(--green-soft); margin: 0 auto 14px; display: flex; align-items: center; justify-content: center; overflow: hidden; transition: all 0.25s; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .cat-card:hover .cat-img-wrap { transform: scale(1.03); }
        .cat-img-wrap img { width: 100%; height: 100%; object-fit: cover; border-radius: 14px; }
        .cat-name { font-size: 13px; font-weight: 700; color: #2d2d2d; margin: 0; }

        /* ─── PRODUCT CARDS ──────────────────────────────────── */
        .prod-card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            overflow: hidden; transition: all 0.25s; position: relative;
            border: 2px solid transparent;
        }
        .prod-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.1); border-color: #E8F5E9; }
        .prod-card .discount-tag {
            position: absolute; top: 12px; left: 12px; z-index: 2;
            background: linear-gradient(135deg, var(--orange), #FF8F00);
            color: #fff; font-size: 11px; font-weight: 800;
            padding: 4px 10px; border-radius: 50px;
        }
        .prod-card .wishlist-btn-lp {
            position: absolute; top: 12px; right: 12px; z-index: 2;
            width: 34px; height: 34px; border-radius: 50%;
            background: rgba(255,255,255,0.9); border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #ccc; font-size: 15px; transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .prod-card .wishlist-btn-lp:hover { color: #e53935; transform: scale(1.15); }
        .prod-img-area { height: 200px; width: 100%; overflow: hidden; background: #f4f6f8; display: flex; align-items: center; justify-content: center; position: relative; }
        .prod-img-area img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .prod-card:hover .prod-img-area img { transform: scale(1.08); }
        .prod-body { padding: 16px; }
        .prod-brand { font-size: 11px; color: #aaa; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .prod-name { font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 6px; line-height: 1.3; }
        .prod-weight { font-size: 12px; color: #bbb; margin-bottom: 12px; }
        .prod-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f5f5f5; padding-top: 12px; }
        .prod-price .current { font-size: 18px; font-weight: 800; color: var(--green-main); }
        .prod-price .original { font-size: 12px; color: #ccc; text-decoration: line-through; display: block; }
        .btn-add-cart {
            background: linear-gradient(135deg, var(--green-main), var(--green-light));
            color: #fff; border: none; border-radius: 50px;
            padding: 8px 16px; font-size: 13px; font-weight: 700; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; gap: 6px;
        }
        .btn-add-cart:hover { transform: scale(1.05); box-shadow: 0 4px 14px rgba(76,175,80,0.4); }

        /* ─── FEATURES ───────────────────────────────────────── */
        .feature-card {
            border-radius: 24px; padding: 36px 28px; height: 100%;
            transition: transform 0.25s;
        }
        .feature-card:hover { transform: translateY(-6px); }
        .feature-icon {
            width: 64px; height: 64px; border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; margin-bottom: 20px;
        }
        .feature-card h5 { font-size: 18px; font-weight: 800; color: #1a1a1a; margin-bottom: 10px; }
        .feature-card p  { color: #888; font-size: 14px; line-height: 1.7; margin: 0; }

        /* ─── TESTIMONIALS ───────────────────────────────────── */
        .review-card {
            background: #fff; border-radius: 20px; padding: 28px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.06); height: 100%;
            border-top: 3px solid var(--green-light);
            transition: transform 0.25s;
        }
        .review-card:hover { transform: translateY(-4px); }
        .reviewer-img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid var(--green-soft); }
        .stars { color: var(--yellow); font-size: 13px; }
        .review-text { color: #555; font-size: 14px; line-height: 1.7; font-style: italic; }

        /* ─── CTA BANNER ─────────────────────────────────────── */
        .cta-section {
            background: linear-gradient(135deg, #0d2e0d 0%, #1a4a20 50%, #2d6a35 100%);
            border-radius: 32px; padding: 72px 48px; position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse at 70% 50%, rgba(76,175,80,0.2) 0%, transparent 60%);
        }
        .cta-section .content { position: relative; z-index: 2; }
        .cta-title { font-size: clamp(28px, 4vw, 44px); font-weight: 900; color: #fff; letter-spacing: -1px; }
        .cta-sub { color: rgba(255,255,255,0.6); font-size: 16px; margin: 16px 0 36px; }
        .btn-cta {
            background: #fff; color: var(--green-dark);
            font-weight: 800; font-size: 16px;
            padding: 16px 40px; border-radius: 50px; border: none;
            box-shadow: 0 8px 28px rgba(0,0,0,0.2);
            transition: all 0.25s; display: inline-flex; align-items: center; gap: 10px;
            cursor: pointer;
        }
        .btn-cta:hover { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(0,0,0,0.3); }

        /* ─── FOOTER ─────────────────────────────────────────── */
        .lp-footer { background: #0d1a0d; padding: 60px 0 30px; }
        .footer-logo { font-size: 22px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .footer-logo .logo-badge { width: 38px; height: 38px; background: linear-gradient(135deg, var(--green-light), #66BB6A); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; }
        .footer-desc { color: rgba(255,255,255,0.45); font-size: 14px; line-height: 1.7; max-width: 280px; }
        .footer-heading { color: rgba(255,255,255,0.8); font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 16px; }
        .footer-links a { display: block; color: rgba(255,255,255,0.45); font-size: 14px; margin-bottom: 10px; transition: color 0.2s; }
        .footer-links a:hover { color: var(--green-light); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.07); padding-top: 24px; margin-top: 40px; }
        .footer-bottom p { color: rgba(255,255,255,0.3); font-size: 13px; margin: 0; }
        .social-icons a { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.07); display: inline-flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.5); margin-right: 8px; transition: all 0.2s; font-size: 14px; }
        .social-icons a:hover { background: var(--green-main); color: #fff; }

        /* ─── AUTH MODAL ─────────────────────────────────────── */
        .auth-modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 2000;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .auth-modal-overlay.show { display: flex; }
        .auth-modal {
            background: #fff; border-radius: 28px; padding: 48px 44px;
            max-width: 440px; width: 90%; text-align: center;
            box-shadow: 0 30px 80px rgba(0,0,0,0.3);
            animation: popIn 0.35s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes popIn { from{transform:scale(0.8);opacity:0} to{transform:scale(1);opacity:1} }
        .modal-icon-wrap { width: 80px; height: 80px; border-radius: 50%; background: var(--green-soft); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .modal-icon-wrap i { font-size: 32px; color: var(--green-main); }
        .auth-modal h3 { font-size: 24px; font-weight: 800; color: #1a1a1a; margin-bottom: 10px; }
        .auth-modal p  { color: #888; font-size: 15px; line-height: 1.6; margin-bottom: 28px; }
        .btn-modal-login {
            display: block; width: 100%;
            background: linear-gradient(135deg, var(--green-main), var(--green-light));
            color: #fff; font-weight: 700; font-size: 16px;
            padding: 14px; border-radius: 14px; border: none; cursor: pointer;
            box-shadow: 0 6px 20px rgba(76,175,80,0.35);
            transition: all 0.2s; margin-bottom: 12px;
        }
        .btn-modal-login:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(76,175,80,0.45); }
        .btn-modal-register {
            display: block; width: 100%;
            background: var(--green-soft); color: var(--green-dark);
            font-weight: 700; font-size: 16px;
            padding: 14px; border-radius: 14px; border: none; cursor: pointer;
            transition: all 0.2s; margin-bottom: 20px;
        }
        .btn-modal-register:hover { background: #C8E6C9; }
        .modal-close { background: none; border: none; color: #ccc; font-size: 13px; cursor: pointer; transition: color 0.2s; }
        .modal-close:hover { color: #888; }

        @media (max-width: 768px) {
            .hero { min-height: auto; padding: 120px 0 60px; }
            .hero-stats { gap: 20px; }
            .stat-divider { display: none; }
            .cta-section { padding: 48px 28px; }
            .auth-modal { padding: 36px 24px; }
            .hero-float-card { display: none; }
        }
    </style>
</head>
<body>

<!-- ─── FLASH MESSAGE (e.g. after logout) ─────────────────────────────────── -->
<?php if (isset($_SESSION['success'])): ?>
<div id="flashMsg" style="
    position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
    background: #2E7D32; color: #fff; padding: 14px 28px;
    border-radius: 50px; font-size: 14px; font-weight: 600;
    box-shadow: 0 8px 28px rgba(0,0,0,0.25); z-index: 9999;
    display: flex; align-items: center; gap: 10px;
    animation: fadeSlideIn 0.4s ease;
">
    <i class="fa-solid fa-circle-check"></i>
    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<script>setTimeout(() => { const m = document.getElementById('flashMsg'); if(m) m.style.opacity='0', m.style.transition='opacity 0.5s', setTimeout(()=>m.remove(),500); }, 3500);</script>
<?php endif; ?>

<!-- ─── AUTH GATE MODAL ─────────────────────────────────────────────────────── -->
<div class="auth-modal-overlay" id="authModal">
    <div class="auth-modal">
        <div class="modal-icon-wrap">
            <i class="fa-solid fa-basket-shopping"></i>
        </div>
        <h3>Login Required</h3>
        <p>Please sign in to your GroceryMart account to continue shopping, add items to your cart, and place orders.</p>
        <a href="login.php" class="btn-modal-login">
            <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In to Continue
        </a>
        <a href="register.php" class="btn-modal-register">
            <i class="fa-solid fa-user-plus me-2"></i> Create a Free Account
        </a>
        <button class="modal-close" onclick="closeAuthModal()">
            <i class="fa-solid fa-xmark me-1"></i> Maybe later
        </button>
    </div>
</div>

<!-- ─── NAVBAR ─────────────────────────────────────────────────────────────── -->
<nav class="lp-nav" id="lpNav">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="landing.php" class="logo">
            <div class="logo-badge"><i class="fa-solid fa-basket-shopping"></i></div>
            GroceryMart
        </a>
        <div class="nav-links d-none d-lg-flex align-items-center">
            <a href="#categories">Categories</a>
            <a href="#products">Products</a>
            <a href="#features">Why Us</a>
            <a href="contact.php">Contact</a>
        </div>
        <div class="d-flex align-items-center gap-3">
            <?php if ($isLoggedIn): ?>
                <a href="index.php" class="btn-nav-signup">
                    <i class="fa-solid fa-store me-1"></i> Go to Shop
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-nav-login">Sign In</a>
                <a href="register.php" class="btn-nav-signup">Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ─── HERO ────────────────────────────────────────────────────────────────── -->
<section class="hero" id="hero">
    <div class="hero-orb"></div>
    <div class="hero-orb"></div>
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left Text -->
            <div class="col-lg-6 hero-content">
                <div class="hero-badge">
                    <span class="dot"></span>
                    <span>⚡ Super Fast Delivery in 30 Minutes</span>
                </div>
                <h1 class="hero-title">
                    Fresh Groceries,<br>
                    Delivered to<br>
                    <span class="highlight">Your Doorstep</span>
                </h1>
                <p class="hero-sub">
                    Order organic fruits, fresh vegetables, dairy, bakery essentials and 200+ daily staples — at the best market prices, straight from the farm.
                </p>
                <div class="hero-actions">
                    <?php if ($isLoggedIn): ?>
                        <a href="shop.php" class="btn-hero-primary">
                            <i class="fa-solid fa-cart-shopping"></i> Shop Now
                        </a>
                        <a href="index.php" class="btn-hero-secondary">
                            Browse All Products <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn-hero-primary">
                            <i class="fa-solid fa-basket-shopping"></i> Start Shopping Free
                        </a>
                        <button onclick="showAuthModal()" class="btn-hero-secondary">
                            View Products <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    <?php endif; ?>
                </div>
                <!-- Live stats -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="num"><?php echo $total_products; ?>+</div>
                        <div class="lbl">Products</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="num"><?php echo max($total_users, 500); ?>+</div>
                        <div class="lbl">Happy Customers</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="num"><?php echo max($total_orders, 1200); ?>+</div>
                        <div class="lbl">Orders Delivered</div>
                    </div>
                </div>
            </div>
            <!-- Right Image -->
            <div class="col-lg-6 hero-content">
                <div class="hero-img-wrap">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=700&q=85" alt="Fresh Groceries" class="hero-img-main">
                    <!-- Float cards -->
                    <div class="hero-float-card card-1">
                        <div class="card-icon" style="background:#E8F5E9;">🚚</div>
                        <div class="card-text">
                            <strong>Free Delivery</strong>
                            <span>On orders above ₹499</span>
                        </div>
                    </div>
                    <div class="hero-float-card card-2">
                        <div class="card-icon" style="background:#FFF8E1;">🌿</div>
                        <div class="card-text">
                            <strong>100% Organic</strong>
                            <span>Farm-to-table freshness</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── MARQUEE ──────────────────────────────────────────────────────────────── -->
<div class="marquee-strip">
    <div class="marquee-inner">
        <span><i class="fa-solid fa-truck-fast"></i> Free Delivery Over ₹499</span>
        <span><i class="fa-solid fa-leaf"></i> 100% Organic Products</span>
        <span><i class="fa-solid fa-tags"></i> Use Code: WELCOME50 for ₹50 Off</span>
        <span><i class="fa-solid fa-clock"></i> 30-Minute Delivery Guarantee</span>
        <span><i class="fa-solid fa-shield-halved"></i> 100% Fresh or Money Back</span>
        <span><i class="fa-solid fa-headset"></i> 24/7 Customer Support</span>
        <span><i class="fa-solid fa-truck-fast"></i> Free Delivery Over ₹499</span>
        <span><i class="fa-solid fa-leaf"></i> 100% Organic Products</span>
        <span><i class="fa-solid fa-tags"></i> Use Code: WELCOME50 for ₹50 Off</span>
        <span><i class="fa-solid fa-clock"></i> 30-Minute Delivery Guarantee</span>
        <span><i class="fa-solid fa-shield-halved"></i> 100% Fresh or Money Back</span>
        <span><i class="fa-solid fa-headset"></i> 24/7 Customer Support</span>
    </div>
</div>

<!-- ─── CATEGORIES ───────────────────────────────────────────────────────────── -->
<section id="categories" class="py-5 my-4">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag"><i class="fa-solid fa-th-large"></i> Browse by Category</div>
            <h2 class="section-heading">Shop What You Need</h2>
            <p class="section-sub mx-auto mt-2">From farm-fresh fruits to household essentials — all in one place.</p>
        </div>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3 g-md-4">
            <?php foreach ($categories as $cat): ?>
                <?php 
                    $cName = trim($cat['name']);
                    if (empty($cName)) continue;
                    $imgUrl = getCategoryImage($cat['image'] ?? '', $cName);
                ?>
                <div class="col">
                    <?php if ($isLoggedIn): ?>
                        <a href="shop.php?category=<?php echo $cat['id']; ?>" class="cat-card">
                    <?php else: ?>
                        <a href="javascript:void(0)" onclick="showAuthModal()" class="cat-card">
                    <?php endif; ?>
                        <div class="cat-img-wrap">
                            <img 
                                src="<?php echo $imgUrl; ?>" 
                                alt="<?php echo htmlspecialchars($cName); ?>"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80';"
                            >
                        </div>
                        <p class="cat-name"><?php echo htmlspecialchars($cName); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ─── HOT DEALS ─────────────────────────────────────────────────────────────── -->
<?php if (!empty($discounted_products)): ?>
<section class="py-5" style="background: linear-gradient(180deg, #F9FBF9, #fff);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div>
                <div class="section-tag"><i class="fa-solid fa-fire-flame-curved" style="color:#FF6F00;"></i> Hot Deals</div>
                <h2 class="section-heading mb-0">Today's Best Offers</h2>
            </div>
            <?php if ($isLoggedIn): ?>
                <a href="shop.php?filter=discounted" class="btn btn-outline-success rounded-pill px-4">View All Deals <i class="fa-solid fa-arrow-right ms-1"></i></a>
            <?php else: ?>
                <button onclick="showAuthModal()" class="btn btn-outline-success rounded-pill px-4">View All Deals <i class="fa-solid fa-arrow-right ms-1"></i></button>
            <?php endif; ?>
        </div>
        <div class="row g-4" id="products">
            <?php foreach ($discounted_products as $prod): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="prod-card">
                        <span class="discount-tag"><?php echo round($prod['discount']); ?>% OFF</span>
                        <button class="wishlist-btn-lp" onclick="requireAuth(event)"><i class="fa-regular fa-heart"></i></button>
                        <div class="prod-img-area">
                            <img src="<?php echo getProductImage($prod['image'], $prod['name']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                        </div>
                        <div class="prod-body">
                            <div class="prod-brand"><?php echo htmlspecialchars($prod['brand'] ?? 'GroceryMart'); ?></div>
                            <div class="prod-name"><?php echo htmlspecialchars($prod['name']); ?></div>
                            <div class="prod-weight"><i class="fa-solid fa-weight-hanging me-1"></i><?php echo htmlspecialchars($prod['weight'] ?? '1 unit'); ?></div>
                            <div class="prod-footer">
                                <div class="prod-price">
                                    <span class="current"><?php echo formatPrice(getDiscountedPrice($prod['price'], $prod['discount'])); ?></span>
                                    <span class="original"><?php echo formatPrice($prod['price']); ?></span>
                                </div>
                                <?php if ($prod['stock'] > 0): ?>
                                    <button class="btn-add-cart" onclick="requireAuth(event)">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill px-2">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ─── POPULAR PRODUCTS ──────────────────────────────────────────────────────── -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div>
                <div class="section-tag"><i class="fa-solid fa-star"></i> Best Sellers</div>
                <h2 class="section-heading mb-0">Popular Products</h2>
            </div>
            <?php if ($isLoggedIn): ?>
                <a href="shop.php" class="btn btn-outline-success rounded-pill px-4">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
            <?php else: ?>
                <button onclick="showAuthModal()" class="btn btn-outline-success rounded-pill px-4">View All <i class="fa-solid fa-arrow-right ms-1"></i></button>
            <?php endif; ?>
        </div>
        <div class="row g-4">
            <?php foreach ($popular_products as $prod): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="prod-card">
                        <?php if ($prod['discount'] > 0): ?>
                            <span class="discount-tag"><?php echo round($prod['discount']); ?>% OFF</span>
                        <?php endif; ?>
                        <button class="wishlist-btn-lp" onclick="requireAuth(event)"><i class="fa-regular fa-heart"></i></button>
                        <div class="prod-img-area">
                            <img src="<?php echo getProductImage($prod['image'], $prod['name']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                        </div>
                        <div class="prod-body">
                            <div class="prod-brand"><?php echo htmlspecialchars($prod['brand'] ?? 'GroceryMart'); ?></div>
                            <div class="prod-name"><?php echo htmlspecialchars($prod['name']); ?></div>
                            <div class="prod-weight"><i class="fa-solid fa-weight-hanging me-1"></i><?php echo htmlspecialchars($prod['weight'] ?? '1 unit'); ?></div>
                            <div class="prod-footer">
                                <div class="prod-price">
                                    <span class="current"><?php echo formatPrice(getDiscountedPrice($prod['price'], $prod['discount'])); ?></span>
                                    <?php if ($prod['discount'] > 0): ?>
                                        <span class="original"><?php echo formatPrice($prod['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($prod['stock'] > 0): ?>
                                    <button class="btn-add-cart" onclick="requireAuth(event)">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill px-2">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ─── FEATURES ─────────────────────────────────────────────────────────────── -->
<section id="features" class="py-5 my-2" style="background: #F9FBF9;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag"><i class="fa-solid fa-shield-halved"></i> Our Promise</div>
            <h2 class="section-heading">Why GroceryMart?</h2>
            <p class="section-sub mx-auto mt-2">We're not just another grocery app — we're your trusted daily partner.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card bg-white shadow-sm border">
                    <div class="feature-icon" style="background:#E8F5E9;color:#2E7D32;"><i class="fa-solid fa-truck-fast"></i></div>
                    <h5>Free & Fast Delivery</h5>
                    <p>Free shipping on orders above ₹499. Delivered in 30 minutes, guaranteed.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card bg-white shadow-sm border">
                    <div class="feature-icon" style="background:#E8F5E9;color:#2E7D32;"><i class="fa-solid fa-leaf"></i></div>
                    <h5>100% Organic & Fresh</h5>
                    <p>Sourced daily from certified organic farms. No preservatives, no compromise.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card bg-white shadow-sm border">
                    <div class="feature-icon" style="background:#E8F5E9;color:#2E7D32;"><i class="fa-solid fa-piggy-bank"></i></div>
                    <h5>Best Market Prices</h5>
                    <p>Guaranteed lowest prices with active coupons. Save more on every order.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card bg-white shadow-sm border">
                    <div class="feature-icon" style="background:#E8F5E9;color:#2E7D32;"><i class="fa-solid fa-headset"></i></div>
                    <h5>24/7 Dedicated Support</h5>
                    <p>Real humans ready to help anytime. Chat, email, or call — we're always on.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── TESTIMONIALS ──────────────────────────────────────────────────────────── -->
<section class="py-5 my-2">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag"><i class="fa-solid fa-heart"></i> Customer Love</div>
            <h2 class="section-heading">What Our Shoppers Say</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="review-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=100&q=80" class="reviewer-img" alt="Sarah">
                        <div>
                            <div class="fw-bold" style="font-size:15px;">Sarah Jenkins</div>
                            <div class="stars">★★★★★</div>
                        </div>
                    </div>
                    <p class="review-text">"GroceryMart completely changed my daily routine. Mangoes and apples were exceptionally fresh, and delivery took less than 20 minutes. Highly recommended!"</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="review-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" class="reviewer-img" alt="Michael">
                        <div>
                            <div class="fw-bold" style="font-size:15px;">Michael Chen</div>
                            <div class="stars">★★★★★</div>
                        </div>
                    </div>
                    <p class="review-text">"I used WELCOME50 and saved ₹50 instantly. The checkout experience is smooth and seamless. The app is well designed and very intuitive. Will definitely reorder!"</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="review-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=100&q=80" class="reviewer-img" alt="Aisha">
                        <div>
                            <div class="fw-bold" style="font-size:15px;">Aisha Rahaman</div>
                            <div class="stars">★★★★⯨</div>
                        </div>
                    </div>
                    <p class="review-text">"The organic paneer is incredibly soft and pure. Love the low-stock alerts — it helps me plan my grocery list before items run out. Great service overall!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── CTA BANNER ────────────────────────────────────────────────────────────── -->
<section class="py-5">
    <div class="container">
        <div class="cta-section">
            <div class="content">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="cta-title">Ready to Shop Fresh?</h2>
                        <p class="cta-sub">Create your free account and get ₹50 off your first order with code <strong style="color:#69F0AE;">WELCOME50</strong></p>
                        <?php if ($isLoggedIn): ?>
                            <a href="shop.php" class="btn-cta">
                                <i class="fa-solid fa-cart-shopping" style="color:var(--green-main);"></i>
                                Browse the Shop
                            </a>
                        <?php else: ?>
                            <a href="register.php" class="btn-cta">
                                <i class="fa-solid fa-user-plus" style="color:var(--green-main);"></i>
                                Create Free Account
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4 text-center d-none d-lg-block">
                        <div style="font-size: 100px; line-height:1; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));">🛒</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── FOOTER ────────────────────────────────────────────────────────────────── -->
<footer class="lp-footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="footer-logo">
                    <div class="logo-badge"><i class="fa-solid fa-basket-shopping"></i></div>
                    GroceryMart
                </div>
                <p class="footer-desc">Your trusted online grocery store. Fresh produce, daily essentials, and more — delivered in 30 minutes.</p>
                <div class="social-icons mt-4">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-heading">Quick Links</div>
                <div class="footer-links">
                    <a href="landing.php">Home</a>
                    <a href="#categories">Categories</a>
                    <a href="#products">Products</a>
                    <a href="contact.php">Contact Us</a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-heading">My Account</div>
                <div class="footer-links">
                    <?php if ($isLoggedIn): ?>
                        <a href="dashboard.php">My Orders</a>
                        <a href="profile.php">My Profile</a>
                        <a href="wishlist.php">Wishlist</a>
                        <a href="login.php?logout=1">Sign Out</a>
                    <?php else: ?>
                        <a href="login.php">Sign In</a>
                        <a href="register.php">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="footer-heading">Available Coupons</div>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <div style="background:rgba(255,255,255,0.05);border:1px dashed rgba(76,175,80,0.4);border-radius:10px;padding:12px 16px;">
                        <code style="color:#69F0AE;font-size:14px;font-weight:700;">WELCOME50</code>
                        <span style="color:rgba(255,255,255,0.4);font-size:12px;display:block;margin-top:2px;">₹50 off on orders above ₹299</span>
                    </div>
                    <div style="background:rgba(255,255,255,0.05);border:1px dashed rgba(76,175,80,0.4);border-radius:10px;padding:12px 16px;">
                        <code style="color:#69F0AE;font-size:14px;font-weight:700;">SAVEMORE100</code>
                        <span style="color:rgba(255,255,255,0.4);font-size:12px;display:block;margin-top:2px;">₹100 off on orders above ₹599</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <p>© <?php echo date('Y'); ?> GroceryMart. All rights reserved.</p>
            <p>Made with <span style="color:#4CAF50;">♥</span> for fresh living</p>
        </div>
    </div>
</footer>

<!-- ─── SCRIPTS ───────────────────────────────────────────────────────────────── -->
<script>
    // Navbar scroll effect
    const nav = document.getElementById('lpNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 60);
    });

    // Auth modal
    function showAuthModal() {
        document.getElementById('authModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeAuthModal() {
        document.getElementById('authModal').classList.remove('show');
        document.body.style.overflow = '';
    }
    // Close on backdrop click
    document.getElementById('authModal').addEventListener('click', function(e) {
        if (e.target === this) closeAuthModal();
    });

    // Guard any action that requires auth (for guests)
    function requireAuth(e) {
        <?php if (!$isLoggedIn): ?>
            e.preventDefault();
            e.stopPropagation();
            showAuthModal();
            return false;
        <?php else: ?>
            // Logged in — do nothing special, let default handler run
        <?php endif; ?>
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
</script>
</body>
</html>
