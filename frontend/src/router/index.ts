import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProfileView from '../views/ProfileView.vue'
import CartView from '../views/CartView.vue'
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
      path: '/PasswordReset',
      name: 'PasswordReset',
      component: PasswordresetView,
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
    }
  ],
})

export default router
