<?php
/**
 * 셋업 점검용 - 배포 직후 브라우저로 /현장소식/_check.php 열어 확인하고 **삭제**한다.
 * 민감정보를 노출하지 않도록 결과만 간단히 출력.
 */

require_once __DIR__ . '/_wp.php';

header('Content-Type: text/plain; charset=UTF-8');

echo "== 현장소식 셋업 점검 ==\n\n";

echo 'PHP 버전            : ' . PHP_VERSION . "\n";
echo 'cURL 확장           : ' . (function_exists('curl_init') ? 'OK' : '없음 (호스팅 문의 필요)') . "\n";
echo 'allow_url_fopen     : ' . (ini_get('allow_url_fopen') ? 'ON' : 'OFF (정상, cURL 사용)') . "\n";
echo 'cache 디렉토리       : ' . (is_dir(CACHE_DIR) ? 'exists' : '없음') . "\n";
echo 'cache 쓰기권한       : ' . (is_writable(CACHE_DIR) ? 'OK' : 'NO (chmod 707 또는 777 필요)') . "\n\n";

echo "-- WP REST API 호출 테스트 --\n";
$t = microtime(true);
$fetched = wp_api_fetch('posts?per_page=1&_fields=id,title');
$ms = round((microtime(true) - $t) * 1000);

if ($fetched === null) {
    echo "결과 : 실패\n";
    echo "  → blog.nusu1119.com 워드프레스가 켜져 있는지, 서브도메인/SSL이 정상인지,\n";
    echo "    카페24가 외부 cURL 호출을 막고 있지는 않은지 확인하세요.\n";
} else {
    $count = count($fetched['data']);
    echo "결과 : OK ({$ms}ms, 글 {$count}건 수신)\n";
    if ($count > 0 && isset($fetched['data'][0]['title']['rendered'])) {
        echo '  최근 글 제목: ' . decode_title($fetched['data'][0]['title']['rendered']) . "\n";
    }
}

echo "\n확인이 끝나면 이 파일(_check.php)을 삭제하세요.\n";
