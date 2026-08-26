<template>
  <div class="relative inline-flex items-center" ref="rootEl">
    <button
      type="button"
      class="header-menu-btn touch-manipulation"
      :class="{ 'is-open': isOpen }"
      aria-label="Toggle menu"
      :aria-expanded="isOpen"
      @click="toggleSidebar"
    >
      <svg class="w-6 h-6 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <nav
      v-if="isOpen"
      class="menu-overlay"
      aria-label="Site menu"
    >
      <NuxtLink
        to="/"
        class="menu-link"
        :class="{ 'is-active': $route.path === '/' }"
        @click="closeSidebar"
      >
        {{ $t('nav.home') }}
      </NuxtLink>
      <NuxtLink
        v-for="page in navigation"
        :key="page.href"
        :to="page.href"
        class="menu-link"
        :class="{ 'is-active': isTabActive(page.href) }"
        @click="closeSidebar"
      >
        {{ page.name }}
      </NuxtLink>

      <NuxtLink
        v-if="userStore.isAdmin"
        to="/dashboard/admin"
        class="menu-link"
        :class="{ 'is-active': $route.path.startsWith('/dashboard/admin') }"
        @click="closeSidebar"
      >
        {{ $t('nav.admin') }}
      </NuxtLink>

      <NuxtLink
        v-if="!role"
        to="/auth/login"
        class="menu-link"
        :class="{ 'is-active': $route.path.startsWith('/auth/login') }"
        @click="closeSidebar"
      >
        {{ $t('auth.signIn') }}
      </NuxtLink>

      <template v-else>
        <span v-if="showLogoutConfirm" class="flex flex-col items-stretch gap-0.5">
          <button type="button" class="menu-link" @click="cancelLogout">
            {{ $t('common.cancel') }}
          </button>
          <button type="button" class="menu-link is-signout" @click="handleLogout">
            {{ $t('common.confirm') }}
          </button>
        </span>
        <button
          v-else
          type="button"
          class="menu-link is-signout"
          @click="openLogoutConfirm"
        >
          {{ $t('nav.signOut') }}
        </button>
      </template>
    </nav>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '~/stores/user'

const { t } = useI18n()
const route = useRoute()
const userStore = useUserStore()
const { role } = storeToRefs(userStore)
const { isOpen, toggle: toggleSidebar, close: closeSidebar } = useSidebar()
const showLogoutConfirm = ref(false)
const rootEl = ref(null)

const navigation = computed(() => [
  { name: t('nav.admission'), href: '/admission' },
  { name: t('nav.academics'), href: '/academics' },
  { name: t('nav.events'), href: '/events' },
  { name: t('nav.contact'), href: '/contacts' },
])

const isTabActive = (href) =>
  route.path === href || route.path.startsWith(`${href}/`)

const openLogoutConfirm = () => {
  showLogoutConfirm.value = true
}

const cancelLogout = () => {
  showLogoutConfirm.value = false
}

const handleLogout = async () => {
  try {
    userStore.logout()
    navigateTo('/auth/login')
  } finally {
    showLogoutConfirm.value = false
    closeSidebar()
  }
}

const onDocClick = (e) => {
  if (rootEl.value && !rootEl.value.contains(e.target)) {
    showLogoutConfirm.value = false
    closeSidebar()
  }
}

onMounted(() => {
  userStore.hydrate()
  document.addEventListener('click', onDocClick)
})
onUnmounted(() => document.removeEventListener('click', onDocClick))
</script>

<style scoped>
.header-menu-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  padding: 0;
  border-radius: 0;
  color: #1d4ed8;
  font-weight: 600;
  transition: color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}
@media (min-width: 640px) {
  .header-menu-btn {
    width: 2.25rem;
    height: 2.25rem;
    padding: 0;
    border-radius: 9999px;
  }
}
@media (hover: hover) {
  .header-menu-btn:hover {
    background: #dbeafe;
    transform: translateY(-1px);
  }
}
.header-menu-btn.is-open {
  background: none;
  box-shadow: none;
}
@media (min-width: 640px) {
  .header-menu-btn.is-open {
    background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%);
    color: #fff;
    box-shadow: 0 10px 20px -10px rgba(29, 78, 216, 0.7);
  }
}
.dark .header-menu-btn {
  color: #60a5fa;
}
.dark .header-menu-btn:hover {
  background: rgba(30, 64, 175, 0.35);
}
.dark .header-menu-btn.is-open {
  color: #fff;
}

.menu-overlay {
  position: absolute;
  left: 0;
  top: 100%;
  z-index: 130;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  gap: 0.1rem;
  padding: 0.25rem;
  margin-top: 0.25rem;
  min-width: 6.5rem;
  background: #fff;
  border: 1px solid #dbeafe;
  border-radius: 0.5rem;
  box-shadow: 0 8px 20px -12px rgba(29, 78, 216, 0.45);
}
.dark .menu-overlay {
  background: #111827;
  border-color: #1e3a8a;
}

.menu-link {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0.2rem 0.4rem;
  border-radius: 0.35rem;
  font-size: 0.62rem;
  font-weight: 600;
  line-height: 1.2;
  color: #1d4ed8;
  white-space: nowrap;
  text-align: left;
  background: none;
  border: 0;
  cursor: pointer;
}
@media (min-width: 640px) {
  .menu-link {
    padding: 0.28rem 0.55rem;
    font-size: 0.8rem;
  }
}
.menu-link:hover {
  background: #dbeafe;
  color: #1e40af;
}
.menu-link.is-active {
  background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%);
  color: #fff;
}
.menu-link.is-signout {
  color: #dc2626;
}
.menu-link.is-signout:hover {
  background: #fef2f2;
  color: #b91c1c;
}
.dark .menu-link {
  color: #60a5fa;
}
.dark .menu-link:hover {
  background: rgba(30, 64, 175, 0.35);
  color: #93c5fd;
}
.dark .menu-link.is-active {
  color: #fff;
}
.dark .menu-link.is-signout {
  color: #f87171;
}
.dark .menu-link.is-signout:hover {
  background: rgba(127, 29, 29, 0.3);
  color: #fca5a5;
}
@media (max-width: 639px) {
  .menu-overlay {
    min-width: 10rem;
    gap: 0.2rem;
    padding: 0.375rem;
    border-radius: 0.625rem;
  }
  .menu-link {
    min-height: 2.5rem;
    padding: 0.55rem 0.7rem;
    font-size: 0.875rem;
    line-height: 1.3;
    border-radius: 0.5rem;
  }
}
</style>
