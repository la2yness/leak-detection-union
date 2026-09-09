<?php
/**
 * 현장소식 - 목록
 *   HTML 모드 : /현장소식/            (SSR, 검색 색인 대상)
 *   JSON 모드 : /현장소식/index.php?format=json&per_page=4   (홈 티저용)
 */

require_once __DIR__ . '/_layout.php';

$page     = max(1, (int) ($_GET['page'] ?? 1));
$isJson   = ($_GET['format'] ?? '') === 'json';
$perPage  = $isJson ? min(6, max(1, (int) ($_GET['per_page'] ?? 4))) : 10;

// _embed 사용 시 _fields 로 필드를 자르면 임베드가 누락될 수 있어 전체 응답을 받는다(캐시됨).
$query = 'posts?_embed=1&per_page=' . $perPage . '&page=' . $page;

$res   = wp_api_get($query, $isJson ? 600 : 300);
$posts = $res['data'];

/* ---------- JSON 모드 (홈 티저) ---------- */
if ($isJson) {
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: public, max-age=300');
    $cards = array_map('post_to_card', array_slice($posts, 0, $perPage));
    echo json_encode($cards, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- HTML 모드 ---------- */
$totalPages = max(1, (int) $res['total_pages']);

layout_top([
    'title'       => $page > 1 ? "현장소식 ({$page}페이지) | 누수탐지연합" : '현장소식 | 누수탐지연합',
    'description' => '누수탐지연합의 실제 출동·시공 현장 사례와 누수 상식, 자주 묻는 질문을 기록합니다.',
    'canonical'   => SITE_BASE . BOARD_PATH . ($page > 1 ? '?page=' . $page : ''),
    'robots'      => 'index, follow',
]);
?>
<section class="blog-page">
  <div class="container">
    <h1>현장소식</h1>
    <p class="blog-page-lead">실제 출동·시공 현장과 누수 상식을 기록합니다</p>

    <?php if (!$posts): ?>
      <p class="blog-empty">
        <?= $res['ok'] ? '아직 등록된 글이 없습니다.' : '글을 불러오지 못했습니다. 잠시 후 다시 확인해 주세요.' ?><br>
        <a href="/">← 누수탐지연합 홈으로</a>
      </p>
    <?php else: ?>
      <div class="blog-list">
        <?php foreach ($posts as $post): $c = post_to_card($post); ?>
          <a class="blog-card" href="<?= e($c['url']) ?>">
            <span class="blog-card-thumb" style="background-image:url('<?= e($c['thumb']) ?>')"></span>
            <span class="blog-card-body">
              <?php if ($c['cat']): ?><span class="blog-card-cat"><?= e($c['cat']) ?></span><?php endif; ?>
              <span class="blog-card-title"><?= e($c['title']) ?></span>
              <span class="blog-card-excerpt"><?= e($c['excerpt']) ?></span>
              <span class="blog-card-date"><?= e($c['date']) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>

      <?php if ($totalPages > 1): ?>
        <nav class="blog-pager">
          <?php if ($page > 1): ?>
            <a href="<?= BOARD_PATH . ($page - 1 > 1 ? '?page=' . ($page - 1) : '') ?>">← 이전</a>
          <?php endif; ?>
          <span><?= $page ?> / <?= $totalPages ?></span>
          <?php if ($page < $totalPages): ?>
            <a href="<?= BOARD_PATH . '?page=' . ($page + 1) ?>">다음 →</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php
render_cta();
layout_bottom();
