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

// Translations for WMP Enhancements
// The $langue variable is available from index.php (e.g., 'english', 'french')
$__wmp_translations = array(
  'english' => array(
    'copyConfig' => 'Copy config',
    'copied' => 'Copied!',
    'copyFailed' => 'Copy failed',
    'repoPlaceholder' => 'owner/repo',
    'open' => 'Open',
    'issues' => 'Issues',
    'pulls' => 'Pulls',
    'formatExpected' => 'Expected format: owner/repo',
    'backToTop' => 'Back to top',
    'openNewTab' => 'Open in new tab',
    'openSameTab' => 'Open in same tab'
  ),
  'french' => array(
    'copyConfig' => 'Copier config',
    'copied' => 'Copie!',
    'copyFailed' => 'Copie impossible',
    'repoPlaceholder' => 'owner/repo',
    'open' => 'Ouvrir',
    'issues' => 'Issues',
    'pulls' => 'Pulls',
    'formatExpected' => 'Format attendu: owner/repo',
    'backToTop' => 'En haut',
    'openNewTab' => 'Ouvrir dans nouvel onglet',
    'openSameTab' => 'Ouvrir dans meme onglet'
  )
);

// Select appropriate translations based on $langue variable
// Default to English if language not found
$__wmp_currentLang = isset($langue) ? $langue : 'english';
$__wmp_tr = isset($__wmp_translations[$__wmp_currentLang]) ? $__wmp_translations[$__wmp_currentLang] : $__wmp_translations['english'];

