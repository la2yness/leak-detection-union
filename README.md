# 누수탐지연합 홈페이지

서울 종로 소재 누수탐지·배관수리 전문업체 **누수탐지연합**의 비즈니스 홈페이지.
`nusu1119.com` · 카페24 웹호스팅 · 빌드 툴 없음.

## 구성

| 영역 | 스택 | 설명 |
|---|---|---|
| 메인 페이지 | 정적 HTML/CSS/JS | 전화 유도 퍼널. `index.html` 한 장 |
| 동별 랜딩페이지 | 정적 HTML | 키워드광고용. `양재동.html`이 템플릿 |
| 현장소식 게시판 | PHP + 워드프레스(헤드리스) | 꾸준한 콘텐츠로 "활동 중" 신호. `현장소식/` |

```
index.html            메인 페이지
양재동.html            동별 랜딩페이지 (템플릿)
css/style.css
js/main.js
images/
  work/               현장 작업사진 (갤러리)
  review/             고객 후기 캡처
  business/           사업자등록증
현장소식/              게시판 (PHP)
  index.php           목록 (+ ?format=json → 홈 티저용)
  post.php            글 1건 (?id=N)
  sitemap.php         글 사이트맵 (/현장소식/sitemap.xml)
  _wp.php             워드프레스 REST 호출(cURL) + 파일 캐시 + 헬퍼
  _layout.php         공통 head/상단바/하단 통화바/푸터
  .htaccess           pretty URL (/현장소식/123 → post.php)
  cache/              API 응답 캐시 (git 미추적, 쓰기권한 필요)
robots.txt · sitemap.xml
```

git·정적배포 대상 아님: `blog/`(서버 워드프레스), `keyword/`, `README.md`, `CLAUDE.md`

## 메인 페이지 섹션

위에서부터: 히어로(전화 CTA) → 서비스 소개 → 특장점 띠 → 고객 후기(마퀴+라이트박스)
→ 서비스 지역(5개 생활권 `<details>`) → 현장 작업사진(그리드+라이트박스)
→ 제공 서비스(6개 카드) → 증상별 서비스(18개 `<details>`) → 현장소식 티저 → 최종 CTA.
상단 고정바 + 하단 고정 통화바로 어디서든 전화 연결.

## 현장소식 게시판

```
[사장님] → nusu1119.com/blog/wp-admin     글쓰기 백엔드 (웹호스팅 /blog 하위 워드프레스, noindex)
                 │ WP REST API
                 ▼
nusu1119.com/현장소식/*.php  PHP가 cURL로 호출 → cache/(목록 5분·글 10분 TTL) → SSR HTML
   /현장소식/          목록 (색인 대상)
   /현장소식/{id}      글 1건 (색인 대상)
   /현장소식/sitemap.xml
nusu1119.com/index.html    정적 유지 + 하단 티저(js `loadBlogTeaser`, 4초 타임아웃, 실패 시 숨김)
                           푸터에 /현장소식/ 정적 링크 (크롤 경로 보장)
```

- 워드프레스는 `/blog` 하위(루트는 정적 사이트). 아무도 직접 방문 안 하므로 테마는 안 꾸민다.
- 검색엔진에는 `nusu1119.com`, `nusu1119.com/현장소식/sitemap.xml` 만 등록. `/blog`는 noindex.
- `allow_url_fopen`이 꺼져 있어 `_wp.php`는 cURL 사용. WP 주소는 `_wp.php`의 `WP_API_BASE` 한 곳.
- `blog/wp-config.php`에 `WP_HOME` / `WP_SITEURL` = `https://nusu1119.com/blog` 로 고정돼 있어야 함.
- 카테고리: 현장사례(`case`) · 누수상식(`tips`) · 자주묻는질문(`faq`) · 공지사항(`notice`). 기본 = 현장사례.

### 사장님 포스팅 (5단계)

1. `nusu1119.com/blog/wp-admin` 로그인
2. 글 → 새로 추가
3. 제목 입력 → 본문에 현장 사진 드래그 + 설명
4. 오른쪽 패널: **대표 이미지** 지정(티저 썸네일용) + **카테고리** 선택
5. 공개

→ 10분 내 `nusu1119.com/현장소식/`와 홈 티저에 자동 반영. 월 2~3회 이상 권장.
빠른 색인: 서치어드바이저 → 요청 → 웹페이지 수집에 새 글 URL 입력.

### 트러블슈팅

| 증상 | 조치 |
|---|---|
| 글이 목록에 안 뜸 | `현장소식/cache/*.json` 삭제 후 재확인 |
| "글을 불러오지 못했습니다" | `nusu1119.com/blog/wp-json/wp/v2/posts?_embed` 가 브라우저에서 JSON을 내는지 확인 |
| cURL 루프백 차단 | `_wp.php`의 `WP_API_BASE`를 `http://127.0.0.1/blog/wp-json/wp/v2/`로 바꾸고 `wp_api_fetch`에 `CURLOPT_HTTPHEADER: ['Host: nusu1119.com']` 추가 |
| `/현장소식/{id}` 404 | `현장소식/.htaccess` rewrite 미동작. 임시로 `post.php?id={id}` 는 동작. 카페24에 mod_rewrite 문의 |
| 한글 폴더 URL 깨짐 | `현장소식/`를 로마자 폴더로 교체 (`_wp.php`의 `BOARD_PATH` + JS/HTML/`.htaccess`/`sitemap.xml`/`robots.txt`의 `/현장소식/` 일괄 치환) |

## 동별 랜딩페이지

키워드광고 랜딩용. `양재동.html` 복사 후 치환:

- `양재동` → 대상 동 (title·description·keywords·canonical·og·JSON-LD·히어로 h1·서비스 소개·증상별 서비스)
- `서초구` → 소속 구 / 서비스 지역 리드의 인접 동(`서초동·우면동·도곡동·개포동·염곡동`) → 대상 동 인접 동
- description 형식: `{동} 누수탐지·배관수리. 수도계량기·천장·배관 누수, 하수구막힘, 수전교체, 동파해빙. 출장비 0원, 원인 못 찾으면 0원.` (~70자)

추가 시: ① 파일 생성 ② `sitemap.xml`에 `<url>`(loc 퍼센트 인코딩) ③ `index.html` 서비스 지역의 해당 동을 링크로 ④ 광고그룹 랜딩 URL을 해당 페이지로(미제작 동은 메인) ⑤ `.blog-teaser` + 푸터 `/현장소식/` 링크 포함 확인.

콘텐츠 소스: `keyword/{동}키워드_*.txt`. 중복 콘텐츠 감점 피하려 광고 집행 동만 순차 제작.

## 배포

정적 파일 + `현장소식/`를 웹호스팅 루트에 그대로 업로드(빌드 없음).
`현장소식/cache/`는 쓰기권한 707(또는 777)만 챙기고 내용은 배포 대상 아님.

## TODO

- 모바일 링크 미리보기 이미지(`images/og-image.jpg`) 교체
- 광고 집행 상위 동 랜딩페이지 순차 제작
- 서비스 지역 목록 법정동 명칭 최종 검수
- 네이버 서치어드바이저 / 구글 서치콘솔에 사이트맵 2종 제출
