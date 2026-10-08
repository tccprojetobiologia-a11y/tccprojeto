<?php
require_once __DIR__ . '/../blog.php';

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
        $html .= '<div class="blog-brand-panel" onclick="openArticle(\'' . htmlspecialchars($firstId, ENT_QUOTES) . '\', event)"><div class="brand-copy"><span class="brand-kicker">CardioWeb</span><span class="brand-name">Cardio</span></div></div>';
        $html .= '<div class="blog-feature-card" onclick="openArticle(\'' . htmlspecialchars($firstId, ENT_QUOTES) . '\', event)"><div style="position:relative;"><img src="' . ($first['image'] ?? 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80') . '" alt="' . htmlspecialchars($first['title'] ?? 'CardioWeb') . '"><span class="feature-badge">Cardio</span></div><div class="feature-copy"><h3>' . ($first['title'] ?? 'CardioWeb') . '</h3><p>' . ($first['summary'] ?? '') . '</p></div></div>';
        $html .= '<div class="blog-side-card" onclick="openArticle(\'' . htmlspecialchars($secondId, ENT_QUOTES) . '\', event)"><img src="' . ($second['image'] ?? 'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=1200&q=80') . '" alt="' . htmlspecialchars($second['title'] ?? 'Saúde cardiovascular') . '"><div class="feature-copy"><h3>' . ($second['title'] ?? 'Nosso foco') . '</h3><p>' . ($second['summary'] ?? 'Ações preventivas e acompanhamento clínico para reduzir riscos cardíacos e melhorar a qualidade de vida.') . '</p></div></div>';
        $html .= '</div><div class="blog-bottom-grid">';

        foreach ($rest as $id => $article) {
            $html .= '<article class="blog-card" onclick="openArticle(\'' . htmlspecialchars((string) $id, ENT_QUOTES) . '\', event)">';
            $html .= '<img src="' . ($article['image'] ?? 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80') . '" alt="' . htmlspecialchars($article['title'] ?? 'CardioWeb') . '">';
            $html .= '<div class="blog-card-body"><div class="blog-card-meta"><span class="blog-badge">Cardio</span><span>' . ($article['date'] ?? 'Atualizado') . '</span></div><h3>' . ($article['title'] ?? '') . '</h3><p>' . ($article['summary'] ?? '') . '</p><div class="blog-card-actions"><span class="blog-link">Ler artigo <i class="fas fa-arrow-right"></i></span></div></div></article>';
        }

        $html .= '</div></div>';
        return $html;
    }
}
