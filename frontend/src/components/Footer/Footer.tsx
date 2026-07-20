
import FooterTop from "@/sections/Footer/FooterTop";
import FooterBottom from "@/sections/Footer/footerBottom";

export default function Footer({ marketingEnabled = false }: { marketingEnabled?: boolean }){
    return (
   
        <footer className="w-full py-unit-xl bg-surface-container-lowest dark:bg-surface-container-lowest border-t border-outline-variant/10 mt-unit-xl">
<FooterTop/>
            <FooterBottom marketingEnabled={marketingEnabled}/>
        </footer>
    )
}
