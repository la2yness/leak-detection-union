# 누수탐지연합 홈페이지

서울 종로 소재 누수탐지·배관수리 전문업체 **누수탐지연합**의 비즈니스 홈페이지

## 기술 스택

- HTML5 / CSS3 / Vanilla JavaScript — 빌드 툴 없는 정적 사이트
- `현장소식/` 만 PHP (카페24 웹호스팅 PHP, 워드프레스 REST 서버 렌더)
- 정적 웹호스팅(카페24) + 커스텀 도메인 `nusu1119.com`

## 폴더 구조

```
index.html
css/style.css
js/main.js
현장소식/            게시판 (PHP, 아래 "현장소식 게시판" 참고)
  index.php          목록 (+ ?format=json 홈 티저용)
  post.php           글 1건 (?id=N)
  sitemap.php        글 URL 사이트맵 (/현장소식/sitemap.xml)
  _wp.php            워드프레스 REST 호출(cURL) + 파일 캐시 + 헬퍼
  _layout.php        공통 head/상단바/하단 통화바/푸터
  _check.php         셋업 점검용 (확인 후 삭제)
  .htaccess          pretty URL rewrite
  cache/             API 응답 캐시 (git 미추적, 쓰기권한 필요)
images/
  business/business-registration.jpg   사업자등록증
  work/work-01.jpg ~ work-12.jpg       현장 작업사진 갤러리에 쓰이는 사진
  review/review-01.png ~ review-18.png  고객 후기 캡처 이미지
  icon-192.png, og-image.jpg
favicon.ico
apple-touch-icon.png
robots.txt
sitemap.xml
```

## 주요 섹션

전화 유도를 목적으로 한 한 페이지 구성. 위에서부터 순서대로 이어진다.

- 히어로: 슬로건과 두 개의 전화번호 CTA 버튼
- 서비스 소개: 누수탐지·배관수리 범위를 문장으로 요약(키워드 밀도 높은 상단 문단)
- 특장점 띠: 경력·A/S·장비를 짧게 요약
- 고객 후기: `images/review/`의 후기를 좌우로 흐르는 마퀴로 보여주고, 이미지 클릭 시 이전/다음 전환이 있는 라이트박스로 확대
- 서비스 지역: 서울 5개 생활권으로 묶어 구별 주요 법정동을 `<details>`로 접어서 나열, 경기 인접 지역은 전화로 안내
- 현장 작업사진: `images/work/`의 작업사진을 그리드로 보여주고, 사진 클릭 시 이전/다음 전환이 있는 라이트박스로 확대
- 제공 서비스: 누수탐지·배관누수·수리·막힘·고압세척·위생기기 6개 분류를 카드로 정리
- 증상별 서비스: 18개 증상을 `<details>` 접기/펼치기 목록으로 안내
- 현장소식 티저: 최종 CTA 앞에 최근 글 미리보기(`js/main.js`의 `loadBlogTeaser`, 실패 시 숨김)
- 상단 고정 바와 하단 고정 통화 바로 어느 위치에서든 바로 전화 연결

## 현장소식 게시판

포털이 "활동하지 않는 사이트"로 판단하지 않도록 꾸준한 글을 올리는 공간.

**구조 (헤드리스 + PHP 서버 렌더)**

```
[사장님] → nusu1119.com/blog/wp-admin   글쓰기 백엔드 전용 (웹호스팅 /blog 하위 워드프레스, 검색 비노출/noindex)
                  │ WP REST API (blog/wp-json/wp/v2/posts, _embed)
                  ▼
nusu1119.com/현장소식/*.php  ← PHP가 cURL로 API 호출 → cache/(10분 TTL) → SSR HTML
   /현장소식/            목록 (검색 색인 대상)
   /현장소식/123         글 1건 (.htaccess pretty URL, 색인 대상)
   /현장소식/sitemap.xml 글 사이트맵
nusu1119.com/index.html  정적 유지 + 하단 티저(JS가 /현장소식/index.php?format=json 호출, 4초 타임아웃, 실패 시 숨김)
                         푸터에 /현장소식/ 정적 링크 (JS 무관 크롤 경로)
```

워드프레스는 웹호스팅 루트가 아니라 `/blog` 하위에 둔다(루트는 정적 사이트). 아무도 직접 방문하지 않으므로 테마는 꾸미지 않는다.
`allow_url_fopen`이 꺼져 있어 `_wp.php`는 반드시 cURL을 쓴다. WP 주소는 `_wp.php`의 `WP_API_BASE` 한 곳에서 관리.

**1회 셋업 체크리스트**

1. 워드프레스를 웹호스팅 `/blog` 폴더에 설치 (또는 루트 설치본을 `/blog`로 이동 후 아래 3번 처리)
2. `blog/wp-config.php` 에 사이트 주소 고정 (이동/서브폴더 설치 시 필수):
   ```php
   define('WP_HOME',    'https://nusu1119.com/blog');
   define('WP_SITEURL', 'https://nusu1119.com/blog');
   ```
   `/* That's all, stop editing! */` 줄 위에 추가.
