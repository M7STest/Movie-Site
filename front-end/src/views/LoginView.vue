<template>
    <div class="login-view">
        <div class="login-container">
            <h1>Movie Search Engine</h1>

            <form @submit.prevent="handleLogin">
                <input id="email" v-model="email" type="email" placeholder="Email" required />

                <input id="password" v-model="password" type="password" placeholder="Password" required />

                <div v-if="error" class="error-message">{{ error }}</div>

                <button type="submit" :disabled="loading">
                    {{ loading ? 'Signing in...' : 'Sign In' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref(null)

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const handleLogin = async () => {
    loading.value = true
    error.value = null

    try {
        const response = await fetch(`${API_BASE_URL}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: email.value,
                password: password.value
            })
        })

        const data = await response.json()

        if (!response.ok) {
            throw new Error(data.message || 'Login failed')
        }

        // Store JWT token
        localStorage.setItem('token', data.token)

        // Redirect to intended page or home
        const redirect = route.query.redirect || '/'
        router.push(redirect)
    } catch (err) {
        error.value = err.message
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.login-view {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.login-container {
    width: 100%;
    max-width: 400px;
}

h1 {
    color: white;
    font-size: 2rem;
    text-align: center;
    margin: 0 0 3rem 0;
    font-weight: 800;
}

form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

input {
    padding: 1rem 1.25rem;
    background: #1e293b;
    border: 2px solid #334155;
    border-radius: 8px;
    color: white;
    font-size: 1rem;
    transition: all 0.3s ease;
}

input::placeholder {
    color: #64748b;
}

input:focus {
    outline: none;
    border-color: #3b82f6;
    background: #1f2937;
}

.error-message {
    padding: 0.875rem;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid #ef4444;
    color: #fca5a5;
    border-radius: 8px;
    font-size: 0.9rem;
    text-align: center;
}

button {
    padding: 1rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 0.5rem;
}

button:hover:not(:disabled) {
    background: #2563eb;
}

button:active:not(:disabled) {
    transform: scale(0.98);
}

button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
