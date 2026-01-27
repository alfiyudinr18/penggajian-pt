@props(['active' => false, 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 text-sm font-medium bg-blue-50 text-blue-600 rounded-lg transition-colors duration-200'
            : 'flex items-center px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <i class="{{ $icon }} w-5 h-5 mr-3 text-center {{ $active ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
    <span>{{ $slot }}</span>
</a>
