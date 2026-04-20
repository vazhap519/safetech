import { getBaseUrl } from "@/lib/config";

export const DEFAULT_IMAGE = "/services/1.jpg";

export function mediaUrl(image, fallback = DEFAULT_IMAGE) {
  const value = String(image || "").trim();
  const fallbackValue = fallback || DEFAULT_IMAGE;

  if (!value) return fallbackValue;
  if (/^(https?:|data:|blob:)/i.test(value)) return value;

  if (value.startsWith("/storage") || value.startsWith("/uploads")) {
    const apiBase = process.env.NEXT_PUBLIC_API_URL?.replace(/\/api\/?$/, "");

    return apiBase ? `${apiBase}${value}` : value;
  }

  if (value.startsWith("/")) return value;

  return `${getBaseUrl().replace(/\/$/, "")}/${value}`;
}
