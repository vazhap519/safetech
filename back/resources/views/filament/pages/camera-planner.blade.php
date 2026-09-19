<x-filament-panels::page>
    <p class="text-sm text-gray-500 dark:text-gray-400">
        ინტერაქტიული პლანერი მუშაობს ასევე ადმინში: ატვირთეთ გეგმა ან ობიექტის ფოტო, დახაზეთ კედლები და მონიშნეთ მონიტორინგის არე.
        შეინახეთ შედეგი JSON-ად ან SVG-ად. კლიენტების გამოგზავნილი გეგმები ჩანს მენიუში „კამერების გეგმები / მოთხოვნები“.
    </p>
    <div style="border:1px solid #64748b55;border-radius:1rem;overflow:hidden;background:#0b1220">
        <iframe
            src="{{ rtrim(config('app.frontend_url', env('FRONTEND_URL', 'https://safetech.ge')), '/') }}/camera-planner?embedded=1"
            title="SafeTech კამერების პლანერი"
            loading="lazy"
            referrerpolicy="strict-origin-when-cross-origin"
            style="width:100%;min-height:1050px;border:0"
        ></iframe>
    </div>
    <a href="{{ rtrim(config('app.frontend_url', env('FRONTEND_URL', 'https://safetech.ge')), '/') }}/camera-planner"
       target="_blank" rel="noopener" class="text-primary-600 underline">
        პლანერის გახსნა ახალ ფანჯარაში (თუ iframe დაბლოკილია)
    </a>
</x-filament-panels::page>
