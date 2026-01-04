import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProfileView from '../views/ProfileView.vue'
import CartView from '../views/CartView.vue'
import CheckoutDeliveryView from '../views/CheckoutDeliveryView.vue'
import CheckoutPaymentView from '../views/CheckoutPaymentView.vue'
import CheckoutConfirmationView from '../views/CheckoutConfirmationView.vue'
import FavView from '@/views/FavView.vue'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import PasswordresetView from '@/views/PasswordresetView.vue'
import OrderStatus from '@/views/OrderStatus.vue'
import OrdersView from '@/views/OrdersView.vue'
import RefundView from '@/views/Footer/RefundView.vue'
import AboutusView from '@/views/Footer/AboutusView.vue'
import DeliveryView from '@/views/Footer/DeliveryView.vue'
import PrivacyView from '@/views/Footer/PrivacyView.vue'
import ProductView from '@/views/ProductView.vue'
import AdminView from '@/views/AdminView.vue'
import ProductsGridView from '@/views/ProductsGridView.vue'
import NewPasswordView from '@/views/NewPasswordView.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfileView,
      meta: { requiresAuth: true },
    },
    {
      path: '/favorites',
      name: 'favorites',
      component: FavView,
    },
    {
      path: '/cart',
      name: 'cart',
      component: CartView,
    },
    {
      path: '/checkout/delivery',
      name: 'checkout-delivery',
      component: CheckoutDeliveryView,
    },
    {
      path: '/checkout/payment',
      name: 'checkout-payment',
      component: CheckoutPaymentView,
    },
    {
      path: '/checkout/confirmation',
      name: 'checkout-confirmation',
      component: CheckoutConfirmationView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/password-reset',
      name: 'password-reset',
      component: PasswordresetView,
    },
    {
      path: '/reset-password',
      name: 'reset-password',
      component: () => import('@/views/ResetPasswordView.vue'),
    },
    {
      path: '/orders/status',
      name: 'orders/status',
      component: OrderStatus
    },
    {
      path: '/orders',
      name: 'orders',
      component: OrdersView,
    },
    {
      path: '/refunds',
      name: 'refunds',
      component: RefundView,
    },
    {
      path: '/aboutus',
      name: 'aboutus',
      component: AboutusView,
    },
    {
      path: '/delivery',
      name: 'delivery',
      component: DeliveryView,
    },
    {
      path: '/privacy-policy',
      name: 'privacy-policy',
      component: PrivacyView
    },
    {
      path: '/product/:slug',
      name: 'product',
      component: ProductView,
      props: true,
    },
    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: AdminView,
      props: true,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/category/:catkey',
      name: 'category',
      component: ProductsGridView,
      props: true,
    },
    {
      path: '/search',
      name: 'search',
      component: () => import('@/views/SearchResultsView.vue'),
    },
    {
    path: '/password-reset/:hash',
      name: 'newpassword-reset',
      component: NewPasswordView,
      props: true,
   }
  ],
})

// Navigation guard: only allow access to routes with `meta.requiresAuth` when a token exists
import { getSessionToken } from '@/api'

router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => (record.meta as any)?.requiresAuth)
  const requiresAdmin = to.matched.some(record => (record.meta as any)?.requiresAdmin)

  if (!requiresAuth && !requiresAdmin) {
    return next()
  }

  // Check for persistent token in localStorage or session token
  const persistentToken = localStorage.getItem('token')
  const sessionToken = getSessionToken()

  if (!persistentToken && !sessionToken) {
    // Not authenticated — redirect to home
    return next({ name: 'home' })
  }

  // If route requires admin, verify stored user role (fast client-side check).
  // If no user data is available, or role is not admin/owner, redirect to home.
  if (requiresAdmin) {
    try {
      const userJson = localStorage.getItem('user')
      const user = userJson ? JSON.parse(userJson) : null

      const role = user?.role ?? null
      if (role === 'admin' || role === 'owner') {
        return next()
      }
      // Not an admin — redirect to home
      return next({ name: 'home' })
    } catch (e) {
      // If parse fails or any error, be conservative and redirect to home
      return next({ name: 'home' })
    }
  }

  return next()
})

export default router
