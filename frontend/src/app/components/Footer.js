import Link from "next/link";
import Brand from "./footer/Brand";
import FooterColumn from "./footer/FooterColumn";
import Bottom from "./footer/Bottom";
import ContactArea from "./footer/ContactArea";
import { getServices, getSettings } from "@/lib/datafetch";

function getFooterTitle(headers, key, fallback) {
  return headers?.find((h) => h.key === key)?.title || fallback;
}

export default async function Footer() {
  const [settings, servicesRes] = await Promise.all([
    getSettings().catch(() => null),
    getServices({ page: 1 }).catch(() => null),
  ]);

  const headers = settings?.headers || [];
  const copy = settings?.copy;
  const contact = settings?.contact;
  const services = servicesRes?.data?.services ?? [];
  const latestServices = services.slice(0, 5);

  return (
    <footer className="bg-[#0B3C5D] text-white">
      <div className="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-4 gap-8 text-center md:text-left">
        <Brand settings={settings} />

        <FooterColumn title={getFooterTitle(headers, "services", "სერვისები")}>
          <ul className="space-y-2 text-sm text-gray-300">
            {latestServices.map((service) => (
              <li key={service.slug}>
                <Link
                  href={`/services/${service.slug}`}
                  className="hover:text-[#00C2A8] transition"
                >
                  {service.title}
                </Link>
              </li>
            ))}
          </ul>
        </FooterColumn>

        <FooterColumn title={getFooterTitle(headers, "company", "კომპანია")}>
          <ul className="space-y-2 text-sm text-gray-300">
            <li>
              <Link href="/about" className="hover:text-[#00C2A8] transition">
                ჩვენს შესახებ
              </Link>
            </li>
            <li>
              <Link href="/blog" className="hover:text-[#00C2A8] transition">
                ბლოგი
              </Link>
            </li>
            <li>
              <Link href="/projects" className="hover:text-[#00C2A8] transition">
                პროექტები
              </Link>
            </li>
            <li>
              <Link href="/contact" className="hover:text-[#00C2A8] transition">
                კონტაქტი
              </Link>
            </li>
            <li>
              <Link href="/privacy" className="hover:text-[#00C2A8] transition">
                კონფიდენციალურობა
              </Link>
            </li>
          </ul>
        </FooterColumn>

        <FooterColumn title={getFooterTitle(headers, "contact", "კონტაქტი")}>
          <ContactArea contact={contact} />
        </FooterColumn>
      </div>

      <Bottom copy={copy} />
    </footer>
  );
}
