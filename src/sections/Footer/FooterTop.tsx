import FooterDescription from "@/components/Footer/components/FooterDescription";
import FooterServices from "@/components/Footer/components/FooterServices";
import FooterCompany from "@/components/Footer/components/FooterCompany";
import FooterContact from "@/components/Footer/components/FooterContact";

export default function FooterTop() {
    return (
        <div className="grid grid-cols-1 gap-unit-lg px-margin-desktop sm:grid-cols-2 lg:grid-cols-4 lg:gap-gutter max-w-container-max mx-auto">
            <FooterDescription/>
            <FooterServices/>
            <FooterCompany/>
            <FooterContact />
        </div>
    )
}
