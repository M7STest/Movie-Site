import { ref, computed } from 'vue'

const token = ref(localStorage.getItem('token'))

// Watch for token changes in localStorage
if (typeof window !== 'undefined') {
  window.addEventListener('storage', (e) => {
    if (e.key === 'token') {
      token.value = e.newValue
    }
  })
}

export function useAuth() {
  const isAuthenticated = computed(() => !!token.value)

  const setToken = (newToken) => {
    token.value = newToken
    localStorage.setItem('token', newToken)
  }

  const logout = () => {
    token.value = null
    localStorage.removeItem('token')
    window.location.href = '/login'
  }

  const getAuthHeaders = () => {
    const currentToken = localStorage.getItem('token')
    
    const headers = {
      'Content-Type': 'application/json'
    }
    
    if (currentToken) {
      headers['Authorization'] = `Bearer ${currentToken}`
    }
    
    return headers
  }

  return {
    token,
    isAuthenticated,
    setToken,
    logout,
    getAuthHeaders
  }
}
