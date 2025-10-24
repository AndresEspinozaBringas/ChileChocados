// ChileChocados - App JavaScript
(function(){
  const root = document.documentElement;
  const themeBtn = document.getElementById('theme-toggle');
  const burgerBtn = document.getElementById('burger-toggle');
  const toast = document.getElementById('toast');

  // Tema
  const saved = localStorage.getItem('theme');
  if(saved){ root.setAttribute('data-theme', saved); }
  else if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches){
    root.setAttribute('data-theme','dark');
  }

  function setTheme(next){
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    if(themeBtn) themeBtn.innerText = next === 'dark' ? 'Claro' : 'Oscuro';
  }

  if(themeBtn){
    const cur = root.getAttribute('data-theme') || 'light';
    themeBtn.innerText = cur === 'dark' ? 'Claro' : 'Oscuro';
    themeBtn.addEventListener('click', ()=>{
      const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      setTheme(next);
      showToast('Tema ' + (next==='dark'?'oscuro':'claro') + ' activado');
    });
  }

  // Menú móvil
  if(burgerBtn){
    burgerBtn.addEventListener('click', ()=>{
      document.body.classList.toggle('nav-open');
      const mobileNav = document.querySelector('.mobile-nav');
      if(mobileNav){
        const hidden = mobileNav.style.display === 'none' || mobileNav.style.display === '';
        mobileNav.style.display = hidden ? 'block' : 'none';
      }
    });
  }

  // Toast
  function showToast(msg){
    if(!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(()=>toast.classList.remove('show'), 1800);
  }

  // Exponer showToast globalmente
  window.showToast = showToast;
})();
