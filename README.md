# 누수탐지연합 홈페이지

서울 종로 소재 누수탐지·배관수리 전문업체 **누수탐지연합**의 비즈니스 홈페이지

## 기술 스택

- HTML5 / CSS3 / Vanilla JavaScript — 빌드 툴 없는 정적 사이트
- 정적 웹호스팅(카페24) + 커스텀 도메인 `nusu1119.com`

## 폴더 구조

```
index.html
css/style.css
js/main.js
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
- 특장점 띠: 경력·A/S·장비를 짧게 요약
- 고객 후기: `images/review/`의 후기를 좌우로 흐르는 마퀴로 보여주고, 이미지 클릭 시 이전/다음 전환이 있는 라이트박스로 확대
- 서비스 지역: 서울 25개 구를 나열하고 경기 인접 지역은 전화로 안내
- 현장 작업사진: `images/work/`의 작업사진을 그리드로 보여주고, 사진 클릭 시 이전/다음 전환이 있는 라이트박스로 확대
- 제공 서비스: 누수탐지·막힘·배관·위생기기 등 6개 분류를 카드로 정리
- 상단 고정 바와 하단 고정 통화 바로 어느 위치에서든 바로 전화 연결

## 배포

정적 파일을 그대로 웹호스팅 루트에 업로드. 별도 빌드 과정 없음.

## 남은 작업 (TODO)

- 모바일 링크 미리보기 이미지 작업.