3. `blog/.htaccess` 의 `RewriteBase` / `RewriteRule` 을 `/blog/` 기준으로 수정 (WP 관리자 → 설정 → 고유주소 저장하면 자동 재생성됨)
4. WP 설정 → 읽기 → "검색엔진이 색인하지 않도록 요청" 체크 (noindex)
5. WP 설정 → 토론 → 댓글/핑백 해제, 샘플 글·페이지 삭제
6. 카테고리 4개 생성: 현장사례(`case`) · 누수상식(`tips`) · 자주묻는질문(`faq`) · 공지사항(`notice`), 기본 카테고리 = 현장사례, "미분류" 삭제
7. 정적 사이트 파일 전체 + `현장소식/` 를 웹호스팅 루트에 업로드, 루트의 워드프레스 잔재(`wp-*.php`, `index.php`, `readme.html`, `license.txt`, `hosting_index.html` 등) 삭제. `현장소식/cache/` 쓰기권한 707(또는 777)
8. 브라우저로 `nusu1119.com/현장소식/_check.php` 열어 cURL·캐시·API 확인 → **`_check.php` 삭제**
9. `nusu1119.com/현장소식/` 접근 확인. 한글 폴더 URL 문제 시 로마자 폴더로 교체(`_wp.php`의 `BOARD_PATH`, JS/HTML/`.htaccess`/`sitemap.xml`/`robots.txt`의 `/현장소식/` 일괄 치환)
10. 네이버 서치어드바이저 / 구글 서치콘솔에 `https://nusu1119.com/현장소식/sitemap.xml` 제출. `/blog`는 등록하지 않음 (noindex)

**사장님 포스팅 (5단계)**

1. `nusu1119.com/blog/wp-admin` 로그인
2. 글 → 새로 추가
3. 제목 입력 → 본문에 현장 사진 드래그 + 설명
4. 오른쪽: 대표 이미지 지정 + 카테고리 선택
5. 공개

→ 약 10분 내(캐시 TTL) `nusu1119.com/현장소식/`와 홈 티저에 자동 반영. 발행 후 서치어드바이저에서 새 URL "수집 요청" 하면 색인이 빠르다.

**트러블슈팅 — 글이 안 보여요**

- 캐시 TTL(10분) 대기, 또는 `현장소식/cache/*.json` 삭제
- `현장소식/cache/` 쓰기권한 확인
- `nusu1119.com/blog/wp-json/wp/v2/posts` 직접 열어 응답(JSON)이 나오는지 확인
- 서버→자기도메인 cURL 루프백이 막혔으면 `_wp.php`의 `WP_API_BASE`를 `http://127.0.0.1/blog/wp-json/wp/v2/` 로 바꾸고 `_wp.php` `wp_api_fetch`에 `CURLOPT_HTTPHEADER: ['Host: nusu1119.com']` 추가, 또는 카페24 문의
- `_check.php`를 다시 올려 원인 확인

**워드프레스가 /blog 로 옮긴 뒤 wp-admin 접속 안 되고 이미지 깨질 때** (지금 상황)

1. `blog/wp-config.php` 에 위 2번의 `WP_HOME` / `WP_SITEURL` 두 줄 추가 → 저장
2. `nusu1119.com/blog/wp-admin` 재접속
3. 로그인되면 설정 → 고유주소 → (변경 없이) **저장** 클릭 → `.htaccess` 재생성
4. 그래도 안 되면 `blog/.htaccess` 를 아래로 교체:
   ```apache
   # BEGIN WordPress
   <IfModule mod_rewrite.c>
   RewriteEngine On
   RewriteBase /blog/
   RewriteRule ^index\.php$ - [L]
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule . /blog/index.php [L]
   </IfModule>
   # END WordPress
   ```
5. DB를 직접 고칠 수 있으면(phpMyAdmin): `wp_options` 테이블에서 `option_name` 이 `siteurl`, `home` 인 두 행의 값을 `https://nusu1119.com/blog` 로

> 카페24 간편설치 워드프레스는 표준 WP다. 카페24와 도메인/경로로 별도 통신하는 부분은 없으니 폴더 이동 자체는 안전하고, 위 siteurl/home/.htaccess 3가지만 맞추면 된다.

## 동별 랜딩페이지

키워드광고 랜딩용으로 동 단위 페이지를 별도 HTML로 둔다. `양재동.html`이 첫 번째이자 템플릿.

- 복제 방법: `양재동.html`을 복사한 뒤 아래를 치환
  - `양재동` → 대상 동 이름 (title·description·keywords·canonical·og·JSON-LD·히어로 h1·서비스 소개·증상별 서비스 전체)
  - `서초구` → 대상 동이 속한 구
  - 서비스 지역 리드 문구의 인접 동(`서초동·우면동·도곡동·개포동·염곡동`) → 대상 동의 인접 동
  - JSON-LD `areaServed` → 대상 동/구
- 페이지 추가 시: ① 파일 생성 ② `sitemap.xml`에 `<url>` 추가(loc은 퍼센트 인코딩) ③ `index.html` 서비스 지역의 해당 동 이름을 링크로 전환 ④ 네이버 광고그룹 랜딩 URL을 해당 동 페이지로 지정(미제작 동은 메인으로) ⑤ `.blog-teaser` 섹션과 푸터 `/현장소식/` 링크가 포함됐는지 확인
- 콘텐츠 소스: `keyword/{동}키워드_*.txt`
- 중복 콘텐츠 감점을 피하기 위해 광고를 집행하는 상위 동만 순차 제작한다.

## 배포

정적 파일을 그대로 웹호스팅 루트에 업로드. 별도 빌드 과정 없음.
`현장소식/`도 그대로 업로드(PHP는 서버에서 실행). `현장소식/cache/`는 쓰기권한만 챙기면 되고 내용은 배포 대상 아님.

## 남은 작업 (TODO)

- 모바일 링크 미리보기 이미지 작업.
- 광고 집행 상위 동 랜딩페이지 순차 제작 (`양재동.html` 템플릿 기준).
- 서비스 지역 목록의 법정동 명칭 최종 검수.
