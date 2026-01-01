import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../api'

export interface CartItem {
  id: number
  product_id: number
  title: string
  price: number
  image: string
  description: string
  quantity: number
}

export const useCartStore = defineStore('cart', () => {
  const cartItems = ref<CartItem[]>([])
  const isLoggedIn = ref(false)

  // Check if user is logged in
  const initializeAuth = async () => {
    try {
      const response = await api.get('/api/user')
      isLoggedIn.value = !!response.data
      console.log('[Cart] Auth initialized. isLoggedIn:', isLoggedIn.value, 'User:', response.data)
    } catch (error) {
      isLoggedIn.value = false
      console.log('[Cart] Auth check failed:', error)
    }
  }

  // Load cart from localStorage or API
  const loadCart = async () => {
    if (isLoggedIn.value) {
      // Load from database via API
      try {
        const response = await api.get('/api/cart')
        cartItems.value = response.data
      } catch (error) {
        console.error('Failed to load cart from database:', error)
      }
    } else {
      // Load from localStorage
      const stored = localStorage.getItem('cart')
      cartItems.value = stored ? JSON.parse(stored) : []
    }
  }

  // Save cart to localStorage (for unauthenticated users)
  const saveToLocalStorage = () => {
    localStorage.setItem('cart', JSON.stringify(cartItems.value))
  }

  // Add item to cart
  const addToCart = async (product: CartItem) => {
    const existingItem = cartItems.value.find(item => item.id === product.id)

    // Ensure auth is initialized before deciding where to save
    if (!isLoggedIn.value) {
      await initializeAuth()
    }

    console.log('[Cart] Adding product:', product.title, 'isLoggedIn:', isLoggedIn.value)

    if (isLoggedIn.value) {
      // Save to database
      try {
        const response = await api.post('/api/cart', {
          product_id: product.id,
          quantity: 1,
        })
        console.log('[Cart] Item added to database:', response.data)
        await loadCart()
      } catch (error) {
        console.error('[Cart] Failed to add item to cart:', error)
      }
    } else {
      // Save to localStorage
      if (existingItem) {
        existingItem.quantity += 1
      } else {
        cartItems.value.push({
          id: product.id,
          product_id: product.id,
          title: product.title,
          price: product.price,
          image: product.image,
          description: product.description,
          quantity: 1,
        })
      }
      saveToLocalStorage()
      console.log('[Cart] Item added to localStorage')
    }
  }

  // Increase quantity
  const increaseQuantity = async (productId: number) => {
    if (isLoggedIn.value) {
      const item = cartItems.value.find(i => i.id === productId)
      if (item) {
        try {
          await api.put(`/api/cart/${productId}`, {
            quantity: item.quantity + 1,
          })
          item.quantity += 1
        } catch (error) {
          console.error('Failed to update quantity:', error)
        }
      }
    } else {
      const item = cartItems.value.find(i => i.id === productId)
      if (item) {
        item.quantity += 1
        saveToLocalStorage()
      }
    }
  }

  // Decrease quantity
  const decreaseQuantity = async (productId: number) => {
    if (isLoggedIn.value) {
      const item = cartItems.value.find(i => i.id === productId)
      if (item && item.quantity > 1) {
        try {
          await api.put(`/api/cart/${productId}`, {
            quantity: item.quantity - 1,
          })
          item.quantity -= 1
        } catch (error) {
          console.error('Failed to update quantity:', error)
        }
      }
    } else {
      const item = cartItems.value.find(i => i.id === productId)
      if (item && item.quantity > 1) {
        item.quantity -= 1
        saveToLocalStorage()
      }
    }
  }

  // Remove item from cart
  const removeItem = async (productId: number) => {
    if (isLoggedIn.value) {
      try {
        await api.delete(`/api/cart/${productId}`)
        cartItems.value = cartItems.value.filter(item => item.id !== productId)
      } catch (error) {
        console.error('Failed to remove item:', error)
      }
    } else {
      cartItems.value = cartItems.value.filter(item => item.id !== productId)
      saveToLocalStorage()
    }
  }

  // Clear entire cart
  const clearCart = async () => {
    if (isLoggedIn.value) {
      try {
        await api.delete('/api/cart')
        cartItems.value = []
      } catch (error) {
        console.error('Failed to clear cart:', error)
      }
    } else {
      cartItems.value = []
      saveToLocalStorage()
    }
  }

  // Migrate localStorage to database on login
  const migrateToDatabase = async () => {
    const localCart = localStorage.getItem('cart')
    if (localCart) {
      try {
        const items = JSON.parse(localCart)
        for (const item of items) {
          await api.post('/api/cart', {
            product_id: item.product_id,
            quantity: item.quantity,
          })
        }
        localStorage.removeItem('cart')
        await loadCart()
      } catch (error) {
        console.error('Failed to migrate cart to database:', error)
      }
    }
  }

  // Computed properties
  const totalItems = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
  })

  return {
    cartItems,
    isLoggedIn,
    totalItems,
    subtotal,
    initializeAuth,
    loadCart,
    addToCart,
    increaseQuantity,
    decreaseQuantity,
    removeItem,
    clearCart,
    migrateToDatabase,
  }
})
