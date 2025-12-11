<template>
    <div class="search-view">
        <h1>Search Movies</h1>

        <SearchBar v-model="searchQuery" :show-type-filter="true" :show-year-filter="true" @filter="handleFilter"
            :debounce="500" />

        <div v-if="loading && currentPage === 1" class="loading">Loading...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <div v-else-if="movies.length === 0" class="no-results">
            <span v-if="searchQuery">No movies found</span>
            <span v-else>Start typing to search for movies</span>
        </div>

        <div v-else>
            <div class="results">
                <div v-for="movie in movies" :key="movie.imdbID">
                    <MovieCard :title="movie.title" :year="movie.year" :imdbID="movie.id" :type="movie.type"
                        :poster="movie.poster" />
                </div>
            </div>

            <div v-if="hasMore" class="load-more">
                <button v-if="!loading" @click="loadMore" class="load-more-button">
                    Load More
                </button>
                <div v-else class="loading-more">Loading more...</div>
            </div>

            <div v-if="moviesMeta" class="pagination-info">
                Showing {{ movies.length }} of {{ moviesMeta.total }} movies (Page {{ moviesMeta.current_page }} of {{
                    moviesMeta.pages }})
            </div>
        </div>
    </div>

    <div v-if="!searchQuery && movies.length === 0" class="recent-section">
        <h2>Recent Movies</h2>

        <div v-if="recentMoviesLoading" class="loading">Loading recent movies...</div>
        <div v-else class="recent-grid">
            <div v-for="movie in recentMovies" :key="movie.id">
                <MovieCard :title="movie.title" :year="movie.year" :imdbID="movie.id" :type="movie.type"
                    :poster="movie.poster" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import SearchBar from '@/components/UI/SearchBar.vue'
import MovieCard from '@/components/Movie/MovieCard.vue'
import { useMovies } from '@/composables/useMovies'

const searchQuery = ref('')
const currentPage = ref(1)
const currentFilters = ref({ query: '', type: '', year: '' })

const { movies, moviesMeta, loading, error, fetchMovies, recentMovies, recentMoviesLoading, fetchRecentMovies } = useMovies()

const hasMore = computed(() => {
    return moviesMeta.value && moviesMeta.value.currentPage < moviesMeta.value.pages
})

onMounted(() => {
    fetchRecentMovies()
})

const handleFilter = async (filters) => {
    currentPage.value = 1
    currentFilters.value = filters
    if (!filters.query) {
        movies.value = []
        return
    }
    fetchMovies(filters.query, filters.type, filters.year, 1)
}

const loadMore = async () => {
    if (!hasMore.value || loading.value) return

    currentPage.value++
    fetchMovies(
        currentFilters.value.query,
        currentFilters.value.type,
        currentFilters.value.year,
        currentPage.value,
        true // append to existing results
    )
}
</script>

<style scoped>
.search-view {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

h1 {
    margin-bottom: 2rem;
}

.loading,
.error,
.no-results {
    text-align: center;
    padding: 2rem;
    font-size: 1.1rem;
}

.error {
    color: #ef4444;
}

.results {
    margin: 2rem 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.load-more {
    display: flex;
    justify-content: center;
    margin: 3rem 0 2rem;
}

.load-more-button {
    padding: 1rem 2.5rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.load-more-button:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.load-more-button:active {
    transform: translateY(0);
}

.loading-more {
    color: #94a3b8;
    font-size: 1rem;
}

.pagination-info {
    text-align: center;
    color: #94a3b8;
    font-size: 0.9rem;
    margin: 2rem 0;
}

.recent-section {
    max-width: 1200px;
    margin: 4rem auto 2rem;
    padding: 0 2rem;
}

.recent-section h2 {
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
    color: #f3f4f6;
}

.recent-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}
</style>