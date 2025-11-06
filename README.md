# WampServer Modern & GitHub Themes

Custom themes and enhanced UI features for WampServer homepage.

## 🎨 Themes

### 1. **github-theme** (Light)

GitHub-inspired light theme with clean design.

-   **Palette**: White panels, `#0969da` accent
-   **Features**: Cards, pills, smooth transitions

### 2. **github-theme-dark** (Dark Dimmed)

GitHub Dark Dimmed variant for night mode.

-   **Palette**: `#22272e` background, `#539bf5` accent
-   **Features**: High contrast, modern cards

### 3. **Modern Dark**

Vibrant dark theme with multiple accent colors.

-   **Palette**: 4 cycling accents (blue, green, orange, pink)
-   **Features**: Colorful UI, micro-interactions

## ✨ Features

### 🐙 GitHub Integration

-   **Auto-detection**: Widget appears only when `.git` exists
-   **Quick access**: Open repo, issues, pulls with one click
-   **Auto-fill**: Reads `owner/repo` from `.git/config`

### 🛠️ Productivity Tools

-   **Sort A→Z**: Alphabetically sort any list
-   **Export JSON**: Copy visible items as JSON
-   **Copy list**: Copy items as plain text
-   **Copy config**: One-click config copy
-   **Collapse/expand**: Per-column and global controls

### ⌨️ Keyboard Shortcuts

| Shortcut | Action                   |
| -------- | ------------------------ |
| `/`      | Focus global search      |
| `Alt+E`  | Expand all lists         |
| `Alt+C`  | Collapse all lists       |
| `Alt+N`  | Toggle "open in new tab" |
| `g g`    | Scroll to top            |

### 🎯 Floating Toolbar

Fixed bottom-right toolbar with:

-   ↑ Scroll to top
-   Toggle expand/collapse all
-   Toggle new tab behavior

### 🏷️ Theme Credit

Custom themes display: **"— <theme name> by [scorpion7slayer](https://github.com/scorpion7slayer)"** next to the title.

## 📦 Installation

1. **Download files**:

    - `index.php` (modified)
    - `wampthemes/enhancements.php` (new)
    - `wampthemes/github-theme/style.css` (new)
    - `wampthemes/github-theme-dark/style.css` (new)
    - `wampthemes/Modern Dark/style.css` (new)

2. **Replace/add** files in your WampServer `www` directory.

3. **Select theme** from the dropdown on the homepage.

## 🔧 Technical Details

### Modified Files

-   **`index.php`**: Fixed duplicate link IDs, added `enhancements.php` include

### New Files

-   **`wampthemes/enhancements.php`**: All JS/CSS enhancements
-   **Theme files**: 3 complete custom themes

### Compatibility

-   ✅ Fully backwards compatible
-   ✅ Doesn't affect existing themes
-   ✅ Works with all WampServer versions (2.5+)
-   ✅ No dependencies

## 🖼️ Screenshots

### github-theme (Light)

Clean, professional GitHub-inspired design.

### github-theme-dark (Dark Dimmed)

Perfect for night coding sessions.

### Modern Dark

Vibrant colors with smooth animations.

## 🤝 Contributing

Feel free to:

-   Report issues
-   Suggest new features
-   Submit pull requests
-   Share your own theme variants

## 📝 License

Created by [scorpion7slayer](https://github.com/scorpion7slayer)

---

**⭐ If you like this project, give it a star!**
