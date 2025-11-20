<template>
  <div class="flex bg-gray-100 dark:bg-gray-900 min-h-screen">
    <!-- Sidebar -->
    <aside
      class="w-64 bg-white dark:bg-gray-800 shadow-lg h-full min-h-screen overflow-y-auto"
    >
      <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Admin Panel</h2>
        <nav class="space-y-2">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="w-full text-left px-4 py-3 rounded-lg transition-all duration-200"
            :class="[
              activeTab === tab.id
                ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md'
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            ]"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-16 mr-16 p-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
          {{ tabs.find((t) => t.id === activeTab).label }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          {{ tabs.find((t) => t.id === activeTab).description }}
        </p>
      </div>

      <!-- Orders Management -->
      <div v-if="activeTab === 'orders'" class="space-y-6">
        <!-- Search Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
          <input
            v-model="orderSearch"
            type="text"
            placeholder="Hľadať objednávky (ID, email, meno)..."
            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Orders Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  ID
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Zákazník
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Produkty
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Celkom
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Stav
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Akcie
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="order in filteredOrders"
                :key="order.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                >
                  #{{ order.id }}
                </td>
                <td class="px-6 py-4 text-sm">
                  <div class="text-gray-900 dark:text-white font-medium">
                    {{ order.customerName }}
                  </div>
                  <div class="text-gray-500 dark:text-gray-400">{{ order.email }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                  {{ order.items }} položiek
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                >
                  {{ order.total }} €
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="getStatusClass(order.status)"
                  >
                    {{ order.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button
                    @click="viewOrder(order)"
                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                  >
                    Zobraziť
                  </button>
                  <button
                    @click="deleteOrder(order.id)"
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                  >
                    Vymazať
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Users Management -->
      <div v-if="activeTab === 'users'" class="space-y-6">
        <!-- Search Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
          <input
            v-model="userSearch"
            type="text"
            placeholder="Hľadať používateľov (email, meno)..."
            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Users Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="user in filteredUsers"
            :key="user.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
          >
            <div class="flex items-center mb-4">
              <div
                class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg"
              >
                {{ user.name.charAt(0) }}
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ user.name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
              </div>
            </div>
            <div class="space-y-2 mb-4">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Objednávky:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{
                  user.orders
                }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Celková suma:</span>
                <span class="font-medium text-gray-900 dark:text-white"
                  >{{ user.totalSpent }} €</span
                >
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Registrovaný:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{
                  user.registered
                }}</span>
              </div>
            </div>
            <div class="flex gap-2">
              <button
                @click="resetUserPassword(user.id)"
                class="flex-1 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-colors"
              >
                Reset hesla
              </button>
              <button
                @click="deleteUser(user.id)"
                class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors"
              >
                Vymazať
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Management -->
      <div v-if="activeTab === 'products'" class="space-y-6">
        <!-- Add Product Button -->
        <div class="flex justify-between items-center">
          <input
            v-model="productSearch"
            type="text"
            placeholder="Hľadať produkty..."
            class="flex-1 mr-4 px-4 py-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <button
            @click="showAddProduct = true"
            class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-medium hover:shadow-lg transition-all"
          >
            + Pridať produkt
          </button>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
          >
            <img
              :src="product.image"
              :alt="product.name"
              class="w-full h-48 object-cover"
            />
            <div class="p-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                {{ product.name }}
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                {{ product.brand }}
              </p>
              <div class="flex justify-between items-center mb-4">
                <span class="text-2xl font-bold text-gray-900 dark:text-white"
                  >{{ product.price }} €</span
                >
                <span class="text-sm text-gray-600 dark:text-gray-400"
                  >Sklad: {{ product.stock }}</span
                >
              </div>
              <div class="flex gap-2">
                <button
                  @click="editProduct(product)"
                  class="flex-1 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg text-sm font-medium transition-colors"
                >
                  Upraviť
                </button>
                <button
                  @click="deleteProduct(product.id)"
                  class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors"
                >
                  Vymazať
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div v-if="activeTab === 'stats'" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                  {{ stat.label }}
                </p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                  {{ stat.value }}
                </p>
                <p
                  class="text-sm mt-2"
                  :class="stat.trend > 0 ? 'text-green-500' : 'text-red-500'"
                >
                  {{ stat.trend > 0 ? "+" : "" }}{{ stat.trend }}% vs minulý mesiac
                </p>
              </div>
              <div
                class="w-12 h-12 rounded-full flex items-center justify-center"
                :style="{ background: stat.color }"
              >
                <span class="text-white text-2xl">{{ stat.icon }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Posledná aktivita
          </h3>
          <div class="space-y-4">
            <div
              v-for="activity in recentActivity"
              :key="activity.id"
              class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700 last:border-0"
            >
              <div class="flex items-center">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center mr-4"
                  :class="
                    activity.type === 'order'
                      ? 'bg-green-100 dark:bg-green-900'
                      : 'bg-blue-100 dark:bg-blue-900'
                  "
                >
                  <span class="text-lg">{{
                    activity.type === "order" ? "🛒" : "👤"
                  }}</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ activity.title }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ activity.time }}
                  </p>
                </div>
              </div>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{
                activity.value
              }}</span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal for Order Details -->
    <div
      v-if="selectedOrder"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click="selectedOrder = null"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-2xl w-full mx-4"
        @click.stop
      >
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
          Objednávka #{{ selectedOrder.id }}
        </h2>
        <div class="space-y-4">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Zákazník</p>
            <p class="text-lg font-medium text-gray-900 dark:text-white">
              {{ selectedOrder.customerName }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ selectedOrder.email }}
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Celková suma</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ selectedOrder.total }} €
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Stav</p>
            <span
              class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
              :class="getStatusClass(selectedOrder.status)"
            >
              {{ selectedOrder.status }}
            </span>
          </div>
        </div>
        <button
          @click="selectedOrder = null"
          class="mt-6 w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-medium hover:shadow-lg transition-all"
        >
          Zavrieť
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdminView",
  data() {
    return {
      activeTab: "stats",
      orderSearch: "",
      userSearch: "",
      productSearch: "",
      selectedOrder: null,
      showAddProduct: false,

      tabs: [
        { id: "stats", label: "Štatistiky", description: "Prehľad výkonu vášho obchodu" },
        {
          id: "orders",
          label: "Objednávky",
          description: "Spravujte objednávky zákazníkov",
        },
        {
          id: "users",
          label: "Používatelia",
          description: "Spravujte používateľské účty",
        },
        {
          id: "products",
          label: "Produkty",
          description: "Spravujte produkty v obchode",
        },
      ],

      stats: [
        {
          label: "Celkový predaj",
          value: "45,231 €",
          trend: 12,
          icon: "💰",
          color: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        },
        {
          label: "Objednávky",
          value: "342",
          trend: 8,
          icon: "📦",
          color: "linear-gradient(135deg, #f093fb 0%, #f5576c 100%)",
        },
        {
          label: "Zákazníci",
          value: "1,234",
          trend: 15,
          icon: "👥",
          color: "linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)",
        },
        {
          label: "Priem. objednávka",
          value: "132 €",
          trend: -3,
          icon: "📊",
          color: "linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)",
        },
      ],

      recentActivity: [
        {
          id: 1,
          type: "order",
          title: "Nová objednávka od Ján Novák",
          value: "234 €",
          time: "pred 5 min",
        },
        {
          id: 2,
          type: "user",
          title: "Nová registrácia: Peter Malý",
          value: "",
          time: "pred 15 min",
        },
        {
          id: 3,
          type: "order",
          title: "Objednávka #3421 bola doručená",
          value: "156 €",
          time: "pred 1 hod",
        },
        {
          id: 4,
          type: "order",
          title: "Nová objednávka od Mária Veselá",
          value: "892 €",
          time: "pred 2 hod",
        },
        {
          id: 5,
          type: "user",
          title: "Nová registrácia: Tomáš Horný",
          value: "",
          time: "pred 3 hod",
        },
      ],

      orders: [
        {
          id: 1001,
          customerName: "Ján Novák",
          email: "jan.novak@gmail.com",
          items: 3,
          total: 1599,
          status: "Doručené",
        },
        {
          id: 1002,
          customerName: "Peter Malý",
          email: "peter.maly@gmail.com",
          items: 1,
          total: 279,
          status: "V preprave",
        },
        {
          id: 1003,
          customerName: "Mária Veselá",
          email: "maria.vesela@gmail.com",
          items: 2,
          total: 2398,
          status: "Spracováva sa",
        },
        {
          id: 1004,
          customerName: "Tomáš Horný",
          email: "tomas.horny@gmail.com",
          items: 4,
          total: 456,
          status: "Doručené",
        },
        {
          id: 1005,
          customerName: "Eva Nová",
          email: "eva.nova@gmail.com",
          items: 1,
          total: 1199,
          status: "Zrušené",
        },
      ],

      users: [
        {
          id: 1,
          name: "Ján Novák",
          email: "jan.novak@gmail.com",
          orders: 5,
          totalSpent: 3245,
          registered: "12.5.2024",
        },
        {
          id: 2,
          name: "Peter Malý",
          email: "peter.maly@gmail.com",
          orders: 2,
          totalSpent: 1890,
          registered: "3.8.2024",
        },
        {
          id: 3,
          name: "Mária Veselá",
          email: "maria.vesela@gmail.com",
          orders: 8,
          totalSpent: 5670,
          registered: "18.3.2024",
        },
        {
          id: 4,
          name: "Tomáš Horný",
          email: "tomas.horny@gmail.com",
          orders: 3,
          totalSpent: 2340,
          registered: "25.6.2024",
        },
        {
          id: 5,
          name: "Eva Nová",
          email: "eva.nova@gmail.com",
          orders: 1,
          totalSpent: 1199,
          registered: "9.10.2024",
        },
        {
          id: 6,
          name: "Michal Dlhý",
          email: "michal.dlhy@gmail.com",
          orders: 12,
          totalSpent: 8920,
          registered: "5.1.2024",
        },
      ],

      products: [
        {
          id: 1,
          name: "iPhone 15 Pro",
          brand: "Apple",
          price: 1299,
          stock: 10,
          image:
            "https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600&q=80",
        },
        {
          id: 2,
          name: "AirPods Pro",
          brand: "Apple",
          price: 279,
          stock: 15,
          image:
            "https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=600&q=80",
        },
        {
          id: 3,
          name: "MacBook Air M2",
          brand: "Apple",
          price: 1199,
          stock: 5,
          image:
            "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80",
        },
        {
          id: 4,
          name: "Samsung Galaxy S24",
          brand: "Samsung",
          price: 999,
          stock: 8,
          image:
            "https://images.unsplash.com/photo-1681592744820-6a0f4f1d3b5b?w=600&q=80",
        },
      ],
    };
  },
  computed: {
    filteredOrders() {
      if (!this.orderSearch) return this.orders;
      const search = this.orderSearch.toLowerCase();
      return this.orders.filter(
        (order) =>
          order.id.toString().includes(search) ||
          order.customerName.toLowerCase().includes(search) ||
          order.email.toLowerCase().includes(search)
      );
    },
    filteredUsers() {
      if (!this.userSearch) return this.users;
      const search = this.userSearch.toLowerCase();
      return this.users.filter(
        (user) =>
          user.name.toLowerCase().includes(search) ||
          user.email.toLowerCase().includes(search)
      );
    },
    filteredProducts() {
      if (!this.productSearch) return this.products;
      const search = this.productSearch.toLowerCase();
      return this.products.filter(
        (product) =>
          product.name.toLowerCase().includes(search) ||
          product.brand.toLowerCase().includes(search)
      );
    },
  },
  methods: {
    getStatusClass(status) {
      const classes = {
        Doručené: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300",
        "V preprave": "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
        "Spracováva sa":
          "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300",
        Zrušené: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300",
      };
      return (
        classes[status] || "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300"
      );
    },
    viewOrder(order) {
      this.selectedOrder = order;
    },
    deleteOrder(id) {
      if (confirm("Naozaj chcete vymazať túto objednávku?")) {
        this.orders = this.orders.filter((order) => order.id !== id);
        alert("Objednávka vymazaná!");
      }
    },
    deleteUser(id) {
      if (confirm("Naozaj chcete vymazať tento účet?")) {
        this.users = this.users.filter((user) => user.id !== id);
        alert("Používateľ vymazaný!");
      }
    },
    resetUserPassword(id) {
      if (confirm("Naozaj chcete resetovať heslo tohto používateľa?")) {
        alert("Reset hesla bol odoslaný na email používateľa!");
      }
    },
    editProduct(product) {
      alert(`Úprava produktu: ${product.name}`);
    },
    deleteProduct(id) {
      if (confirm("Naozaj chcete vymazať tento produkt?")) {
        this.products = this.products.filter((product) => product.id !== id);
        alert("Produkt vymazaný!");
      }
    },
  },
};
</script>

<style scoped>
@media (max-width: 768px) {
  aside {
    width: 100%;
    position: relative;
  }
  main {
    margin-left: 0;
  }
}
</style>
