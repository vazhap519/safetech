<x-filament-panels::page>
    <div class="rounded-xl border border-gray-200 p-6 dark:border-gray-700">
        <h2 class="text-xl font-bold mb-3">ინტერაქტიული კამერების განლაგება</h2>
        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
            კამერების პლანერის გახსნის შემდეგ ატვირთეთ სართულის გეგმა ან ობიექტის ფოტო,
            მონიშნეთ კედლები და მონიტორინგის არე, განათავსეთ კამერები და შეამოწმეთ მიახლოებითი დაფარვა.
            შეცდომით შეცვლილ გეგმას დააბრუნებთ Undo ფუნქციით. შეგიძლიათ შეინახოთ JSON/SVG და მოგვიანებით გააგრძელოთ.
        </p>
        <a href="{{ rtrim(config('app.frontend_url', env('FRONTEND_URL', 'https://safetech.ge')), '/') }}/camera-planner"
           target="_blank" rel="noopener" style="display:inline-block;padding:.85rem 1.4rem;background:#f59e0b;color:#111827;border-radius:.65rem;font-weight:700">
            კამერების პლანერის გახსნა ↗
        </a>
        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            პლანერი იხსნება უსაფრთხო ახალ ჩანართში: საიტზე ჩართული frame-ancestors 'none' და X-Frame-Options DENY
            არ გვაძლევს მისი iframe-ში ჩასმის უფლებას უსაფრთხოების პოლიტიკის დასუსტების გარეშე.
            კლიენტების მიერ გამოგზავნილი გეგმები და დაცული ფოტოების ბმულები ხელმისაწვდომია მხოლოდ ავტორიზებულ ადმინში —
            „კამერების გეგმები / მოთხოვნები“.
        </p>
    </div>
</x-filament-panels::page>
