import Link from "next/link";
import Typography from "@/components/ui/Typography";

const companyLinks = [
    { href: "/about", label: "ჩვენს შესახებ" },
    { href: "/projects", label: "პროექტები" },
    { href: "/services", label: "სერვისები" },
    { href: "/contact", label: "კონტაქტი" },
];

export default function FooterCompany() {
    return (
        <nav aria-label="კომპანია" className="space-y-4">
            <Typography as="h2" variant="footer-title">კომპანია</Typography>
            <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                {companyLinks.map((item) => <li key={item.href}><Link className="transition-colors duration-300 hover:text-secondary" href={item.href}>{item.label}</Link></li>)}
            </ul>
        </nav>
    );
}
