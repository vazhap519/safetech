<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:s="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
  exclude-result-prefixes="s image xhtml">
  <xsl:output method="html" encoding="UTF-8" indent="yes" />

  <xsl:template match="/">
    <html lang="en">
      <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>SafeTech Sitemap</title>
        <style>
          :root { color-scheme: dark; font-family: Arial, sans-serif; }
          body { margin: 0; background: #070b14; color: #e1e2ed; }
          main { width: min(1120px, calc(100% - 32px)); margin: 40px auto; }
          h1 { margin: 0 0 8px; font-size: clamp(28px, 4vw, 44px); }
          p { margin: 0 0 24px; color: #aeb6c8; }
          table { width: 100%; border-collapse: collapse; overflow: hidden; border: 1px solid rgba(255,255,255,.12); border-radius: 12px; background: #111827; }
          th, td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,.08); text-align: left; vertical-align: top; }
          th { color: #93c5fd; background: #0f172a; font-size: 13px; text-transform: uppercase; letter-spacing: .04em; }
          tr:last-child td { border-bottom: 0; }
          a { color: #67e8f9; overflow-wrap: anywhere; }
          .muted { color: #94a3b8; }
          .count { display: inline-flex; margin-left: 8px; padding: 3px 9px; border-radius: 999px; background: #1d4ed8; font-size: 13px; }
          @media (max-width: 720px) { main { margin: 24px auto; } th:nth-child(n+2), td:nth-child(n+2) { display: none; } }
        </style>
      </head>
      <body>
        <main>
          <h1>SafeTech Sitemap <span class="count"><xsl:value-of select="count(s:sitemapindex/s:sitemap | s:urlset/s:url)" /></span></h1>
          <p>This view is for people. Search engines receive the same standards-compliant XML.</p>

          <xsl:choose>
            <xsl:when test="s:sitemapindex">
              <table>
                <thead><tr><th>Sitemap</th><th>Last modified</th></tr></thead>
                <tbody>
                  <xsl:for-each select="s:sitemapindex/s:sitemap">
                    <tr>
                      <td><a href="{s:loc}"><xsl:value-of select="s:loc" /></a></td>
                      <td class="muted"><xsl:value-of select="s:lastmod" /></td>
                    </tr>
                  </xsl:for-each>
                </tbody>
              </table>
            </xsl:when>
            <xsl:otherwise>
              <table>
                <thead><tr><th>URL</th><th>Last modified</th><th>Frequency</th><th>Priority</th></tr></thead>
                <tbody>
                  <xsl:for-each select="s:urlset/s:url">
                    <tr>
                      <td>
                        <a href="{s:loc}"><xsl:value-of select="s:loc" /></a>
                        <xsl:if test="image:image/image:loc">
                          <div class="muted"><xsl:value-of select="image:image/image:loc" /></div>
                        </xsl:if>
                      </td>
                      <td class="muted"><xsl:value-of select="s:lastmod" /></td>
                      <td class="muted"><xsl:value-of select="s:changefreq" /></td>
                      <td class="muted"><xsl:value-of select="s:priority" /></td>
                    </tr>
                  </xsl:for-each>
                </tbody>
              </table>
            </xsl:otherwise>
          </xsl:choose>
        </main>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
