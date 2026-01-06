# WampServer Modern & GitHub Themes

Modern and responsive themes for WampServer homepage with GitHub integration.

## 🎨 Available Themes

-   **github-theme** - GitHub-inspired light theme
-   **github-theme-dark** - GitHub Dark Dimmed theme
-   **Modern Dark** - Vibrant dark theme with multiple accent colors

All themes are **fully responsive** and automatically adapt to mobile, tablet, and desktop screens.

## 🌐 Live Demo

**[View the standalone HTML demo](https://scorpion7slayer.github.io/wampserver-modern-and-github-theme/index.html)** - A static demonstration page that showcases all themes and features without requiring WampServer or PHP. Perfect for previewing the themes before installation!

The demo includes:
- All three themes with live switching
- Full internationalization (English/French)
- Mock data simulating real WampServer environment
- All interactive features (modals, buttons, GitHub integration)

## 📦 Installation

### Step 1: Download the files

Clone or download this repository:

```bash
git clone https://github.com/scorpion7slayer/wampserver-modern-and-github-theme.git
```

Or download the ZIP file from GitHub.

### Step 2: Copy the files

Copy the following files to your WampServer `www` directory (typically `C:\wamp64\www` or `C:\wamp\www`):

-   `index.php` (replaces the existing file)
-   `wampthemes/` (the complete folder with all themes)

**Final structure:**
```
C:\wamp64\www\
├── index.php
├── wampthemes/
│   ├── enhancements.php
│   ├── github-theme/
│   │   └── style.css
│   ├── github-theme-dark/
│   │   └── style.css
│   └── Modern Dark/
│       └── style.css
└── ... (other WampServer files)
```

### Step 3: Select a theme

1. Open your browser and go to `http://localhost`
2. In the dropdown menu at the top right, select your preferred theme
3. The theme will be applied immediately and saved automatically

## ✨ Features

-   🎨 **3 modern themes** with professional design
-   🌐 **Internationalization** - Full English and French support
-   📱 **Enhanced responsive design** - Optimized for all screen sizes with 6 breakpoints
-   📱 **Touch-optimized** - 44px minimum touch targets for mobile devices (you never know)
-   🐙 **GitHub integration** - Automatic Git repository detection
-   ⌨️ **Keyboard shortcuts** for quick navigation
-   🛠️ **Productivity tools** - Config copy (with browser fallback), GitHub links
-   🎯 **Floating toolbar** for quick access
-   ♿ **Accessibility** - Focus states and touch optimizations

## 🌐 Language Support

The interface automatically adapts to your WampServer language setting:

-   **English** - Full UI translation
-   **Français** - Traduction complète de l'interface
-   **Fallback** - Defaults to English for other languages

Language selection is synchronized with WampServer's language preference.

## 🔧 Compatibility

-   ✅ WampServer 2.5 and higher
-   ✅ Compatible with all existing themes
-   ✅ No external dependencies
-   ✅ Backward compatible
-   ✅ Modern browsers (full features)
-   ✅ Older browsers (graceful fallback for clipboard API)
-   ✅ Mobile devices (touch-optimized)

## 📊 Technical Highlights

-   **Optimized code** - 45% reduction in Modern Dark theme (525 lines removed)
-   **Responsive breakpoints** - 6 breakpoints (480px, 750px, 900px, 980px, 1200px, desktop)
-   **Touch targets** - Minimum 44px for accessibility
-   **Performance** - No external dependencies, vanilla JS/CSS
-   **Security** - CodeQL scanned, no vulnerabilities

## 📝 License

Created by [scorpion7slayer](https://github.com/scorpion7slayer)

---

**⭐ If you like this project, give it a star!**
