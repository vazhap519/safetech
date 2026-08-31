export function redirectLegacySitemap() {
    // Keep the redirect relative so reverse-proxy internal origins such as
    // localhost:3000 can never leak into the public Location header.
    return new Response(null, {
        headers: { Location: "/sitemap.xml" },
        status: 308,
    });
}
