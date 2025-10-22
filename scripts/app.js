
// ChileChocados – v3 JS (theme toggle, hamburger, small UX niceties)
(function(){
  const root = document.documentElement;
  const themeBtn = document.getElementById('theme-toggle');
  const burgerBtn = document.getElementById('burger-toggle');
  const toast = document.getElementById('toast');

  // init theme from localStorage or prefers
  const saved = localStorage.getItem('theme');
  if(saved){ root.setAttribute('data-theme', saved); }
  else if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches){
    root.setAttribute('data-theme','dark');
  }

  function setTheme(next){
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    themeBtn && (themeBtn.innerText = next === 'dark' ? 'Claro' : 'Oscuro');
  }

  themeBtn && themeBtn.addEventListener('click', ()=>{
    const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    setTheme(next);
    showToast('Tema ' + (next==='dark'?'oscuro':'claro') + ' activado');
  });

  burgerBtn && burgerBtn.addEventListener('click', ()=>{
    document.body.classList.toggle('nav-open');
  });

  // Fake share action
  const shareLink = document.querySelector('[data-share]');
  shareLink && shareLink.addEventListener('click', (e)=>{
    e.preventDefault();
    showToast('Post corporativo generado (simulación)');
  });

  // Tag toggle (publish step selections)
  document.querySelectorAll('.tag').forEach(tag=>{
    tag.addEventListener('click', ()=>{
      tag.classList.toggle('active');
    })
  });

  function showToast(msg){
    if(!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(()=>toast.classList.remove('show'), 1800);
  }

  // Set initial text for theme toggle
  if(themeBtn){
    const cur = root.getAttribute('data-theme') || 'light';
    themeBtn.innerText = cur === 'dark' ? 'Claro' : 'Oscuro';
  }
})();

// v12 – quick filters + result indicator + basic autocomplete helpers
(function(){
  // Quick filters on list.html
  const filterBar = document.getElementById('quick-filters');
  const resultLabel = document.getElementById('result-indicator');
  if(filterBar && resultLabel){
    let baseCount = 48;
    function setActive(btn){
      [...filterBar.querySelectorAll('button')].forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
    }
    filterBar.addEventListener('click', (e)=>{
      const btn = e.target.closest('button');
      if(!btn) return;
      setActive(btn);
      const type = btn.dataset.type || 'all';
      // Simple demo of counts
      const map = {all: 48, sin: 33, des: 15, precio: 29, conv: 19};
      const region = document.getElementById('region-title')?.textContent || 'todas las regiones';
      resultLabel.textContent = `Mostrando ${map[type]} publicaciones en ${region}`;
    });
  }

  // Autocomplete with <datalist>: nothing needed—native. But we add helper to sync query params demo.
  const regionInputs = document.querySelectorAll('input[list="regiones"]');
  regionInputs.forEach(input=>{
    input.addEventListener('change', ()=>{
      const el = document.getElementById('region-title');
      if(el && input.value) el.textContent = input.value;
    });
  });
})();

