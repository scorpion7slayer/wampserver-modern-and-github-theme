<?php
/**
 * Détection Git pour les projets WampServer
 * Scanne chaque projet et extrait les informations du dépôt GitHub
 */

function detectProjectGit($projectPath) {
    $gitDir = $projectPath . DIRECTORY_SEPARATOR . '.git';
    
    if (!is_dir($gitDir)) {
        return null;
    }
    
    $gitConfigFile = $gitDir . DIRECTORY_SEPARATOR . 'config';
    if (!is_file($gitConfigFile)) {
        return null;
    }
    
    $cfg = @file_get_contents($gitConfigFile);
    if ($cfg === false) {
        return null;
    }
    
    // Extraire l'URL du remote origin
    if (!preg_match('/^\s*url\s*=\s*(.+)$/mi', $cfg, $m)) {
        return null;
    }
    
    $url = trim($m[1]);
    $repoSlug = '';
    
    // Format git@github.com:owner/repo.git
    if (preg_match('#github\.com:([^/]+)/([^.\s]+)(?:\.git)?$#', $url, $mm)) {
        $repoSlug = $mm[1] . '/' . $mm[2];
    }
    // Format https://github.com/owner/repo(.git)
    elseif (preg_match('#github\.com/([^/]+)/([^.\s/]+)(?:\.git)?$#', $url, $mm)) {
        $repoSlug = $mm[1] . '/' . $mm[2];
    }
    
    if (empty($repoSlug)) {
        return null;
    }
    
    // Extraire owner et repo
    $parts = explode('/', $repoSlug);
    
    return [
        'slug' => $repoSlug,
        'owner' => $parts[0],
        'repo' => $parts[1],
        'url' => 'https://github.com/' . $repoSlug
    ];
}

function scanAllProjectsGit($projectsList) {
    $gitProjects = [];
    
    foreach ($projectsList as $projectName) {
        $gitInfo = detectProjectGit($projectName);
        if ($gitInfo !== null) {
            $gitProjects[$projectName] = $gitInfo;
        }
    }
    
    return $gitProjects;
}
