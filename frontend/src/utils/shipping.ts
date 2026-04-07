export const FREE_SHIPPING_THRESHOLD = 900;

export const SHIPPING_FEES = {
  standard: 4.99,
  express: 9.99,
  pickup: 0,
} as const;

export function getBaseShippingFee(method: string | null | undefined): number {
  const normalizedMethod = String(method || "standard").toLowerCase();

  if (normalizedMethod === "pickup") return SHIPPING_FEES.pickup;
  if (normalizedMethod === "express") return SHIPPING_FEES.express;
  return SHIPPING_FEES.standard;
}

export function calculateShippingFee(
  method: string | null | undefined,
  subtotal: number | null | undefined
): number {
  const normalizedMethod = String(method || "standard").toLowerCase();
  const orderSubtotal = Number(subtotal) || 0;

  // Pickup is always free regardless of subtotal.
  if (normalizedMethod === "pickup") return SHIPPING_FEES.pickup;

  if (orderSubtotal >= FREE_SHIPPING_THRESHOLD) {
    return 0;
  }

  return getBaseShippingFee(normalizedMethod);
}
