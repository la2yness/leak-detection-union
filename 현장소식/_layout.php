<?php
/**
 * 현장소식 - 공통 레이아웃 (index.html 의 상단바 / 하단 통화바 / 푸터를 그대로 옮김)
 * 경로가 /현장소식/ 하위이므로 자산 경로는 절대경로(/css, /js, /images)를 쓴다.
 */

require_once __DIR__ . '/_wp.php';

/**
 * @param array{
 *   title:string, description?:string, canonical:string,
 *   og_image?:string, jsonld?:string, robots?:string
 * } $meta
 */
function layout_top(array $meta): void
{
    $title       = $meta['title'];
    $description = $meta['description'] ?? '누수탐지연합 현장 사례와 누수 상식을 기록합니다.';
    $canonical   = $meta['canonical'];
    $ogImage     = $meta['og_image'] ?? (SITE_BASE . '/images/og-image.jpg');
    $robots      = $meta['robots'] ?? 'index, follow';
    ?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<meta name="robots" content="<?= e($robots) ?>">
<meta name="naver-site-verification" content="418d4ac7554d9c9e0f8033d0da20d7df61a4b3a4" />
<link rel="canonical" href="<?= e($canonical) ?>">

<meta property="og:type" content="article">
<meta property="og:site_name" content="누수탐지연합">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:locale" content="ko_KR">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($title) ?>">
<meta name="twitter:description" content="<?= e($description) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">
<meta name="theme-color" content="#0B1740">

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" sizes="512x512" href="/images/icon-512.png">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="stylesheet" href="/css/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
<?php if (!empty($meta['jsonld'])): ?>
<script type="application/ld+json"><?= $meta['jsonld'] ?></script>
<?php endif; ?>
</head>
<body>

<!-- Top bar -->
<header class="topbar" id="topbar">
  <div class="container topbar-inner">
    <a href="/" class="logo">
      <span class="logo-icon" aria-hidden="true">
        <svg viewBox="0 0 48 48" width="26" height="26">
          <path d="M24 4C24 4 12 18 12 27a12 12 0 0 0 24 0C36 18 24 4 24 4z" fill="url(#dropGrad)"/>
          <circle cx="20" cy="26" r="9" fill="none" stroke="#0B1740" stroke-width="3"/>
          <line x1="26.5" y1="32.5" x2="33" y2="39" stroke="#0B1740" stroke-width="3.5" stroke-linecap="round"/>
          <defs>
            <linearGradient id="dropGrad" x1="12" y1="4" x2="36" y2="39" gradientUnits="userSpaceOnUse">
              <stop offset="0" stop-color="#29B6F6"/>
              <stop offset="1" stop-color="#1565C0"/>
            </linearGradient>
          </defs>
        </svg>
      </span>
      <span class="logo-text">누수탐지연합</span>
    </a>
    <div class="topbar-phones">
      <a href="tel:<?= PHONE_1 ?>" class="topbar-phone phone-link" data-phone="<?= PHONE_1 ?>">
        <span aria-hidden="true">📞</span><?= PHONE_1 ?>
      </a>
      <a href="tel:<?= PHONE_2 ?>" class="topbar-phone num-2 phone-link" data-phone="<?= PHONE_2 ?>">
        <span aria-hidden="true">📞</span><?= PHONE_2 ?>
      </a>
    </div>
  </div>
</header>

<main id="top">
<?php
}

function layout_bottom(): void
{
    ?>
</main>

<!-- Footer -->
<footer class="site-footer">
  <div class="container footer-inner">
    <p class="footer-brand">누수탐지연합</p>
    <p>상호 누수탐지연합 · 대표 서승태 · 사업자등록번호 221-27-24116</p>
    <p>서울특별시 종로구 율곡로14길 14, 2층 201호(연건동)</p>
    <p><a href="/images/business/business-registration.jpg" target="_blank" rel="noopener">사업자정보 확인</a></p>
    <p class="footer-copyright">&copy; 2026 누수탐지연합. All rights reserved.</p>
  </div>
</footer>

<!-- Sticky bottom call bar -->
<div class="bottom-bar" id="bottom-bar">
  <a href="tel:<?= PHONE_1 ?>" class="bottom-bar-btn phone-link" data-phone="<?= PHONE_1 ?>">
    <span aria-hidden="true">📞</span> <?= PHONE_1 ?>
  </a>
  <a href="tel:<?= PHONE_2 ?>" class="bottom-bar-btn num-2 phone-link" data-phone="<?= PHONE_2 ?>">
    <span aria-hidden="true">📞</span> <?= PHONE_2 ?>
  </a>
</div>

<script src="/js/main.js"></script>
</body>
</html>
<?php
}

/** 글 하단 / 목록 하단 공용 전화 CTA */
function render_cta(): void
{
    ?>
<section class="final-cta">
  <div class="container">
    <h2>누수·배관 문제, 전화 한 통이면<br>바로 확인해 드립니다</h2>
    <div class="final-cta-buttons">
      <a href="tel:<?= PHONE_1 ?>" class="btn btn-cta phone-link" data-phone="<?= PHONE_1 ?>">
        <span aria-hidden="true">📞</span> <?= PHONE_1 ?>
      </a>
      <a href="tel:<?= PHONE_2 ?>" class="btn btn-cta num-2 phone-link" data-phone="<?= PHONE_2 ?>">
        <span aria-hidden="true">📞</span> <?= PHONE_2 ?>
      </a>
    </div>
  </div>
</section>
<?php
}
