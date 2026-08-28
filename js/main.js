(function () {
  'use strict';

  /* Scroll reveal for below-the-fold sections */
  var revealTargets = document.querySelectorAll(
    '.services-strip .container, .reviews-head, .marquee-row, .quick-facts-grid .fact, .area-section .area-panel, .services-detail .service-card, .final-cta .container'
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

  /* Work gallery lightbox */
  var galleryItems = document.querySelectorAll('.gallery-item');
  var lightbox = document.getElementById('lightbox');

  if (galleryItems.length && lightbox) {
    var lightboxImg = lightbox.querySelector('.lightbox-img');
    var currentIndex = 0;

    function showImage(index) {
      currentIndex = (index + galleryItems.length) % galleryItems.length;
      var src = galleryItems[currentIndex].querySelector('img').getAttribute('src');
      lightboxImg.setAttribute('src', src);
    }

    function goToImage(index) {
      lightboxImg.classList.add('switching');
      setTimeout(function () {
        showImage(index);
        lightboxImg.classList.remove('switching');
      }, 180);
    }

    function openLightbox(index) {
      showImage(index);
      lightbox.classList.add('open');
    }

    function closeLightbox() {
      lightbox.classList.remove('open');
      setTimeout(function () { lightboxImg.setAttribute('src', ''); }, 200);
    }

    galleryItems.forEach(function (item, index) {
      item.addEventListener('click', function () { openLightbox(index); });
    });

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
})();
