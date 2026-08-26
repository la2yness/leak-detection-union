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
  business/business-registration.jpg   사업자등록증 (원본, 추후 블러본으로 교체 예정)
  icon-192.png, og-image.jpg
favicon.ico
apple-touch-icon.png
robots.txt
sitemap.xml
```

## 배포

정적 파일을 그대로 웹호스팅 루트에 업로드. 별도 빌드 과정 없음.

## 남은 작업 (TODO)

- [ ] 실제 고객 후기 확보 후 `.marquee-track`(2줄)의 `.review-chip` 플레이스홀더 문구를 실제 후기로 교체
- [ ] 서비스 지역이 확정되면 `.quick-facts`의 "종로 기반 · 수도권 방문" 문구를 구체적인 지역명으로 교체
- [ ] `images/business/business-registration.jpg`를 생년월일 블러 처리된 버전으로 교체
- [ ] GA4 / 네이버 애널리틱스 추적 ID 발급 후 `index.html` `<head>`의 TODO 주석 위치에 삽입
- [ ] 카카오 상담 채널(오픈채팅/채널) 준비 시 CTA 버튼 추가 고려
