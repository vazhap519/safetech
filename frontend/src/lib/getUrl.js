import { getBaseUrl } from "@/lib/config";

export async function getCurrentUrl(path = "") {
  const baseUrl = getBaseUrl().replace(/\/$/, "");
  const normalizedPath = path.startsWith("/") ? path : `/${path}`;

  return `${baseUrl}${normalizedPath}`;
}
