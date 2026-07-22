import FooterDescription from "@/components/Footer/components/FooterDescription";
import FooterServices from "@/components/Footer/components/FooterServices";
import FooterCompany from "@/components/Footer/components/FooterCompany";
import FooterContact from "@/components/Footer/components/FooterContact";

export default function FooterTop() {
    return (
        <div className="footer-grid mx-auto grid max-w-container-max gap-10 px-5 md:px-8 xl:px-14">
            <FooterDescription />
            <FooterServices />
            <FooterCompany />
            <FooterContact />
        </div>
    )
}
