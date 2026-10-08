<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Usuário';
$user_email = $_SESSION['user_email'] ?? $_SESSION['user_telefone'] ?? 'usuario@email.com';
$login_type = $_SESSION['login_type'] ?? 'Padrão';

$page = $_GET['page'] ?? 'monitoramento';

// Incluir arquivos de seções
require_once __DIR__ . '/blog.php';
require_once __DIR__ . '/consultas.php';
require_once __DIR__ . '/exames.php';
require_once __DIR__ . '/informacoes.php';
require_once __DIR__ . '/suporte.php';
require_once __DIR__ . '/inicio.php';

// Monta o HTML de cada seção UMA VEZ e passa pro JavaScript como JSON
$blogHtml = function_exists('getDashboardBlogHtml') ? getDashboardBlogHtml() : (function_exists('getBlogHtml') ? getBlogHtml() : '');
$consultasHtml = function_exists('getConsultasHtml') ? getConsultasHtml() : '';
$examesHtml = function_exists('getExamesHtml') ? getExamesHtml() : '';
$informacoesHtml = function_exists('getInformacoesHtml') ? getInformacoesHtml() : '';
$suporteHtml = function_exists('getSuporteHtml') ? getSuporteHtml() : '';
$inicioHtml = function_exists('getInicioHtml') ? getInicioHtml($user_name) : '';

