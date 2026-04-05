<template>
	<div class="min-h-screen bg-white dark:bg-gray-900">
		<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white">
					{{ $t("pages.orders.list.title") }}
				</h1>
				<p class="text-gray-600 dark:text-gray-400 mt-2">
					{{ $t("pages.orders.list.total_orders", { count: orders.length }) }}
				</p>
			</div>

			<div class="flex gap-2 mb-6 overflow-x-auto pb-2">
				<button
					v-for="filter in filters"
					:key="filter.value"
					@click="activeFilter = filter.value"
					class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors"
					:class="
						activeFilter === filter.value
							? 'bg-indigo-600 text-white'
							: 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
					"
				>
					{{ filter.label }}
				</button>
			</div>

			<div class="space-y-4">
				<div
					v-for="order in filteredOrders"
					:key="order.id"
					class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
				>
					<div class="p-6">
						<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
							<div>
								<div class="flex items-center gap-3 mb-2">
									<h3 class="text-lg font-bold text-gray-900 dark:text-white">
										{{ $t("pages.orders.list.order_number", { id: order.id }) }}
									</h3>
									<span
										class="px-3 py-1 rounded-full text-xs font-semibold"
										:class="getStatusClass(order.status_key)"
									>
										{{ getOrderStatusLabel(order.status_key) }}
									</span>
								</div>
								<p class="text-sm text-gray-600 dark:text-gray-400">
									{{ order.date }}
								</p>
							</div>
							<div class="text-left sm:text-right">
								<p class="text-2xl font-bold text-gray-900 dark:text-white">
									{{ formatPrice(order.total) }} €
								</p>
								<p class="text-sm text-gray-600 dark:text-gray-400">
									{{ getItemsCountLabel(order.itemsCount) }}
								</p>
							</div>
						</div>

						<div class="flex gap-3 mb-4 overflow-x-auto pb-2">
							<img
								v-for="(item, index) in order.items.slice(0, 4)"
								:key="index"
								:src="item.image"
								:alt="item.name"
								class="w-14 h-14 sm:w-16 sm:h-16 object-cover rounded-lg flex-shrink-0"
							/>
							<div
								v-if="order.items.length > 4"
								class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0"
							>
								<span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
									+{{ order.items.length - 4 }}
								</span>
							</div>
						</div>

						<div class="flex flex-wrap gap-2 sm:gap-3">
							<button
								@click="viewOrderDetail(order.id)"
								class="w-full sm:flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors"
							>
								{{
									order.status_key === "delivered"
										? $t("pages.orders.list.action_view_detail")
										: $t("pages.orders.list.action_track")
								}}
							</button>
							<button
								v-if="order.status_key === 'delivered'"
								class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
							>
								{{ $t("pages.orders.list.action_buy_again") }}
							</button>
							<button
								v-if="order.status_key === 'delivered'"
								class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
							>
								{{ $t("pages.orders.list.action_download_invoice") }}
							</button>
							<button
								v-if="order.status_key === 'pending'"
								@click="cancelOrder(order.id)"
								class="px-4 py-2 text-sm border border-red-300 dark:border-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg font-medium transition-colors"
							>
								{{ $t("pages.orders.list.action_cancel") }}
							</button>
							<button
								v-if="order.status_key === 'delivered'"
								@click="refundOrder(order.id)"
								class="px-4 py-2 text-sm border border-purple-300 dark:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg font-medium transition-colors"
							>
								{{ $t("pages.orders.list.action_refund") }}
							</button>
						</div>
					</div>
				</div>
			</div>

			<div v-if="filteredOrders.length === 0" class="text-center py-16">
				<svg
					class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4"
					fill="none"
					stroke="currentColor"
					viewBox="0 0 24 24"
				>
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
					/>
				</svg>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
					{{ $t("pages.orders.list.empty_title") }}
				</h3>
				<p class="text-gray-600 dark:text-gray-400 mb-6">
					{{
						activeFilter === "all"
							? $t("pages.orders.list.empty_all")
							: $t("pages.orders.list.empty_filtered")
					}}
				</p>
				<button
					@click="$router.push('/')"
					class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors"
				>
					{{ $t("pages.orders.list.start_shopping") }}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
import api from "@/api.ts";

