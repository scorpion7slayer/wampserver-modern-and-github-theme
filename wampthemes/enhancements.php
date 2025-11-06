<?php
// Detection d'un depot Git et extraction eventuelle du slug owner/repo
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
$__wmp_gitBootstrap = '<script>window.WMP_GIT = { hasGit: ' . ($__wmp_hasGit ? 'true' : 'false') . ', repo: ' . json_encode($__wmp_repoSlug, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ' };</script>';

$pageContents .= <<<EOCSSJS
{$__wmp_gitBootstrap}
<!-- WMP Enhancements v1.1 - ASCII cleaned -->
<style>
  .list-tools{display:flex;gap:8px;align-items:center;margin:6px 0 8px}
  .wmp-btn{padding:6px 10px;border:1px solid #d0d7de;background:#f6f8fa;border-radius:6px;cursor:pointer}
  .wmp-btn:hover{filter:brightness(0.98)}
  .wmp-floating{position:fixed;right:14px;bottom:14px;display:flex;flex-direction:column;gap:8px;z-index:9999}
  .wmp-floating .wmp-btn{box-shadow:0 8px 24px rgba(140,149,159,0.2)}
  .wmp-globalbar{max-width:1120px;margin:8px auto 6px;padding:6px 20px;display:flex;flex-wrap:wrap;gap:8px;align-items:center}
  .wmp-field{padding:6px 8px;border:1px solid #d0d7de;border-radius:6px;background:#fff;min-width:140px}
  .wmp-sep{flex:0 0 1px;height:24px;background:#d0d7de;margin:0 4px}
  @media (prefers-color-scheme: dark){
    .wmp-btn{background:rgba(0,0,0,.15);border-color:#444c56;color:#c9d1d9}
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
  function persistCollapsed(titleEl, collapsed){ 
    const key = (titleEl.textContent||'').toLowerCase().replace(/[^a-z0-9]+/g,'_').replace(/^_|_$/g,'');
    localStorage.setItem('wmp_collapse_'+key, collapsed ? '1':'0'); 
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
        const old = btn.textContent; btn.textContent = 'Copie!'; setTimeout(()=>btn.textContent=old, 1500);
      }catch(e){ alert('Copie impossible'); }
    });
    h2.after(btn);
  }

  function addGlobalBar(){
    const util = document.querySelector('.utility');
    if(!util) return;
    const hasGit = !!(window.WMP_GIT && window.WMP_GIT.hasGit);
    if(!hasGit) return;

    const bar = document.createElement('div');
    bar.className = 'wmp-globalbar';
    const parts = [
      '<input id="wmp-gh-repo" class="wmp-field" type="text" placeholder="owner/repo" />',
      '<button id="wmp-gh-open" class="wmp-btn" type="button">Ouvrir</button>',
      '<button id="wmp-gh-issues" class="wmp-btn" type="button">Issues</button>',
      '<button id="wmp-gh-pulls" class="wmp-btn" type="button">Pulls</button>'
    ];
    bar.innerHTML = parts.join('');
    util.after(bar);

    const openUrl = (url)=>{
      const newTab = localStorage.getItem('wmp_open_new_tab')==='1';
      if(newTab) window.open(url, '_blank', 'noopener'); else window.location.href = url;
    };

    const repoInput = bar.querySelector('#wmp-gh-repo');
    if(window.WMP_GIT && window.WMP_GIT.repo && repoInput){ repoInput.value = window.WMP_GIT.repo; }

    const btnOpen = bar.querySelector('#wmp-gh-open');
    const btnIssues = bar.querySelector('#wmp-gh-issues');
    const btnPulls = bar.querySelector('#wmp-gh-pulls');
    const repoPattern = /^[^/\s]+\/[\w.-]+$/;
    if(btnOpen) btnOpen.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!repoPattern.test(v)) return alert('Format attendu: owner/repo'); openUrl('https://github.com/'+v); });
    if(btnIssues) btnIssues.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!repoPattern.test(v)) return alert('Format attendu: owner/repo'); openUrl('https://github.com/'+v+'/issues'); });
    if(btnPulls) btnPulls.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!repoPattern.test(v)) return alert('Format attendu: owner/repo'); openUrl('https://github.com/'+v+'/pulls'); });

    // keyboard shortcuts
    document.addEventListener('keydown', (e)=>{
      const tag = (e.target && e.target.tagName)||''; const typing = tag==='INPUT' || tag==='TEXTAREA' || e.isComposing;
      if(e.altKey && !typing){
        if(e.key.toLowerCase()==='e'){ document.querySelectorAll('.column ul').forEach((ul)=>ul.hidden=false); }
        if(e.key.toLowerCase()==='c'){ document.querySelectorAll('.column ul').forEach((ul)=>ul.hidden=true); }
        if(e.key.toLowerCase()==='n'){ const on=!(localStorage.getItem('wmp_open_new_tab')==='1'); localStorage.setItem('wmp_open_new_tab', on?'1':'0'); }
      }
    });

    // gg to top
    (function(){ let lastG=0; document.addEventListener('keydown',(e)=>{ if((e.target&&/INPUT|TEXTAREA/.test(e.target.tagName))) return; if(e.key.toLowerCase()==='g'){ const t=Date.now(); if(t-lastG<350){ window.scrollTo({top:0,behavior:'smooth'});} lastG=t; } });})();
  }

  function addFloatingTools(){
    const wrap = document.createElement('div');
    wrap.className = 'wmp-floating';
    const btnTop = document.createElement('button'); btnTop.className='wmp-btn'; btnTop.textContent='En haut';
    const btnNewTab = document.createElement('button'); btnNewTab.className='wmp-btn'; btnNewTab.textContent='Ouvrir dans nouvel onglet';
    wrap.append(btnTop, btnNewTab);
    document.body.appendChild(wrap);

    const setNewTab = (on)=>{
      localStorage.setItem('wmp_open_new_tab', on ? '1' : '0');
      document.querySelectorAll('.column a').forEach(a=>{
        if(on){ a.setAttribute('target','_blank'); a.setAttribute('rel','noreferrer noopener'); }
        else { a.removeAttribute('target'); a.removeAttribute('rel'); }
      });
      btnNewTab.textContent = on ? 'Ouvrir dans meme onglet' : 'Ouvrir dans nouvel onglet';
    };

    btnTop.addEventListener('click', ()=> window.scrollTo({top:0,behavior:'smooth'}));
    const initialNewTab = localStorage.getItem('wmp_open_new_tab') === '1';
    setNewTab(initialNewTab);
    btnNewTab.addEventListener('click', ()=> setNewTab(!(localStorage.getItem('wmp_open_new_tab')==='1')));
  }

  function bootstrap(){
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
    span.innerHTML = ' - ' + theme + ' by <a href="https://github.com/scorpion7slayer" target="_blank" rel="noopener noreferrer">scorpion7slayer</a>';
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
