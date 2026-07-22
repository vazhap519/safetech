
import FooterTop from "@/sections/Footer/FooterTop";
import FooterBottom from "@/sections/Footer/footerBottom";

export default function Footer({ marketingEnabled = false }: { marketingEnabled?: boolean }){
    return (
        <footer className="mt-16 w-full border-t border-outline-variant/10 bg-surface-container-lowest py-12 md:mt-20 md:py-16">
            <FooterTop />
            <FooterBottom marketingEnabled={marketingEnabled} />
        </footer>
    )
}