// v13 – approve->upsell checkbox to cart
(function(){
  const chk = document.getElementById('chk-destacado');
  const btn = document.getElementById('go-pay');
  if(chk && btn){
    function sync(){ btn.classList.toggle('primary', chk.checked); btn.textContent = chk.checked ? 'Ir a pagar (Flow)' : 'Continuar'; }
    chk.addEventListener('change', sync); sync();
  }
})();
// v14 – main photo picker, share modal, favorites
(function(){
  // --- Main photo picker on publish.html ---
  const gallery = document.querySelector('.gallery');
  if(gallery){
    gallery.querySelectorAll('.slot').forEach((slot, idx)=>{
      // add radio for main photo
      if(!slot.querySelector('input[type="radio"]')){
        const r = document.createElement('input');
        r.type = 'radio'; r.name = 'mainPhoto'; r.value = idx+1;
        slot.appendChild(r);
      }
    });
    gallery.addEventListener('change', (e)=>{
      if(e.target.name === 'mainPhoto'){
        gallery.querySelectorAll('.slot').forEach(s=>s.classList.remove('selected','main'));
        const sel = e.target.closest('.slot');
        sel.classList.add('selected','main');
      }
    });
  }

  // --- Share modal on detail.html ---
  const shareBtn = document.querySelector('[data-share-modal]');
  const backdrop = document.getElementById('share-backdrop');
  if(shareBtn && backdrop){
    shareBtn.addEventListener('click', (e)=>{ e.preventDefault(); backdrop.classList.add('show'); });
    backdrop.addEventListener('click', (e)=>{
      if(e.target === backdrop || e.target.closest('[data-close]')){ backdrop.classList.remove('show'); }
    });
    // simple copy
    const copy = document.getElementById('copy-link');
    if(copy){
      copy.addEventListener('click', ()=>{
        navigator.clipboard && navigator.clipboard.writeText(location.href);
        const t=document.getElementById('toast'); if(t){t.textContent='Link copiado'; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),1200);}
      });
    }
  }

  // --- Favorites (localStorage) ---
  function getFavs(){ try{return JSON.parse(localStorage.getItem('cc:favs')||'[]')}catch(e){return[]} }
  function setFavs(arr){ localStorage.setItem('cc:favs', JSON.stringify(arr)); }
  function toggleFav(id, meta){
    let favs = getFavs();
    const i = favs.findIndex(x=>x.id===id);
    if(i>=0){ favs.splice(i,1); } else { favs.push(meta); }
    setFavs(favs);
    return favs;
  }

  // Button on detail page
  const favBtn = document.getElementById('fav-toggle');
  if(favBtn){
    const pid = favBtn.dataset.pid || '123';
    const meta = {id: pid, title: 'Título del vehículo', price: '$3.500.000', status:'vigente'};
    // init state
    if(getFavs().some(x=>x.id===pid)) favBtn.classList.add('active');
    favBtn.addEventListener('click', ()=>{
      const favs = toggleFav(pid, meta);
      favBtn.classList.toggle('active');
      const msg = favBtn.classList.contains('active') ? 'Agregado a favoritos' : 'Eliminado de favoritos';
      const t=document.getElementById('toast'); if(t){t.textContent=msg; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),1200);}
    });
  }

  // Favorites page render
  const favList = document.getElementById('favorites-list');
  if(favList){
    const favs = getFavs();
    if(!favs.length){ favList.innerHTML = '<p class="meta">Aún no tienes favoritos.</p>'; }
    else{
      favList.innerHTML = favs.map(f=>`<div class="card row" style="justify-content:space-between"><div>${f.title} · <span class="meta">${f.status==='vendido'?'Vendido':'Vigente'}</span></div><div class="row" style="gap:8px"><span class="meta">${f.price||'A convenir'}</span><a class="btn" href="detail.html">Ver</a></div></div>`).join('');
    }
  }

  // Simulate notifications: mark first favorite as vendido on admin action (demo)
  const markSold = document.getElementById('mark-sold-demo');
  if(markSold){
    markSold.addEventListener('click', ()=>{
      const favs = getFavs();
      if(favs.length){
        favs[0].status = 'vendido';
        setFavs(favs);
        const t=document.getElementById('toast'); if(t){t.textContent='Notificación: un favorito fue marcado como vendido'; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),1600);}
      }
    });
  }
})();
// v16 mobile nav fix: toggle inline display for .mobile-nav
(function(){
  const burgerBtn = document.getElementById('burger-toggle');
  const mobileNav = document.querySelector('.mobile-nav');
  if(burgerBtn && mobileNav){
    burgerBtn.addEventListener('click', ()=>{
      const hidden = mobileNav.style.display === 'none' || mobileNav.style.display === '';
      mobileNav.style.display = hidden ? 'block' : 'none';
    });
  }
})();