<?php
declare(strict_types=1);

class Markdown extends ParsedownExtra {
    private bool $gmRole = false;

    public function __construct(bool $gmRole = false) {
        parent::__construct();
        $this->gmRole = $gmRole;
        $this->BlockTypes[':'][] = 'FencedDiv';
        $this->setBreaksEnabled(false);
    }

    // ── [[wiki-link]] and [[slug|label]] pre-pass ──────────────────────────────

    public function text($text): string {
        $text = $this->rewriteWikiLinks($text);
        return parent::text($text);
    }

    private function rewriteWikiLinks(string $src): string {
        return preg_replace_callback('/\[\[([^\]]+)\]\]/', function (array $m): string {
            $inner = $m[1];
            if (str_contains($inner, '|')) {
                [$slug, $label] = explode('|', $inner, 2);
            } else {
                $slug  = $inner;
                $label = $inner;
            }
            $slug = self::slugify($slug);
            $exists = Db::one('SELECT id FROM pages WHERE slug = ?', [$slug]);
            $class = $exists ? '' : ' class="wiki-redlink"';
            return '<a href="/wiki/' . htmlspecialchars($slug) . '"' . $class . '>'
                 . htmlspecialchars($label) . '</a>';
        }, $src);
    }

    // ── ::: fenced div blocks ──────────────────────────────────────────────────

    protected function blockFencedDiv(array $line): ?array {
        if (!preg_match('/^:::[ \t]*(\S+)(.*)$/', $line['text'], $m)) return null;
        return [
            'char'    => ':',
            'divType' => trim($m[1]),
            'attrs'   => trim($m[2]),
            'element' => ['name' => 'div', 'text' => ''],
            'lines'   => [],
        ];
    }

    protected function blockFencedDivContinue(array $line, array $block): ?array {
        if ($line['text'] === ':::') {
            $block['complete'] = true;
            return $block;
        }
        $block['lines'][] = $line['body'];
        return $block;
    }

    protected function blockFencedDivComplete(array $block): array {
        $inner = implode("\n", $block['lines']);
        $type  = $block['divType'];
        $attrs = $block['attrs'];

        $html = match(true) {
            $type === 'gm'        => $this->renderGmBlock($inner, $attrs),
            $type === 'statblock' => $this->renderStatblock($inner),
            $type === 'map'       => $this->renderMap($attrs, $inner),
            default               => '<div class="fenced-' . htmlspecialchars($type) . '">'
                                     . $this->text($inner) . '</div>',
        };

        return ['markup' => $html];
    }

    // ── GM Block ───────────────────────────────────────────────────────────────

    private function renderGmBlock(string $inner, string $attrs): string {
        $label    = trim(trim($attrs, '[]')) ?: 'GM Eyes Only';
        $reveal   = str_contains($attrs, '[reveal]');
        $isGm     = $this->gmRole;

        if ($isGm) {
            $body = $this->text($inner);
            return '<div class="gm-block">'
                 . '<div class="gm-tag">' . htmlspecialchars($label) . '</div>'
                 . '<div class="gm-body">' . $body . '</div>'
                 . '</div>';
        }

        if ($reveal) {
            $body = $this->text($inner);
            return '<div class="gm-block is-hidden" hx-on:click="this.classList.remove(\'is-hidden\')">'
                 . '<div class="gm-tag">' . htmlspecialchars($label) . '</div>'
                 . '<div class="gm-body">' . $body . '</div>'
                 . '</div>';
        }

        // Player sees no content at all
        return '<div class="gm-block is-hidden">'
             . '<div class="gm-tag">' . htmlspecialchars($label) . '</div>'
             . '</div>';
    }

    // ── Statblock (Pathfinder 1e) ──────────────────────────────────────────────

