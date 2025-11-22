// src/utils/themeUtils.js

/**
 * Theme Management Utilities
 */

export const ThemeManager = {
  /**
   * Initialize theme on app start
   */
  init() {
    const savedTheme = localStorage.getItem('theme');
    
    if (savedTheme === 'dark') {
      document.documentElement.classList.add('dark');
      return 'dark';
    } else if (savedTheme === 'light') {
      document.documentElement.classList.remove('dark');
      return 'light';
    } else {
      // Use system preference
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (prefersDark) {
        document.documentElement.classList.add('dark');
        return 'dark';
      }
      return 'light';
    }
  },

  /**
   * Set theme
   * @param {string} theme - 'dark' or 'light'
   */
  setTheme(theme) {
    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
    
    // Dispatch event
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: theme }));
  },

  /**
   * Toggle between dark and light mode
   */
  toggle() {
    const isDark = document.documentElement.classList.contains('dark');
    this.setTheme(isDark ? 'light' : 'dark');
    return !isDark ? 'dark' : 'light';
  },

  /**
   * Get current theme
   * @returns {string} 'dark' or 'light'
   */
  getTheme() {
    return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
  },

  /**
   * Check if dark mode is active
   * @returns {boolean}
   */
  isDarkMode() {
    return document.documentElement.classList.contains('dark');
  }
};

/**
 * Language Management Utilities
 */

export const LanguageManager = {
  languages: [
    { code: 'sk', name: 'Slovenčina', flag: '🇸🇰' },
    { code: 'cz', name: 'Čeština', flag: '🇨🇿' },
    { code: 'en', name: 'English', flag: '🇬🇧' },
    { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
    { code: 'hu', name: 'Magyar', flag: '🇭🇺' },
  ],

  /**
   * Initialize language on app start
   */
  init() {
    const savedLang = localStorage.getItem('language');
    if (savedLang) {
      return this.getLanguageByCode(savedLang) || this.languages[0];
    }
    
    // Default to Slovak
    return this.languages[0];
  },

  /**
   * Set language
   * @param {string} code - Language code (e.g., 'sk', 'en')
   */
  setLanguage(code) {
    const lang = this.getLanguageByCode(code);
    if (lang) {
      localStorage.setItem('language', code);
      
      // Dispatch event
      window.dispatchEvent(new CustomEvent('language-changed', { detail: lang }));
      
      return lang;
    }
    return null;
  },

  /**
   * Get language by code
   * @param {string} code - Language code
   * @returns {object|null}
   */
  getLanguageByCode(code) {
    return this.languages.find(l => l.code === code) || null;
  },

  /**
   * Get current language
   * @returns {object}
   */
  getCurrentLanguage() {
    const savedLang = localStorage.getItem('language');
    return this.getLanguageByCode(savedLang) || this.languages[0];
  },

  /**
   * Get all available languages
   * @returns {array}
   */
  getLanguages() {
    return this.languages;
  }
};

/**
 * Listen for system theme changes
 */
export function watchSystemTheme() {
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
  
  mediaQuery.addEventListener('change', (e) => {
    const savedTheme = localStorage.getItem('theme');
    
    // Only auto-switch if user hasn't manually set a preference
    if (!savedTheme) {
      ThemeManager.setTheme(e.matches ? 'dark' : 'light');
    }
  });
}

/**
 * Export default object with all utilities
 */
export default {
  ThemeManager,
  LanguageManager,
  watchSystemTheme
};