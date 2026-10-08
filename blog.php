<?php
if (!function_exists('getBlogArticles')) {
    function getBlogArticles() {
        return [
            'hipertensao' => [
                'title' => 'Hipertensão arterial: o que ela faz com o corpo',
                'date' => 'Atualizado em 2026 • 8 min',
                'summary' => 'A pressão alta aumenta o esforço do coração e pode danificar artérias, rins, cérebro e olhos quando não é controlada.',
                'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80',
                'content' => '<p>A hipertensão arterial é a elevação persistente da pressão do sangue dentro das artérias. Ela geralmente é silenciosa, e por isso é chamada de "assassina silenciosa".</p><h3>O que ela faz no corpo</h3><p>Com a pressão elevada, as artérias ficam mais rígidas e menos elásticas. O coração passa a bater com mais força, o que pode levar ao aumento do músculo cardíaco e ao risco de insuficiência cardíaca, AVC e doença renal.</p><h3>Como melhorar</h3><p>Controle a pressão com acompanhamento médico, reduza o sal, prefira alimentos naturais, mantenha atividade física e acompanhe a medição em casa.</p>'
            ],
            'insuficiencia' => [
                'title' => 'Insuficiência cardíaca: o que acontece com o coração',
                'date' => 'Atualizado em 2026 • 9 min',
                'summary' => 'O coração continua funcionando, mas não consegue suprir a demanda do corpo com a mesma eficiência.',
                'image' => 'https://images.unsplash.com/photo-1538108149393-fbbd81895973?auto=format&fit=crop&w=1200&q=80',
                'content' => '<p>Insuficiência cardíaca não significa que o coração parou. Ela significa que ele está trabalhando menos bem para bombear sangue.</p><h3>Como a doença afeta o corpo</h3><p>Quando o bombeamento fica menos eficiente, o sangue pode se acumular em alguns setores, causando falta de ar, cansaço e inchaço nas pernas.</p><h3>Como viver melhor</h3><p>Siga a medicação prescrita, controle o sal, pesa-se diariamente e mantenha acompanhamento regular.</p>'
            ],
            'arritmia' => [
                'title' => 'Arritmias: quando o ritmo do coração muda',
                'date' => 'Atualizado em 2026 • 7 min',
                'summary' => 'A alteração no ritmo pode causar palpitações, tonturas e sensação de coração acelerado ou irregular.',
                'image' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=1200&q=80',
                'content' => '<p>Arritmias são alterações na formação ou condução do impulso elétrico que coordena os batimentos do coração.</p><h3>O que acontece</h3><p>Em vez de bater de forma regular, o coração pode acelerar, desacelerar ou ficar irregular.</p><h3>Como viver melhor</h3><p>Evite cafeína, não use remédios sem indicação e mantenha acompanhamento com o cardiologista.</p>'
            ],
            'colesterol' => [
                'title' => 'Colesterol e placas nas artérias',
                'date' => 'Atualizado em 2026 • 6 min',
                'summary' => 'O excesso de gordura no sangue favorece a formação de placas que podem estreitar e bloquear as artérias.',
                'image' => 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&w=1200&q=80',
                'content' => '<p>O colesterol é essencial para o corpo, mas em excesso pode se acumular na parede das artérias.</p><h3>Como isso afeta o coração</h3><p>Se as artérias do coração ficam obstruídas, o fluxo de sangue pode diminuir, aumentando o risco de angina, infarto e AVC.</p><h3>Como melhorar</h3><p>Alimentação com menos ultraprocessados, atividade física regular e adesão à medicação.</p>'
            ],
            'exames' => [
                'title' => 'Como interpretar os exames cardiológicos',
                'date' => 'Atualizado em 2026 • 8 min',
                'summary' => 'Os exames ajudam a entender a estrutura, a função e o ritmo do coração.',
                'image' => 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&w=1200&q=80',
                'content' => '<p>Os exames cardiológicos ajudam a responder perguntas importantes sobre o coração.</p><h3>Exames mais comuns</h3><p>Eletrocardiograma, ecocardiograma, Holter e exames laboratoriais.</p><h3>O que o médico considera</h3><p>Um exame normal não descarta completamente um problema, e um exame alterado nem sempre significa doença grave.</p>'
            ]
        ];
    }
}

if (!function_exists('getDashboardBlogHtml')) {
    function getDashboardBlogHtml() {
        $articles = getBlogArticles();
        $html = '<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">';
        foreach ($articles as $id => $a) {
            $html .= '<div style="background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05); cursor:pointer;" onclick="openArticle(\'' . $id . '\')">';
            $html .= '<img src="' . htmlspecialchars($a['image']) . '" style="width:100%; height:180px; object-fit:cover;">';
            $html .= '<div style="padding:18px;">';
            $html .= '<div style="font-size:11px; color:#8a6770; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">' . htmlspecialchars($a['date']) . '</div>';
            $html .= '<h3 style="font-size:1.05rem; color:#1e2a3a; margin-bottom:8px;">' . htmlspecialchars($a['title']) . '</h3>';
            $html .= '<p style="color:#475569; font-size:0.88rem; line-height:1.5;">' . htmlspecialchars($a['summary']) . '</p>';
            $html .= '</div></div>';
        }
        $html .= '</div>';
        return $html;
    }
}