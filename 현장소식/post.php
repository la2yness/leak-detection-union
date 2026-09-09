<?php
/**
 * 현장소식 - 글 1건
 *   /현장소식/123            (.htaccess rewrite)
 *   /현장소식/post.php?id=123 (rewrite 미동작 시 폴백)
 */

require_once __DIR__ . '/_layout.php';

$id = (int) ($_GET['id'] ?? 0);

$post = null;
if ($id > 0) {
    $res  = wp_api_get("posts/{$id}?_embed=1", 600);
    $data = $res['data'];
    // 단건 조회는 객체 하나를 반환
    if (isset($data['id'])) {
        $post = $data;
    }
}

/* ---------- 404 ---------- */
if (!$post) {
    http_response_code(404);
    layout_top([
        'title'     => '글을 찾을 수 없습니다 | 누수탐지연합',
        'canonical' => SITE_BASE . BOARD_PATH,
        'robots'    => 'noindex, follow',
    ]);
    ?>
    <section class="blog-page">
      <div class="container">
        <p class="blog-empty">
          요청하신 글을 찾을 수 없습니다.<br>
          <a href="<?= BOARD_PATH ?>">← 현장소식 목록</a>
        </p>
      </div>
    </section>
    <?php
    render_cta();
    layout_bottom();
    exit;
}

/* ---------- 본문 ---------- */
$title   = decode_title($post['title']['rendered'] ?? '');
$dateIso = $post['date'] ?? '';
$modIso  = $post['modified'] ?? $dateIso;
$content = $post['content']['rendered'] ?? '';
$cat     = primary_category($post);
$img     = featured_image($post, 'large');
$hasImg  = $img !== FALLBACK_IMG;
$desc    = excerpt_text($post['excerpt']['rendered'] ?? ($content ?: ''), 150);
$canonical = SITE_BASE . BOARD_PATH . $id;

$jsonld = json_encode(array_filter([
    '@context'      => 'https://schema.org',
    '@type'         => 'Article',
    'headline'      => $title,
    'datePublished' => $dateIso ? date('c', strtotime($dateIso)) : null,
    'dateModified'  => $modIso ? date('c', strtotime($modIso)) : null,
    'image'         => $hasImg ? $img : (SITE_BASE . '/images/og-image.jpg'),
    'author'        => ['@type' => 'Organization', 'name' => '누수탐지연합'],
    'publisher'     => [
        '@type' => 'Organization',
        'name'  => '누수탐지연합',
        'logo'  => ['@type' => 'ImageObject', 'url' => SITE_BASE . '/images/icon-512.png'],
    ],
    'mainEntityOfPage' => $canonical,
], function ($v) { return $v !== null; }),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT);

layout_top([
    'title'       => $title . ' | 누수탐지연합',
    'description' => $desc,
    'canonical'   => $canonical,
    'og_image'    => $hasImg ? $img : null,
    'jsonld'      => $jsonld,
    'robots'      => 'index, follow',
]);
?>
<article class="blog-single">
  <div class="container">
    <p class="blog-single-meta">
      <a href="<?= BOARD_PATH ?>">현장소식</a>
      <?php if ($cat): ?><span aria-hidden="true"> · </span><?= e($cat) ?><?php endif; ?>
      <span aria-hidden="true"> · </span><?= e(fmt_date($dateIso)) ?>
    </p>
    <h1 class="blog-single-title"><?= e($title) ?></h1>

    <?php if ($hasImg): ?>
      <img class="blog-single-hero" src="<?= e($img) ?>" alt="<?= e($title) ?>" loading="eager" decoding="async">
    <?php endif; ?>

    <div class="blog-single-content">
      <?= $content /* 본인 WP 백엔드에서 생성한 HTML */ ?>
    </div>

    <div class="blog-single-cta">
      <p>누수·배관 문제로 도움이 필요하시면 전화 주세요. 현장에 직접 방문해 확인해 드립니다.</p>
      <div class="final-cta-buttons">
        <a href="tel:<?= PHONE_1 ?>" class="btn btn-cta phone-link" data-phone="<?= PHONE_1 ?>">
          <span aria-hidden="true">📞</span> <?= PHONE_1 ?>
        </a>
        <a href="tel:<?= PHONE_2 ?>" class="btn btn-cta num-2 phone-link" data-phone="<?= PHONE_2 ?>">
          <span aria-hidden="true">📞</span> <?= PHONE_2 ?>
        </a>
      </div>
      <p class="blog-single-back"><a href="<?= BOARD_PATH ?>">← 현장소식 목록</a> · <a href="/">누수탐지연합 홈</a></p>
    </div>
  </div>
</article>
<?php
layout_bottom();
