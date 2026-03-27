
<div style="font-family: Arial; max-width: 600px">

{{-- 🔵 TITLE --}}
<div style="color:#1a0dab; font-size:18px; font-weight:500;">
    {{ $get('seo.title') ?? $get('title') ?? 'SEO Title Example' }}
</div>

{{-- 🟢 URL --}}
<div style="color:#006621; font-size:14px; margin:4px 0;">
    {{ url($get('slug') ?? '/') }}
</div>

{{-- ⚫ DESCRIPTION --}}
<div style="color:#545454; font-size:14px;">
    {{ $get('seo.description') ?? $get('description') ?? 'აქ გამოჩნდება description...' }}
</div>

</div>
