<?php
// Détection d'un dépôt Git et extraction éventuelle du slug owner/repo
$__wmp_root = realpath(__DIR__ . '/..');
$__wmp_hasGit = is_dir($__wmp_root . DIRECTORY_SEPARATOR . '.git');
$__wmp_repoSlug = '';
if ($__wmp_hasGit && is_file($__wmp_root . DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR . 'config')) {
  $cfg = @file_get_contents($__wmp_root . DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR . 'config');
  if ($cfg !== false) {
    if (preg_match('/^\s*url\s*=\s*(.+)$/mi', $cfg, $m)) {
      $url = trim($m[1]);
      // Transforme plusieurs formes d'URL vers owner/repo
      // git@github.com:owner/repo.git
      if (preg_match('#github\.com:([^/]+)/([^.\s]+)(?:\.git)?$#', $url, $mm)) {
        $__wmp_repoSlug = $mm[1] . '/' . $mm[2];
      }
      // https://github.com/owner/repo(.git)
      elseif (preg_match('#github\.com/([^/]+)/([^.\s/]+)(?:\.git)?$#', $url, $mm)) {
        $__wmp_repoSlug = $mm[1] . '/' . $mm[2];
      }
    }
  }
}
$__wmp_gitBootstrap = '<script>window.WMP_GIT = { hasGit: ' . ($__wmp_hasGit ? 'true' : 'false') . ', repo: ' . json_encode($__wmp_repoSlug) . ' };</script>';

