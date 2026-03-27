import { headers } from "next/headers";

export async function getCurrentUrl(path = "") {
  const headersList = await headers(); // ✅ MUST

  const host = headersList.get("host");

  const protocol =
    process.env.NODE_ENV === "development" ? "http" : "https";

  return `${protocol}://${host}${path}`;
}