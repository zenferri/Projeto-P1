(function() {
  // Respeita usuários com redução de movimento ativa
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) return;

  // Seleciona todos os elementos que devem ser revelados
  const elements = document.querySelectorAll('.reveal');
  if (!elements.length) return;

  // Configura o observador de interseção (vigia elementos do site para para avisar qd entram ou saem da área visivel - viewport)
  const observer = new IntersectionObserver((entries, obs) => {
    for (const entry of entries) {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        // Anima apenas na primeira vez
        obs.unobserve(entry.target);
      }
    }
  }, {
    root: null,
    threshold: 0.15,
    rootMargin: '0px 0px -10% 0px'
  });

  // Observa cada elemento
  elements.forEach(el => observer.observe(el));
})();