$pageContents .= <<<EOCSSJS
{$__wmp_gitBootstrap}
<style>
  .list-tools{display:flex;gap:8px;align-items:center;margin:6px 0 8px}
  .wmp-filter{flex:1;padding:6px 8px;border:1px solid #d0d7de;border-radius:6px;background:rgba(255,255,255,.9)}
  .wmp-btn{padding:6px 10px;border:1px solid #d0d7de;background:#f6f8fa;border-radius:6px;cursor:pointer}
  .wmp-btn:hover{filter:brightness(0.98)}
  .live-count{margin-left:6px;font-size:.85em;color:#57606a}
  .wmp-floating{position:fixed;right:14px;bottom:14px;display:flex;flex-direction:column;gap:8px;z-index:9999}
  .wmp-floating .wmp-btn{box-shadow:0 8px 24px rgba(140,149,159,0.2)}
  .wmp-globalbar{max-width:1120px;margin:8px auto 6px;padding:6px 20px;display:flex;flex-wrap:wrap;gap:8px;align-items:center}
  .wmp-field{padding:6px 8px;border:1px solid #d0d7de;border-radius:6px;background:#fff;min-width:140px}
  .wmp-sep{flex:0 0 1px;height:24px;background:#d0d7de;margin:0 4px}
  @media (prefers-color-scheme: dark){
    .wmp-filter{background:rgba(0,0,0,.15);border-color:#444c56;color:#c9d1d9}
    .wmp-btn{background:rgba(0,0,0,.15);border-color:#444c56;color:#c9d1d9}
    .live-count{color:#8b949e}
    .wmp-field{background:rgba(0,0,0,.15);border-color:#444c56;color:#c9d1d9}
    .wmp-sep{background:#444c56}
  }
</style>
<style>
  #head .innerhead .wmp-theme-credit{margin-left:10px;font-size:.9rem;color:#57606a;white-space:nowrap}
  #head .innerhead .wmp-theme-credit a{color:inherit;text-decoration:none;border-bottom:1px dotted currentColor;transition:all .2s}
  #head .innerhead .wmp-theme-credit a:hover{color:#0969da;border-bottom-style:solid}
  @media (prefers-color-scheme: dark){
    #head .innerhead .wmp-theme-credit{color:#8b949e}
    #head .innerhead .wmp-theme-credit a:hover{color:#539bf5}
  }
</style>
<script>
(function(){
  function keyFor(titleEl){
    return (titleEl.textContent||'').toLowerCase().replace(/[^a-z0-9]+/g,'_').replace(/^_|_$/g,'');
  }

  function makeFilter(titleEl, listEl){
    const tools = document.createElement('div');
    tools.className = 'list-tools';
    const input = document.createElement('input');
    input.type = 'search';
    input.className = 'wmp-filter';
    input.placeholder = 'Filtrer…';
    const btnCollapse = document.createElement('button');
    btnCollapse.type = 'button';
    btnCollapse.className = 'wmp-btn';
    btnCollapse.title = 'Replier/Déplier la liste';
    btnCollapse.textContent = '▾';
    const btnCopyList = document.createElement('button');
    btnCopyList.type = 'button';
    btnCopyList.className = 'wmp-btn';
    btnCopyList.title = 'Copier les éléments';
    btnCopyList.textContent = 'Copier';
    const btnSort = document.createElement('button');
    btnSort.type = 'button'; btnSort.className='wmp-btn'; btnSort.title='Trier A→Z'; btnSort.textContent='Trier';
    const btnExportJson = document.createElement('button');
    btnExportJson.type='button'; btnExportJson.className='wmp-btn'; btnExportJson.title='Exporter JSON (éléments visibles)'; btnExportJson.textContent='JSON';
    tools.appendChild(input);
    tools.appendChild(btnCollapse);
    tools.appendChild(btnCopyList);
    tools.appendChild(btnSort);
    tools.appendChild(btnExportJson);
    titleEl.after(tools);

    btnCollapse.addEventListener('click', ()=>{ listEl.hidden = !listEl.hidden; persistCollapsed(titleEl, listEl.hidden); });
    btnCopyList.addEventListener('click', async ()=>{
      const text = Array.from(listEl.querySelectorAll('li')).map(li=>li.innerText||li.textContent||'').filter(Boolean).join('\n');
      try{ await navigator.clipboard.writeText(text); btnCopyList.textContent='Copié !'; setTimeout(()=>btnCopyList.textContent='Copier',1200);}catch(e){ alert('Impossible de copier'); }
    });
    btnSort.addEventListener('click', ()=> sortList(listEl));
    btnExportJson.addEventListener('click', async ()=>{
      const items = Array.from(listEl.querySelectorAll('li')).filter(li=>li.style.display!=="none").map(li=>{
        const a = li.querySelector('a');
        return a ? {text:a.textContent.trim(), href:a.getAttribute('href')} : {text:(li.textContent||'').trim()};
      });
      try{ await navigator.clipboard.writeText(JSON.stringify(items,null,2)); btnExportJson.textContent='Exporté !'; setTimeout(()=>btnExportJson.textContent='JSON',1200);}catch(e){ alert('Impossible de copier'); }
    });
    const ensureBadge = ()=>{
      let badge = titleEl.querySelector('.live-count');
      if(!badge){ badge = document.createElement('span'); badge.className='live-count'; titleEl.appendChild(badge); }
      return badge;
    }
    const doFilter = ()=>{
      const q = input.value.trim().toLowerCase();
      let visible = 0;
      listEl.querySelectorAll('li').forEach(li=>{
        const show = q === '' ? true : li.textContent.toLowerCase().includes(q);
        li.style.display = show ? '' : 'none';
        if(show) visible++;
      });
      ensureBadge().textContent = ' ('+visible+')';
      persistFilter(titleEl, q);
    };
    input.addEventListener('input', doFilter);

    // restore persisted state
    const k = keyFor(titleEl);
    const savedQ = localStorage.getItem('wmp_filter_'+k) || '';
    if(savedQ){ input.value = savedQ; }
    const savedCollapsed = localStorage.getItem('wmp_collapse_'+k);
    if(savedCollapsed === '1'){ listEl.hidden = true; }
    doFilter();
  }

  function persistFilter(titleEl, q){ localStorage.setItem('wmp_filter_'+keyFor(titleEl), q); }
  function persistCollapsed(titleEl, collapsed){ localStorage.setItem('wmp_collapse_'+keyFor(titleEl), collapsed ? '1':'0'); }

  function sortList(listEl){
    const items = Array.from(listEl.querySelectorAll('li'));
    items.sort((a,b)=> (a.textContent||'').localeCompare(b.textContent||'', undefined, {numeric:true,sensitivity:'base'}));
    items.forEach(li=> listEl.appendChild(li));
  }

  function addConfigCopy(){
    const cfg = document.querySelector('.config .innerconfig');
    if(!cfg) return;
    const h2 = cfg.querySelector('h2');
    const target = cfg.querySelector('dl');
    if(!h2 || !target) return;
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'wmp-btn';
    btn.style.float = 'right';
    btn.textContent = 'Copier config';
    btn.addEventListener('click', async ()=>{
      try{
        await navigator.clipboard.writeText(target.innerText || target.textContent || '');
        const old = btn.textContent; btn.textContent = 'Copié !'; setTimeout(()=>btn.textContent=old, 1500);
      }catch(e){ alert('Impossible de copier'); }
    });
    h2.after(btn);
  }

  function addGlobalBar(){
    const util = document.querySelector('.utility');
    if(!util) return;
    const bar = document.createElement('div');
    bar.className = 'wmp-globalbar';
    const parts = [
      '<input id="wmp-global-search" class="wmp-field" type="search" placeholder="Rechercher partout… (/)" />'
    ];
    const hasGit = !!(window.WMP_GIT && window.WMP_GIT.hasGit);
    if(hasGit){
      parts.push(
        '<span class="wmp-sep"></span>',
        '<input id="wmp-gh-repo" class="wmp-field" type="text" placeholder="owner/repo" />',
        '<button id="wmp-gh-open" class="wmp-btn" type="button">Ouvrir</button>',
        '<button id="wmp-gh-issues" class="wmp-btn" type="button">Issues</button>',
        '<button id="wmp-gh-pulls" class="wmp-btn" type="button">Pulls</button>',
        '<span class="wmp-sep"></span>',
        '<input id="wmp-gh-query" class="wmp-field" type="search" placeholder="Recherche GitHub…" />',
        '<button id="wmp-gh-search" class="wmp-btn" type="button">Chercher</button>'
      );
    }
    bar.innerHTML = parts.join('');
    util.after(bar);

    const openUrl = (url)=>{
      const newTab = localStorage.getItem('wmp_open_new_tab')==='1';
      if(newTab) window.open(url, '_blank', 'noopener'); else window.location.href = url;
    };

    const globalInput = bar.querySelector('#wmp-global-search');
    const hasGitNow = !!(window.WMP_GIT && window.WMP_GIT.hasGit);
    let repoInput = null, qInput = null;
    if(hasGitNow){
      repoInput = bar.querySelector('#wmp-gh-repo');
      qInput = bar.querySelector('#wmp-gh-query');
      if(window.WMP_GIT && window.WMP_GIT.repo && repoInput){ repoInput.value = window.WMP_GIT.repo; }
    }

    const restore = ()=>{
      const g = localStorage.getItem('wmp_global_filter')||''; if(g){ globalInput.value=g; }
      applyGlobalFilter(g);
    };
    const applyGlobalFilter = (q)=>{
      const needle = (q||'').toLowerCase();
      document.querySelectorAll('.column ul').forEach(list=>{
        let visible=0;
        list.querySelectorAll('li').forEach(li=>{
          const show = needle==='' || (li.textContent||'').toLowerCase().includes(needle);
          if(show) visible++;
          // combine with existing per-list filter: only hide further
          li.style.display = show ? '' : 'none';
        });
        const title = list.closest('.column')?.querySelector('h2');
        if(title){
          let badge = title.querySelector('.live-count');
          if(!badge){ badge = document.createElement('span'); badge.className='live-count'; title.appendChild(badge); }
          badge.textContent = ' ('+visible+')';
        }
      });
    };

    globalInput.addEventListener('input', ()=>{ const v=globalInput.value; localStorage.setItem('wmp_global_filter', v); applyGlobalFilter(v); });
    globalInput.addEventListener('keydown', (e)=>{ if(e.key==='Escape'){ globalInput.value=''; globalInput.dispatchEvent(new Event('input')); }});

    if(hasGitNow){
      const btnOpen = bar.querySelector('#wmp-gh-open');
      const btnIssues = bar.querySelector('#wmp-gh-issues');
      const btnPulls = bar.querySelector('#wmp-gh-pulls');
      const btnSearch = bar.querySelector('#wmp-gh-search');
      if(btnOpen) btnOpen.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!/^[^\/\s]+\/[\w.-]+$/.test(v)) return alert('Format attendu: owner/repo'); openUrl('https://github.com/'+v); });
      if(btnIssues) btnIssues.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!/^[^\/\s]+\/[\w.-]+$/.test(v)) return alert('Format attendu: owner/repo'); openUrl('https://github.com/'+v+'/issues'); });
      if(btnPulls) btnPulls.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!/^[^\/\s]+\/[\w.-]+$/.test(v)) return alert('Format attendu: owner/repo'); openUrl('https://github.com/'+v+'/pulls'); });
      if(btnSearch) btnSearch.addEventListener('click', ()=>{ const q=(qInput.value||'').trim(); if(!q) return; openUrl('https://github.com/search?q='+encodeURIComponent(q)); });
    }

    // keyboard shortcuts
    document.addEventListener('keydown', (e)=>{
      const tag = (e.target && e.target.tagName)||''; const typing = tag==='INPUT' || tag==='TEXTAREA' || e.isComposing;
      if(e.key==='/' && !typing){ e.preventDefault(); globalInput.focus(); }
      if(e.altKey && !typing){
        if(e.key.toLowerCase()==='e'){ document.querySelectorAll('.column ul').forEach((ul)=>ul.hidden=false); }
        if(e.key.toLowerCase()==='c'){ document.querySelectorAll('.column ul').forEach((ul)=>ul.hidden=true); }
        if(e.key.toLowerCase()==='n'){ const on=!(localStorage.getItem('wmp_open_new_tab')==='1'); localStorage.setItem('wmp_open_new_tab', on?'1':'0'); }
      }
    });

    // gg to top
    (function(){ let lastG=0; document.addEventListener('keydown',(e)=>{ if((e.target&&/INPUT|TEXTAREA/.test(e.target.tagName))) return; if(e.key.toLowerCase()==='g'){ const t=Date.now(); if(t-lastG<350){ window.scrollTo({top:0,behavior:'smooth'});} lastG=t; } });})();

    restore();
  }

  function addFloatingTools(){
    const wrap = document.createElement('div');
    wrap.className = 'wmp-floating';
    const btnTop = document.createElement('button'); btnTop.className='wmp-btn'; btnTop.textContent='↑ Haut';
    const btnExpand = document.createElement('button'); btnExpand.className='wmp-btn'; btnExpand.textContent='Tout ouvrir';
    const btnCollapse = document.createElement('button'); btnCollapse.className='wmp-btn'; btnCollapse.textContent='Tout fermer';
    const btnNewTab = document.createElement('button'); btnNewTab.className='wmp-btn'; btnNewTab.textContent='Ouvrir dans nouvel onglet';
    wrap.append(btnTop, btnExpand, btnCollapse, btnNewTab);
    document.body.appendChild(wrap);

    const setNewTab = (on)=>{
      localStorage.setItem('wmp_open_new_tab', on ? '1' : '0');
      document.querySelectorAll('.column a').forEach(a=>{
        if(on){ a.setAttribute('target','_blank'); a.setAttribute('rel','noreferrer noopener'); }
        else { a.removeAttribute('target'); a.removeAttribute('rel'); }
      });
      btnNewTab.textContent = on ? 'Ouvrir dans même onglet' : 'Ouvrir dans nouvel onglet';
    };

    btnTop.addEventListener('click', ()=> window.scrollTo({top:0,behavior:'smooth'}));
    btnExpand.addEventListener('click', ()=>{
      document.querySelectorAll('.column').forEach(col=>{
        const title = col.querySelector('h2'); const list = col.querySelector('ul'); if(list && title){ list.hidden = false; persistCollapsed(title,false); }
      });
    });
    btnCollapse.addEventListener('click', ()=>{
      document.querySelectorAll('.column').forEach(col=>{
        const title = col.querySelector('h2'); const list = col.querySelector('ul'); if(list && title){ list.hidden = true; persistCollapsed(title,true); }
      });
    });
    const initialNewTab = localStorage.getItem('wmp_open_new_tab') === '1';
    setNewTab(initialNewTab);
    btnNewTab.addEventListener('click', ()=> setNewTab(!(localStorage.getItem('wmp_open_new_tab')==='1')));
  }

  function bootstrap(){
    document.querySelectorAll('.column').forEach(col=>{
      const title = col.querySelector('h2');
      const list = col.querySelector('ul');
      if(title && list) makeFilter(title, list);
    });
    addConfigCopy();
    addGlobalBar();
    addFloatingTools();
  }

  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bootstrap);
  else bootstrap();
})();
</script>
<script>
(function(){
  function applyThemeCredit(){
    var allowed = {"Modern Dark":1,"github-theme":1,"github-theme-dark":1};
    var h1 = document.querySelector('#head .innerhead h1');
    if(!h1) return;
    var span = h1.querySelector('.wmp-theme-credit');
    if(!span){ span = document.createElement('span'); span.className='wmp-theme-credit'; h1.appendChild(span); }
    var theme = localStorage.getItem('wampStyle') || 'classic';
    if(!allowed[theme]){ span.textContent=''; span.style.display='none'; return; }
    span.innerHTML = ' — ' + theme + ' by <a href="https://github.com/scorpion7slayer" target="_blank" rel="noopener noreferrer">scorpion7slayer</a>';
    span.style.display='';
  }
  function bind(){
    applyThemeCredit();
    var sel = document.getElementById('themes');
    if(sel){ sel.addEventListener('change', function(){ setTimeout(applyThemeCredit, 0); }); }
    window.addEventListener('storage', function(e){ if(e.key==='wampStyle') applyThemeCredit(); });
    document.addEventListener('DOMContentLoaded', applyThemeCredit);
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', bind);
  else bind();
})();
</script>
EOCSSJS;