// Se não existir getDashboardBlogHtml, monta um fallback
if (empty($blogHtml) && function_exists('getBlogArticles')) {
    $articles = getBlogArticles();
    $html = '<div class="blog-shell"><div class="info-card"><h3><i class="fas fa-newspaper"></i> Artigos Recentes</h3>';
    foreach ($articles as $id => $a) {
        $html .= '<div class="blog-post cursor-pointer" onclick="openArticle(\'' . htmlspecialchars($id, ENT_QUOTES) . '\', event)">';
        $html .= '<div class="blog-title">' . htmlspecialchars($a['title']) . '</div>';
        $html .= '<div class="blog-date">' . htmlspecialchars($a['date']) . '</div>';
        $html .= '</div>';
    }
    $html .= '</div></div>';
    $blogHtml = $html;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardioWeb - Painel Principal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f6ecee; overflow: hidden; height: 100vh; }
        .app-container { display: flex; height: 100vh; width: 100%; }
        .sidebar { width: 280px; background: linear-gradient(180deg, #4c0719 0%, #7e1b31 100%); color: white; display: flex; flex-direction: column; box-shadow: 4px 0 20px rgba(0,0,0,0.12); overflow-y: auto; }
        .logo-area { padding: 30px 25px; border-bottom: 1px solid rgba(255,255,255,0.12); margin-bottom: 30px; }
        .logo { display: flex; align-items: center; gap: 12px; }
        .logo-icon { background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; }
        .logo-text h2 { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .logo-text p { font-size: 10px; opacity: 0.8; margin-top: 4px; }
        .nav-menu { flex: 1; padding: 0 20px; }
        .nav-item { display: flex; align-items: center; gap: 14px; padding: 14px 18px; margin-bottom: 8px; border-radius: 12px; cursor: pointer; transition: all 0.3s; color: rgba(255,255,255,0.8); }
        .nav-item:hover { background: rgba(255,255,255,0.12); color: white; }
        .nav-item.active { background: rgba(255,255,255,0.18); color: white; font-weight: 500; }
        .nav-item i { width: 24px; font-size: 20px; }
        .nav-item span { font-size: 15px; }
        .user-section { padding: 20px; margin: 20px; background: linear-gradient(135deg, #7a1d34 0%, #5c1230 100%); border-radius: 16px; margin-top: auto; margin-bottom: 20px; }
        .user-avatar { width: 50px; height: 50px; background: rgba(255,255,255,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; margin-bottom: 12px; }
        .user-name { font-weight: 600; font-size: 16px; margin-bottom: 4px; }
        .user-email { font-size: 11px; opacity: 0.8; margin-bottom: 12px; word-break: break-all; }
        .logout-btn { background: rgba(255,255,255,0.2); color: white; padding: 8px 12px; border-radius: 10px; text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 8px; justify-content: center; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f8fafc; }
        .main-header { background: white; padding: 20px 30px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 24px; font-weight: 700; color: #1e2a3a; }
        .header-actions { display: flex; gap: 15px; }
        .header-icon { width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; }
        .header-icon:hover { background: #e2e8f0; }
        .content-area { flex: 1; overflow-y: auto; padding: 30px; }
        .welcome-card { background: linear-gradient(135deg, #851e32 0%, #5a1e2c 100%); color: white; padding: 30px; border-radius: 20px; margin-bottom: 30px; }
        .welcome-card h2 { font-size: 28px; margin-bottom: 10px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.3s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .stat-icon { width: 50px; height: 50px; background: #fff0f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #851e32; margin-bottom: 15px; }
        .stat-card h3 { font-size: 28px; color: #1e2a3a; margin-bottom: 5px; }
        .stat-card p { color: #64748b; font-size: 14px; }
        .info-card { background: white; border-radius: 16px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .info-card h3 { color: #1e2a3a; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; }
        .blog-post { padding: 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.3s; }
        .blog-post:hover { background: #f8fafc; }
        .blog-title { font-weight: 600; color: #1e2a3a; margin-bottom: 5px; }
        .blog-date { font-size: 12px; color: #94a3b8; }
        .support-card { background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 15px; }
        .article-container { max-width: 900px; margin: 0 auto; background: white; border-radius: 16px; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .article-back-btn { display: inline-flex; align-items: center; gap: 8px; color: #851e32; margin-bottom: 20px; cursor: pointer; padding: 8px 12px; border-radius: 8px; transition: all 0.3s; font-weight: 500; }
        .article-back-btn:hover { background: #f8fafc; }
        .article-header { margin-bottom: 30px; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; }
        .article-title { font-size: 32px; font-weight: 700; color: #1e2a3a; margin-bottom: 10px; line-height: 1.3; }
        .article-meta { display: flex; gap: 20px; color: #666; font-size: 14px; }
        .article-meta-item { display: flex; align-items: center; gap: 5px; }
        .article-content { line-height: 1.8; color: #333; font-size: 16px; }
        .article-content p { margin-bottom: 20px; text-align: justify; }
        .article-content h3 { font-size: 20px; font-weight: 700; color: #1e2a3a; margin: 30px 0 15px 0; }
        .article-image-inline { max-width: 300px; height: auto; border-radius: 12px; margin: 15px 15px 15px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .article-image-inline.right { float: right; margin-left: 15px; margin-right: 0; }
        .cursor-pointer { cursor: pointer; }
        .blog-shell {
            display: flex;
            flex-direction: column;
            gap: 28px;
            min-height: 100%;
        }
        .cardio-hero {
            position: relative;
            min-height: 560px;
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0.54) 100%),
                url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
            border-radius: 0;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.06);
        }
        .cardio-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(248, 214, 0, 0.35), transparent 46%);
        }
        .cardio-hero-controls {
            position: absolute;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(17, 17, 17, 0.42);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            z-index: 2;
        }
        .cardio-hero-controls i {
            font-size: 22px;
            color: white;
        }
        .cardio-hero-content {
            position: absolute;
            left: 50%;
            bottom: 18px;
            transform: translateX(-50%);
            width: min(980px, 80%);
            z-index: 2;
        }
        .cardio-yellow-panel {
            background: linear-gradient(135deg, rgba(130, 18, 35, 0.96) 0%, rgba(157, 30, 50, 0.96) 100%);
            color: #fdf2f5;
            padding: 42px 28px 22px;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
            border-radius: 0;
            box-shadow: 0 26px 42px rgba(62, 11, 20, 0.2);
        }
        .cardio-yellow-panel .line {
            display: block;
            font-size: clamp(2.1rem, 3vw, 4.0rem);
            font-weight: 900;
            letter-spacing: -0.08em;
            line-height: 0.92;
            text-transform: uppercase;
            text-align: center;
            color: #fdf2f5;
        }
        .cardio-yellow-panel .line + .line {
            margin-top: 6px;
        }
        .cardio-yellow-panel .line.black-outline {
            display: inline-block;
            width: 100%;
            position: relative;
            padding: 0 2px;
        }
        .cardio-yellow-panel .line.black-outline::before {
            content: "";
            position: absolute;
            inset: 0;
            border-bottom: 6px solid rgba(255,255,255,0.65);
            bottom: -10px;
            left: 0;
            width: 100%;
        }
        .cardio-heart-stage {
            position: relative;
            padding: 26px 24px 0;
        }
        .heart-orbit {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: min(330px, 70vw);
            height: 330px;
            margin: 0 auto;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.10), rgba(244,212,0,0.10) 44%, rgba(255,255,255,0.02) 72%);
            box-shadow: inset 0 0 50px rgba(244,212,0,0.15);
            animation: heartPulse 2.6s ease-in-out infinite;
        }
        @keyframes heartPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }
        .heart-svg {
            width: 60%;
            height: 60%;
            filter: drop-shadow(0 16px 28px rgba(244,212,0,0.28));
        }
        .heart-svg path {
            fill: url(#heartGradient);
            stroke: rgba(255,255,255,0.35);
            stroke-width: 3;
        }
        #heartGradient stop:first-child { stop-color: #ffd7db; }
        #heartGradient stop:nth-child(2) { stop-color: #c51c3c; }
        #heartGradient stop:last-child { stop-color: #6b0d1a; }
        .cardio-section {
            display: grid;
            grid-template-columns: 1.15fr 1fr 0.9fr;
            gap: 26px;
            align-items: start;
        }
        .cardio-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(148,163,184,0.12);
            box-shadow: 0 12px 24px rgba(15,23,42,0.05);
            padding: 24px 22px;
        }
        .cardio-card h3 {
            font-size: clamp(1.8rem, 2vw, 2.4rem);
            letter-spacing: -0.06em;
            font-weight: 900;
            color: #111827;
            margin-bottom: 16px;
        }
        .cardio-card p {
            color: #4b5563;
            line-height: 1.75;
            font-size: 1rem;
        }
        .cardio-card .mini-tag {
            display: inline-block;
            background: rgba(244,212,0,0.16);
            color: #7a6300;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 800;
            margin-bottom: 12px;
        }
        .cardio-stack {
            display: grid;
            gap: 18px;
        }
        .pill-info {
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(249,250,251,1));
            border: 1px solid rgba(148,163,184,0.12);
            padding: 18px 18px;
            box-shadow: 0 10px 20px rgba(15,23,42,0.04);
        }
        .pill-info h4 {
            margin: 0 0 8px;
            font-size: 1.2rem;
            font-weight: 800;
            color: #111827;
        }
        .pill-info p {
            margin: 0;
            color: #4b5563;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        .cardio-lists {
            display: grid;
            gap: 18px;
            margin-top: 30px;
        }
        .cardio-list-item {
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 18px;
            align-items: center;
            background: rgba(255,255,255,0.94);
            border: 1px solid rgba(148,163,184,0.12);
            padding: 18px 16px;
            box-shadow: 0 10px 20px rgba(15,23,42,0.04);
        }
        .cardio-list-badge {
            width: 92px;
            height: 92px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(244,212,0,0.18), rgba(254,243,195,0.65));
            border: 1px solid rgba(244,212,0,0.4);
            color: #a26900;
            font-size: 38px;
            font-weight: 900;
        }
        .cardio-list-item h4 {
            margin: 0 0 8px;
            color: #111827;
            font-weight: 800;
            font-size: 1.2rem;
        }
        .cardio-list-item p {
            margin: 0;
            color: #4b5563;
            line-height: 1.6;
            font-size: 0.96rem;
        }
        .cardio-video-fallback {
            position: absolute;
            right: 28px;
            bottom: 26px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 2;
            color: white;
            font-weight: 700;
            font-size: 0.92rem;
        }
        .cardio-video-fallback .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.30);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
        }
        @media (max-width: 900px) {
            .cardio-section {
                grid-template-columns: 1fr;
            }
            .cardio-hero { min-height: 440px; }
        }
        .blog-feature-grid { display: grid; grid-template-columns: 1.45fr 1fr 1fr; gap: 24px; align-items: stretch; }
        .blog-brand-panel { position: relative; display: flex; align-items: flex-end; min-height: 350px; padding: 34px 26px 30px; background: linear-gradient(135deg, #f9dd17 0%, #f2d000 100%); clip-path: polygon(0 0, 86% 0, 100% 100%, 0 100%); box-shadow: 0 18px 32px rgba(133, 30, 50, 0.12); overflow: hidden; cursor: pointer; }
        .blog-brand-panel::before { content: ""; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.18)); pointer-events: none; }
        .brand-copy { position: relative; z-index: 1; display: flex; flex-direction: column; gap: 4px; color: #1a1a1a; }
        .brand-kicker { font-size: 1.1rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #1b1b1b; }
        .brand-name { font-size: clamp(3rem, 5vw, 5.5rem); font-weight: 900; line-height: 0.88; letter-spacing: -0.08em; color: #0d0d0d; }
        .blog-feature-card, .blog-side-card { background: rgba(255,255,255,0.94); border: 1px solid rgba(148,163,184,0.15); box-shadow: 0 12px 26px rgba(15,23,42,0.06); overflow: hidden; border-radius: 0; cursor: pointer; }
        .blog-feature-card { display: flex; flex-direction: column; }
        .blog-feature-card img, .blog-side-card img { width: 100%; height: 215px; object-fit: cover; display: block; background: #e2e8f0; }
        .blog-feature-card .feature-badge, .blog-side-card .feature-badge { position: absolute; left: 18px; bottom: 18px; background: rgba(133, 30, 50, 0.9); color: white; font-size: 0.7rem; letter-spacing: 0.08em; text-transform: uppercase; padding: 8px 10px; border-radius: 10px; font-weight: 800; }
        .blog-feature-card .feature-copy, .blog-side-card .feature-copy { padding: 19px 18px 16px; }
        .blog-feature-card h3, .blog-side-card h3 { margin: 0 0 10px; font-size: 1.3rem; line-height: 1.15; letter-spacing: -0.04em; color: #1e2a3a; }
        .blog-feature-card p, .blog-side-card p { margin: 0; color: #475569; line-height: 1.6; font-size: 0.95rem; }
        .blog-side-card { display: flex; flex-direction: column; }
        .blog-side-card img { height: 180px; }
        .blog-bottom-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; }
        .blog-card { background: rgba(255,255,255,0.96); border: 1px solid rgba(148,163,184,0.14); box-shadow: 0 12px 26px rgba(15,23,42,0.05); overflow: hidden; cursor: pointer; transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .blog-card:hover { transform: translateY(-4px); box-shadow: 0 20px 32px rgba(15,23,42,0.08); }
        .blog-card img { width: 100%; height: 220px; object-fit: cover; display: block; background: #e2e8f0; }
        .blog-card-body { padding: 18px 15px 16px; }
        .blog-card-meta { display: flex; justify-content: space-between; gap: 10px; align-items: center; font-size: 11px; color: #6b7280; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.08em; }
        .blog-badge { background: rgba(133, 30, 50, 0.08); color: #851e32; padding: 6px 9px; border-radius: 999px; font-weight: 800; }
        .blog-card h3 { font-size: 1.05rem; line-height: 1.2; color: #1e2a3a; margin: 0 0 6px; letter-spacing: -0.04em; }
        .blog-card p { color: #475569; line-height: 1.5; margin: 0; font-size: 0.86rem; }
        .blog-card-actions { margin-top: 14px; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .blog-link { display: inline-flex; align-items: center; gap: 8px; color: #851e32; font-weight: 800; text-decoration: none; font-size: 0.82rem; }
        .blog-link:hover { opacity: 0.85; }
        .chat-sidebar { width: 350px; background: #fff5f6; border-left: 1px solid #f3d8de; display: flex; flex-direction: column; box-shadow: -4px 0 20px rgba(0,0,0,0.05); }
        .chat-header { padding: 20px; border-bottom: 1px solid #f3d8de; background: linear-gradient(135deg, #7a1e31 0%, #a22a44 100%); color: white; }
        .chat-header h3 { font-size: 18px; display: flex; align-items: center; gap: 10px; }
        .chat-header p { font-size: 12px; opacity: 0.9; margin-top: 5px; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px; }
        .message { display: flex; gap: 12px; max-width: 90%; }
        .message.user { align-self: flex-end; flex-direction: row-reverse; }
        .message-avatar { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .message.user .message-avatar { background: #851e32; color: white; }
        .message.bot .message-avatar { background: #10b981; color: white; }
        .message-bubble { background: #f1f5f9; padding: 10px 15px; border-radius: 18px; font-size: 13px; line-height: 1.4; color: #1e2a3a; }
        .message.user .message-bubble { background: #851e32; color: white; }
        .chat-input-area { padding: 15px 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; }
        .chat-input { flex: 1; padding: 12px; border: 1px solid #e2e8f0; border-radius: 25px; outline: none; font-family: inherit; }
        .chat-input:focus { border-color: #851e32; }
        .chat-send { width: 45px; height: 45px; background: #851e32; border: none; border-radius: 50%; color: white; cursor: pointer; transition: all 0.3s; }
        .chat-send:hover { background: #5a1e2c; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-content { background: white; border-radius: 20px; width: 90%; max-width: 500px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { padding: 25px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h2 { font-size: 22px; font-weight: 700; color: #1e2a3a; margin: 0; display: flex; align-items: center; gap: 10px; }
        .modal-close { background: none; border: none; font-size: 28px; color: #999; cursor: pointer; transition: color 0.3s; }
        .modal-close:hover { color: #851e32; }
        .modal-body { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #1e2a3a; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea { font-family: inherit; border: 1px solid #ddd; border-radius: 8px; transition: border-color 0.3s; width: 100%; padding: 12px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #851e32; box-shadow: 0 0 0 3px rgba(133,30,50,0.1); }
        .contact-btn { background: #851e32; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; font-family: inherit; }
        .contact-btn:hover { background: #5a1e2c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(133,30,50,0.3); }
        .contact-btn:active { transform: translateY(0); }
        @media (max-width: 1000px) { .chat-sidebar { width: 300px; } }
        @media (max-width: 800px) { .chat-sidebar { display: none; } }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="logo-area">
                <div class="logo">
                    <div class="logo-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="logo-text">
                        <h2>CardioWeb</h2>
                        <p>Saúde & Monitoramento</p>
                    </div>
                </div>
            </div>
            <div class="nav-menu">
                <div class="nav-item <?php echo $page == 'monitoramento' ? 'active' : ''; ?>" onclick="changePage('monitoramento')">
                    <i class="fas fa-heartbeat"></i><span>Monitoramento</span>
                </div>
                <div class="nav-item <?php echo $page == 'blog' ? 'active' : ''; ?>" onclick="changePage('blog')">
                    <i class="fas fa-newspaper"></i><span>Blog</span>
                </div>
                <div class="nav-item <?php echo $page == 'exames' ? 'active' : ''; ?>" onclick="changePage('exames')">
                    <i class="fas fa-flask"></i><span>Exames</span>
                </div>
                <div class="nav-item <?php echo $page == 'informacoes' ? 'active' : ''; ?>" onclick="changePage('informacoes')">
                    <i class="fas fa-info-circle"></i><span>Informações</span>
                </div>
                <div class="nav-item <?php echo $page == 'suporte' ? 'active' : ''; ?>" onclick="changePage('suporte')">
                    <i class="fas fa-headset"></i><span>Suporte</span>
                </div>
            </div>
            <div class="user-section">
                <div class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($user_email); ?></div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>

        <!-- CONTEÚDO PRINCIPAL -->
        <div class="main-content">
            <div class="main-header">
                <h1 class="page-title" id="pageTitle">Monitoramento</h1>
                <div class="header-actions">
                    <div class="header-icon"><i class="fas fa-bell"></i></div>
                    <div class="header-icon"><i class="fas fa-cog"></i></div>
                </div>
            </div>
            <div class="content-area" id="contentArea">
                <!-- Conteúdo dinâmico -->
            </div>
        </div>

        <!-- MODAL NOVA CONSULTA -->
        <div id="consultaModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-calendar-plus"></i> Agendar Nova Consulta</h2>
                    <button class="modal-close" onclick="closeConsultaModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="consultaForm" onsubmit="agendarConsulta(event)">
                        <div class="form-group">
                            <label><i class="fas fa-user-md"></i> Médico:</label>
                            <select id="medicSelect" required>
                                <option value="">Selecione um médico</option>
                                <option value="Dr. Roberto Mendes|Cardiologia">Dr. Roberto Mendes - Cardiologia</option>
                                <option value="Dra. Aline Costa|Arritmologia">Dra. Aline Costa - Arritmologia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Data:</label>
                            <input type="date" id="dataConsulta" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Horário:</label>
                            <select id="horaConsulta" required>
                                <option value="">Selecione um horário</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-stethoscope"></i> Tipo de Consulta:</label>
                            <select id="tipoConsulta" required>
                                <option value="">Selecione o tipo</option>
                                <option value="Presencial">Presencial</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-file-alt"></i> Observações (opcional):</label>
                            <textarea id="obsConsulta" placeholder="Descreva os sintomas ou motivo da consulta..."></textarea>
                        </div>
                        <div style="display:flex; gap:10px; margin-top:20px;">
                            <button type="submit" class="contact-btn" style="flex:1;"><i class="fas fa-check"></i> Confirmar Agendamento</button>
                            <button type="button" class="contact-btn" style="flex:1; background:#6c757d;" onclick="closeConsultaModal()"><i class="fas fa-times"></i> Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CHAT -->
        <div class="chat-sidebar">
            <div class="chat-header">
                <h3><i class="fas fa-comment-dots"></i> Assistente CardioWeb</h3>
                <p>💬 Converse comigo sobre sua saúde!</p>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="message bot">
                    <div class="message-avatar"><i class="fas fa-robot"></i></div>
                    <div class="message-bubble">Olá! Eu sou o assistente do CardioWeb. Como posso ajudar você hoje? 💙</div>
                </div>
            </div>
            <div class="chat-input-area">
                <input type="text" class="chat-input" id="chatInput" placeholder="Digite sua mensagem..." onkeypress="if(event.key === 'Enter') sendMessage()">
                <button class="chat-send" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <script>
        // ========== DADOS DOS ARTIGOS ==========
        const articlesData = <?php echo json_encode(function_exists('getBlogArticles') ? getBlogArticles() : []); ?>;

        // ========== HTML DAS SEÇÕES (vindos do PHP) ==========
        const sectionHtml = {
            'monitoramento': <?php echo json_encode($inicioHtml, JSON_UNESCAPED_UNICODE); ?>,
            'blog':         `
                <div class="blog-shell">
                    <section class="cardio-hero">
                        <div class="cardio-hero-controls"><i class="fas fa-pause"></i></div>
                        <div class="cardio-hero-content">
                            <div class="cardio-yellow-panel">
                                <span class="line">VIVA MELHOR</span>
                                <span class="line black-outline">SEU CORAÇÃO</span>
                            </div>
                        </div>
                        <div class="cardio-video-fallback">
                            <span class="circle"><i class="fas fa-play"></i></span>
                            <span>Mais vídeos</span>
                        </div>
                    </section>

                    <div class="cardio-heart-stage">
                        <div class="heart-orbit">
                            <svg class="heart-svg" viewBox="0 0 512 512" aria-label="Coração cardiologia">
                                <defs>
                                    <linearGradient id="heartGradient" x1="0%" x2="100%" y1="0%" y2="100%">
                                        <stop offset="0%" stop-color="#fff6b8" />
                                        <stop offset="36%" stop-color="#f5d40b" />
                                        <stop offset="100%" stop-color="#d49500" />
                                    </linearGradient>
                                </defs>
                                <path d="M256 448l-32-29C91 311 48 270 48 181c0-47 38-88 84-88 35 0 65 17 86 46 21-29 51-46 86-46 46 0 84 41 84 88 0 89-43 130-176 238l-32 29z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="cardio-section">
                        <div class="cardio-card">
                            <div class="mini-tag">Cardiologia</div>
                            <h3>Cuide do seu coração com informação e prevenção.</h3>
                            <p>O coração é o centro da saúde do corpo. Quando ele funciona bem, a circulação, a energia e a qualidade de vida melhoram de forma visível. Cuidar da pressão, do colesterol, do sono e da atividade física faz diferença todos os dias.</p>
                        </div>

                        <div class="cardio-stack">
                            <div class="pill-info">
                                <h4>Prevenção</h4>
                                <p>Hábitos simples e consistentes reduzem muito o risco de problemas cardiovasculares ao longo dos anos.</p>
                            </div>
                            <div class="pill-info">
                                <h4>Diagnóstico</h4>
                                <p>Exames regulares ajudam a identificar alterações precocemente, antes que se tornem complicações.</p>
                            </div>
                        </div>

                        <div class="cardio-card">
                            <div class="mini-tag">Saúde</div>
                            <h3>O seu ritmo importa.</h3>
                            <p>Uma rotina com alimentação equilibrada, controle do estresse e acompanhamento médico fortalece o bem-estar cardiovascular e melhora a autonomia do paciente.</p>
                        </div>
                    </div>

                    <div class="cardio-lists">
                        <div class="cardio-list-item">
                            <div class="cardio-list-badge"><i class="fas fa-heartbeat"></i></div>
                            <div>
                                <h4>Hipertensão arterial</h4>
                                <p>A pressão alta costuma ser silenciosa, mas pode danificar vasos, rins e cérebro ao longo do tempo. O controle pode evitar complicações sérias.</p>
                            </div>
                        </div>
                        <div class="cardio-list-item">
                            <div class="cardio-list-badge"><i class="fas fa-wave-square"></i></div>
                            <div>
                                <h4>Arritmias</h4>
                                <p>Alterações no ritmo podem causar palpitações, tontura e sensação de aperto. O diagnóstico precoce ajuda a reduzir riscos e orientar o tratamento certo.</p>
                            </div>
                        </div>
                        <div class="cardio-list-item">
                            <div class="cardio-list-badge"><i class="fas fa-stethoscope"></i></div>
                            <div>
                                <h4>Colesterol e circulação</h4>
                                <p>O acúmulo de placas nas artérias pode diminuir o fluxo sanguíneo e aumentar as chances de AVC e infarto. O cuidado diário faz diferença.</p>
                            </div>
                        </div>
                        <div class="cardio-list-item">
                            <div class="cardio-list-badge"><i class="fas fa-file-medical"></i></div>
                            <div>
                                <h4>Exames e acompanhamento</h4>
                                <p>Eletrocardiograma, ecocardiograma e exames laboratoriais ajudam a identificar riscos e acompanhar a evolução do tratamento de forma segura.</p>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            'exames':       <?php echo json_encode($examesHtml, JSON_UNESCAPED_UNICODE); ?>,
            'informacoes':  <?php echo json_encode($informacoesHtml, JSON_UNESCAPED_UNICODE); ?>,
            'suporte':      <?php echo json_encode($suporteHtml, JSON_UNESCAPED_UNICODE); ?>
        };

        // ============================================================
        // FUNÇÕES DE NAVEGAÇÃO
        // ============================================================
        function changePage(page) {
            const normalizedPage = page === 'inicio' ? 'monitoramento' : page;
            const url = new URL(window.location.href);
            url.searchParams.set('page', normalizedPage);
            window.history.pushState({}, '', url);
            const titles = {
                'monitoramento': 'Monitoramento',
                'blog': 'Blog',
                'exames': 'Exames',
                'informacoes': 'Informações',
                'suporte': 'Suporte'
            };
            document.getElementById('pageTitle').innerText = titles[normalizedPage] || 'Monitoramento';
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            const activeItem = Array.from(document.querySelectorAll('.nav-item')).find(item => item.getAttribute('onclick') === `changePage('${normalizedPage}')`);
            if (activeItem) activeItem.classList.add('active');
            loadContent(normalizedPage);
        }

        function openArticle(articleId, event) {
            if (event) event.stopPropagation();
            const article = articlesData[articleId];
            if (!article) return;
            const contentArea = document.getElementById('contentArea');
            contentArea.innerHTML = `
                <div class="article-container">
                    <div class="article-back-btn" onclick="changePage('blog')"><i class="fas fa-arrow-left"></i> Voltar aos artigos</div>
                    <div class="article-header"><h1 class="article-title">${article.title}</h1><div class="article-meta"><div class="article-meta-item"><i class="fas fa-calendar"></i> ${article.date}</div></div></div>
                    <div class="article-content">${article.content}</div>
                    <div style="margin-top:40px; padding-top:20px; border-top:2px solid #f0f0f0;"><div class="article-back-btn" onclick="changePage('blog')"><i class="fas fa-arrow-left"></i> Voltar aos artigos</div></div>
                </div>
            `;
            document.getElementById('pageTitle').innerText = 'Artigo';
        }

        function loadContent(page) {
            const contentArea = document.getElementById('contentArea');
            const normalizedPage = page === 'inicio' ? 'monitoramento' : page;
            const html = sectionHtml[normalizedPage] || sectionHtml['monitoramento'];
            contentArea.innerHTML = html;
        }

        // ============================================================
        // CHAT
        // ============================================================
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const msg = input.value.trim();
            if (!msg) return;
            addMessage(msg, 'user');
            input.value = '';
            setTimeout(() => {
                const response = getBotResponse(msg);
                addMessage(response, 'bot');
            }, 500);
        }
        function addMessage(text, sender) {
            const container = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = `message ${sender}`;
            const avatar = sender === 'user' ? '<div class="message-avatar"><i class="fas fa-user"></i></div>' : '<div class="message-avatar"><i class="fas fa-robot"></i></div>';
            div.innerHTML = avatar + `<div class="message-bubble">${text}</div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }
        function getBotResponse(msg) {
            const m = msg.toLowerCase();
            if (m.includes('olá') || m.includes('oi')) return 'Olá! Como posso ajudar? 💙';
            if (m.includes('pressão')) return 'A pressão ideal é abaixo de 120/80 mmHg. Mantenha uma alimentação saudável!';
            if (m.includes('consulta')) return 'Para agendar uma consulta, acesse o menu "Consultas" ou ligue para (11) 4002-8922.';
            if (m.includes('exame')) return 'Seus exames ficam disponíveis na seção "Exames" após liberação médica.';
            return 'Entendi! Para mais informações, leia nossos artigos no blog ou acesse o suporte. 💙';
        }

        // ============================================================
        // MODAL DE AGENDAMENTO
        // ============================================================
        function openConsultaModal() {
            const modal = document.getElementById('consultaModal');
            modal.style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dataConsulta').min = today;
            const horaSelect = document.getElementById('horaConsulta');
            horaSelect.innerHTML = '<option value="">Selecione um horário</option>';
            document.getElementById('medicSelect').onchange = carregarHorariosDisponiveis;
            document.getElementById('dataConsulta').onchange = carregarHorariosDisponiveis;
        }

        function closeConsultaModal() {
            document.getElementById('consultaModal').style.display = 'none';
            document.getElementById('consultaForm').reset();
            document.getElementById('horaConsulta').innerHTML = '<option value="">Selecione um horário</option>';
        }

        function carregarHorariosDisponiveis() {
            const medicoSelect = document.getElementById('medicSelect');
            const data = document.getElementById('dataConsulta').value;
            const horaSelect = document.getElementById('horaConsulta');
            horaSelect.innerHTML = '<option value="">Selecione um horário</option>';
            if (!medicoSelect.value || !data) return;

            const medicoNome = medicoSelect.value.split('|')[0];
            const todosHorarios = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];

            fetch(`api/horarios_ocupados.php?medico=${encodeURIComponent(medicoNome)}&data=${data}`)
                .then(response => response.json())
                .then(ocupados => {
                    const disponiveis = todosHorarios.filter(h => !ocupados.includes(h));
                    if (disponiveis.length === 0) {
                        horaSelect.innerHTML = '<option value="">Nenhum horário disponível nesta data</option>';
                        return;
                    }
                    disponiveis.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h;
                        opt.textContent = h;
                        horaSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    todosHorarios.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h;
                        opt.textContent = h;
                        horaSelect.appendChild(opt);
                    });
                });
        }

        document.addEventListener('click', function(event) {
            const modal = document.getElementById('consultaModal');
            if (modal && event.target === modal) closeConsultaModal();
        });

        function agendarConsulta(event) {
            event.preventDefault();
            const medicSelect = document.getElementById('medicSelect');
            const dataConsulta = document.getElementById('dataConsulta');
            const horaConsulta = document.getElementById('horaConsulta');
            const tipoConsulta = document.getElementById('tipoConsulta');
            const obsConsulta = document.getElementById('obsConsulta');

            if (!medicSelect.value || !dataConsulta.value || !horaConsulta.value || !tipoConsulta.value) {
                alert('Por favor, preencha todos os campos obrigatórios!');
                return;
            }

            const [nomeMedico, especialidade] = medicSelect.value.split('|');
            const idPaciente = '<?php echo $user_id; ?>';
            const nomePaciente = '<?php echo htmlspecialchars($user_name); ?>';

            fetch('api/solicitar_consulta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_paciente: idPaciente,
                    nome_paciente: nomePaciente,
                    medico: nomeMedico,
                    especialidade: especialidade,
                    data: dataConsulta.value,
                    hora: horaConsulta.value,
                    tipo: tipoConsulta.value,
                    observacoes: obsConsulta.value || ''
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success) {
                    alert('✓ Consulta solicitada com sucesso!');
                    closeConsultaModal();
                    location.reload();
                } else {
                    alert('❌ Erro: ' + (result.error || 'Erro desconhecido'));
                }
            })
            .catch(function(error) {
                alert('Erro ao solicitar consulta: ' + error.message);
            });
        }

        // ============================================================
        // CARREGAR CONTEÚDO INICIAL
        // ============================================================
        loadContent('<?php echo htmlspecialchars($page, ENT_QUOTES); ?>');
    </script>
</body>
</html>