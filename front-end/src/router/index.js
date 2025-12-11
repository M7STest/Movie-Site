import { createRouter, createWebHistory } from 'vue-router'
import BaseLayout from '../layout/BaseLayout.vue'
import LoginLayout from '../layout/LoginLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      component: LoginLayout,
      children: [
        {
          path: '',
          name: 'login',
          component: () => import('../views/LoginView.vue'),
          meta: { requiresGuest: true }
        }
      ]
    },
    {
      path: '/',
      component: BaseLayout,
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('../views/SearchView.vue'),
          meta: { requiresAuth: true }
        },
        {
          path: 'movie/:id',
          name: 'movie',
          component: () => import('../views/MovieView.vue'),
          meta: { requiresAuth: true }
        }
      ]
    }
  ],
})

// Navigation guard for JWT authentication
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)

  if (requiresAuth && !token) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else if (requiresGuest && token) {
    next({ name: 'home' })
  } else {
    next()
  }
})

export default router
