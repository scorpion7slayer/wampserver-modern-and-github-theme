<?php
$pageContents .= <<<'EOGITJS'
<!-- GitHub Integration Styles -->
<style>
.project-git-info {
    margin-top: 8px;
}

.git-link {
    margin: 4px 0 8px 0;
}

.git-link a {
    color: var(--accent, #5fb4ff);
    text-decoration: none;
}

.github-integration {
    background: var(--panel-2, #253347);
    border: 1px solid var(--border, #33445b);
    border-radius: 8px;
    padding: 12px;
    margin-top: 8px;
    font-size: 12px;
    min-height: 0;
}

.github-integration.loading {
    text-align: center;
    color: var(--muted, #a9b4c3);
    padding: 8px 12px;
}

.github-integration.error {
    text-align: center;
    padding: 8px 12px;
    background: var(--panel-2, #2d2a24);
    border-color: var(--danger, #e5534b);
}

.github-integration.empty {
    display: none;
}

.github-error-message {
    color: var(--danger, #ff7b72);
    margin-bottom: 8px;
}

.github-retry-btn {
    padding: 6px 12px;
    background: var(--accent, #5fb4ff);
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 11px;
    transition: background 0.2s;
}

.github-retry-btn:hover {
    background: var(--accent-hover, #6cb6ff);
}

.github-section {
    margin: 10px 0;
}

.github-section-title {
    font-weight: 600;
    color: var(--text, #e6edf3);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.github-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.github-tab {
    padding: 4px 10px;
    background: var(--panel-3, #2b3a52);
    border: 1px solid var(--border, #33445b);
    border-radius: 6px;
    cursor: pointer;
    font-size: 11px;
    transition: all 0.2s;
}

.github-tab:hover {
    background: var(--accent, #5fb4ff);
    color: #fff;
}

.github-tab.active {
    background: var(--accent, #5fb4ff);
    color: #fff;
}

.github-content {
    display: none;
}

.github-content.active {
    display: block;
}

.repo-stats {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin: 8px 0;
}

.repo-stat {
    display: flex;
    align-items: center;
    gap: 4px;
    color: var(--muted, #a9b4c3);
}

.commit-item, .branch-item, .pr-item, .issue-item {
    padding: 6px;
    margin: 4px 0;
    background: var(--panel, #202b3a);
    border-radius: 4px;
    border-left: 3px solid var(--accent, #5fb4ff);
}

.commit-sha {
    font-family: monospace;
    color: var(--accent, #5fb4ff);
    font-size: 11px;
}

.commit-message {
    color: var(--text, #e6edf3);
    margin: 2px 0;
}

.commit-meta {
    color: var(--muted, #a9b4c3);
    font-size: 10px;
}

.branch-item {
    display: inline-block;
    margin: 4px;
    padding: 4px 8px;
}

.pr-title, .issue-title {
    color: var(--text, #e6edf3);
    font-weight: 500;
}

.pr-meta, .issue-meta {
    color: var(--muted, #a9b4c3);
    font-size: 10px;
}

.issue-labels {
    display: flex;
    gap: 4px;
    margin-top: 4px;
    flex-wrap: wrap;
}

.issue-label {
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 10px;
}

@media screen and (max-width: 750px) {
    .github-integration {
        padding: 8px;
        font-size: 11px;
    }
    
    .github-tabs {
        flex-direction: column;
    }
    
    .github-tab {
        width: 100%;
        text-align: center;
    }
}
</style>

<!-- GitHub Integration JavaScript -->
<script>
(function() {
    const CACHE_DURATION = 10 * 60 * 1000; // 10 minutes - longer cache to reduce API calls
    const MAX_RETRIES = 2;
    const INITIAL_RETRY_DELAY = 1000; // 1 second
    
    // Translations
    const translations = {
        fr: {
            loading: 'Chargement des données GitHub...',
            rateLimit: 'Limite de requêtes API GitHub atteinte. Veuillez réessayer dans ',
            rateLimitMinutes: ' minutes.',
            networkError: 'Erreur réseau. Impossible de contacter GitHub.',
            apiError: 'Erreur API GitHub. Code: ',
            notFound: 'Dépôt non trouvé sur GitHub.',
            genericError: 'Erreur de chargement des données GitHub.',
            retry: 'Réessayer',
            noCommits: 'Aucun commit récent',
            noBranches: 'Aucune branche',
            noPRs: 'Aucune PR ouverte',
            noIssues: 'Aucune issue ouverte',
            by: 'par',
            commits: 'Commits',
            branches: 'Branches',
            prs: 'PRs',
            issues: 'Issues'
        },
        en: {
            loading: 'Loading GitHub data...',
            rateLimit: 'GitHub API rate limit reached. Please retry in ',
            rateLimitMinutes: ' minutes.',
            networkError: 'Network error. Unable to contact GitHub.',
            apiError: 'GitHub API error. Code: ',
            notFound: 'Repository not found on GitHub.',
            genericError: 'Error loading GitHub data.',
            retry: 'Retry',
            noCommits: 'No recent commits',
            noBranches: 'No branches',
            noPRs: 'No open PRs',
            noIssues: 'No open issues',
            by: 'by',
            commits: 'Commits',
            branches: 'Branches',
            prs: 'PRs',
            issues: 'Issues'
        }
    };
    
    // Detect language from page
    const currentLang = document.documentElement.lang || 
                        (document.querySelector('html[lang]')?.getAttribute('lang')) || 
                        (navigator.language.startsWith('fr') ? 'fr' : 'en');
    const lang = translations[currentLang] || translations.en;
    
    function getCacheKey(owner, repo, endpoint) {
        return `gh_${owner}_${repo}_${endpoint}`;
    }
    
    function getRateLimitCacheKey(owner, repo) {
        return `gh_ratelimit_${owner}_${repo}`;
    }
    
    function getCache(key) {
        try {
            const cached = localStorage.getItem(key);
            if (!cached) return null;
            
            const data = JSON.parse(cached);
            if (Date.now() - data.timestamp > CACHE_DURATION) {
                localStorage.removeItem(key);
                return null;
            }
            return data.value;
        } catch (e) {
            return null;
        }
    }
    
    function setCache(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify({
                value: value,
                timestamp: Date.now()
            }));
        } catch (e) {
            console.warn('Cache storage failed:', e);
        }
    }
    
    function checkRateLimit(owner, repo) {
        const rateLimitKey = getRateLimitCacheKey(owner, repo);
        const rateLimitData = getCache(rateLimitKey);
        if (rateLimitData && rateLimitData.resetTime > Date.now()) {
            return rateLimitData;
        }
        localStorage.removeItem(rateLimitKey);
        return null;
    }
    
    function setRateLimit(owner, repo, resetTime) {
        const rateLimitKey = getRateLimitCacheKey(owner, repo);
        setCache(rateLimitKey, { resetTime });
    }
    
    async function fetchGitHubAPI(owner, repo, endpoint, retryCount = 0) {
        const cacheKey = getCacheKey(owner, repo, endpoint);
        const cached = getCache(cacheKey);
        if (cached) return cached;
        
        // Check if we're rate limited
        const rateLimit = checkRateLimit(owner, repo);
        if (rateLimit) {
            const minutesLeft = Math.ceil((rateLimit.resetTime - Date.now()) / 60000);
            throw new Error(`RATE_LIMIT:${minutesLeft}`);
        }
        
        try {
            const response = await fetch(`https://api.github.com/repos/${owner}/${repo}${endpoint}`);
            
            // Check rate limit headers
            const remaining = response.headers.get('X-RateLimit-Remaining');
            const reset = response.headers.get('X-RateLimit-Reset');
            
            if (remaining === '0' && reset) {
                const resetTime = parseInt(reset) * 1000;
                setRateLimit(owner, repo, resetTime);
            }
            
            if (response.status === 403 && remaining === '0') {
                const resetTime = parseInt(reset) * 1000;
                const minutesLeft = Math.ceil((resetTime - Date.now()) / 60000);
                throw new Error(`RATE_LIMIT:${minutesLeft}`);
            }
            
            if (response.status === 404) {
                throw new Error('NOT_FOUND');
            }
            
            if (!response.ok) {
                throw new Error(`HTTP_${response.status}`);
            }
            
            const data = await response.json();
            setCache(cacheKey, data);
            return data;
        } catch (error) {
            if (error.message.startsWith('RATE_LIMIT:') || 
                error.message === 'NOT_FOUND' || 
                error.message.startsWith('HTTP_')) {
                throw error;
            }
            
            // Network error - retry with exponential backoff
            if (retryCount < MAX_RETRIES) {
                const delay = INITIAL_RETRY_DELAY * Math.pow(2, retryCount);
                await new Promise(resolve => setTimeout(resolve, delay));
                return fetchGitHubAPI(owner, repo, endpoint, retryCount + 1);
            }
            
            console.error('GitHub API error:', error);
            throw new Error('NETWORK_ERROR');
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);
        
        if (currentLang === 'fr') {
            if (minutes < 60) return `il y a ${minutes}min`;
            if (hours < 24) return `il y a ${hours}h`;
            return `il y a ${days}j`;
        } else {
            if (minutes < 60) return `${minutes}m ago`;
            if (hours < 24) return `${hours}h ago`;
            return `${days}d ago`;
        }
    }
    
    function renderRepoInfo(container, data) {
        const stats = `
            <div class="repo-stats">
                <span class="repo-stat">⭐ ${data.stargazers_count}</span>
                <span class="repo-stat">🍴 ${data.forks_count}</span>
                ${data.language ? `<span class="repo-stat">📝 ${data.language}</span>` : ''}
                ${data.license ? `<span class="repo-stat">📜 ${data.license.spdx_id}</span>` : ''}
            </div>
        `;
        container.innerHTML = stats;
    }
    
    function renderCommits(container, commits) {
        const html = commits.slice(0, 5).map(commit => `
            <div class="commit-item">
                <span class="commit-sha">${commit.sha.substring(0, 7)}</span>
                <div class="commit-message">${commit.commit.message.split('\n')[0]}</div>
                <div class="commit-meta">
                    ${commit.commit.author.name} • ${formatDate(commit.commit.author.date)}
                </div>
            </div>
        `).join('');
        container.innerHTML = html || `<div style="color: var(--muted);">${lang.noCommits}</div>`;
    }
    
    function renderBranches(container, branches) {
        const html = branches.map(branch => `
            <div class="branch-item">
                🌿 ${branch.name}
            </div>
        `).join('');
        container.innerHTML = html || `<div style="color: var(--muted);">${lang.noBranches}</div>`;
    }
    
    function renderPRs(container, prs) {
        const html = prs.map(pr => `
            <div class="pr-item">
                <div class="pr-title">
                    <a href="${pr.html_url}" target="_blank" rel="noopener">#${pr.number} - ${pr.title}</a>
                </div>
                <div class="pr-meta">
                    ${lang.by} @${pr.user.login} • ${formatDate(pr.created_at)}
                </div>
            </div>
        `).join('');
        container.innerHTML = html || `<div style="color: var(--muted);">${lang.noPRs}</div>`;
    }
    
    function renderIssues(container, issues) {
        const html = issues.map(issue => `
            <div class="issue-item">
                <div class="issue-title">
                    <a href="${issue.html_url}" target="_blank" rel="noopener">#${issue.number} - ${issue.title}</a>
                </div>
                <div class="issue-meta">
                    ${lang.by} @${issue.user.login} • ${formatDate(issue.created_at)}
                </div>
                ${issue.labels.length ? `
                    <div class="issue-labels">
                        ${issue.labels.map(label => `
                            <span class="issue-label" style="background-color: #${label.color}; color: ${getContrastColor(label.color)}">
                                ${label.name}
                            </span>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        `).join('');
        container.innerHTML = html || `<div style="color: var(--muted);">${lang.noIssues}</div>`;
    }
    
    function getContrastColor(hexcolor) {
        const r = parseInt(hexcolor.substring(0, 2), 16);
        const g = parseInt(hexcolor.substring(2, 4), 16);
        const b = parseInt(hexcolor.substring(4, 6), 16);
        const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        return (yiq >= 128) ? '#000' : '#fff';
    }
    
    async function loadGitHubIntegration(element) {
        const owner = element.closest('.project-git-info').dataset.owner;
        const repo = element.closest('.project-git-info').dataset.repo;
        
        if (!owner || !repo) return;
        
        element.classList.add('loading');
        element.classList.remove('error', 'empty');
        element.innerHTML = `<div class="loading">${lang.loading}</div>`;
        
        try {
            // Charger les données
            const [repoData, commits, branches, pulls, issues] = await Promise.all([
                fetchGitHubAPI(owner, repo, ''),
                fetchGitHubAPI(owner, repo, '/commits'),
                fetchGitHubAPI(owner, repo, '/branches'),
                fetchGitHubAPI(owner, repo, '/pulls'),
                fetchGitHubAPI(owner, repo, '/issues')
            ]);
            
            element.classList.remove('loading');
            
            // Créer l'interface
            element.innerHTML = `
                <div id="repo-info-${repo}"></div>
                <div class="github-tabs">
                    <button class="github-tab active" data-tab="commits">📝 ${lang.commits}</button>
                    <button class="github-tab" data-tab="branches">🌿 ${lang.branches}</button>
                    <button class="github-tab" data-tab="pulls">🔀 ${lang.prs} (${pulls.length})</button>
                    <button class="github-tab" data-tab="issues">🐛 ${lang.issues} (${issues.length})</button>
                </div>
                <div class="github-content active" data-content="commits" id="commits-${repo}"></div>
                <div class="github-content" data-content="branches" id="branches-${repo}"></div>
                <div class="github-content" data-content="pulls" id="pulls-${repo}"></div>
                <div class="github-content" data-content="issues" id="issues-${repo}"></div>
            `;
            
            // Rendre les données
            renderRepoInfo(element.querySelector(`#repo-info-${repo}`), repoData);
            renderCommits(element.querySelector(`#commits-${repo}`), commits);
            renderBranches(element.querySelector(`#branches-${repo}`), branches);
            renderPRs(element.querySelector(`#pulls-${repo}`), pulls);
            renderIssues(element.querySelector(`#issues-${repo}`), issues);
            
            // Gérer les onglets
            element.querySelectorAll('.github-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    element.querySelectorAll('.github-tab').forEach(t => t.classList.remove('active'));
                    element.querySelectorAll('.github-content').forEach(c => c.classList.remove('active'));
                    
                    tab.classList.add('active');
                    const content = element.querySelector(`[data-content="${tab.dataset.tab}"]`);
                    if (content) content.classList.add('active');
                });
            });
            
        } catch (error) {
            element.classList.remove('loading');
            element.classList.add('error');
            
            let errorMessage = lang.genericError;
            
            if (error.message.startsWith('RATE_LIMIT:')) {
                const minutes = error.message.split(':')[1];
                errorMessage = lang.rateLimit + minutes + lang.rateLimitMinutes;
            } else if (error.message === 'NOT_FOUND') {
                errorMessage = lang.notFound;
            } else if (error.message.startsWith('HTTP_')) {
                const code = error.message.replace('HTTP_', '');
                errorMessage = lang.apiError + code;
            } else if (error.message === 'NETWORK_ERROR') {
                errorMessage = lang.networkError;
            }
            
            element.innerHTML = `
                <div class="github-error-message">${errorMessage}</div>
                <button class="github-retry-btn" onclick="this.closest('.github-integration').dispatchEvent(new CustomEvent('retry'))">${lang.retry}</button>
            `;
            
            // Add retry listener
            element.addEventListener('retry', function retryHandler() {
                element.removeEventListener('retry', retryHandler);
                loadGitHubIntegration(element);
            });
        }
    }
    
    function initGitHubIntegrations() {
        document.querySelectorAll('.github-integration').forEach(loadGitHubIntegration);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGitHubIntegrations);
    } else {
        initGitHubIntegrations();
    }
})();
</script>
EOGITJS;