export default {
	data() {
		return {
			activeFilter: "all",
			orders: [],
		};
	},
	computed: {
		filters() {
			return [
				{ label: this.$t("pages.orders.list.filter_all"), value: "all" },
				{ label: this.$t("pages.orders.list.filter_processing"), value: "processing" },
				{ label: this.$t("pages.orders.list.filter_delivered"), value: "delivered" },
				{ label: this.$t("pages.orders.list.filter_cancelled"), value: "cancelled" },
			];
		},
		filteredOrders() {
			if (this.activeFilter === "all") {
				return this.orders;
			}

			const statusMap = {
				processing: ["pending", "paid", "shipped"],
				delivered: ["delivered"],
				cancelled: ["cancelled"],
			};

			return this.orders.filter((order) =>
				statusMap[this.activeFilter].includes(order.status_key)
			);
		},
	},
	mounted() {
		window.scrollTo({ top: 0, behavior: "smooth" });
		this.fetchOrders();
	},
	methods: {
		async fetchOrders() {
			try {
				const resp = await api.get("/api/orders");
				let data = resp.data;
				if (data && data.success && data.order) {
					data = [data.order];
				}
				if (!Array.isArray(data)) return;

				this.orders = data.map((o) => {
					const statusKey = this.normalizeStatus(o.status);
					return {
						id: o.id,
						date: this.formatDate(o.created_at),
						status_key: statusKey,
						total: Number(o.total) || 0,
						itemsCount: (o.items || []).length,
						items: (o.items || []).map((i) => ({
							name:
								i.product_name ||
								i.product?.title ||
								this.$t("pages.orders.list.product_fallback"),
							image: this.getProductImageUrl(
								i.product_image || i.image || i.product?.image || null
							),
						})),
					};
				});
			} catch (e) {
				console.error("Failed to load orders", e);
			}
		},
		getActiveLocale() {
			return localStorage.getItem("language") || localStorage.getItem("locale") || "sk-SK";
		},
		normalizeStatus(status) {
			const normalized = String(status || "").trim().toLowerCase();
			const map = {
				pending: "pending",
				paid: "paid",
				cancelled: "cancelled",
				shipped: "shipped",
				delivered: "delivered",
				"čakajúce": "pending",
				cakajuce: "pending",
				"spracováva sa": "paid",
				spracovava: "paid",
				"v preprave": "shipped",
				"odoslané": "shipped",
				odoslane: "shipped",
				"doručené": "delivered",
				dorucene: "delivered",
				"zrušené": "cancelled",
				zrusene: "cancelled",
			};

			return map[normalized] || "pending";
		},
		formatDate(date) {
			if (!date) return "";
			const d = new Date(date);
			return d.toLocaleDateString(this.getActiveLocale(), {
				year: "numeric",
				month: "long",
				day: "numeric",
			});
		},
		formatPrice(price) {
			return Number(price).toFixed(2);
		},
		getItemsCountLabel(count) {
			return this.$t("pages.orders.list.items_count", { count });
		},
		getOrderStatusLabel(statusKey) {
			return this.$t(`pages.orders.status.${statusKey}`);
		},
		getStatusClass(statusKey) {
			const classes = {
				pending: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
				paid: "bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200",
				shipped: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
				delivered: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
				cancelled: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
			};
			return (
				classes[statusKey] || "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200"
			);
		},
		getProductImageUrl(image) {
			const placeholder = "https://via.placeholder.com/200?text=Product";
			if (!image) return placeholder;

			const src = String(image).trim();
			if (!src) return placeholder;
			if (src.startsWith("http://") || src.startsWith("https://")) return src;

			const base = import.meta.env.VITE_API_URL || "https://backend.techstore.lznet.work";
			if (src.startsWith("/storage/")) return `${base}${src}`;
			if (src.startsWith("storage/")) return `${base}/${src}`;
			return `${base}/storage/${src.replace(/^\/+/, "")}`;
		},
		viewOrderDetail(orderId) {
			this.$router.push(`/orders/${orderId}`);
		},
		async cancelOrder(orderId) {
			if (await window.appConfirm(this.$t("pages.orders.list.confirm_cancel"))) {
				const order = this.orders.find((o) => o.id === orderId);
				if (order) {
					order.status_key = "cancelled";
				}
			}
		},
		async refundOrder(orderId) {
			if (!(await window.appConfirm(this.$t("pages.orders.list.confirm_refund")))) return;
			const order = this.orders.find((o) => o.id === orderId);
			if (order) {
				order.status_key = "paid";
			}
		},
	},
};
</script>