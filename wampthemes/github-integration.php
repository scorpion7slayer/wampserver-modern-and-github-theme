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

@media screen and (max-width: 750px) {
    .project-git-info {
        font-size: 11px;
    }
}
</style>
EOGITJS;
