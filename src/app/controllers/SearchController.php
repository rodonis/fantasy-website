<?php
declare(strict_types=1);

class SearchController {
    public function index(array $p): void {
        $q    = trim($_GET['q'] ?? '');
        $isHx = isset($_SERVER['HTTP_HX_REQUEST']);

        if ($q === '') {
            if ($isHx) { echo ''; return; }
            header('Location: /');
            exit;
        }

        $gmWhere = Auth::isGm() ? '' : 'AND pg.visibility = "public"';
        $results = Db::all(
            "SELECT slug, title, category,
                    MATCH(title, body_md) AGAINST(? IN BOOLEAN MODE) AS score
             FROM pages pg
             WHERE MATCH(title, body_md) AGAINST(? IN BOOLEAN MODE) $gmWhere
             ORDER BY score DESC LIMIT 20",
            [$q . '*', $q . '*']
        );

        if ($isHx) {
            require __DIR__ . '/../views/search-results.php';
            return;
        }

        $tweaks = get_tweaks();
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/search-results.php';
        require __DIR__ . '/../views/layout-close.php';
    }
}
