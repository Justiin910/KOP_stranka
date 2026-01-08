<template>
  <div class="page-checkout-payment-bg">
    <div class="max-w-7xl mx-auto px-6 py-12">
      <!-- Progress Steps -->
      <div class="mb-12">
        <div class="flex items-center justify-center">
          <div v-for="(step, index) in steps" :key="index" class="flex items-center">
            <div class="flex flex-col items-center">
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors"
                :class="
                  step.active
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'
                "
              >
                {{ index + 1 }}
              </div>
              <span
                class="mt-2 text-sm font-medium"
                :class="
                  step.active
                    ? 'text-blue-600 dark:text-blue-400'
                    : 'text-gray-500 dark:text-gray-400'
                "
              >
                {{ step.label }}
              </span>
            </div>
            <div
              v-if="index < steps.length - 1"
              class="w-24 h-1 mx-4 mb-6"
              :class="
                steps[index + 1].active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'
              "
            ></div>
          </div>
        </div>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Platba</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Payment + Delivery Summary -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Delivery summary -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
          >
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
              Doručenie
            </h2>
            <div v-if="!delivery" class="text-gray-500">
              Žiadne doručenie uložené. Prosím vyplňte doručenie najprv.
            </div>
            <div v-else class="space-y-2">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Spôsob: <span class="font-medium">{{ delivery.method }}</span>
              </div>
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Meno: <span class="font-medium">{{ delivery.address.fullName }}</span>
              </div>
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Telefón: <span class="font-medium">{{ delivery.phone }}</span>
              </div>
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Email: <span class="font-medium">{{ delivery.address.email }}</span>
              </div>
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Adresa:
                <span class="font-medium"
                  >{{ delivery.address.street }}, {{ delivery.address.zip }},
                  {{ delivery.address.city }}, {{ delivery.address.country }}</span
                >
              </div>
            </div>
          </div>

          <!-- Payment methods -->
          <div
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700"
          >
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
              Spôsob platby
            </h2>

            <div class="space-y-4">
              <label
                class="flex items-start p-4 border rounded-lg cursor-pointer"
                :class="
                  selectedPayment === 'card'
                    ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                    : 'border-gray-200 dark:border-gray-700'
                "
              >
                <input type="radio" v-model="selectedPayment" value="card" class="mt-1" />
                <div class="ml-4">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-credit-card text-blue-600"></i>
                    <div class="font-medium text-gray-900 dark:text-white">
                      Platobná karta
                    </div>
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    Kreditná / debetná karta (Visa, MasterCard)
                  </div>
                </div>
              </label>

              <label
                class="flex items-start p-4 border rounded-lg cursor-pointer"
                :class="
                  selectedPayment === 'googlepay'
                    ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                    : 'border-gray-200 dark:border-gray-700'
                "
              >
                <input
                  type="radio"
                  v-model="selectedPayment"
                  value="googlepay"
                  class="mt-1"
                />
                <div class="ml-4">
                  <div class="flex items-center gap-2">
                    <i class="fab fa-google text-2xl text-blue-600"></i>
                    <div class="ml-2 font-medium text-gray-900 dark:text-white">
                      Google Pay
                    </div>
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    Rýchla platba cez Google Pay (ak podporované)
                  </div>
                </div>
              </label>

              <label
                class="flex items-start p-4 border rounded-lg cursor-pointer"
                :class="
                  selectedPayment === 'paypal'
                    ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                    : 'border-gray-200 dark:border-gray-700'
                "
              >
                <input
                  type="radio"
                  v-model="selectedPayment"
                  value="paypal"
                  class="mt-1"
                />
                <div class="ml-4">
                  <div class="flex items-center gap-2">
                    <i class="fab fa-paypal text-blue-600"></i>
                    <div class="font-medium text-gray-900 dark:text-white">PayPal</div>
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    Platba cez PayPal účet
                  </div>
                </div>
              </label>
            </div>

            <!-- Card form -->
            <div v-if="selectedPayment === 'card'" class="mt-6 space-y-3">
              <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1"
                  >Meno na karte</label
                >
                <input
                  v-model="card.name"
                  type="text"
                  class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                  placeholder="Jan Novak"
                  maxlength="100"
                />
              </div>
              <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1"
                  >Číslo karty</label
                >
                <input
                  v-model="card.number"
                  type="text"
                  inputmode="numeric"
                  @input="formatCardNumber"
                  maxlength="19"
                  class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                  placeholder="1234 5678 9012 3456"
                />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1"
                    >Expirácia (MM/YY)</label
                  >
                  <input
                    v-model="card.expiry"
                    type="text"
                    maxlength="7"
                    @input="formatExpiry"
                    class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    placeholder="08/2026"
                  />
                </div>
                <div>
                  <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1"
                    >CVC</label
                  >
                  <input
                    v-model="card.cvc"
                    type="text"
                    maxlength="4"
                    inputmode="numeric"
                    class="w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    placeholder="123"
                  />
                </div>
              </div>
            </div>

            <!-- Google Pay button -->
            <div v-if="selectedPayment === 'googlepay'" class="mt-6">
              <div id="google-pay-button"></div>
              <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Kliknite na tlačidlo Google Pay vyššie pre platbu.
              </p>
            </div>

            <!-- PayPal button -->
            <div v-if="selectedPayment === 'paypal'" class="mt-6">
              <div id="paypal-button-container"></div>
              <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Kliknite na tlačidlo PayPal vyššie pre platbu.
              </p>
            </div>
          </div>
        </div>

        <!-- Order summary -->
        <div class="lg:col-span-1">
          <div
            class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6"
          >
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
              Súhrn objednávky
            </h2>

            <div
              class="space-y-3 mb-6 border-b border-gray-200 dark:border-gray-700 pb-6"
            >
              <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Spolu za položky</span>
                <span>{{ cartSubtotal.toFixed(2) }} €</span>
              </div>
              <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Doručenie</span>
                <span>{{ deliveryFee.toFixed(2) }} €</span>
              </div>
              <div
                class="flex justify-between text-lg font-bold text-gray-900 dark:text-white"
              >
                <span>Celkom</span>
                <span>{{ (cartSubtotal + deliveryFee).toFixed(2) }} €</span>
              </div>
            </div>

            <div class="space-y-3 mb-6">
              <h3 class="font-semibold text-gray-900 dark:text-white">Položky</h3>
              <div v-if="cartStore.cartItems.length === 0" class="text-gray-500">
                Váš košík je prázdny
              </div>
              <div v-else class="space-y-3">
                <div
                  v-for="item in cartStore.cartItems"
                  :key="item.id"
                  class="flex justify-between text-sm text-gray-600 dark:text-gray-400"
                >
                  <span>{{ item.title }} x{{ item.quantity }}</span>
                  <span
                    >{{
                      (parseFloat(String(item.price)) * item.quantity).toFixed(2)
                    }}
                    €</span
                  >
                </div>
              </div>
            </div>

            <button
              @click="confirmPayment"
              class="w-full btn-primary-lg"
            >
              Potvrdiť objednávku
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useCartStore } from "../stores/cartStore";
import { useRouter } from "vue-router";

