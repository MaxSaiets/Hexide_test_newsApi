@props(['block'])

@php $url = str_contains($block->image_path, 'http') ? $block->image_path : Storage::url($block->image_path); @endphp

@if($block->type->value === 'image')
    <img src="{{ $url }}" class="w-full rounded-lg object-cover" />

@elseif($block->type->value === 'text_image_right')
    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <div class="lg:col-span-2">
            <p class="text-base text-gray-900">{{ $block->text_content }}</p>
        </div>
        <img src="{{ $url }}" class="mt-4 lg:mt-0 w-full rounded-lg object-cover" />
    </div>

@elseif($block->type->value === 'text_image_left')
    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <img src="{{ $url }}" class="mb-4 lg:mb-0 w-full rounded-lg object-cover" />
        <div class="lg:col-span-2">
            <p class="text-base text-gray-900">{{ $block->text_content }}</p>
        </div>
    </div>
@endif
