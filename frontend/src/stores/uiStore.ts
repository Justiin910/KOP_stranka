import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const notifications = ref([] as Array<{ id: number; message: string; type?: string }>)

  const confirmState = ref<{
    show: boolean
    message: string
    resolve: ((value: boolean) => void) | null
  }>({ show: false, message: '', resolve: null })

  function notify(message: string, type = 'info', duration = 4000) {
    const id = Date.now() + Math.floor(Math.random() * 1000)
    notifications.value.push({ id, message, type })
    setTimeout(() => {
      const idx = notifications.value.findIndex((n) => n.id === id)
      if (idx !== -1) notifications.value.splice(idx, 1)
    }, duration)
  }

  function remove(id: number) {
    const idx = notifications.value.findIndex((n) => n.id === id)
    if (idx !== -1) notifications.value.splice(idx, 1)
  }

  function confirm(message: string): Promise<boolean> {
    return new Promise((resolve) => {
      confirmState.value.show = true
      confirmState.value.message = message
      confirmState.value.resolve = resolve
    })
  }

  function resolveConfirm(value: boolean) {
    if (confirmState.value.resolve) confirmState.value.resolve(value)
    confirmState.value.show = false
    confirmState.value.message = ''
    confirmState.value.resolve = null
  }

  return {
    notifications,
    notify,
    remove,
    confirmState,
    confirm,
    resolveConfirm,
  }
})
