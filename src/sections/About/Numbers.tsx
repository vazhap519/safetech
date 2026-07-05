import NumbersComponent from "@/components/About/NumbersComponent";

export default function NumbersSections() {
    return (
        <section className="py-unit-xl bg-surface-container-lowest border-y border-outline-variant/10">
        <div className="max-w-container-max mx-auto px-margin-desktop">
        <div className="grid grid-cols-1 min-[380px]:grid-cols-2 lg:grid-cols-4 gap-gutter">

       <NumbersComponent/>
        </div>
        </div>
        </section>
    )
}