export default {
  name: "CheckoutPaymentView",
  setup() {
    const cartStore = useCartStore();
    const router = useRouter();
    return { cartStore, router };
  },
  data() {
    return {
      steps: [
        { label: "Košík", active: false },
        { label: "Doručenie", active: false },
        { label: "Platba", active: true },
        { label: "Potvrdenie", active: false },
      ],
      delivery: null,
      selectedPayment: "card",
      card: {
        name: "",
        number: "",
        expiry: "",
        cvc: "",
      },
      orderReference: String(Math.floor(Math.random() * 900000) + 100000),
      googlePayReady: false,
      paypalReady: false,
    };
  },
  computed: {
    cartSubtotal() {
      return this.cartStore.subtotal || 0;
    },
    deliveryFee() {
      // map delivery method to fee (same as delivery view)
      if (!this.delivery) return 0;
      if (this.delivery.method === "express") return 9.99;
      if (this.delivery.method === "pickup") return 0;
      return 4.99;
    },
  },
  methods: {
    loadDelivery() {
      const raw = sessionStorage.getItem("checkoutDelivery");
      if (!raw) {
        // redirect back to delivery step
        this.router.push("/checkout/delivery");
        return;
      }
      try {
        this.delivery = JSON.parse(raw);
      } catch (e) {
        console.error("Invalid checkoutDelivery in sessionStorage", e);
        this.router.push("/checkout/delivery");
      }
    },
    formatCardNumber() {
      // simple grouping e.g. 1234 5678 9012 3456
      this.card.number = this.card.number
        .replace(/\D/g, "")
        .substring(0, 16)
        .replace(/(.{4})/g, "$1 ")
        .trim();
    },
    formatExpiry() {
      let digits = this.card.expiry.replace(/\D/g, "").substring(0, 6); // MMYYYY max

      if (digits.length <= 2) {
        this.card.expiry = digits;
        return;
      }

      // split into month and year parts
      let monthPart = digits.substring(0, 2);
      let yearPart = digits.substring(2);

      // normalize month (1-12)
      let monthNum = parseInt(monthPart, 10);
      if (isNaN(monthNum) || monthNum < 1) monthNum = 1;
      if (monthNum > 12) monthNum = 12;
      monthPart = String(monthNum).padStart(2, "0");

      // limit year to 4 digits and max 9999
      if (yearPart.length > 4) yearPart = yearPart.substring(0, 4);
      if (yearPart.length > 0) {
        let yearNum = parseInt(yearPart, 10);
        if (!isNaN(yearNum) && yearNum > 9999) yearPart = "9999";
      }

      this.card.expiry = yearPart.length > 0 ? monthPart + "/" + yearPart : monthPart;
    },
    validateCard() {
      const digits = this.card.number.replace(/\D/g, "");
      if (digits.length !== 16) return false;
      if (!/^\d{2}\/\d{4}$/.test(this.card.expiry)) return false;
      if (!/^[0-9]{3,4}$/.test(this.card.cvc)) return false;
      if (!this.card.name) return false;
      return true;
    },

    // Google Pay integration
    async initGooglePay() {
      try {
        const script = document.createElement("script");
        script.src = "https://pay.google.com/gstatic/s/pay/api/hosted/v21/pay-button.js";
        script.async = true;
        document.head.appendChild(script);

        script.onload = () => {
          this.renderGooglePayButton();
        };
      } catch (error) {
        console.error("Failed to load Google Pay:", error);
      }
    },

    renderGooglePayButton() {
      try {
        const paymentsClient = new google.payments.api.PaymentsClient({
          environment: "TEST",
        });

        const button = document.querySelector("#google-pay-button");
        if (!button) return;

        const isReadyToPayRequest = {
          apiVersion: 2,
          apiVersionMinor: 0,
          allowedPaymentMethods: [
            {
              type: "CARD",
              parameters: {
                allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                allowedCardNetworks: ["MASTERCARD", "VISA"],
              },
            },
          ],
        };

        paymentsClient.isReadyToPay(isReadyToPayRequest).then((response) => {
          if (response.result) {
            const paymentDataRequest = {
              apiVersion: 2,
              apiVersionMinor: 0,
              allowedPaymentMethods: [
                {
                  type: "CARD",
                  tokenizationSpecification: {
                    type: "PAYMENT_GATEWAY",
                    parameters: {
                      gateway: "example",
                      gatewayMerchantId: "exampleGatewayMerchantId",
                    },
                  },
                  parameters: {
                    allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                    allowedCardNetworks: ["MASTERCARD", "VISA"],
                  },
                },
              ],
              transactionInfo: {
                totalPriceStatus: "FINAL",
                totalPrice: (this.cartSubtotal + this.deliveryFee).toFixed(2),
                currencyCode: "EUR",
              },
              merchantInfo: {
                merchantName: "KOP Obchod",
              },
              callbackIntents: ["PAYMENT_AUTHORIZATION"],
            };

            button.innerHTML = "";
            google.payments.api.PaymentsClient.prefetchPaymentData(paymentDataRequest);

            button.addEventListener("click", () => {
              paymentsClient
                .loadPaymentData(paymentDataRequest)
                .then((paymentData) => {
                  this.handleGooglePaymentAuthorization(paymentData);
                })
                .catch((err) => {
                  console.error("Payment failed:", err);
                  alert("Platba cez Google Pay zlyhala");
                });
            });

            // Create button dynamically
            button.innerHTML =
              '<button style="width: 200px; padding: 10px; background: #1f2937; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Platiť cez Google Pay</button>';
            this.googlePayReady = true;
          }
        });
      } catch (error) {
        console.error("Google Pay initialization failed:", error);
      }
    },

    handleGooglePaymentAuthorization(paymentData) {
      // Extract payment token from Google Pay response
      const token = paymentData.paymentMethodData.tokenizationData.token;

      // Create order with payment token
      this.createOrder({
        method: "googlepay",
        token: token,
      });
    },

    // PayPal integration
    async initPayPal() {
      try {
        const script = document.createElement("script");
        const clientId = import.meta.env.VITE_PAYPAL_CLIENT_ID || 'sb';
        const envMode = import.meta.env.VITE_PAYPAL_ENVIRONMENT || 'sandbox';
        // Use provided client id; Vite exposes VITE_ prefixed vars to the frontend
        script.src = `https://www.paypal.com/sdk/js?client-id=${clientId}&currency=EUR&intent=capture`;
        script.async = true;
        document.head.appendChild(script);

        script.onload = () => {
          this.renderPayPalButton();
        };
      } catch (error) {
        console.error("Failed to load PayPal:", error);
      }
    },

    renderPayPalButton() {
      try {
        if (typeof paypal !== "undefined" && paypal.Buttons) {
          const button = paypal.Buttons({
            createOrder: (data, actions) => {
              return actions.order.create({
                purchase_units: [
                  {
                    amount: {
                      value: (this.cartSubtotal + this.deliveryFee).toFixed(2),
                      currency_code: "EUR",
                    },
                  },
                ],
                payer: {
                  name: {
                    given_name:
                      this.delivery?.address?.fullName?.split(" ")[0] || "Customer",
                    surname:
                      this.delivery?.address?.fullName?.split(" ").slice(1).join(" ") ||
                      "Surname",
                  },
                  email_address: this.delivery?.address?.email || "",
                  address: {
                    address_line_1: this.delivery?.address?.street || "",
                    admin_area_2: this.delivery?.address?.city || "",
                    postal_code: this.delivery?.address?.zip || "",
                    country_code: "SK",
                  },
                },
              });
            },
            onApprove: (data, actions) => {
              return actions.order.capture().then((details) => {
                this.handlePayPalApproval(details);
              });
            },
            onError: (err) => {
              console.error("PayPal error:", err);
              alert("Platba cez PayPal zlyhala");
            },
          });

          const container = document.querySelector("#paypal-button-container");
          if (container) {
            button.render(container);
            this.paypalReady = true;
          }
        }
      } catch (error) {
        console.error("PayPal button initialization failed:", error);
      }
    },

    handlePayPalApproval(details) {
      // Extract payment details from PayPal response
      const token = details.id;

      // Create order with payment token
      this.createOrder({
        method: "paypal",
        token: token,
        paypalDetails: details,
      });
    },

    // Create order and process payment
    async createOrder(paymentData) {
      try {
        // Validate delivery
        if (!this.delivery) {
          alert("Chýba informácia o doručení");
          return;
        }

        // Create order object
        const order = {
          id: "TMP-" + Date.now(),
          reference: "ORD-" + Math.floor(Math.random() * 1000000) + "-" + Date.now(),
          delivery: this.delivery,
          payment: {
            method: paymentData.method,
            token: paymentData.token,
            card:
              this.selectedPayment === "card"
                ? {
                    name: this.card.name,
                    last4: this.card.number.replace(/\D/g, "").slice(-4),
                  }
                : null,
            paypalDetails: paymentData.paypalDetails || null,
          },
          items: this.cartStore.cartItems,
          total: (this.cartSubtotal + this.deliveryFee).toFixed(2),
          created_at: new Date().toISOString(),
          status: "pending",
        };

        // Save to localStorage (persists through navigation)
        localStorage.setItem("checkoutOrder", JSON.stringify(order));

        // Prepare items for API
        const items = order.items.map((item) => ({
          product_id: item.product_id || item.id,
          quantity: item.quantity,
          price: parseFloat(String(item.price)),
        }));

        // Call backend API to create order
        const response = await fetch("/api/orders", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${localStorage.getItem("token") || ""}`,
          },
          body: JSON.stringify({
            reference: order.reference,
            delivery_method: order.delivery.method,
            address: JSON.stringify(order.delivery.address),
            phone: order.delivery.phone,
            payment_method: order.payment.method,
            payment_token: order.payment.token,
            total: parseFloat(order.total),
            items: items,
          }),
        });

        if (!response.ok) {
          let errorMsg = "Failed to create order";
          try {
            const error = await response.json();
            errorMsg = error.message || error.error || errorMsg;
          } catch (e) {
            // Response is not JSON (e.g., 404 HTML page)
            errorMsg = `Server error (${response.status}): ${response.statusText}`;
          }
          throw new Error(errorMsg);
        }

        const result = await response.json();
        console.log("Order created successfully:", result);

        // Update order with real ID from backend
        if (result.order && result.order.id) {
          order.id = result.order.id;
          localStorage.setItem("checkoutOrder", JSON.stringify(order));
        }

        // Clear cart
        await this.cartStore.clearCart();

        // Verify localStorage has data
        console.log("✅ Order saved to localStorage:", localStorage.getItem("checkoutOrder") ? "YES" : "NO");

        // Navigate to confirmation (don't clear yet - let view do it)
        this.router.push("/checkout/confirmation");
      } catch (error) {
        console.error("Order creation failed:", error);
        alert("Nepodarilo sa vytvoriť objednávku: " + error.message);
      }
    },

    async confirmPayment() {
      if (!this.delivery) {
        alert("Chýba informácia o doručení");
        return;
      }

      // For card payment, validate and process directly
      if (this.selectedPayment === "card") {
        if (!this.validateCard()) {
          alert("Skontrolujte údaje o karte");
          return;
        }

        // Create order with card payment
        await this.createOrder({
          method: "card",
          token: "mock_card_token_" + Date.now(),
        });
      }
      // Google Pay and PayPal have their own buttons, so this won't be called for them
      else if (this.selectedPayment === "googlepay" && !this.googlePayReady) {
        alert("Google Pay sa zatiaľ načítava. Skúste znova.");
      } else if (this.selectedPayment === "paypal" && !this.paypalReady) {
        alert("PayPal sa zatiaľ načítava. Skúste znova.");
      }
    },
  },
  mounted() {
    this.loadDelivery();
    // Initialize payment providers
    this.initGooglePay();
    this.initPayPal();
  },
};
</script>
