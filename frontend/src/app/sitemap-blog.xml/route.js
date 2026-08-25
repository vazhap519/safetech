export const dynamic = "force-dynamic";

export async function GET(request) {
  return Response.redirect(new URL("/sitemap.xml", request.url), 308);
}
