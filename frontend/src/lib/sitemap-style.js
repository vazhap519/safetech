const stylesheetInstruction =
  '<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>';

export function addSitemapStylesheet(xml) {
  const value = String(xml || "");

  if (value.includes("<?xml-stylesheet")) return value;

  return value.replace(
    /^(<\?xml[^?]*\?>)/,
    `$1\n${stylesheetInstruction}`,
  );
}

export async function styleSitemapResponse(response) {
  const xml = await response.text();

  return new Response(addSitemapStylesheet(xml), {
    status: response.status,
    statusText: response.statusText,
    headers: response.headers,
  });
}
