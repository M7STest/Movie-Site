<template>
    <div class="movie-card" @click="$router.push(`/movie/${imdbID}`)">
        <div class="card-image">
            <img :src="poster" :alt="title" />
            <div class="card-overlay">
                <div class="card-content">
                    <h3 class="card-title">{{ title }}</h3>
                    <div class="card-meta">
                        <span class="year">{{ year }}</span>
                        <span class="type">{{ type }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    title: {
        type: String,
    },
    year: {
        type: String,
    },
    imdbID: {
        type: String,
    },
    type: {
        type: String,
    },
    poster: {
        type: String
    },
})

</script>

<style scoped>
.movie-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #1f2937;
    aspect-ratio: 2/3;
}

.movie-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.card-image {
    position: relative;
    width: 100%;
    height: 100%;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top,
            rgba(0, 0, 0, 0.9) 0%,
            rgba(0, 0, 0, 0.6) 50%,
            rgba(0, 0, 0, 0.3) 70%,
            transparent 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 1.5rem;
    opacity: 1;
    transition: opacity 0.3s ease;
}

.movie-card:hover .card-overlay {
    opacity: 1;
}

.card-content {
    color: white;
}

.card-title {
    margin: 0 0 0.75rem 0;
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1.3;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.card-meta {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    font-size: 0.875rem;
}

.year,
.type {
    padding: 0.25rem 0.75rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(8px);
    border-radius: 6px;
    font-weight: 500;
    text-transform: capitalize;
}

/* Fallback for missing posters */
.card-image img[src=""],
.card-image img[src="N/A"] {
    display: none;
}

.card-image img[src=""]~.card-overlay,
.card-image img[src="N/A"]~.card-overlay {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>