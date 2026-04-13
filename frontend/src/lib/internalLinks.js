export function injectInternalLinks(content, keywordMap) {
  if (!content) return content;

  let result = content;

  const used = new Set();
  let linkCount = 0;
  const MAX_LINKS = 3;

  /* =========================
     🔥 SMART RULES
  ========================= */

  const shouldLink = (priority) => {
    if (priority === 1) return true;
    if (priority === 2) return Math.random() > 0.3;
    return Math.random() > 0.6;
  };

  const hasContext = (text, keyword) => {
    const t = text.toLowerCase();

    return (
      t.includes(keyword.toLowerCase()) &&
      (t.includes("სისტემა") ||
        t.includes("მომსახურება") ||
        t.includes("ინსტალაცია") ||
        t.includes("მხარდაჭერა"))
    );
  };

  const randomizeAnchor = (match) => {
    const variations = [
      match,
      `${match} სერვისი`,
      `${match} მომსახურება`,
    ];

    return variations[Math.floor(Math.random() * variations.length)];
  };

  /* =========================
     🔥 HTML SAFE SPLIT
  ========================= */

  const parts = result.split(/(<a.*?>.*?<\/a>)/gi);

  const safeReplace = (text) => {
    let updated = text;

    keywordMap
      .sort((a, b) => a.priority - b.priority)
      .forEach((item) => {
        if (used.has(item.url) || linkCount >= MAX_LINKS) return;

        item.keywords.forEach((keyword) => {
          if (used.has(item.url) || linkCount >= MAX_LINKS) return;

          // 🔥 escape regex chars
          const escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

          // 🔥 partial + ქართული suffix
          const regex = new RegExp(`(${escaped}[ა-ჰ]*)`, "i");

          if (
            regex.test(updated) &&
            hasContext(updated, keyword) &&
            (linkCount === 0 || shouldLink(item.priority))
          ) {
            updated = updated.replace(regex, (match) => {
              const anchor = randomizeAnchor(match);

              return `<a href="${item.url}" class="text-[#00C2A8] font-medium hover:underline">${anchor}</a>`;
            });

            used.add(item.url);
            linkCount++;
          }
        });
      });

    return updated;
  };

  /* =========================
     🔥 PROCESS TEXT ONLY
  ========================= */

  const processed = parts.map((part) => {
    if (part.startsWith("<a")) return part; // არ ვეხებით უკვე არსებულ ლინკებს
    return safeReplace(part);
  });

  return processed.join("");
}