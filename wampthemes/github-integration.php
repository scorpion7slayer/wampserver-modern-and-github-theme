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
}

.github-integration.loading {
    text-align: center;
    color: var(--muted, #a9b4c3);
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
    const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes
    
    function getCacheKey(owner, repo, endpoint) {
        return `gh_${owner}_${repo}_${endpoint}`;
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
    
    async function fetchGitHubAPI(owner, repo, endpoint) {
        const cacheKey = getCacheKey(owner, repo, endpoint);
        const cached = getCache(cacheKey);
        if (cached) return cached;
        
        try {
            const response = await fetch(`https://api.github.com/repos/${owner}/${repo}${endpoint}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            setCache(cacheKey, data);
            return data;
        } catch (error) {
            console.error('GitHub API error:', error);
            throw error;
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);
        
        if (minutes < 60) return `il y a ${minutes}min`;
        if (hours < 24) return `il y a ${hours}h`;
        return `il y a ${days}j`;
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
        container.innerHTML = html || '<div style="color: var(--muted);">Aucun commit récent</div>';
    }
    
    function renderBranches(container, branches) {
        const html = branches.map(branch => `
            <div class="branch-item">
                🌿 ${branch.name}
            </div>
        `).join('');
        container.innerHTML = html || '<div style="color: var(--muted);">Aucune branche</div>';
    }
    
    function renderPRs(container, prs) {
        const html = prs.map(pr => `
            <div class="pr-item">
                <div class="pr-title">
                    <a href="${pr.html_url}" target="_blank" rel="noopener">#${pr.number} - ${pr.title}</a>
                </div>
                <div class="pr-meta">
                    par @${pr.user.login} • ${formatDate(pr.created_at)}
                </div>
            </div>
        `).join('');
        container.innerHTML = html || '<div style="color: var(--muted);">Aucune PR ouverte</div>';
    }
    
    function renderIssues(container, issues) {
        const html = issues.map(issue => `
            <div class="issue-item">
                <div class="issue-title">
                    <a href="${issue.html_url}" target="_blank" rel="noopener">#${issue.number} - ${issue.title}</a>
                </div>
                <div class="issue-meta">
                    par @${issue.user.login} • ${formatDate(issue.created_at)}
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
        container.innerHTML = html || '<div style="color: var(--muted);">Aucune issue ouverte</div>';
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
        
        element.innerHTML = '<div class="loading">Chargement des données GitHub...</div>';
        
        try {
            // Charger les données
            const [repoData, commits, branches, pulls, issues] = await Promise.all([
                fetchGitHubAPI(owner, repo, ''),
                fetchGitHubAPI(owner, repo, '/commits'),
                fetchGitHubAPI(owner, repo, '/branches'),
                fetchGitHubAPI(owner, repo, '/pulls'),
                fetchGitHubAPI(owner, repo, '/issues')
            ]);
            
            // Créer l'interface
            element.innerHTML = `
                <div id="repo-info-${repo}"></div>
                <div class="github-tabs">
                    <button class="github-tab active" data-tab="commits">📝 Commits</button>
                    <button class="github-tab" data-tab="branches">🌿 Branches</button>
                    <button class="github-tab" data-tab="pulls">🔀 PRs (${pulls.length})</button>
                    <button class="github-tab" data-tab="issues">🐛 Issues (${issues.length})</button>
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
            element.innerHTML = '<div style="color: var(--muted);">Erreur de chargement des données GitHub</div>';
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