    private function renderStatblock(string $src): string {
        $sections = [];
        $current  = 'meta';
        $meta     = [];

        foreach (explode("\n", $src) as $line) {
            if (preg_match('/^#\s+(.+)$/', $line, $m)) {
                $current = strtolower(trim($m[1]));
                $sections[$current] = $sections[$current] ?? [];
                continue;
            }
            if ($current === 'meta' && str_contains($line, ':')) {
                [$k, $v] = explode(':', $line, 2);
                $meta[strtolower(trim($k))] = trim($v);
            } else {
                $sections[$current][] = $line;
            }
        }

        $name = htmlspecialchars($meta['name'] ?? 'Unknown');
        $cr   = htmlspecialchars($meta['cr']   ?? '—');
        $xp   = htmlspecialchars($meta['xp']   ?? '');
        $type = htmlspecialchars($meta['type']  ?? '');
        $init = htmlspecialchars($meta['init']  ?? '');
        $senses = htmlspecialchars($meta['senses'] ?? '');
        $aura   = htmlspecialchars($meta['aura']   ?? '');

        $stats = ['str','dex','con','int','wis','cha'];
        $statGrid = '';
        if (array_intersect($stats, array_keys($meta))) {
            $statGrid = '<div class="statblock-grid">';
            foreach ($stats as $s) {
                $val = $meta[$s] ?? '—';
                $mod = '—';
                if ($val !== '—' && is_numeric($val)) {
                    $n   = (int)$val;
                    $m2  = (int)floor(($n - 10) / 2);
                    $mod = ($m2 >= 0 ? '+' : '') . $m2;
                }
                $statGrid .= '<div><label>' . strtoupper($s) . '</label>'
                           . '<b>' . htmlspecialchars($val) . '</b>'
                           . '<small>' . htmlspecialchars($mod) . '</small></div>';
            }
            $statGrid .= '</div>';
        }

        $statsMeta = [];
        foreach (['bab','cmb','cmd','feats','skills','languages','gear'] as $k) {
            if (isset($meta[$k])) $statsMeta[$k] = $meta[$k];
        }

        $h = '<div class="statblock">';
        $h .= '<div class="statblock-head">';
        $h .= '<div><div class="statblock-name">' . $name . '</div></div>';
        $h .= '<div class="statblock-cr"><small>Challenge</small>CR ' . $cr;
        if ($xp) $h .= ' · ' . $xp . ' XP';
        $h .= '</div></div>';

        // Source row
        $sourceItems = [];
        if ($xp)     $sourceItems[] = '<span><b>XP</b> ' . htmlspecialchars($xp) . '</span>';
        if ($type)   $sourceItems[] = '<span><b>Type</b> ' . htmlspecialchars($type) . '</span>';
        if ($init)   $sourceItems[] = '<span><b>Init</b> ' . htmlspecialchars($init) . '</span>';
        if ($senses) $sourceItems[] = '<span><b>Senses</b> ' . htmlspecialchars($senses) . '</span>';
        if ($aura)   $sourceItems[] = '<span><b>Aura</b> ' . htmlspecialchars($aura) . '</span>';
        if ($sourceItems) {
            $h .= '<div class="statblock-source">' . implode('', $sourceItems) . '</div>';
        }

        // Sections
        $sectionOrder = ['defense','offense','statistics','special abilities'];
        $allSections  = array_unique(array_merge($sectionOrder, array_keys($sections)));

        foreach ($allSections as $sec) {
            if ($sec === 'meta' || !isset($sections[$sec])) continue;
            $lines = array_filter($sections[$sec], fn($l) => trim($l) !== '');
            if (!$lines && $sec === 'statistics' && $statGrid) {
                $h .= '<div class="statblock-section">' . ucwords($sec) . '</div>';
                $h .= $statGrid;
                foreach ($statsMeta as $k => $v) {
                    $h .= '<div class="statblock-row"><b>' . ucfirst($k) . '</b> ' . htmlspecialchars($v) . '</div>';
                }
                continue;
            }
            if (!$lines) continue;
            $h .= '<div class="statblock-section">' . ucwords($sec) . '</div>';
            if ($sec === 'statistics' && $statGrid) {
                $h .= $statGrid;
                foreach ($statsMeta as $k => $v) {
                    $h .= '<div class="statblock-row"><b>' . ucfirst($k) . '</b> ' . htmlspecialchars($v) . '</div>';
                }
                continue;
            }
            if ($sec === 'special abilities') {
                $h .= '<div class="statblock-special">' . $this->text(implode("\n", $lines)) . '</div>';
            } else {
                foreach ($lines as $row) {
                    if (!preg_match('/^([A-Za-z][^:]+):\s*(.+)$/', $row, $rm)) {
                        $h .= '<div class="statblock-row">' . htmlspecialchars($row) . '</div>';
                        continue;
                    }
                    $h .= '<div class="statblock-row"><b>' . htmlspecialchars($rm[1]) . '</b> '
                        . htmlspecialchars($rm[2]) . '</div>';
                }
            }
        }

        $h .= '</div>';
        return $h;
    }

