import ConsentSettingsButton from "@/components/analytics/ConsentSettingsButton";
import FooterCopy from "@/components/Footer/components/FooterCopy";
import TranslatedText from "@/components/i18n/TranslatedText";
import LocalizedLink from "@/components/ui/LocalizedLink";

export default async function FooterBottom({ marketingEnabled = false }: { marketingEnabled?: boolean }) {
    return (
        <div className="mx-auto mt-10 flex max-w-container-max flex-col items-center gap-unit-sm border-t border-outline-variant/10 px-5 pt-6 text-center font-label-md text-sm text-on-surface-variant/60 sm:flex-row sm:justify-between sm:text-left md:px-8 xl:px-14">
            <FooterCopy />
            <div className="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 sm:justify-end">
                <LocalizedLink
                    className="transition-colors hover:text-on-surface"
                    href="/privacy"
                >
                    <TranslatedText
                        fallback={{
                            ka: "კონფიდენციალურობა",
                            en: "Privacy",
                            ru: "Конфиденциальность",
                        }}
                        translationKey="footer.legal.privacy"
                    />
                </LocalizedLink>
                <LocalizedLink
                    className="transition-colors hover:text-on-surface"
                    href="/terms"
                >
                    <TranslatedText
                        fallback={{
                            ka: "პირობები",
                            en: "Terms",
                            ru: "Условия",
                        }}
                        translationKey="footer.legal.terms"
                    />
                </LocalizedLink>
                <ConsentSettingsButton enabled={marketingEnabled} />
            </div>
        </div>
    );
}
