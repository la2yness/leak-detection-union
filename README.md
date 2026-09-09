# 누수탐지연합 홈페이지

서울 종로 소재 누수탐지·배관수리 전문업체 **누수탐지연합**의 비즈니스 홈페이지.
`nusu1119.com` · 카페24 웹호스팅 · 빌드 툴 없음.

## 구성

| 영역 | 스택 | 설명 |
|---|---|---|
| 메인 페이지 | 정적 HTML/CSS/JS | 전화 유도 퍼널, `index.html` |
| 동별 랜딩페이지 | 정적 HTML | 키워드 광고용, `양재동.html`이 템플릿 |
| 현장소식 게시판 | PHP + 워드프레스 | 검색엔진 노출용 콘텐츠, `현장소식/` |

```
index.html            메인 페이지
양재동.html            동별 랜딩페이지 템플릿
css/style.css
js/main.js
images/
  work/               현장 작업사진
  review/             고객 후기
  business/           사업자등록증 등
현장소식/              게시판 (PHP)
  index.php           
  post.php            
  sitemap.php         
  _wp.php             워드프레스 REST 호출(cURL) + 파일 캐시 + 헬퍼
  _layout.php         공통 인터페이스
  .htaccess           pretty URL
  cache/              API 응답 캐시
robots.txt
sitemap.xml
```

## 메인 페이지 섹션

히어로(전화 CTA)
→ 서비스 소개
→ 특장점 띠
→ 고객 후기(마퀴 + 라이트박스)
→ 서비스 지역(5개 생활권 `<details>`)
→ 현장 작업사진(그리드 + 라이트박스)
→ 제공 서비스(6개 카드)
→ 증상별 서비스(18개 `<details>`)
→ 현장소식 티저
→ 최종 CTA
상단 고정바 + 하단 고정 통화바

## 현장소식 게시판

```
[관리자] → nusu1119.com/blog/wp-admin     글쓰기 백엔드 (웹호스팅 /blog 하위 워드프레스, noindex)
                 │ WP REST API
                 ▼
nusu1119.com/현장소식/*.php  PHP가 cURL로 호출 → cache/(목록 5분·글 10분 TTL) → SSR HTML
   /현장소식/          목록 (색인 대상)
   /현장소식/{id}      글 1건 (색인 대상)
   /현장소식/sitemap.xml
nusu1119.com/index.html    정적 유지 + 하단 티저(js `loadBlogTeaser`, 4초 타임아웃)
                           푸터에 /현장소식/ 정적 링크로 크롤 경로 보장
```

- 워드프레스는 `/blog` 하위(루트는 정적 사이트). 직접 방문하지 않으므로 테마는 꾸미지 않는다
- 검색엔진에는 `nusu1119.com`, `nusu1119.com/현장소식` 만 등록. `/blog`는 noindex
- `allow_url_fopen`이 꺼져 있어 `_wp.php`는 cURL 사용. WP 주소는 `_wp.php`의 `WP_API_BASE`
- `blog/wp-config.php`에 `WP_HOME` / `WP_SITEURL` = `https://nusu1119.com/blog` 로 고정돼 있어야 함
- 카테고리: 현장사례(`case`) · 누수상식(`tips`) · 자주묻는질문(`faq`) · 공지사항(`notice`)

### 관리자 포스팅 방법

1. `nusu1119.com/blog/wp-admin` 로그인
2. 글 → 새로 추가
3. 제목 입력 → 본문에 현장 사진 드래그 + 설명
4. 오른쪽 패널: 대표 이미지 지정(티저 썸네일용) + 카테고리 선택
5. 공개

→ 10분 내 `nusu1119.com/현장소식/`과 홈 티저에 자동 반영

## 동별 랜딩페이지

키워드광고 랜딩용. `양재동.html` 복사 후 치환:

- `양재동` → 대상 동 (title·description·keywords·canonical·og·JSON-LD·히어로 h1·서비스 소개·증상별 서비스)
- `서초구` → 소속 구 / 서비스 지역 리드의 인접 동(`서초동·우면동·도곡동·개포동·염곡동`) → 대상 동 인접 동
- description 형식: `{동} 누수탐지·배관수리. 수도계량기·천장·배관 누수, 하수구막힘, 수전교체, 동파해빙. 출장비 0원, 원인 못 찾으면 0원.` (~70자)

추가 시: ① 파일 생성 ② `sitemap.xml`에 `<url>`(loc 퍼센트 인코딩) ③ `index.html` 서비스 지역의 해당 동을 링크로 ④ 광고그룹 랜딩 URL을 해당 페이지로(미제작 동은 메인) ⑤ `.blog-teaser` + 푸터 `/현장소식/` 링크 포함 확인.

콘텐츠 소스: `keyword/{동}키워드_*.txt`. 중복 콘텐츠 감점 피하려 광고 집행 동만 순차 제작.

## 배포

정적 파일 + `현장소식/`를 웹호스팅 루트에 그대로 업로드(빌드 없음)
`현장소식/cache/`는 쓰기권한 707 유지

## TODO

- 모바일 링크 미리보기 이미지(`images/og-image.jpg`) 교체
- 광고 집행 상위 동 랜딩페이지 순차 제작
- 서비스 지역 목록 법정동 명칭 최종 검수
- 네이버 서치어드바이저 / 구글 서치콘솔에 사이트맵 2종 제출
