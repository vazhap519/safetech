import { formatPhoneInternational } from "@/lib/phone";

export default function ContactCTA({ data }) {
  const intl = formatPhoneInternational(data?.phone);

  return (
    <section className="py-20 bg-[#0B3C5D] text-white text-center">
      <h2 className="text-3xl font-bold">
        {data?.cta_title}
      </h2>

      <a
        href={intl ? `tel:+${intl}` : "#"}
        className="inline-block mt-6 bg-[#00C2A8] px-6 py-3 rounded-xl"
      >
        📞 დაგვიკავშირდით
      </a>
    </section>
  );
}