import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../api'

export interface CartItem {
  id: number
  product_id: number
  title: string
  price: number
  variant_price?: number
  image: string
  description: string
  quantity: number
  variant_options?: Record<string, string>
  variants?: Record<string, string[]>
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
      const raw = stored ? JSON.parse(stored) : []
      // Normalize loaded items: ensure numeric unique id, parse variant_options if string
      cartItems.value = raw.map((it: any) => {
        // parse variant_options if it's a string
        let variant_options = it.variant_options
        if (typeof variant_options === 'string') {
          try {
            variant_options = JSON.parse(variant_options)
          } catch (e) {
            // keep as string if parsing fails
          }
        }

        // parse variants if it's a string
        let variants = it.variants
        if (typeof variants === 'string') {
          try {
            variants = JSON.parse(variants)
          } catch (e) {
            // keep as string if parsing fails
          }
        }

        // ensure id exists and is numeric
        let id = it.id
        if (!id) {
          id = Number(String(Date.now()) + String(Math.floor(Math.random() * 1000)))
        }

        return {
          id,
          product_id: it.product_id ?? it.id ?? it.productId,
          title: it.title ?? it.name,
          price: it.price ?? 0,
          variant_price: it.variant_price ?? undefined,
          image: it.image ?? '',
          description: it.description ?? '',
          quantity: it.quantity ?? 1,
          variant_options: variant_options ?? null,
          variants: variants ?? undefined,
        }
      })
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
        const payload: any = {
          product_id: product.id,
          quantity: 1,
        }
        
        // Include variant_options if provided and not empty
        if (product.variant_options && Object.keys(product.variant_options).length > 0) {
          payload.variant_options = product.variant_options
          console.log('[Cart] Including variants:', payload.variant_options)
        } else {
          console.log('[Cart] No variants selected')
        }
        
        console.log('[Cart] Payload:', payload)
        const response = await api.post('/api/cart', payload)
        console.log('[Cart] Item added to database:', response.data)
        await loadCart()
      } catch (error) {
        console.error('[Cart] Failed to add item to cart:', error)
      }
    } else {
      // Save to localStorage and keep separate items for different variant selections
      const normalizeVariants = (v?: Record<string, string> | null | string) => {
        if (!v) return null
        // if string, try to parse
        let obj: any = v
        if (typeof v === 'string') {
          try {
            obj = JSON.parse(v)
          } catch (e) {
            // keep as string
            return JSON.stringify(v)
          }
        }
        try {
          const keys = Object.keys(obj).sort()
          const ordered: Record<string, string> = {}
          for (const k of keys) ordered[k] = obj[k]
          return JSON.stringify(ordered)
        } catch (e) {
          return JSON.stringify(obj)
        }
      }

      // Deep-clone product.variant_options to avoid storing a reference
      const cloneVariantOptions = (v?: Record<string, string> | null | string) => {
        if (!v) return null
        if (typeof v === 'string') {
          try {
            return JSON.parse(v)
          } catch (e) {
            return v
          }
        }
        try {
          return JSON.parse(JSON.stringify(v))
        } catch (e) {
          return v
        }
      }

      const targetVariant = normalizeVariants(cloneVariantOptions(product.variant_options))

      const existing = cartItems.value.find(item => {
        if (item.product_id !== product.id) return false
        return normalizeVariants(item.variant_options) === targetVariant
      })

      if (existing) {
        existing.quantity += 1
      } else {
        // Generate a unique id for local cart item so UI actions work by id
        const localId = Number(String(Date.now()) + String(Math.floor(Math.random() * 1000)))
        const newItem = {
          id: localId,
          product_id: product.id,
          title: product.title,
          price: product.calculated_price ?? product.price,
          variant_price: product.variant_price,
          image: product.image,
          description: product.description,
          quantity: 1,
          // store a deep-cloned copy so later changes in the product view
          // won't mutate previously added cart items
          variant_options: cloneVariantOptions(product.variant_options),
          variants: product.variants ? JSON.parse(JSON.stringify(product.variants)) : undefined,
        }
        cartItems.value.push(newItem)
      }
      saveToLocalStorage()
      console.log('[Cart] Local item saved:', {
        isLoggedIn: isLoggedIn.value,
        cartSize: cartItems.value.length,
        lastItem: cartItems.value[cartItems.value.length - 1]
      })
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
          const payload: any = {
            product_id: item.product_id,
            quantity: item.quantity,
          }
          if (item.variant_options && Object.keys(item.variant_options).length > 0) {
            payload.variant_options = item.variant_options
          }
          await api.post('/api/cart', payload)
        }
        localStorage.removeItem('cart')
        await loadCart()
      } catch (error) {
        console.error('Failed to migrate cart to database:', error)
      }
    }
  }

  // Update cart item variants
  const updateCartItemVariants = async (cartItemId: number, variantOptions: Record<string, string>) => {
    if (isLoggedIn.value) {
      try {
        await api.put(`/api/cart/${cartItemId}`, {
          variant_options: variantOptions,
        })
        await loadCart()
      } catch (error) {
        console.error('Failed to update cart item variants:', error)
        throw error
      }
    } else {
      // For local storage, update the item
      const item = cartItems.value.find(i => i.id === cartItemId)
      if (item) {
        item.variant_options = variantOptions
        saveToLocalStorage()
      }
    }
  }

  // Computed properties
  const totalItems = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => {
      const price = item.variant_price ?? item.price
      return sum + price * item.quantity
    }, 0)
  })

  // Auto-initialize auth and cart on store load
  ;(async () => {
    await initializeAuth()
    // If user is logged in and there's a local cart, migrate it
    const hasLocal = !!localStorage.getItem('cart')
    if (isLoggedIn.value && hasLocal) {
      try {
        await migrateToDatabase()
      } catch (e) {
        console.error('Auto-migrate failed', e)
      }
    }
    // Load current cart (from DB if logged, local otherwise)
    await loadCart()
  })()

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
    updateCartItemVariants,
  }
})
