import axios from 'axios'
import router from './router'

const api = axios.create();

api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
api.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector(
    'meta[name="csrf-token"]'
).content

api.interceptors.response.use(
    response => response,
    error => {
        const {status} = error.response

        // Show the user a 500 error
        if (status >= 500) {
            Cms.flash('error', error.response.data.message)
        }

        // Handle Session Timeouts
        if (status === 401) {
            window.location.href = '/admin'
        }

        // Handle Not Found
        if (status === 404) {
            router.push({name: 'not-found'})
        }

        // Handle Token Timeouts
        if (status === 419) {
            refreshToken()
        }

        return Promise.reject(error)
    }
)

async function refreshToken() {
    await api({
        method: 'get',
        url: '/sanctum/csrf-cookie'
    })
}

export default api