import Link from "next/link";
import Typography from "@/components/ui/Typography";

const serviceLinks = [
    { href: "/services/cctv", label: "ვიდეომეთვალყურეობა" },
    { href: "/services/access-control", label: "დაშვების კონტროლი" },
    { href: "/services/networking", label: "ქსელური ინფრასტრუქტურა" },
    { href: "/services/server-infrastructure", label: "სერვერული სისტემები" },
];

export default function FooterServices() {
    return (
        <nav aria-label="სერვისები" className="space-y-4">
            <Typography as="h2" variant="footer-title">სერვისები</Typography>
            <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                {serviceLinks.map((item) => <li key={item.href}><Link className="transition-colors duration-300 hover:text-secondary" href={item.href}>{item.label}</Link></li>)}
            </ul>
        </nav>
    );
}
