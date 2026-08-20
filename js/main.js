(function () {
  'use strict';

  /* Scroll reveal for below-the-fold sections */
  var revealTargets = document.querySelectorAll(
    '.services-strip .container, .reviews-head, .marquee-row, .quick-facts-grid .fact, .final-cta .container'
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
})();
