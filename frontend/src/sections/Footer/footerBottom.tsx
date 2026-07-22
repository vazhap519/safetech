import ConsentSettingsButton from "@/components/analytics/ConsentSettingsButton";
import FooterCopy from "@/components/Footer/components/FooterCopy";

export default async function FooterBottom({ marketingEnabled = false }: { marketingEnabled?: boolean }) {
    return (
        <div className="mx-auto mt-10 flex max-w-container-max flex-col items-center gap-unit-sm border-t border-outline-variant/10 px-5 pt-6 text-center font-label-md text-sm text-on-surface-variant/60 sm:flex-row sm:justify-between sm:text-left md:px-8 xl:px-14">
            <FooterCopy />
            <ConsentSettingsButton enabled={marketingEnabled} />
        </div>

    )
}
