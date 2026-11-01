@php($style = match ($status) {
    'terkirim' => ['Terkirim', 'bg-primary-50 text-primary-700 ring-primary-200', '#2A9D6F'],
    'diterima' => ['Diterima', 'bg-emerald-50 text-emerald-700 ring-emerald-200', '#17573B'],
    'perlu_perbaikan' => ['Perlu Perbaikan', 'bg-red-50 text-red-600 ring-red-200', '#DC2626'],
    default => ['Belum Dikirim', 'bg-slate-100 text-slate-500 ring-slate-200', '#94A3B8'],
})
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 whitespace-nowrap '.$style[1]]) }}>
    <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $style[2] }}"></span>
    {{ $style[0] }}
</span>
