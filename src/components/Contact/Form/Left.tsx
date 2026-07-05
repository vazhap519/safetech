"use client";

import PrivacyConsent from "@/components/forms/PrivacyConsent";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import Button from "@/components/ui/Button";
import { Input, Select, Textarea } from "@/components/ui/Input";
import Typography from "@/components/ui/Typography";
import { useLeadForm } from "@/hooks/useLeadForm";

export default function Left() {
    const { status, message, submit } = useLeadForm("contact-page");
    const { t } = useLocalization();

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
                {t("contact.form.title", {
                    ka: "საკონტაქტო ფორმა",
                    en: "Contact form",
                    ru: "Контактная форма",
                })}
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
                        label={t("forms.fullName", {
                            ka: "სახელი და გვარი",
                            en: "Full name",
                            ru: "Имя и фамилия",
                        })}
                        type="text"
                        autoComplete="name"
                        required
                    />

                    <Input
                        id="company"
                        name="company"
                        label={t("forms.company", {
                            ka: "კომპანია",
                            en: "Company",
                            ru: "Компания",
                        })}
                        type="text"
                    />
                </div>

                <div className="grid gap-unit-md md:grid-cols-2">
                    <Input
                        id="phone"
                        name="phone"
                        label={t("forms.phoneNumber", {
                            ka: "ტელეფონის ნომერი",
                            en: "Phone number",
                            ru: "Номер телефона",
                        })}
                        type="tel"
                        autoComplete="tel"
                    />

                    <Input
                        id="email"
                        name="email"
                        label={t("forms.email", {
                            ka: "ელფოსტა",
                            en: "Email",
                            ru: "Email",
                        })}
                        type="email"
                        autoComplete="email"
                    />
                </div>

                <p className="text-sm text-on-surface-variant">
                    {t("forms.contactHint", {
                        ka: "მიუთითეთ ტელეფონის ნომერი ან ელფოსტა, რომ დაგიკავშირდეთ.",
                        en: "Provide a phone number or email address so we can contact you.",
                        ru: "Укажите номер телефона или email, чтобы мы могли с вами связаться.",
                    })}
                </p>

                <div className="grid gap-unit-md md:grid-cols-3">
                    <Select
                        id="service"
                        name="service"
                        label={t("forms.service", {
                            ka: "სერვისი",
                            en: "Service",
                            ru: "Услуга",
                        })}
                        options={[
                            t("forms.options.service.cctv", {
                                ka: "ვიდეოსამეთვალყურეო სისტემები",
                                en: "CCTV systems",
                                ru: "Системы видеонаблюдения",
                            }),
                            t("forms.options.service.access", {
                                ka: "დაშვების კონტროლი",
                                en: "Access control",
                                ru: "Контроль доступа",
                            }),
                            t("forms.options.service.network", {
                                ka: "ქსელური ინფრასტრუქტურა",
                                en: "Network infrastructure",
                                ru: "Сетевая инфраструктура",
                            }),
                            t("forms.options.service.server", {
                                ka: "სერვერული გადაწყვეტილებები",
                                en: "Server solutions",
                                ru: "Серверные решения",
                            }),
                        ]}
                    />

                    <Select
                        id="project-size"
                        name="project-size"
                        label={t("forms.projectSize", {
                            ka: "პროექტის ზომა",
                            en: "Project size",
                            ru: "Размер проекта",
                        })}
                        options={[
                            t("forms.options.size.small", {
                                ka: "მცირე (1-10 ერთეული)",
                                en: "Small (1-10 units)",
                                ru: "Малый (1-10 единиц)",
                            }),
                            t("forms.options.size.medium", {
                                ka: "საშუალო (10-50 ერთეული)",
                                en: "Medium (10-50 units)",
                                ru: "Средний (10-50 единиц)",
                            }),
                            t("forms.options.size.large", {
                                ka: "დიდი (50+ ერთეული)",
                                en: "Large (50+ units)",
                                ru: "Крупный (50+ единиц)",
                            }),
                        ]}
                    />

                    <Select
                        id="property-type"
                        name="property-type"
                        label={t("forms.propertyType", {
                            ka: "ობიექტის ტიპი",
                            en: "Property type",
                            ru: "Тип объекта",
                        })}
                        options={[
                            t("forms.options.property.office", {
                                ka: "ბიზნეს ცენტრი",
                                en: "Business center",
                                ru: "Бизнес-центр",
                            }),
                            t("forms.options.property.warehouse", {
                                ka: "საწყობი / ინდუსტრიული",
                                en: "Warehouse / industrial",
                                ru: "Склад / промышленный объект",
                            }),
                            t("forms.options.property.residential", {
                                ka: "საცხოვრებელი კომპლექსი",
                                en: "Residential complex",
                                ru: "Жилой комплекс",
                            }),
                            t("forms.options.property.retail", {
                                ka: "საცალო ვაჭრობა",
                                en: "Retail",
                                ru: "Розничная торговля",
                            }),
                        ]}
                    />
                </div>

                <Textarea
                    id="message"
                    name="message"
                    label={t("forms.message", {
                        ka: "შეტყობინება",
                        en: "Message",
                        ru: "Сообщение",
                    })}
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
                    {status === "submitting"
                        ? t("forms.submitting", {
                              ka: "იგზავნება...",
                              en: "Sending...",
                              ru: "Отправка...",
                          })
                        : t("forms.send", {
                              ka: "გაგზავნა",
                              en: "Send",
                              ru: "Отправить",
                          })}
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
