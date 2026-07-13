import PartnerComponent from "@/components/Service/PartnerComponent/PartnerComponent";

export default function PartnerSection() {
    return (
        <section className="py-unit-lg border-y border-outline-variant/10 bg-surface-container-lowest/50">
            <div
                className="px-margin-desktop max-w-container-max mx-auto flex flex-wrap justify-center md:justify-between items-center gap-unit-lg opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
              <PartnerComponent />

            </div>
        </section>
    )
}