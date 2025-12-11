<template>
    <div>
        <nav class="top-nav">
            <router-link to="/" class="logo">Movie Search Engine</router-link>
            <button @click="handleLogout" class="logout-button">Logout</button>
        </nav>
        <div>
            <router-view />
        </div>
    </div>
</template>


<script setup>
import { useAuth } from '@/composables/useAuth'

const { logout } = useAuth()

const handleLogout = () => {
    logout()
}
</script>

<style scoped>
.top-nav {
    background: #1f2937;
    padding: 1rem 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    text-decoration: none;
    transition: opacity 0.2s ease;
}

.logo:hover {
    opacity: 0.8;
}

.logout-button {
    padding: 0.5rem 1.25rem;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.logout-button:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
}

.logout-button:active {
    transform: scale(0.98);
}
</style>
