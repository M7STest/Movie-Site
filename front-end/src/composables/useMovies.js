import { ref } from 'vue'
import { useAuth } from './useAuth'

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

export function useMovies() {
  const { getAuthHeaders } = useAuth()

  const movies = ref([])
  const moviesMeta = ref({})
  const movie = ref(null)
  const recentMovies = ref([])
  const recentMoviesLoading = ref(false)
  const loading = ref(false)
  const error = ref(null)

  const fetchMovies = async (title, type, year, page, append = false) => {
    loading.value = true
    error.value = null
    
    try {
        const url = `${API_BASE_URL}/movies/search`
        const headers = getAuthHeaders()

        const body = {title}
        if (type != "" && type) body.type = type
        if (year != "" && year) body.year = parseInt(year)
        if (page != "" && page) body.page = parseInt(page)
        
        const response = await fetch(url, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(body)
        })
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        
        if (append) {
            movies.value = [...movies.value, ...data.data]
        } else {
            movies.value = data.data
        }
        
        moviesMeta.value = data.meta
        
        return data
    } catch (err) {
        error.value = err.message
        console.error('Error fetching movies:', err)
        throw err
    } finally {
        loading.value = false
    }
  }

  const fetchMovie = async (id) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch(`${API_BASE_URL}/movie/${id}`, {
        headers: getAuthHeaders()
      })
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      
      const data = await response.json()
      movie.value = data.data
      
      return movie.value
    } catch (err) {
      error.value = err.message
      console.error('Error fetching movie:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchRecentMovies = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch(`${API_BASE_URL}/movies/recent`, {
        headers: getAuthHeaders()
      })
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      const data = await response.json()
      recentMovies.value = data.data
      
      return data
    } catch (err) {
      error.value = err.message
      console.error('Error fetching recent movies:', err)
      throw err
    } finally {
      loading.value = false
    }
  }


  return {
    movies,
    moviesMeta,
    movie,
    loading,
    error,
    recentMovies,
    recentMoviesLoading,
    fetchMovies,
    fetchMovie,
    fetchRecentMovies
  }
}