$__wmp_gitBootstrap = '<script>window.WMP_GIT = { hasGit: ' . ($__wmp_hasGit ? 'true' : 'false') . ', repo: ' . json_encode($__wmp_repoSlug, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ' }; window.WMP_TR = ' . json_encode($__wmp_tr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ';</script>';

$pageContents .= <<<EOCSSJS
{$__wmp_gitBootstrap}
<!-- WMP Enhancements v1.2 - Responsive improvements -->
<style>
  .list-tools{display:flex;gap:8px;align-items:center;margin:6px 0 8px;flex-wrap:wrap}
  .wmp-btn{padding:6px 10px;border:1px solid #d0d7de;background:#f6f8fa;border-radius:6px;cursor:pointer;font-size:13px;transition:all 0.15s ease}
  .wmp-btn:hover{filter:brightness(0.98);transform:translateY(-1px)}
  .wmp-btn:active{transform:translateY(0)}
  .wmp-floating{position:fixed;right:14px;bottom:14px;display:flex;flex-direction:column;gap:8px;z-index:9999}
  .wmp-floating .wmp-btn{box-shadow:0 8px 24px rgba(140,149,159,0.2)}
  .wmp-globalbar{max-width:1120px;margin:8px auto 6px;padding:6px 20px;display:flex;flex-wrap:wrap;gap:8px;align-items:center}
  .wmp-field{padding:6px 8px;border:1px solid #d0d7de;border-radius:6px;background:#fff;min-width:140px;font-size:13px}
  .wmp-sep{flex:0 0 1px;height:24px;background:#d0d7de;margin:0 4px}
  
  /* Responsive enhancements */
  @media screen and (max-width: 750px) {
    .wmp-globalbar{padding:6px 12px;gap:6px}
    .wmp-field{min-width:100px;flex:1;font-size:12px}
    .wmp-btn{padding:8px 12px;font-size:12px;flex:1 1 auto}
    .wmp-floating{right:10px;bottom:10px;gap:6px}
    .wmp-floating .wmp-btn{font-size:11px;padding:6px 8px}
  }
  
  @media screen and (max-width: 480px) {
    .wmp-globalbar{flex-direction:column;align-items:stretch}
    .wmp-field{width:100%}
    .wmp-btn{width:100%;justify-content:center}
    .wmp-floating{right:8px;bottom:8px}
  }
  
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
  
  /* Responsive theme credit */
  @media screen and (max-width: 750px) {
    #head .innerhead .wmp-theme-credit{font-size:.8rem;margin-left:0;width:100%;text-align:center;margin-top:4px}
  }
  
  @media screen and (max-width: 480px) {
    #head .innerhead .wmp-theme-credit{font-size:.75rem;display:block}
  }
  
  @media (prefers-color-scheme: dark){
    #head .innerhead .wmp-theme-credit{color:#8b949e}
    #head .innerhead .wmp-theme-credit a:hover{color:#539bf5}
  }
</style>
<script>
(function(){
  // Get translations from window.WMP_TR or fall back to English defaults
  const tr = window.WMP_TR || {
    copyConfig: 'Copy config',
    copied: 'Copied!',
    copyFailed: 'Copy failed',
    repoPlaceholder: 'owner/repo',
    open: 'Open',
    issues: 'Issues',
    pulls: 'Pulls',
    formatExpected: 'Expected format: owner/repo',
    backToTop: 'Back to top',
    openNewTab: 'Open in new tab',
    openSameTab: 'Open in same tab'
  };

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
    
    // Prevent duplicate buttons
    if(h2.nextElementSibling && h2.nextElementSibling.classList && h2.nextElementSibling.classList.contains('wmp-btn')) return;
    
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'wmp-btn';
    btn.style.float = 'right';
    btn.textContent = tr.copyConfig;
    btn.addEventListener('click', async ()=>{
      try{
        const text = target.innerText || target.textContent || '';
        if(!text.trim()){ alert(tr.copyFailed); return; }
        
        // Modern browsers
        if(navigator.clipboard && navigator.clipboard.writeText){
          await navigator.clipboard.writeText(text);
          const old = btn.textContent; btn.textContent = tr.copied; setTimeout(()=>btn.textContent=old, 1500);
        }
        // Fallback for older browsers
        else{
          const textarea = document.createElement('textarea');
          textarea.value = text;
          textarea.style.position = 'fixed';
          textarea.style.opacity = '0';
          document.body.appendChild(textarea);
          textarea.select();
          try{
            document.execCommand('copy');
            const old = btn.textContent; btn.textContent = tr.copied; setTimeout(()=>btn.textContent=old, 1500);
          }catch(err){
            alert(tr.copyFailed);
          }
          document.body.removeChild(textarea);
        }
      }catch(e){ 
        console.error('Copy failed:', e);
        alert(tr.copyFailed); 
      }
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
      '<input id="wmp-gh-repo" class="wmp-field" type="text" placeholder="' + tr.repoPlaceholder + '" />',
      '<button id="wmp-gh-open" class="wmp-btn" type="button">' + tr.open + '</button>',
      '<button id="wmp-gh-issues" class="wmp-btn" type="button">' + tr.issues + '</button>',
      '<button id="wmp-gh-pulls" class="wmp-btn" type="button">' + tr.pulls + '</button>'
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
    if(btnOpen) btnOpen.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!repoPattern.test(v)) return alert(tr.formatExpected); openUrl('https://github.com/'+v); });
    if(btnIssues) btnIssues.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!repoPattern.test(v)) return alert(tr.formatExpected); openUrl('https://github.com/'+v+'/issues'); });
    if(btnPulls) btnPulls.addEventListener('click', ()=>{ const v=(repoInput.value||'').trim(); if(!repoPattern.test(v)) return alert(tr.formatExpected); openUrl('https://github.com/'+v+'/pulls'); });

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
    const btnTop = document.createElement('button'); btnTop.className='wmp-btn'; btnTop.textContent=tr.backToTop;
    const btnNewTab = document.createElement('button'); btnNewTab.className='wmp-btn'; btnNewTab.textContent=tr.openNewTab;
    wrap.append(btnTop, btnNewTab);
    document.body.appendChild(wrap);

    const setNewTab = (on)=>{
      localStorage.setItem('wmp_open_new_tab', on ? '1' : '0');
      document.querySelectorAll('.column a').forEach(a=>{
        if(on){ a.setAttribute('target','_blank'); a.setAttribute('rel','noreferrer noopener'); }
        else { a.removeAttribute('target'); a.removeAttribute('rel'); }
      });
      btnNewTab.textContent = on ? tr.openSameTab : tr.openNewTab;
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