    // ── Map Block ──────────────────────────────────────────────────────────────

    private function renderMap(string $attrLine, string $body): string {
        // Parse attrs: src=/path caption="..." scale="..."
        preg_match('/src=(\S+)/', $attrLine, $srcM);
        preg_match('/caption="([^"]*)"/', $attrLine, $capM);
        preg_match('/scale="([^"]*)"/', $attrLine, $scaleM);
        $src     = $srcM[1]   ?? null;
        $caption = $capM[1]   ?? null;
        $scale   = $scaleM[1] ?? null;

        $pins = [];
        foreach (explode("\n", $body) as $line) {
            if (!preg_match('/^pin\s+/', $line)) continue;
            preg_match('/x=([\d.]+)/',     $line, $xm);
            preg_match('/y=([\d.]+)/',     $line, $ym);
            preg_match('/label="([^"]*)"/',$line, $lm);
            preg_match('/cap=(\d)/',       $line, $cm);
            if (isset($xm[1], $ym[1])) {
                $pins[] = [
                    'x'   => (float)$xm[1],
                    'y'   => (float)$ym[1],
                    'lbl' => $lm[1] ?? '',
                    'cap' => isset($cm[1]) && $cm[1],
                ];
            }
        }

        $h = '<div class="wiki-map">';

        if ($src) {
            $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
            if ($ext === 'svg') {
                $h .= '<img src="' . htmlspecialchars($src) . '" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" alt="map" />';
            } else {
                $h .= '<img src="' . htmlspecialchars($src) . '" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" alt="map" />';
            }
        }

        if ($pins) {
            $h .= '<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="position:absolute;inset:0;width:100%;height:100%;">';
            foreach ($pins as $p) {
                $r    = $p['cap'] ? 1.2 : 0.9;
                $size = $p['cap'] ? 3.5 : 3;
                $h .= '<g class="pin">';
                $h .= '<circle class="dot" cx="' . $p['x'] . '" cy="' . $p['y'] . '" r="' . $r . '"/>';
                if ($p['lbl']) {
                    $class = $p['cap'] ? 'cap' : '';
                    $h .= '<text x="' . $p['x'] . '" y="' . ($p['y'] - $size) . '" class="' . $class . '">'
                        . htmlspecialchars($p['lbl']) . '</text>';
                }
                $h .= '</g>';
            }
            $h .= '</svg>';
        }

        // Compass
        $h .= '<svg class="compass" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="0.8">'
            . '<circle cx="28" cy="28" r="22"/>'
            . '<path d="M28 8 L31 28 L28 48 L25 28 Z" fill="currentColor" fill-opacity="0.4"/>'
            . '<path d="M8 28 L28 31 L48 28 L28 25 Z"/>'
            . '<text x="28" y="6" text-anchor="middle" font-size="6" fill="currentColor" stroke="none" font-family="serif">N</text>'
            . '</svg>';

        if ($scale) {
            $h .= '<div class="scale"><i></i> ' . htmlspecialchars($scale) . '</div>';
        }

        $h .= '</div>';

        if ($caption) {
            $h .= '<p class="wiki-figcap">' . htmlspecialchars($caption) . '</p>';
        }

        return $h;
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public static function slugify(string $s): string {
        $s = mb_strtolower(trim($s));
        $s = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $s);
        $s = preg_replace('/[\s_]+/', '-', $s);
        $s = preg_replace('/-+/', '-', $s);
        return trim($s, '-');
    }

    public static function extractWikiLinks(string $src): array {
        preg_match_all('/\[\[([^\]|]+)(?:\|[^\]]+)?\]\]/', $src, $m);
        return array_map([self::class, 'slugify'], $m[1]);
    }

    public static function extractTags(string $src): array {
        preg_match_all('/^tags:\s*(.+)$/mi', $src, $m);
        if (!isset($m[1][0])) return [];
        return array_map('trim', explode(',', $m[1][0]));
    }
}
