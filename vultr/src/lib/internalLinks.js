export function injectInternalLinks(text, links = []) {
  if (!text || !Array.isArray(links)) return text;

  let result = text;

  links.forEach((item) => {
    if (!item?.keyword || !item?.url) return;

    const regex = new RegExp(`\\b${item.keyword}\\b`, "gi");

    result = result.replace(
      regex,
      `<a href="${item.url}" class="text-[#00C2A8] underline">${item.keyword}</a>`
    );
  });

  return result;
}