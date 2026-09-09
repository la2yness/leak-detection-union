<?php
/**
 * 현장소식 - 워드프레스 REST 연동 공통 모듈
 *
 * 워드프레스는 nusu1119.com/blog/ 하위에 설치(글쓰기 백엔드 전용, 검색 비노출).
 * 이 파일이 서버측에서 cURL 로 WP REST API 를 호출하고 파일 캐시에 저장한다.
 * 카페24 웹호스팅은 allow_url_fopen=OFF 이므로 반드시 cURL 을 쓴다.
 *
 * 같은 서버라 cURL 루프백이 막히면 WP_API_BASE 를 'http://127.0.0.1/blog/wp-json/wp/v2/'
 * (Host 헤더 필요) 등으로 바꿔야 할 수 있다. 우선 공개 https 주소로 시도한다.
 */

const WP_API_BASE = 'https://nusu1119.com/blog/wp-json/wp/v2/';
const SITE_BASE   = 'https://nusu1119.com';
const BOARD_PATH  = '/현장소식/';
const CACHE_DIR   = __DIR__ . '/cache';
const FALLBACK_IMG = '/images/og-image.jpg';
const PHONE_1 = '02-2039-9941';
const PHONE_2 = '010-2197-5784';

/**
 * WP REST API GET.
 *
 * @return array{data: array, total_pages: int, ok: bool}
 *   - 성공: 캐시 갱신 후 데이터 반환
 *   - 실패 + 만료 캐시 있음: 만료된 캐시 반환 (stale-while-error), ok=false
 *   - 실패 + 캐시 없음: data=[] , ok=false
 */
function wp_api_get(string $path, int $ttl = 600): array
{
    $cacheFile = CACHE_DIR . '/' . md5($path) . '.json';

    // 1) 신선한 캐시가 있으면 즉시 반환
    if (is_readable($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        $cached = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($cached)) {
            $cached['ok'] = true;
            return $cached;
        }
    }

    // 2) API 호출
    $result = wp_api_fetch($path);

    if ($result !== null) {
        $payload = [
            'data'        => $result['data'],
            'total_pages' => $result['total_pages'],
        ];
        if (is_dir(CACHE_DIR) && is_writable(CACHE_DIR)) {
            @file_put_contents($cacheFile, json_encode($payload), LOCK_EX);
        }
        $payload['ok'] = true;
        return $payload;
    }

    // 3) 실패 → 만료된 캐시라도 반환
    if (is_readable($cacheFile)) {
        $cached = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($cached)) {
            $cached['ok'] = false;
            return $cached;
        }
    }

    // 4) 캐시도 없음
    return ['data' => [], 'total_pages' => 1, 'ok' => false];
}

/**
 * 실제 cURL 호출. 실패 시 null.
 *
 * @return array{data: array, total_pages: int}|null
 */
function wp_api_fetch(string $path): ?array
{
    if (!function_exists('curl_init')) {
        return null;
    }

    $url = WP_API_BASE . ltrim($path, '/');
    $totalPages = 1;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_USERAGENT      => 'nusu1119-board/1.0 (+https://nusu1119.com/현장소식/)',
        CURLOPT_HEADERFUNCTION => function ($curl, $header) use (&$totalPages) {
            $parts = explode(':', $header, 2);
            if (count($parts) === 2 && strtolower(trim($parts[0])) === 'x-wp-totalpages') {
                $totalPages = max(1, (int) trim($parts[1]));
            }
            return strlen($header);
        },
    ]);

    $body   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errno  = curl_errno($ch);
    curl_close($ch);

    if ($errno !== 0 || $status < 200 || $status >= 300 || $body === false) {
        return null;
    }

    $data = json_decode((string) $body, true);
    if (!is_array($data)) {
        return null;
    }

    return ['data' => $data, 'total_pages' => $totalPages];
}

/* ---------- 출력 헬퍼 ---------- */

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** WP 가 돌려준 rendered 제목의 엔티티를 실제 문자로 되돌린다. */
function decode_title(?string $s): string
{
    return html_entity_decode((string) $s, ENT_QUOTES, 'UTF-8');
}

/** rendered HTML → 평문 발췌 */
function excerpt_text(?string $html, int $len = 100): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $html)));
    $text = preg_replace('/\s*\[&hellip;\]\s*$/u', '', $text);
    if (mb_strlen($text, 'UTF-8') > $len) {
        $text = mb_substr($text, 0, $len, 'UTF-8') . '…';
    }
    return $text;
}

function fmt_date(?string $iso): string
{
    $ts = strtotime((string) $iso);
    return $ts ? date('Y.m.d', $ts) : '';
}

/** _embed 응답에서 대표 이미지 URL 추출 (없으면 폴백) */
function featured_image(array $post, string $size = 'medium'): string
{
    $media = $post['_embedded']['wp:featuredmedia'][0] ?? null;
    if (is_array($media)) {
        $sized = $media['media_details']['sizes'][$size]['source_url'] ?? null;
        if ($sized) {
            return $sized;
        }
        if (!empty($media['source_url'])) {
            return $media['source_url'];
        }
    }
    return FALLBACK_IMG;
}

/** _embed 응답에서 첫 카테고리 이름 */
function primary_category(array $post): string
{
    $terms = $post['_embedded']['wp:term'][0] ?? [];
    foreach ($terms as $term) {
        if (($term['taxonomy'] ?? '') === 'category') {
            return (string) ($term['name'] ?? '');
        }
    }
    return '';
}

/** 글 1건을 티저/목록 카드용 배열로 정규화 */
function post_to_card(array $post): array
{
    $id = (int) ($post['id'] ?? 0);
    return [
        'id'      => $id,
        'title'   => decode_title($post['title']['rendered'] ?? ''),
        'date'    => fmt_date($post['date'] ?? ''),
        'thumb'   => featured_image($post),
        'excerpt' => excerpt_text($post['excerpt']['rendered'] ?? '', 90),
        'cat'     => primary_category($post),
        'url'     => BOARD_PATH . $id,
    ];
}
