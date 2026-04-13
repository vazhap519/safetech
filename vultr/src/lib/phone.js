export function formatPhone(phone) {
  if (!phone) return null;

  // remove non-digits
  let clean = phone.replace(/\D/g, "");

  // remove 995 if exists
  if (clean.startsWith("995")) {
    clean = clean.slice(3);
  }

  return clean;
}

export function formatPhoneInternational(phone) {
  const clean = formatPhone(phone);
  if (!clean) return null;

  return `995${clean}`;
}