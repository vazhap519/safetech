/* eslint-disable react/no-unescaped-entities */
import Icon from "@/components/ui/Icon";

export default function FaqSeqAction() {
    return (
        <section className="py-unit-xl px-margin-desktop max-w-container-max mx-auto">
            <div className="grid md:grid-cols-2 gap-unit-xl items-start">
                <div>
                    <h2 className="font-headline-lg text-headline-lg text-on-surface mb-unit-md">ხშირად დასმული
                        კითხვები</h2>
                    <p className="font-body-md text-body-md text-on-surface-variant mb-unit-lg">თუ ვერ იპოვეთ თქვენთვის
                        საინტერესო კითხვაზე პასუხი, მოგვწერეთ ან დაგვიკავშირდით.</p>
                    <button
                        className="bg-primary text-on-primary px-unit-lg py-unit-sm rounded-lg font-label-md text-label-md hover:opacity-90 transition-opacity">კონტაქტი
                    </button>
                </div>
                <div className="space-y-unit-md">
                    <details className="glass-card rounded-xl overflow-hidden group">
                        <summary className="p-unit-md flex justify-between items-center cursor-pointer list-none">
                            <span className="font-headline-md text-[18px]">რა დრო სჭირდება კამერების მონტაჟს?</span>
                            <Icon
                                className="transition-transform group-open:rotate-180"
                                name="expand_more"
                            />
                        </summary>
                        <div
                            className="px-unit-md pb-unit-md text-on-surface-variant border-t border-outline-variant/10 pt-4">
                            მონტაჟის დრო დამოკიდებულია ობიექტის სირთულესა და კამერების რაოდენობაზე. საშუალოდ,
                            სტანდარტული სისტემის გამართვას 1-2 სამუშაო დღე სჭირდება.
                        </div>
                    </details>
                    <details className="glass-card rounded-xl overflow-hidden group">
                        <summary className="p-unit-md flex justify-between items-center cursor-pointer list-none">
                            <span className="font-headline-md text-[18px]">გაქვთ თუ არა გარანტია მოწყობილობებზე?</span>
                            <Icon
                                className="transition-transform group-open:rotate-180"
                                name="expand_more"
                            />
                        </summary>
                        <div
                            className="px-unit-md pb-unit-md text-on-surface-variant border-t border-outline-variant/10 pt-4">
                            დიახ, ყველა ჩვენს მიერ მოწოდებულ მოწყობილობასა და შესრულებულ სამუშაოზე ვრცელდება 1-დან 3
                            წლამდე ოფიციალური გარანტია.
                        </div>
                    </details>
                    <details className="glass-card rounded-xl overflow-hidden group">
                        <summary className="p-unit-md flex justify-between items-center cursor-pointer list-none">
                            <span className="font-headline-md text-[18px]">როგორ ხდება Wi-Fi დაფარვის გათვლა?</span>
                            <Icon
                                className="transition-transform group-open:rotate-180"
                                name="expand_more"
                            />
                        </summary>
                        <div
                            className="px-unit-md pb-unit-md text-on-surface-variant border-t border-outline-variant/10 pt-4">
                            ჩვენ ვიყენებთ სპეციალურ პროგრამულ უზრუნველყოფას და "Site Survey" მეთოდოლოგიას, რათა
                            განვსაზღვროთ სიგნალის ოპტიმალური გავრცელება.
                        </div>
                    </details>
                </div>
            </div>
        </section>
    )
}
