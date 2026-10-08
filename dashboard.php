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

// Conteúdo padrão (Início)
$page = $_GET['page'] ?? 'inicio';
require_once __DIR__ . '/blog.php';
require_once __DIR__ . '/consultas.php';
require_once __DIR__ . '/exames.php';
require_once __DIR__ . '/informacoes.php';

if (!function_exists('getDashboardBlogHtml')) {
    function getDashboardBlogHtml()
    {
        $articles = getBlogArticles();
        $keys = array_keys($articles);
        $firstId = $keys[0] ?? 'hipertensao';
        $secondId = $keys[1] ?? $firstId;
        $first = $articles[$firstId] ?? [];
        $second = $articles[$secondId] ?? [];
        $rest = array_filter($articles, function ($key) use ($firstId, $secondId) {
            return $key !== $firstId && $key !== $secondId;
        }, ARRAY_FILTER_USE_KEY);

        $html = '<div class="blog-shell">';
        $html .= '<div class="blog-feature-grid">';
        $html .= '<div class="blog-brand-panel" data-article-id="' . htmlspecialchars($firstId, ENT_QUOTES) . '"><div class="brand-copy"><span class="brand-kicker">CardioWeb</span><span class="brand-name">Cardio</span></div></div>';
        $html .= '<div class="blog-feature-card" data-article-id="' . htmlspecialchars($firstId, ENT_QUOTES) . '"><div style="position:relative;"><img src="' . ($first['image'] ?? 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80') . '" alt="' . htmlspecialchars($first['title'] ?? 'CardioWeb') . '"><span class="feature-badge">Cardio</span></div><div class="feature-copy"><h3>' . ($first['title'] ?? 'CardioWeb') . '</h3><p>' . ($first['summary'] ?? '') . '</p></div></div>';
        $html .= '<div class="blog-side-card" data-article-id="' . htmlspecialchars($secondId, ENT_QUOTES) . '"><img src="' . ($second['image'] ?? 'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=1200&q=80') . '" alt="' . htmlspecialchars($second['title'] ?? 'Saúde cardiovascular') . '"><div class="feature-copy"><h3>' . ($second['title'] ?? 'Nosso foco') . '</h3><p>' . ($second['summary'] ?? 'Ações preventivas e acompanhamento clínico para reduzir riscos cardíacos e melhorar a qualidade de vida.') . '</p></div></div>';
        $html .= '</div><div class="blog-bottom-grid">';

        foreach ($rest as $id => $article) {
            $html .= '<article class="blog-card" data-article-id="' . htmlspecialchars((string) $id, ENT_QUOTES) . '">';
            $html .= '<img src="' . ($article['image'] ?? 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80') . '" alt="' . htmlspecialchars($article['title'] ?? 'CardioWeb') . '">';
            $html .= '<div class="blog-card-body"><div class="blog-card-meta"><span class="blog-badge">Cardio</span><span>' . ($article['date'] ?? 'Atualizado') . '</span></div><h3>' . ($article['title'] ?? '') . '</h3><p>' . ($article['summary'] ?? '') . '</p><div class="blog-card-actions"><span class="blog-link">Ler artigo <i class="fas fa-arrow-right"></i></span></div></div></article>';
        }

        $html .= '</div></div>';
        return $html;
    }
}

if (!function_exists('getDashboardAgendaHtml')) {
    function getDashboardAgendaHtml()
    {
        return getConsultasHtml();
    }
}

if (!function_exists('getDashboardExamesHtml')) {
    function getDashboardExamesHtml()
    {
        return getExamesHtml();
    }
}

if (!function_exists('getDashboardInformacoesHtml')) {
    function getDashboardInformacoesHtml()
    {
        return getInformacoesHtml();
    }
}

if (!function_exists('getDashboardMonitoramentoHtml')) {
    function getDashboardMonitoramentoHtml()
    {
        return '<div class="welcome-card"><h2>Bem-vindo de volta, ' . htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') . '! 👋</h2><p>Monitore sua saúde cardiológica em tempo real e mantenha seus exames em dia.</p></div><div class="stats-grid"><div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><h3>12</h3><p>Registros de saúde</p></div><div class="stat-card"><div class="stat-icon"><i class="fas fa-heartbeat"></i></div><h3>72</h3><p>Batimentos/min</p></div><div class="stat-card"><div class="stat-icon"><i class="fas fa-calendar-check"></i></div><h3>2</h3><p>Consultas agendadas</p></div><div class="stat-card"><div class="stat-icon"><i class="fas fa-trophy"></i></div><h3>85%</h3><p>Meta de saúde</p></div></div><div class="info-card"><h3><i class="fas fa-heart"></i> Últimos Registros</h3><div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;"><span>Pressão Arterial</span><span><strong>120/80 mmHg</strong></span><span style="color:#10b981;">Normal</span></div><div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;"><span>Colesterol Total</span><span><strong>180 mg/dL</strong></span><span style="color:#10b981;">Normal</span></div><div style="display:flex; justify-content:space-between; padding:12px 0;"><span>Glicemia</span><span><strong>95 mg/dL</strong></span><span style="color:#10b981;">Normal</span></div></div><div class="info-card"><h3><i class="fas fa-calendar-alt"></i> Agenda</h3><div style="display:flex; align-items:center; gap:15px; padding:12px 0;"><div style="min-width:50px; text-align:center;"><div style="font-size:20px; font-weight:700; color:#851e32;">15</div><div style="font-size:11px; color:#666;">ABR</div></div><div style="flex:1;"><div style="font-weight:600;">Cardiologista - Dr. Carlos</div><div style="font-size:12px; color:#666;">10:00 - Consulta presencial</div></div><div style="display:flex; align-items:center; gap:8px;"><button type="button" style="font-size:11px; background:#e8f5e9; color:#2e7d32; padding:4px 10px; border:none; border-radius:20px; cursor:pointer;" onclick="editarConsulta(1)">Editar</button></div></div><div style="display:flex; align-items:center; gap:15px; padding:12px 0;"><div style="min-width:50px; text-align:center;"><div style="font-size:20px; font-weight:700; color:#851e32;">22</div><div style="font-size:11px; color:#666;">ABR</div></div><div style="flex:1;"><div style="font-weight:600;">Exame de Rotina</div><div style="font-size:12px; color:#666;">08:30 - Laboratório</div></div><div style="display:flex; align-items:center; gap:8px;"><button type="button" style="font-size:11px; background:#fff3e0; color:#ff9800; padding:4px 10px; border:none; border-radius:20px; cursor:pointer;" onclick="editarConsulta(2)">Editar</button></div></div></div>';
    }
}

if (!function_exists('getDashboardSuporteHtml')) {
    function getDashboardSuporteHtml()
    {
        return '<div class="info-card"><h3><i class="fas fa-headset"></i> Central de Suporte</h3><p>Estamos aqui para ajudar! Escolha uma opção abaixo:</p></div><div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#e8f5e9; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-phone" style="color:#2e7d32; font-size:24px;"></i></div><div><div style="font-weight:600;">Atendimento Telefônico</div><div style="font-size:12px; color:#666;">Segunda a Sexta, 8h às 18h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">(11) 4002-8922</div></div></div></div><div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#e3f2fd; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-envelope" style="color:#1976d2; font-size:24px;"></i></div><div><div style="font-weight:600;">E-mail</div><div style="font-size:12px; color:#666;">Respondemos em até 24h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">suporte@cardioweb.com</div></div></div></div><div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#fff3e0; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-whatsapp" style="color:#25d366; font-size:28px;"></i></div><div><div style="font-weight:600;">WhatsApp</div><div style="font-size:12px; color:#666;">Atendimento 24h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">(11) 9 9999-9999</div></div></div></div><div class="info-card"><h3><i class="fas fa-question-circle"></i> Perguntas Frequentes</h3><details style="margin-bottom:10px;"><summary style="cursor:pointer; font-weight:500; padding:10px; background:#f8fafc; border-radius:8px;">Como agendar uma consulta?</summary><p style="padding:10px; color:#666;">Acesse o menu "Consultas" e clique em "Agendar nova consulta". Escolha o médico e horário disponível.</p></details><details style="margin-bottom:10px;"><summary style="cursor:pointer; font-weight:500; padding:10px; background:#f8fafc; border-radius:8px;">Como acessar meus exames?</summary><p style="padding:10px; color:#666;">Os exames ficam disponíveis na seção "Exames" após liberação do médico responsável.</p></details></div>';
    }
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
        /* ===== SEU CSS EXISTENTE (mantenha o mesmo) ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(214, 94, 120, 0.24), transparent 26%),
                radial-gradient(circle at bottom right, rgba(74, 144, 226, 0.08), transparent 32%),
                linear-gradient(135deg, #f9eef1 0%, #f4f8fb 38%, #eef4f8 100%);
            overflow: hidden;
            height: 100vh;
            color: #1e2a3a;
        }
        .app-container {
            display: flex;
            height: 100vh;
            width: 100%;
            position: relative;
        }
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2f0613 0%, #681427 42%, #4b0d20 100%);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 22px 0 50px rgba(90, 16, 29, 0.24);
            overflow-y: auto;
            position: relative;
        }
        .sidebar::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.08), transparent 34%);
            pointer-events: none;
        }
        .logo-area { padding: 28px 22px 22px; border-bottom: 1px solid rgba(255,255,255,0.09); margin-bottom: 18px; position: relative; z-index: 1; }
        .logo { display: flex; align-items: center; gap: 12px; }
        .logo-icon {
            background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.12));
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.28), 0 12px 28px rgba(0,0,0,0.18);
        }
        .logo-text h2 { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
        .logo-text p { font-size: 10px; opacity: 0.85; margin-top: 4px; letter-spacing: 0.08em; text-transform: uppercase; }
        .nav-menu { flex: 1; padding: 8px 18px 0; position: relative; z-index: 1; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            margin-bottom: 8px;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.28s ease;
            color: rgba(255,255,255,0.78);
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }
        .nav-item::before {
            content: "";
            position: absolute;
            left: -10px;
            top: 50%;
            width: 6px;
            height: 0;
            border-radius: 999px;
            background: linear-gradient(180deg, #fff, #f4c6d1);
            transform: translateY(-50%);
            transition: height 0.2s ease;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: white;
            transform: translateX(3px);
            border-color: rgba(255,255,255,0.06);
        }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(255,255,255,0.16), rgba(255,255,255,0.08));
            color: white;
            font-weight: 700;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08), 0 10px 22px rgba(0,0,0,0.12);
        }
        .nav-item.active::before { height: 62%; }
        .nav-item i { width: 24px; font-size: 18px; text-align: center; }
        .nav-item span { font-size: 15px; }
        .user-section {
            padding: 18px 18px 20px;
            margin: 18px;
            background: linear-gradient(135deg, rgba(255,255,255,0.14), rgba(255,255,255,0.06));
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            margin-top: auto;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(12px);
        }
        .user-avatar {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #ffe8ee, rgba(255,255,255,0.3));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 12px;
            color: #4a0d1f;
            box-shadow: 0 10px 22px rgba(0,0,0,0.1);
        }
        .user-name { font-weight: 700; font-size: 16px; margin-bottom: 4px; }
        .user-email { font-size: 11px; opacity: 0.8; margin-bottom: 14px; word-break: break-all; }
        .logout-btn {
            background: rgba(255,255,255,0.12);
            color: white;
            padding: 10px 12px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.08);
            font-weight: 600;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.18); transform: translateY(-1px); }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: linear-gradient(180deg, #f8f5f5 0%, #f1f7fb 100%); }
        .main-header {
            background: rgba(255,255,255,0.78);
            backdrop-filter: blur(14px);
            padding: 22px 30px;
            border-bottom: 1px solid rgba(180, 190, 205, 0.35);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 24px rgba(15,23,42,0.04);
        }
        .page-title { font-size: 24px; font-weight: 800; color: #1e2a3a; letter-spacing: -0.04em; }
        .header-actions { display: flex; gap: 15px; }
        .header-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #f1f5f9, #edf2f7);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #475569;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
        }
        .header-icon:hover { background: #e2e8f0; transform: translateY(-2px); }
        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 28px 30px 42px;
            position: relative;
        }
        .content-area::before {
            content: "";
            position: fixed;
            inset: 90px auto auto 270px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(133, 30, 50, 0.04);
            filter: blur(20px);
            pointer-events: none;
        }
        .welcome-card {
            background: linear-gradient(135deg, #851e32 0%, #a92f49 42%, #6d1528 100%);
            color: white;
            padding: 30px 32px;
            border-radius: 24px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 48px rgba(133, 30, 50, 0.22);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .welcome-card::before {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            right: -60px;
            top: -70px;
            background: rgba(255,255,255,0.07);
            border-radius: 50%;
        }
        .welcome-card::after {
            content: "";
            position: absolute;
            left: -20px;
            bottom: -40px;
            width: 160px;
            height: 160px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .welcome-card h2 { font-size: 30px; margin-bottom: 10px; position: relative; z-index: 1; }
        .welcome-card p { position: relative; z-index: 1; opacity: 0.94; font-size: 15px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .stat-card {
            background: rgba(255,255,255,0.9);
            padding: 22px 20px;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15,23,42,0.06);
            transition: all 0.28s ease;
            border: 1px solid rgba(148,163,184,0.12);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: "";
            position: absolute;
            inset: auto auto 0 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #851e32, #f1b3bf);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 36px rgba(15,23,42,0.12);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #fff1f3, #fce7ed);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            color: #851e32;
            margin-bottom: 16px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
        }
        .stat-card h3 { font-size: 30px; color: #1e2a3a; margin-bottom: 6px; letter-spacing: -0.05em; }
        .stat-card p { color: #64748b; font-size: 14px; }
        .info-card {
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(148,163,184,0.12);
            border-radius: 20px;
            padding: 24px 22px;
            margin-bottom: 22px;
            box-shadow: 0 12px 28px rgba(15,23,42,0.04);
            position: relative;
            overflow: hidden;
        }
        .info-card h3 {
            color: #1e2a3a;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eef2f7;
            font-size: 20px;
        }
        .blog-post {
            padding: 16px 14px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 12px;
        }
        .blog-post:hover { background: #faf7f8; transform: translateX(3px); }
        .blog-title { font-weight: 700; color: #1e2a3a; margin-bottom: 6px; }
        .blog-date { font-size: 12px; color: #94a3b8; }
        .support-card {
            background: linear-gradient(180deg, #f8fafc 0%, #f7f2f4 100%);
            padding: 18px 18px;
            border-radius: 16px;
            margin-bottom: 16px;
            border: 1px solid #edf2f7;
            box-shadow: 0 10px 20px rgba(15,23,42,0.03);
        }
        .blog-shell {
            display: flex;
            flex-direction: column;
            gap: 26px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .blog-feature-grid {
            display: grid;
            grid-template-columns: 1.45fr 1fr 1fr;
            gap: 24px;
            align-items: stretch;
        }
        .blog-brand-panel {
            position: relative;
            display: flex;
            align-items: flex-end;
            min-height: 350px;
            padding: 34px 26px 30px;
            background: linear-gradient(135deg, #f9dd17 0%, #f2d000 100%);
            clip-path: polygon(0 0, 86% 0, 100% 100%, 0 100%);
            box-shadow: 0 18px 32px rgba(133, 30, 50, 0.12);
            border: 1px solid rgba(133, 30, 50, 0.05);
            overflow: hidden;
            cursor: pointer;
        }
        .blog-brand-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.18));
            pointer-events: none;
        }
        .brand-copy {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            color: #1a1a1a;
        }
        .brand-kicker {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1b1b1b;
        }
        .brand-name {
            font-size: clamp(3rem, 5vw, 5.5rem);
            font-weight: 900;
            line-height: 0.88;
            letter-spacing: -0.08em;
            color: #0d0d0d;
        }
        .blog-feature-card,
        .blog-side-card {
            background: rgba(255,255,255,0.94);
            border: 1px solid rgba(148,163,184,0.15);
            box-shadow: 0 12px 26px rgba(15,23,42,0.06);
            overflow: hidden;
            border-radius: 0;
        }
        .blog-feature-card {
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        .blog-feature-card img,
        .blog-side-card img {
            width: 100%;
            height: 215px;
            object-fit: cover;
            display: block;
            background: #e2e8f0;
        }
        .blog-feature-card .feature-badge,
        .blog-side-card .feature-badge {
            position: absolute;
            left: 18px;
            bottom: 18px;
            background: rgba(133, 30, 50, 0.9);
            color: white;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 8px 10px;
            border-radius: 10px;
            font-weight: 800;
        }
        .blog-feature-card .feature-copy,
        .blog-side-card .feature-copy {
            padding: 19px 18px 16px;
        }
        .blog-feature-card h3,
        .blog-side-card h3 {
            margin: 0 0 10px;
            font-size: 1.3rem;
            line-height: 1.15;
            letter-spacing: -0.04em;
            color: #1e2a3a;
        }
        .blog-feature-card p,
        .blog-side-card p {
            margin: 0;
            color: #475569;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        .blog-side-card {
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        .blog-side-card img {
            height: 180px;
        }
        .blog-bottom-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }
        .blog-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(148,163,184,0.14);
            box-shadow: 0 12px 26px rgba(15,23,42,0.05);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .blog-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 32px rgba(15,23,42,0.08);
        }
        .blog-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            background: #e2e8f0;
        }
        .blog-card-body {
            padding: 18px 15px 16px;
        }
        .blog-card-meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .blog-badge {
            background: rgba(133, 30, 50, 0.08);
            color: #851e32;
            padding: 6px 9px;
            border-radius: 999px;
            font-weight: 800;
        }
        .blog-card h3 {
            font-size: 1.05rem;
            line-height: 1.2;
            color: #1e2a3a;
            margin: 0 0 6px;
            letter-spacing: -0.04em;
        }
        .blog-card p {
            color: #475569;
            line-height: 1.5;
            margin: 0;
            font-size: 0.86rem;
        }
        .blog-card-actions {
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .blog-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #851e32;
            font-weight: 800;
            text-decoration: none;
            font-size: 0.82rem;
        }
        .blog-link:hover { opacity: 0.85; }
        .article-container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255,255,255,0.96);
            border-radius: 22px;
            padding: 32px 30px;
            box-shadow: 0 14px 34px rgba(15,23,42,0.07);
            border: 1px solid rgba(148,163,184,0.15);
        }
        .article-hero-image {
            width: 100%;
            max-height: 320px;
            object-fit: cover;
            border-radius: 18px;
            margin: 20px 0 26px;
            display: block;
        }
        .article-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #851e32;
            margin-bottom: 20px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 700;
        }
        .article-back-btn:hover { background: #f9f1f3; }
        .article-header { margin-bottom: 26px; border-bottom: 1px solid #eef2f7; padding-bottom: 18px; }
        .article-title { font-size: 32px; font-weight: 800; color: #1e2a3a; margin-bottom: 10px; line-height: 1.25; }
        .article-meta { display: flex; gap: 20px; color: #64748b; font-size: 14px; }
        .article-meta-item { display: flex; align-items: center; gap: 5px; }
        .article-content { line-height: 1.8; color: #334155; font-size: 16px; }
        .article-content p { margin-bottom: 20px; text-align: justify; }
        .article-content h3 { font-size: 20px; font-weight: 700; color: #1e2a3a; margin: 28px 0 12px 0; }
        .article-image-inline { max-width: 300px; height: auto; border-radius: 12px; margin: 15px 15px 15px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .article-image-inline.right { float: right; margin-left: 15px; margin-right: 0; }
        .cursor-pointer { cursor: pointer; }
        .chat-sidebar {
            width: 350px;
            background: linear-gradient(180deg, #fff9f9 0%, #fff3f5 100%);
            border-left: 1px solid #f3d8de;
            display: flex;
            flex-direction: column;
            box-shadow: -10px 0 26px rgba(133,30,50,0.06);
        }
        .chat-header {
            padding: 18px 20px;
            border-bottom: 1px solid #f3d8de;
            background: linear-gradient(135deg, #7a1e31 0%, #a22a44 100%);
            color: white;
        }
        .chat-header h3 { font-size: 18px; display: flex; align-items: center; gap: 10px; }
        .chat-header p { font-size: 12px; opacity: 0.9; margin-top: 5px; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px; }
        .message { display: flex; gap: 12px; max-width: 90%; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .message.user { align-self: flex-end; flex-direction: row-reverse; }
        .message-avatar { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .message.user .message-avatar { background: #851e32; color: white; }
        .message.bot .message-avatar { background: #10b981; color: white; }
        .message-bubble {
            background: #f1f5f9;
            padding: 11px 14px;
            border-radius: 18px;
            font-size: 13px;
            line-height: 1.45;
            color: #1e2a3a;
            box-shadow: 0 4px 12px rgba(15,23,42,0.04);
        }
        .message.user .message-bubble { background: linear-gradient(135deg, #851e32 0%, #9c2f47 100%); color: white; }
        .chat-input-area {
            padding: 16px 18px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px;
            background: rgba(255,255,255,0.7);
        }
        .chat-input {
            flex: 1;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            outline: none;
            font-family: inherit;
            background: white;
            transition: all 0.2s ease;
        }
        .chat-input:focus { border-color: #851e32; box-shadow: 0 0 0 4px rgba(133,30,50,0.08); }
        .chat-send {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, #851e32, #a22d46);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 18px rgba(133,30,50,0.2);
        }
        .chat-send:hover { transform: translateY(-2px) scale(1.02); box-shadow: 0 12px 22px rgba(133,30,50,0.28); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.55); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000; animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-content {
            background: rgba(255,255,255,0.98);
            border-radius: 24px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 32px 70px rgba(15,23,42,0.2);
            animation: slideUp 0.3s ease-out;
            border: 1px solid rgba(148,163,184,0.18);
        }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header {
            padding: 22px 24px;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h2 { font-size: 22px; font-weight: 800; color: #1e2a3a; margin: 0; display: flex; align-items: center; gap: 10px; }
        .modal-close { background: none; border: none; font-size: 28px; color: #999; cursor: pointer; transition: color 0.3s; }
        .modal-close:hover { color: #851e32; }
        .modal-body { padding: 26px 24px 24px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; color: #1e2a3a; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea {
            font-family: inherit;
            border: 1px solid #dfe7ef;
            border-radius: 12px;
            transition: all 0.2s ease;
            width: 100%;
            padding: 12px 14px;
            background: #f8fafc;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #851e32;
            background: white;
            box-shadow: 0 0 0 4px rgba(133,30,50,0.08);
        }
        .contact-btn {
            background: linear-gradient(135deg, #851e32 0%, #a82740 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
            box-shadow: 0 12px 22px rgba(133,30,50,0.18);
        }
        .contact-btn:hover {
            background: linear-gradient(135deg, #a82740 0%, #701826 100%);
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(133,30,50,0.25);
        }
        .contact-btn:active { transform: translateY(0); }
        @media (max-width: 1000px) { .chat-sidebar { width: 300px; } }
        @media (max-width: 800px) { .chat-sidebar { display: none; } }
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #030712 0%, #111827 45%, #0f172a 100%);
            }
            .main-content { background: #0b1220; }
            .main-header { background: rgba(15,23,42,0.8); border-color: rgba(148,163,184,0.18); }
            .page-title, .stat-card h3, .info-card h3, .article-title, .blog-title { color: #e2e8f0; }
            .stat-card, .info-card, .article-container, .support-card { background: rgba(15,23,42,0.9); border-color: rgba(148,163,184,0.12); }
            .welcome-card { background: linear-gradient(135deg, #851e32 0%, #a62d45 100%); }
            .stat-card p, .blog-date, .article-meta, .article-content, .support-card, .form-group label { color: #cbd5e1; }
            .chat-input, .form-group input, .form-group select, .form-group textarea {
                background: rgba(15,23,42,0.85);
                border-color: rgba(148,163,184,0.18);
                color: #e2e8f0;
            }
        }
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
                <div class="nav-item <?php echo $page == 'inicio' || $page == 'monitoramento' ? 'active' : ''; ?>" data-page="inicio" onclick="changePage('inicio')">
                    <i class="fas fa-heartbeat"></i><span>Monitoramento</span>
                </div>
                <div class="nav-item <?php echo $page == 'agenda' || $page == 'consultas' ? 'active' : ''; ?>" data-page="agenda" onclick="changePage('agenda')">
                    <i class="fas fa-calendar-alt"></i><span>Agenda</span>
                </div>
                <div class="nav-item <?php echo $page == 'blog' ? 'active' : ''; ?>" data-page="blog" onclick="changePage('blog')">
                    <i class="fas fa-newspaper"></i><span>Blog</span>
                </div>
                <div class="nav-item <?php echo $page == 'exames' ? 'active' : ''; ?>" data-page="exames" onclick="changePage('exames')">
                    <i class="fas fa-flask"></i><span>Exames</span>
                </div>
                <div class="nav-item <?php echo $page == 'informacoes' ? 'active' : ''; ?>" data-page="informacoes" onclick="changePage('informacoes')">
                    <i class="fas fa-info-circle"></i><span>Informações</span>
                </div>
                <div class="nav-item <?php echo $page == 'suporte' ? 'active' : ''; ?>" data-page="suporte" onclick="changePage('suporte')">
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
                            <select id="medicSelect" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                                <option value="">Selecione um médico</option>
                                <option value="Dr. Roberto Mendes|Cardiologia">Dr. Roberto Mendes - Cardiologia</option>
                                <option value="Dra. Aline Costa|Arritmologia">Dra. Aline Costa - Arritmologia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Data:</label>
                            <input type="date" id="dataConsulta" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Horário:</label>
                            <select id="horaConsulta" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                                <option value="">Selecione um horário</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-stethoscope"></i> Tipo de Consulta:</label>
                            <select id="tipoConsulta" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                                <option value="">Selecione o tipo</option>
                                <option value="Presencial">Presencial</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-file-alt"></i> Observações (opcional):</label>
                            <textarea id="obsConsulta" placeholder="Descreva os sintomas ou motivo da consulta..." style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px; resize:vertical; min-height:80px;"></textarea>
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
        const articlesData = <?php echo json_encode(getBlogArticles()); ?>;

        // ============================================================
        // FUNÇÕES DE NAVEGAÇÃO
        // ============================================================
        function normalizePage(page) {
            const aliases = {
                'inicio': 'inicio',
                'monitoramento': 'inicio',
                'agenda': 'agenda',
                'consultas': 'agenda',
                'blog': 'blog',
                'exames': 'exames',
                'informacoes': 'informacoes',
                'suporte': 'suporte'
            };
            return aliases[page] || 'inicio';
        }

        function changePage(page) {
            const normalizedPage = normalizePage(page);
            const url = new URL(window.location.href);
            url.searchParams.set('page', normalizedPage);
            window.history.pushState({}, '', url);
            const titles = {
                'inicio': 'Monitoramento',
                'blog': 'Blog',
                'agenda': 'Agenda',
                'exames': 'Exames',
                'informacoes': 'Informações',
                'suporte': 'Suporte'
            };
            document.getElementById('pageTitle').innerText = titles[normalizedPage] || 'Monitoramento';
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            const activeItem = document.querySelector(`.nav-item[data-page="${normalizedPage}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }
            loadContent(normalizedPage);
        }

        function bindBlogCardClicks() {
            const cards = document.querySelectorAll('[data-article-id]');
            cards.forEach((card) => {
                card.onclick = function (event) {
                    if (event) event.preventDefault();
                    if (event) event.stopPropagation();
                    const articleId = this.getAttribute('data-article-id');
                    if (articleId) {
                        openArticle(articleId);
                    }
                };
            });
        }

        function openArticle(articleId, event) {
            if (event) event.preventDefault();
            if (event) event.stopPropagation();
            const article = articlesData[articleId];
            if (!article) return;
            const contentArea = document.getElementById('contentArea');
            contentArea.innerHTML = `
                <div class="article-container">
                    <div class="article-back-btn" onclick="changePage('blog')"><i class="fas fa-arrow-left"></i> Voltar aos artigos</div>
                    <div class="article-header">
                        <h1 class="article-title">${article.title}</h1>
                        <div class="article-meta">
                            <div class="article-meta-item"><i class="fas fa-calendar"></i> ${article.date}</div>
                            <div class="article-meta-item"><i class="fas fa-heartbeat"></i> Cardiologia</div>
                        </div>
                    </div>
                    ${article.image ? `<img class="article-hero-image" src="${article.image}" alt="${article.title}">` : ''}
                    <div class="article-content">${article.content}</div>
                    <div style="margin-top:40px; padding-top:20px; border-top:2px solid #f0f0f0;"><div class="article-back-btn" onclick="changePage('blog')"><i class="fas fa-arrow-left"></i> Voltar aos artigos</div></div>
                </div>
            `;
            document.getElementById('pageTitle').innerText = 'Artigo';
        }

        function loadContent(page) {
            const contentArea = document.getElementById('contentArea');
            const normalizedPage = normalizePage(page);

            if (normalizedPage === 'inicio') {
                contentArea.innerHTML = `
                    <div class="welcome-card">
                        <h2>Bem-vindo de volta, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
                        <p>Monitore sua saúde cardiológica em tempo real e mantenha seus exames em dia.</p>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><h3>12</h3><p>Registros de saúde</p></div>
                        <div class="stat-card"><div class="stat-icon"><i class="fas fa-heartbeat"></i></div><h3>72</h3><p>Batimentos/min</p></div>
                        <div class="stat-card"><div class="stat-icon"><i class="fas fa-calendar-check"></i></div><h3>2</h3><p>Consultas agendadas</p></div>
                        <div class="stat-card"><div class="stat-icon"><i class="fas fa-trophy"></i></div><h3>85%</h3><p>Meta de saúde</p></div>
                    </div>
                    <div class="info-card">
                        <h3><i class="fas fa-heart"></i> Últimos Registros</h3>
                        <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;"><span>Pressão Arterial</span><span><strong>120/80 mmHg</strong></span><span style="color:#10b981;">Normal</span></div>
                        <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;"><span>Colesterol Total</span><span><strong>180 mg/dL</strong></span><span style="color:#10b981;">Normal</span></div>
                        <div style="display:flex; justify-content:space-between; padding:12px 0;"><span>Glicemia</span><span><strong>95 mg/dL</strong></span><span style="color:#10b981;">Normal</span></div>
                    </div>
                    <div class="info-card">
                        <h3><i class="fas fa-calendar-alt"></i> Agenda</h3>
                        <div style="display:flex; align-items:center; gap:15px; padding:12px 0;">
                            <div style="min-width:50px; text-align:center;"><div style="font-size:20px; font-weight:700; color:#851e32;">15</div><div style="font-size:11px; color:#666;">ABR</div></div>
                            <div style="flex:1;"><div style="font-weight:600;">Cardiologista - Dr. Carlos</div><div style="font-size:12px; color:#666;">10:00 - Consulta presencial</div></div>
                            <div style="display:flex; align-items:center; gap:8px;"><button type="button" style="font-size:11px; background:#e8f5e9; color:#2e7d32; padding:4px 10px; border:none; border-radius:20px; cursor:pointer;" onclick="editarConsulta(1)">Editar</button></div>
                        </div>
                        <div style="display:flex; align-items:center; gap:15px; padding:12px 0;">
                            <div style="min-width:50px; text-align:center;"><div style="font-size:20px; font-weight:700; color:#851e32;">22</div><div style="font-size:11px; color:#666;">ABR</div></div>
                            <div style="flex:1;"><div style="font-weight:600;">Exame de Rotina</div><div style="font-size:12px; color:#666;">08:30 - Laboratório</div></div>
                            <div style="display:flex; align-items:center; gap:8px;"><button type="button" style="font-size:11px; background:#fff3e0; color:#ff9800; padding:4px 10px; border:none; border-radius:20px; cursor:pointer;" onclick="editarConsulta(2)">Editar</button></div>
                        </div>
                    </div>
                `;
            } else if (normalizedPage === 'blog') {
                contentArea.innerHTML = getDashboardBlogHtml();
                bindBlogCardClicks();
            } else if (normalizedPage === 'agenda') {
                contentArea.innerHTML = getDashboardAgendaHtml();
            } else if (normalizedPage === 'exames') {
                contentArea.innerHTML = getDashboardExamesHtml();
            } else if (normalizedPage === 'informacoes') {
                contentArea.innerHTML = getDashboardInformacoesHtml();
            } else if (normalizedPage === 'suporte') {
                contentArea.innerHTML = getDashboardSuporteHtml();
            } else {
                contentArea.innerHTML = getDashboardMonitoramentoHtml();
            }
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

        function editarConsulta(id) {
            const texto = prompt('Anote o que você precisa lembrar sobre essa consulta:', 'Consulta agendada');
            if (texto === null) return;
            const item = document.querySelectorAll('.info-card button[onclick^="editarConsulta"]')[id - 1];
            if (item) {
                item.textContent = 'Editado';
                item.style.background = '#dbeafe';
                item.style.color = '#1d4ed8';
            }
            console.log('Consulta ' + id + ' anotada:', texto);
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
            
            // Buscar horários ocupados do banco
            fetch(`api/horarios_ocupados.php?medico=${encodeURIComponent(medicoNome)}&data=${data}`)
                .then(response => response.json())
                .then(ocupados => {
                    const todosHorarios = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];
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
                    // Fallback: todos horários disponíveis
                    const todosHorarios = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];
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

        // ============================================================
        // AGENDAR CONSULTA - ENVIA PARA O BANCO DE DADOS
        // ============================================================
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

            // Enviar para o banco de dados via API
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
                    alert('✓ Consulta solicitada com sucesso!\nAguardando confirmação do médico.');
                    closeConsultaModal();
                    // Recarregar a página para atualizar a lista de consultas
                    location.reload();
                } else {
                    alert('❌ Erro: ' + result.error);
                }
            })
            .catch(function(error) {
                alert('Erro ao solicitar consulta: ' + error.message);
                console.error('Erro:', error);
            });
        }

        // ============================================================
        // CARREGAR CONTEÚDO INICIAL
        // ============================================================
        loadContent('<?php echo $page; ?>');
    </script>
</body>
</html>