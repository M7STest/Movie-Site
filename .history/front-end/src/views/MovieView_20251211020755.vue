<template>
    <div class="movie-view">
        <div v-if="loading" class="loading">Loading...</div>
        <div v-else-if="error" class="error">{{ error }}</div>

        <div v-else-if="movie" class="movie-details">
            <!-- Hero Section -->
            <div class="hero-section">
                <div class="backdrop">
                    <img :src="movie.poster" :alt="movie.title" />
                </div>
                <div class="hero-content">
                    <div class="poster-container">
                        <img :src="movie.poster" :alt="movie.title" class="poster" />
                    </div>
                    <div class="movie-info">
                        <h1 class="title">{{ movie.title }}</h1>
                        <div class="meta">
                            <span class="year">{{ movie.year }}</span>
                            <span class="rating">⭐ {{ movie.imdbRating }}/10</span>
                            <span class="runtime">{{ movie.runtime }}</span>
                            <span class="genre">{{ movie.genre }}</span>
                        </div>
                        <p class="plot">{{ movie.plot }}</p>
                        <div class="ratings-grid">
                            <div class="rating-item">
                                <span class="rating-label">IMDb</span>
                                <span class="rating-value">{{ movie.ratings?.imdb }}</span>
                            </div>
                            <div class="rating-item">
                                <span class="rating-label">Rotten Tomatoes</span>
                                <span class="rating-value">{{ movie.ratings?.rottenTomatoes }}</span>
                            </div>
                            <div class="rating-item">
                                <span class="rating-label">Metacritic</span>
                                <span class="rating-value">{{ movie.ratings?.metacritic }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="info-section">
                <div class="info-grid">
                    <div class="info-item">
                        <h3>Director</h3>
                        <p>{{ movie.director }}</p>
                    </div>
                    <div class="info-item">
                        <h3>Writer</h3>
                        <p>{{ movie.writer }}</p>
                    </div>
                    <div class="info-item">
                        <h3>Language</h3>
                        <p>{{ movie.language }}</p>
                    </div>
                    <div class="info-item">
                        <h3>Country</h3>
                        <p>{{ movie.country }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useMovies } from '@/composables/useMovies'

const route = useRoute()
const { movie, loading, error, fetchMovie } = useMovies()

onMounted(() => {
    fetchMovie(route.params.id)
})

</script>

<style scoped>
.movie-view {
    min-height: 100vh;
    background: #0f172a;
    color: white;
}

.loading,
.error {
    text-align: center;
    padding: 4rem 2rem;
    font-size: 1.25rem;
}

.error {
    color: #ef4444;
}

/* Hero Section */
.hero-section {
    position: relative;
    min-height: 500px;
    overflow: hidden;
}

.backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
}

.backdrop::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.95));
}

.backdrop img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: blur(8px);
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem 2rem;
    display: flex;
    gap: 3rem;
}

.poster-container {
    flex-shrink: 0;
}

.poster {
    width: 300px;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
}

.movie-info {
    flex: 1;
}

.title {
    font-size: 3rem;
    margin: 0 0 1rem 0;
    font-weight: 800;
}

.meta {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.meta span {
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    font-size: 0.95rem;
    backdrop-filter: blur(10px);
}

.plot {
    font-size: 1.1rem;
    line-height: 1.7;
    margin-bottom: 2rem;
    color: #cbd5e1;
}

.ratings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.rating-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.rating-label {
    display: block;
    font-size: 0.875rem;
    color: #94a3b8;
    margin-bottom: 0.5rem;
}

.rating-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #fbbf24;
}

/* Info Section */
.info-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem 2rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.info-item h3 {
    font-size: 0.875rem;
    color: #94a3b8;
    margin: 0 0 0.5rem 0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.info-item p {
    margin: 0;
    font-size: 1.1rem;
}

/* Actors Section */
.actors-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem 3rem;
}

.actors-section h2 {
    font-size: 2rem;
    margin-bottom: 1.5rem;
}

.actors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.actor-card {
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    gap: 1rem;
    align-items: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.actor-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.actor-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    flex-shrink: 0;
}

.actor-info h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
}

.actor-info p {
    margin: 0;
    font-size: 0.875rem;
    color: #94a3b8;
}

/* Similar Movies */
.similar-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem 4rem;
}

.similar-section h2 {
    font-size: 2rem;
    margin-bottom: 1.5rem;
}

.similar-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.similar-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease;
    aspect-ratio: 2/3;
}

.similar-card:hover {
    transform: scale(1.05);
}

.similar-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.similar-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
}

.similar-info h4 {
    margin: 0 0 0.5rem 0;
    font-size: 0.95rem;
}

.similar-rating {
    font-size: 0.875rem;
    color: #fbbf24;
}

@media (max-width: 768px) {
    .hero-content {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .poster {
        width: 250px;
    }

    .title {
        font-size: 2rem;
    }

    .meta {
        justify-content: center;
    }
}
</style>