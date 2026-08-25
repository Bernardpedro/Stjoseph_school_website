const THEME_KEY = 'sj_theme'
const isDarkMode = ref(false)
let started = false

const apply = (dark: boolean) => {
  isDarkMode.value = dark
  if (!import.meta.client) return

  const root = document.documentElement
  root.classList.toggle('dark', dark)
  root.style.colorScheme = dark ? 'dark' : 'light'
  document.body?.classList.toggle('dark', dark)
  localStorage.setItem(THEME_KEY, dark ? 'dark' : 'light')
}

export function useTheme() {
  const initTheme = () => {
    if (!import.meta.client) return

    const saved = localStorage.getItem(THEME_KEY)
    apply(saved === 'dark')
    started = true
  }

  if (import.meta.client && !started) {
    initTheme()
  }

  const toggleTheme = () => apply(!isDarkMode.value)

  return { isDarkMode, toggleTheme, initTheme }
}
