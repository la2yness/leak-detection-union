(function () {
  'use strict';

  /* Scroll reveal for below-the-fold sections */
  var revealTargets = document.querySelectorAll(
    '.services-strip .container, .service-intro .container, .highlight-strip .hl-item, .reviews-head, .marquee-row, .area-section .area-panel, .symptom-section .symptom-list, .services-detail .service-card, .blog-teaser .container, .final-cta .container'
  );
  revealTargets.forEach(function (el) { el.classList.add('reveal'); });

  if ('IntersectionObserver' in window && revealTargets.length) {
    var revealObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            revealObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
    );
    revealTargets.forEach(function (el) { revealObserver.observe(el); });
  } else {
    revealTargets.forEach(function (el) { el.classList.add('revealed'); });
  }

  /* Phone number clipboard copy (desktop only) */
  var isTouchDevice = window.matchMedia('(hover: none), (pointer: coarse)').matches;

  var toast = document.createElement('div');
  toast.className = 'copy-toast';
  toast.setAttribute('role', 'status');
  document.body.appendChild(toast);

  var toastTimer = null;
  function showToast(message) {
    toast.textContent = message;
    toast.classList.add('show');
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toast.classList.remove('show'); }, 1800);
  }

  function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(text);
    }
    var input = document.createElement('input');
    input.value = text;
    input.style.position = 'fixed';
    input.style.opacity = '0';
    document.body.appendChild(input);
    input.select();
    try { document.execCommand('copy'); } catch (err) { /* no-op */ }
    document.body.removeChild(input);
    return Promise.resolve();
  }

  if (!isTouchDevice) {
    document.querySelectorAll('.phone-link').forEach(function (link) {
      link.addEventListener('click', function (e) {
        var phone = link.getAttribute('data-phone');
        if (!phone) return;
        e.preventDefault();
        copyText(phone).then(function () {
          showToast(phone + ' 복사되었습니다');
        });
      });
    });
  }

  /* Lightbox (work gallery + reviews) */
  var lightbox = document.getElementById('lightbox');

  if (lightbox) {
    var lightboxImg = lightbox.querySelector('.lightbox-img');
    var activeList = [];
    var currentIndex = 0;

    function showImage(index) {
      currentIndex = (index + activeList.length) % activeList.length;
      lightboxImg.setAttribute('src', activeList[currentIndex]);
    }

    function goToImage(index) {
      lightboxImg.classList.add('switching');
      setTimeout(function () {
        showImage(index);
        lightboxImg.classList.remove('switching');
      }, 180);
    }

    function openLightbox(list, index) {
      activeList = list;
      showImage(index);
      lightbox.classList.add('open');
    }

    function closeLightbox() {
      lightbox.classList.remove('open');
      setTimeout(function () { lightboxImg.setAttribute('src', ''); }, 200);
    }

    var galleryItems = document.querySelectorAll('.gallery-item');
    if (galleryItems.length) {
      var gallerySrcs = Array.prototype.map.call(galleryItems, function (item) {
        return item.querySelector('img').getAttribute('src');
      });
      galleryItems.forEach(function (item, index) {
        item.addEventListener('click', function () { openLightbox(gallerySrcs, index); });
      });
    }

    var reviewImgs = document.querySelectorAll('.review-photo:not([aria-hidden]) img');
    if (reviewImgs.length) {
      var reviewSrcs = Array.prototype.map.call(reviewImgs, function (img) {
        return img.getAttribute('src');
      });
      document.querySelectorAll('.review-photo').forEach(function (photo) {
        photo.addEventListener('click', function () {
          var src = photo.querySelector('img').getAttribute('src');
          var idx = reviewSrcs.indexOf(src);
          openLightbox(reviewSrcs, idx < 0 ? 0 : idx);
        });
      });
    }

    lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    lightbox.querySelector('.lightbox-prev').addEventListener('click', function () { goToImage(currentIndex - 1); });
    lightbox.querySelector('.lightbox-next').addEventListener('click', function () { goToImage(currentIndex + 1); });

    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox) closeLightbox();
    });

    document.addEventListener('keydown', function (e) {
      if (!lightbox.classList.contains('open')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') goToImage(currentIndex - 1);
      if (e.key === 'ArrowRight') goToImage(currentIndex + 1);
    });
  }

  /* 현장소식 티저 - /현장소식/index.php?format=json (WordPress 백엔드 최신 글)
     같은 도메인이라 CORS 무관. 실패/타임아웃 시 섹션을 숨긴 채로 둔다(퍼널 페이지 보호). */
  (function () {
    var section = document.querySelector('.blog-teaser');
    var list = document.getElementById('blog-teaser-list');
    if (!section || !list || !('fetch' in window)) return;

    var URL = '/현장소식/index.php?format=json&per_page=4';
    var FALLBACK_IMG = '/images/og-image.jpg';

    function esc(s) {
      return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
      });
    }

    var controller = ('AbortController' in window) ? new AbortController() : null;
    var timer = setTimeout(function () { if (controller) controller.abort(); }, 4000);

    fetch(URL, { signal: controller && controller.signal })
      .then(function (r) { if (!r.ok) throw new Error('bad status'); return r.json(); })
      .then(function (posts) {
        clearTimeout(timer);
        if (!Array.isArray(posts) || !posts.length) return;
        list.innerHTML = posts.slice(0, 4).map(function (p) {
          var img = p.thumb || FALLBACK_IMG;
          return '<a class="blog-card" href="' + esc(p.url) + '">' +
            '<span class="blog-card-thumb" style="background-image:url(\'' + esc(img) + '\')"></span>' +
            '<span class="blog-card-body">' +
              (p.cat ? '<span class="blog-card-cat">' + esc(p.cat) + '</span>' : '') +
              '<span class="blog-card-title">' + esc(p.title) + '</span>' +
              '<span class="blog-card-excerpt">' + esc(p.excerpt) + '</span>' +
              '<span class="blog-card-date">' + esc(p.date) + '</span>' +
            '</span></a>';
        }).join('');
        section.hidden = false;
        // hidden 이었으므로 IntersectionObserver 가 놓칠 수 있어 직접 표시
        var inner = section.querySelector('.container');
        if (inner) {
          requestAnimationFrame(function () { inner.classList.add('revealed'); });
        }
      })
      .catch(function () { clearTimeout(timer); /* 숨긴 채로 둠 */ });
  })();
})();
