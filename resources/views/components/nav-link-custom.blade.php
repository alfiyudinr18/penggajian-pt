@props(['active' => false, 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'group flex items-center w-full px-3 py-3 text-sm font-medium bg-blue-50 text-blue-600 rounded-lg transition-all duration-200'
            : 'group flex items-center w-full px-3 py-3 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{-- Ikon: Pastikan ukurannya fix dan tidak mengecil (shrink-0) --}}
    <div class="mr-3 flex-shrink-0 w-6 text-center">
        <i class="{{ $icon }} text-lg {{ $active ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
    </div>
    {{-- Teks: Whitespace nowrap agar tidak turun ke bawah --}}
    <span class="whitespace-nowrap">{{ $slot }}</span>
</a>
