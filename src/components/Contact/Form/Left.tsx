"use client";

import PrivacyConsent from "@/components/forms/PrivacyConsent";
import Button from "@/components/ui/Button";
import { Input, Select, Textarea } from "@/components/ui/Input";
import Typography from "@/components/ui/Typography";
import { useLeadForm } from "@/hooks/useLeadForm";

export default function Left() {
    const { status, message, submit } = useLeadForm("contact-page");

    return (
        <div
            className="
                glass-panel
                relative
                overflow-hidden
                rounded-2xl
                p-unit-md
                sm:p-unit-lg
                lg:col-span-7
            "
        >
            <div
                className="
                    absolute
                    left-0
                    top-0
                    h-1
                    w-full
                    bg-gradient-to-r
                    from-primary
                    to-secondary
                    opacity-50
                "
            />

            <Typography
                as="h3"
                variant="section-title"
                className="mb-unit-lg"
            >
                საკონტაქტო ფორმა
            </Typography>

            <form className="space-y-unit-lg" onSubmit={submit}>
                <div
                    aria-hidden="true"
                    className="absolute -left-[9999px]"
                    tabIndex={-1}
                >
                    <label htmlFor="contact-website">Website</label>
                    <input
                        autoComplete="off"
                        id="contact-website"
                        name="website"
                        tabIndex={-1}
                        type="text"
                    />
                </div>

                <div className="grid gap-unit-md md:grid-cols-2">
                    <Input
                        id="name"
                        name="name"
                        label="სახელი და გვარი"
                        type="text"
                        autoComplete="name"
                        required
                    />

                    <Input
                        id="company"
                        name="company"
                        label="კომპანია"
                        type="text"
                    />
                </div>

                <div className="grid gap-unit-md md:grid-cols-2">
                    <Input
                        id="phone"
                        name="phone"
                        label="ტელეფონის ნომერი"
                        type="tel"
                        autoComplete="tel"
                    />

                    <Input
                        id="email"
                        name="email"
                        label="ელფოსტა"
                        type="email"
                        autoComplete="email"
                    />
                </div>

                <p className="text-sm text-on-surface-variant">
                    მიუთითეთ ტელეფონის ნომერი ან ელფოსტა, რომ დაგიკავშირდეთ.
                </p>

                <div className="grid gap-unit-md md:grid-cols-3">
                    <Select
                        id="service"
                        name="service"
                        label="სერვისი"
                        options={[
                            "ვიდეოსამეთვალყურეო სისტემები",
                            "დაშვების კონტროლი",
                            "ქსელური ინფრასტრუქტურა",
                            "სერვერული გადაწყვეტილებები",
                        ]}
                    />

                    <Select
                        id="project-size"
                        name="project-size"
                        label="პროექტის ზომა"
                        options={[
                            "მცირე (1-10 ერთეული)",
                            "საშუალო (10-50 ერთეული)",
                            "დიდი (50+ ერთეული)",
                        ]}
                    />

                    <Select
                        id="property-type"
                        name="property-type"
                        label="ობიექტის ტიპი"
                        options={[
                            "ბიზნეს ცენტრი",
                            "საწყობი / ინდუსტრიული",
                            "საცხოვრებელი კომპლექსი",
                            "საცალო ვაჭრობა",
                        ]}
                    />
                </div>

                <Textarea
                    id="message"
                    name="message"
                    label="შეტყობინება"
                    rows={5}
                />

                <PrivacyConsent />

                <Button
                    disabled={status === "submitting"}
                    type="submit"
                    variant="primary"
                    size="lg"
                    fullWidth
                >
                    {status === "submitting" ? "იგზავნება..." : "გაგზავნა"}
                </Button>
                <p
                    aria-live="polite"
                    className={status === "error" ? "text-error" : "text-success"}
                    role="status"
                >
                    {message}
                </p>
            </form>
        </div>
    );
}
