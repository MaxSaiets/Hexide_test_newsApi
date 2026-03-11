@php
    use App\Enums\NewsBlockType;
@endphp

<x-app>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('news.index') }}" class="text-indigo-600 hover:underline">{{ __('messages.news.news_link') }}</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-500">{{ Str::limit($news->title, 50) }}</li>
            </ol>
        </nav>

        @if($news->image)
            <img src="{{ $news->image }}" alt="{{ $news->title }}"
                class="w-full rounded-lg object-cover max-h-96 mb-6" />
        @endif

        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $news->title }}</h1>

        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200">
            @if($news->user->avatar)
                <img src="{{ $news->user->avatar }}" class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-lg">👤</div>
            @endif
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $news->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $news->published_at?->format('d M Y, H:i') }}</p>
            </div>
        </div>

        @if($news->short_description)
            <p class="text-lg text-gray-600 mb-8">{{ $news->short_description }}</p>
        @endif

        <div class="space-y-8">
            @forelse($news->blocks->sortBy('position') as $block)
                @if($block->type === NewsBlockType::Text)
                    <div class="prose max-w-none">
                        <p class="text-base text-gray-900">{{ $block->text_content ?? '' }}</p>
                    </div>

                @elseif($block->type === NewsBlockType::Image)
                    @if(isset($block->image_path))
                        <img src="{{ $block->image_path }}"
                            class="w-full rounded-lg object-cover" />
                    @endif

                @elseif($block->type === NewsBlockType::TextImageRight)
                    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                        <div class="lg:col-span-2">
                            <p class="text-base text-gray-900">{{ $block->text_content ?? '' }}</p>
                        </div>
                        @if(isset($block->image_path))
                            <img src="{{ $block->image_path }}"
                                class="mt-4 lg:mt-0 w-full rounded-lg object-cover" />
                        @endif
                    </div>

                @elseif($block->type === NewsBlockType::TextImageLeft)
                    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                        @if(isset($block->image_path))
                            <img src="{{ $block->image_path }}"
                                class="mb-4 lg:mb-0 w-full rounded-lg object-cover" />
                        @endif
                        <div class="lg:col-span-2">
                            <p class="text-base text-gray-900">{{ $block->text_content ?? '' }}</p>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-gray-500">{{ __('messages.news.no_content') }}</p>
            @endforelse
        </div>

        <div class="mt-10">
            <a href="{{ route('news.index') }}"
                class="rounded-md bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700">
                {{ __('messages.news.back_to_news') }}
            </a>
        </div>
    </div>
</x-app>
