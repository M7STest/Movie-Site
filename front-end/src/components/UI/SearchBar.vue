<template>
    <div class="search-bar">
        <div class="search-container">
            <div class="search-input-wrapper">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>

                <input v-model="searchQuery" type="text" :placeholder="placeholder" class="search-input" />

                <button v-if="searchQuery || selectedType || selectedYear" @click="clearSearch" class="clear-button"
                    aria-label="Clear search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div v-if="showFilters" class="filters-wrapper">
                <select v-if="showTypeFilter" v-model="selectedType" class="filter-select">
                    <option value="">All Types</option>
                    <option value="movie">Movie</option>
                    <option value="series">Series</option>
                    <option value="episode">Episode</option>
                </select>

                <select v-if="showYearFilter" v-model="selectedYear" class="filter-select">
                    <option value="">All Years</option>
                    <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Search movies...'
    },
    debounce: {
        type: Number,
        default: 300
    },
    showTypeFilter: {
        type: Boolean,
        default: false
    },
    showYearFilter: {
        type: Boolean,
        default: false
    },
    yearRange: {
        type: Object,
        default: () => ({ start: 1900, end: new Date().getFullYear() })
    }
})

const emit = defineEmits(['update:modelValue', 'search', 'filter'])

const searchQuery = ref(props.modelValue)
const selectedType = ref('')
const selectedYear = ref('')
let debounceTimer = null

const showFilters = computed(() => props.showTypeFilter || props.showYearFilter)

const years = computed(() => {
    const yearList = []
    for (let year = props.yearRange.end; year >= props.yearRange.start; year--) {
        yearList.push(year)
    }
    return yearList
})

const emitSearch = () => {
    const filters = {
        query: searchQuery.value,
        type: selectedType.value,
        year: selectedYear.value
    }
    emit('search', searchQuery.value)
    emit('filter', filters)
}

watch(searchQuery, (newValue) => {
    emit('update:modelValue', newValue)

    if (debounceTimer) {
        clearTimeout(debounceTimer)
    }

    debounceTimer = setTimeout(() => {
        emitSearch()
    }, props.debounce)
})

watch([selectedType, selectedYear], () => {
    emitSearch()
})

watch(() => props.modelValue, (newValue) => {
    searchQuery.value = newValue
})

const clearSearch = () => {
    searchQuery.value = ''
    selectedType.value = ''
    selectedYear.value = ''
}
</script>

<style scoped>
.search-bar {
    width: 100%;
}

.search-container {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 250px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.search-input-wrapper:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-icon {
    position: absolute;
    left: 16px;
    color: #9ca3af;
    pointer-events: none;
    transition: color 0.3s ease;
}

.search-input-wrapper:focus-within .search-icon {
    color: #3b82f6;
}

.search-input {
    width: 100%;
    padding: 14px 48px;
    font-size: 16px;
    border: none;
    outline: none;
    background: transparent;
    color: #1f2937;
    border-radius: 12px;
}

.search-input::placeholder {
    color: #9ca3af;
}

.clear-button {
    position: absolute;
    right: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    padding: 0;
    background: #f3f4f6;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s ease;
}

.clear-button:hover {
    background: #e5e7eb;
    color: #374151;
}

.clear-button:active {
    transform: scale(0.95);
}

.filters-wrapper {
    display: flex;
    gap: 12px;
}

.filter-select {
    padding: 14px 16px;
    font-size: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    background: #ffffff;
    color: #1f2937;
    cursor: pointer;
    transition: all 0.3s ease;
    outline: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    min-width: 140px;
}

.filter-select:hover {
    border-color: #d1d5db;
}

.filter-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .search-input-wrapper {
        background: #1f2937;
        border-color: #374151;
    }

    .search-input-wrapper:focus-within {
        border-color: #3b82f6;
    }

    .search-input {
        color: #f9fafb;
    }

    .search-input::placeholder {
        color: #6b7280;
    }

    .clear-button {
        background: #374151;
        color: #9ca3af;
    }

    .clear-button:hover {
        background: #4b5563;
        color: #d1d5db;
    }

    .filter-select {
        background: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }

    .filter-select:hover {
        border-color: #4b5563;
    }

    .filter-select:focus {
        border-color: #3b82f6;
    }
}
</style>