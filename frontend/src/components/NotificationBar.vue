<template>
  <div>
    <!-- Top-right notifications -->
    <div class="fixed top-4 right-4 space-y-3 z-50">
      <div
        v-for="n in ui.notifications"
        :key="n.id"
        class="px-4 py-2 rounded-lg shadow-md max-w-xs break-words"
        :class="n.type === 'error' ? 'bg-red-600 text-white' : 'bg-indigo-600 text-white'"
      >
        <div class="flex items-start justify-between gap-2">
          <div class="text-sm">{{ n.message }}</div>
          <button
            @click="ui.remove(n.id)"
            class="ml-2 text-white opacity-80 hover:opacity-100"
          >
            ×
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm modal -->
    <transition name="modal">
      <div
        v-if="ui.confirmState.show"
        class="fixed inset-0 backdrop-blur-lg flex items-center justify-center z-50"
        @click="ui.resolveConfirm(false)"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4"
          @click.stop
        >
          <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">
            Potvrdenie
          </h3>
          <p class="text-gray-700 dark:text-gray-300 mb-4">
            {{ ui.confirmState.message }}
          </p>
          <div class="flex gap-3 justify-end">
            <button
              @click="ui.resolveConfirm(false)"
              class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
            >
              Zrušiť
            </button>
            <button
              @click="ui.resolveConfirm(true)"
              class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition-colors"
            >
              Áno
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { useUiStore } from "@/stores/uiStore";
const ui = useUiStore();
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from {
  opacity: 0;
}

.modal-enter-from > div {
  transform: scale(0.95) translateY(-20px);
}

.modal-leave-to {
  opacity: 0;
}

.modal-leave-to > div {
  transform: scale(0.95) translateY(-20px);
}

.modal-enter-to > div,
.modal-leave-from > div {
  transform: scale(1) translateY(0);
}
</style>
