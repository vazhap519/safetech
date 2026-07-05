import Typography from "@/components/ui/Typography";

export default function Benefits() {
    return (
        <div className="glass-panel p-unit-md rounded-xl hover:scale-105 transition-transform duration-300">
            <div className="flex items-start gap-unit-md">
                <span className="material-symbols-outlined text-secondary text-4xl" data-icon="architecture">architecture</span>
                <div>

                    <Typography as="h3"
                                variant="contact-title">
                        პროფესიონალური დაგეგმვა
                    </Typography>
                    <Typography
                        as="p"
                        variant="section-description"
                        className="max-w-2xl mx-auto"
                    >
                        თითოეული პროექტი მუშავდება ინდივიდუალურად, ობიექტის სპეციფიკის გათვალისწინებით.
                    </Typography>

                </div>
            </div>
        </div>

    )
}