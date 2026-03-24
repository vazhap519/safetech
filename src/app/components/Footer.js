import Link from "next/link";
import Brand from "./footer/Brand";
import { getSettings } from "@/lib/datafetch";
import FooterColumn from "./footer/FooterColumn";
import Bottom from "./footer/Bottom";
import ContactArea from "./footer/ContactArea";
export default async function Footer() {
  const settings = await getSettings();
const headers = settings?.headers || [];
function getFooterTitle(headers, key, fallback) {
  return headers?.find((h) => h.key === key)?.title || fallback;
}
const copy=settings?.copy;
const contact=settings?.contact
  return (
    <footer className="bg-[#0B3C5D] text-white">
      <div className="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-4 gap-8 text-center md:text-left">

        {/* Brand */}
<Brand settings={settings}/>

        {/* Services */}
<FooterColumn
  title={getFooterTitle(headers, "services", "სერვისები")}
>
          <ul className="space-y-2 text-sm text-gray-300">
            <li><Link href="/services/cctv" className="hover:text-[#00C2A8] transition">CCTV მონტაჟი</Link></li>
            <li><Link href="/services/pos" className="hover:text-[#00C2A8] transition">POS სისტემები</Link></li>
            <li><Link href="/services/internet" className="hover:text-[#00C2A8] transition">ინტერნეტი</Link></li>
            <li><Link href="/services/router" className="hover:text-[#00C2A8] transition">Router Setup</Link></li>
            <li><Link href="/services/os" className="hover:text-[#00C2A8] transition">OS ინსტალაცია</Link></li>
          </ul>
  </FooterColumn>

        {/* Company */}
<FooterColumn
  title={getFooterTitle(headers, "company", "კომპანია")}
>
          <ul className="space-y-2 text-sm text-gray-300">
            <li><Link href="/about" className="hover:text-[#00C2A8] transition">ჩვენს შესახებ</Link></li>
            <li><Link href="/blog" className="hover:text-[#00C2A8] transition">ბლოგი</Link></li>
            <li><Link href="/contact" className="hover:text-[#00C2A8] transition">კონტაქტი</Link></li>
            <li><Link href="/privacy" className="hover:text-[#00C2A8] transition">Privacy Policy</Link></li>
          </ul>
  </FooterColumn>

        {/* Contact */}
<FooterColumn
  title={getFooterTitle(headers, "contact", "კონტაქტი")}
>


          <ContactArea contact={contact}/>
       </FooterColumn>

      </div>

      {/* Bottom */}
   <Bottom copy={copy}/>
    </footer>
  );
}