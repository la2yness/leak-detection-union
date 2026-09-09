<?php
/**
 * 현장소식 - 글 URL 사이트맵
 *   /현장소식/sitemap.xml  (.htaccess rewrite → sitemap.php)
 * 네이버 서치어드바이저 / 구글 서치콘솔에 이 주소를 별도 제출한다.
 */

require_once __DIR__ . '/_wp.php';

header('Content-Type: application/xml; charset=UTF-8');

$res   = wp_api_get('posts?per_page=100&_fields=id,modified,date', 1800);
$posts = $res['data'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// 목록 페이지
echo "  <url>\n";
echo '    <loc>' . e(SITE_BASE . BOARD_PATH) . "</loc>\n";
echo "    <changefreq>weekly</changefreq>\n";
echo "  </url>\n";

foreach ($posts as $post) {
    $id  = (int) ($post['id'] ?? 0);
    if ($id <= 0) {
        continue;
    }
    $mod = strtotime($post['modified'] ?? $post['date'] ?? 'now');
    echo "  <url>\n";
    echo '    <loc>' . e(SITE_BASE . BOARD_PATH . $id) . "</loc>\n";
    echo '    <lastmod>' . date('Y-m-d', $mod ?: time()) . "</lastmod>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "  </url>\n";
}

echo '</urlset>' . "\n";
