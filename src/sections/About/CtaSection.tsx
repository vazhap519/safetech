import Typography from "@/components/ui/Typography";
import ActionLink from "@/components/ui/ActionLink";

export default function CtaSection(){
    return (
        <section className="py-unit-xl px-margin-desktop relative overflow-hidden">
            <div className="absolute inset-0 mesh-gradient opacity-30"></div>
            <div
                className="relative z-10 max-w-container-max mx-auto px-unit-lg sm:px-unit-xl text-center py-unit-xl glass-card rounded-3xl border-primary/20">
                <Typography as="h2" variant="section-title">
                    მზად ხართ უსაფრთხო ინფრასტრუქტურისთვის?
                </Typography>

                <Typography as="p" variant="section-description">
                    დაიწყეთ თქვენი ბიზნესის ტექნოლოგიური ტრანსფორმაცია დღესვე. ჩვენი ექსპერტები დაგეხმარებიან საუკეთესო
                    არქიტექტურის შერჩევაში.
                </Typography>
             <ActionLink href="/contact" className="mt-unit-lg px-8 py-4">მოთხოვნა</ActionLink>

            </div>
        </section>

    )
}
