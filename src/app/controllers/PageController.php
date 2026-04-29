<?php
declare(strict_types=1);

class PageController {

    public function home(array $p): void {
        $recent  = Db::all('SELECT slug, title, category, updated_at FROM pages WHERE visibility = "public" ORDER BY updated_at DESC LIMIT 10');
        $featured = Db::one('SELECT slug, title, body_md, category FROM pages WHERE visibility = "public" ORDER BY updated_at DESC LIMIT 1');
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/home.php';
        require __DIR__ . '/../views/layout-close.php';
    }

    public function view(array $p): void {
        $slug   = $p['slug'];
        $page   = Db::one('SELECT * FROM pages WHERE slug = ?', [$slug]);

        if (!$page) {
            if (Auth::isLoggedIn()) {
                header('Location: /wiki/' . $slug . '/create');
                exit;
            }
            http_response_code(404);
            require __DIR__ . '/../views/404.php';
            return;
        }

        if ($page['visibility'] === 'gm' && !Auth::isGm()) {
            http_response_code(404);
            require __DIR__ . '/../views/404.php';
            return;
        }

        $md       = new Markdown(Auth::isGm());
        $html     = $md->text($page['body_md']);
        $toc      = $this->buildToc($page['body_md']);
        $tags     = Db::all('SELECT tag FROM tags WHERE page_id = ?', [$page['id']]);
        $breadcrumbs = $this->buildBreadcrumbs($page);

        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/article.php';
        require __DIR__ . '/../views/layout-close.php';
    }

    public function edit(array $p): void {
        Auth::requireGm();
        $slug   = $p['slug'];
        $page   = Db::one('SELECT * FROM pages WHERE slug = ?', [$slug]);
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/edit.php';
        require __DIR__ . '/../views/layout-close.php';
    }

    public function create(array $p): void {
        Auth::requireGm();
        $slug   = $p['slug'];
        $page   = null;
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/edit.php';
        require __DIR__ . '/../views/layout-close.php';
    }

    public function save(array $p): void {
        Auth::requireGm();
        $slug  = $p['slug'];
        $title = trim($_POST['title'] ?? '');
        $body  = $_POST['body'] ?? '';
        $cat   = strtolower(trim($_POST['category'] ?? 'uncategorized'));
        $newCat = strtolower(trim($_POST['new_category'] ?? ''));
        if ($newCat !== '') $cat = $newCat;
        $cat   = preg_replace('/[^a-z0-9\- ]+/', '', $cat) ?: 'uncategorized';
        $vis   = Auth::isGm() ? ($_POST['visibility'] ?? 'public') : 'public';
        $comment = trim($_POST['comment'] ?? '');
        $tagsRaw = trim($_POST['tags'] ?? '');

        if (!$title || !$body) {
            header('Location: /wiki/' . $slug . '/edit');
            exit;
        }

        $existing = Db::one('SELECT id FROM pages WHERE slug = ?', [$slug]);
        $userId   = Auth::user()['id'];

        Db::run(
            'INSERT INTO categories (slug, name, created_by) VALUES (?,?,?) ON DUPLICATE KEY UPDATE name = VALUES(name)',
            [$cat, ucwords(str_replace(['-', '_'], ' ', $cat)), $userId]
        );

        if ($existing) {
            Db::run('UPDATE pages SET title=?, body_md=?, visibility=?, category=?, updated_by=? WHERE id=?',
                [$title, $body, $vis, $cat, $userId, $existing['id']]);
            Db::run('INSERT INTO revisions (page_id, body_md, user_id, comment) VALUES (?,?,?,?)',
                [$existing['id'], $body, $userId, $comment]);
            $pageId = $existing['id'];
        } else {
            Db::run('INSERT INTO pages (slug, title, body_md, visibility, category, updated_by) VALUES (?,?,?,?,?,?)',
                [$slug, $title, $body, $vis, $cat, $userId]);
            $pageId = (int)Db::get()->lastInsertId();
            Db::run('INSERT INTO revisions (page_id, body_md, user_id, comment) VALUES (?,?,?,?)',
                [$pageId, $body, $userId, 'Initial version']);
        }

        // Rebuild links
        Db::run('DELETE FROM links WHERE from_slug = ?', [$slug]);
        foreach (Markdown::extractWikiLinks($body) as $target) {
            try {
                Db::run('INSERT IGNORE INTO links (from_slug, to_slug) VALUES (?,?)', [$slug, $target]);
            } catch (PDOException) {}
        }

        // Rebuild tags from explicit tags field
        Db::run('DELETE FROM tags WHERE page_id = ?', [$pageId]);
        $tags = array_filter(array_map('trim', preg_split('/[,\n]/', $tagsRaw)));
        foreach ($tags as $tag) {
            $tag = preg_replace('/\s+/', ' ', $tag);
            if ($tag === '') continue;
            try { Db::run('INSERT IGNORE INTO tags (page_id, tag) VALUES (?,?)', [$pageId, $tag]); }
            catch (PDOException) {}
        }

        header('Location: /wiki/' . $slug);
        exit;
    }


    public function delete(array $p): void {
        Auth::requireGm();
        $slug = $p['slug'];
        $page = Db::one('SELECT id FROM pages WHERE slug = ?', [$slug]);
        if ($page) {
            Db::run('DELETE FROM pages WHERE id = ?', [$page['id']]);
        }
        header('Location: /');
        exit;
    }

    public function preview(array $p): void {
        $body = $_POST['body'] ?? '';
        $md   = new Markdown(Auth::isGm());
        echo $md->text($body);
    }

    public function history(array $p): void {
        $slug   = $p['slug'];
        $page   = Db::one('SELECT * FROM pages WHERE slug = ?', [$slug]);
        if (!$page) { http_response_code(404); require __DIR__ . '/../views/404.php'; return; }
        $revs = Db::all(
            'SELECT r.id, r.comment, r.created_at, u.display_name FROM revisions r
             LEFT JOIN users u ON u.id = r.user_id WHERE r.page_id = ? ORDER BY r.created_at DESC',
            [$page['id']]
        );
        require __DIR__ . '/../views/layout.php';
        require __DIR__ . '/../views/history.php';
        require __DIR__ . '/../views/layout-close.php';
    }

    public function backlinks(array $p): void {
        $slug = $p['slug'];
        $links = Db::all(
            'SELECT l.from_slug, pg.title FROM links l
             LEFT JOIN pages pg ON pg.slug = l.from_slug
             WHERE l.to_slug = ?',
            [$slug]
        );
        require __DIR__ . '/../views/partials/backlinks.php';
    }

    private function buildToc(string $md): array {
        preg_match_all('/^(#{2,3})\s+(.+)$/m', $md, $m, PREG_SET_ORDER);
        $toc = [];
        foreach ($m as $h) {
            $level = strlen($h[1]);
            $text  = trim($h[2]);
            $id    = preg_replace('/[^a-z0-9]+/', '-', strtolower($text));
            $toc[] = ['level' => $level, 'text' => $text, 'id' => $id];
        }
        return $toc;
    }

    private function buildBreadcrumbs(array $page): array {
        $crumbs = [['label' => 'Codex', 'href' => '/']];
        $cat    = $page['category'] ?? '';
        if ($cat !== '') {
            $crumbs[] = ['label' => ucwords(str_replace(['-', '_'], ' ', $cat)), 'href' => '/?cat=' . urlencode($cat)];
        }
        $crumbs[] = ['label' => $page['title'], 'href' => null];
        return $crumbs;
    }
}
