<x-app>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('messages.news.latest_news') }}</h2>

            <form method="GET" action="{{ route('news.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.news.search_placeholder') }}"
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    {{ __('messages.news.search_button') }}
                </button>
                @if($search)
                    <a href="{{ route('news.index') }}"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        {{ __('messages.news.reset_button') }}
                    </a>
                @endif
            </form>
        </div>

        @if($news->isEmpty())
            <p class="text-center text-gray-500 py-12">{{ __('messages.news.no_news') }}</p>
        @else
            <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                @foreach($news as $item)
                    <div class="group relative">
                        @if($item->image)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                class="aspect-square w-full rounded-md bg-gray-200 object-cover group-hover:opacity-75 lg:aspect-auto lg:h-80" />
                        @else
                            <div class="aspect-square w-full rounded-md bg-gray-200 flex items-center justify-center text-gray-400 text-4xl lg:aspect-auto lg:h-80">
                                📷
                            </div>
                        @endif

                        <div class="mt-4 flex justify-between">
                            <div>
                                <h3 class="text-sm text-gray-700">
                                    <a href="{{ route('news.show', $item->slug) }}">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        {{ $item->title }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $item->user->name ?? __('messages.news.author') }}
                                </p>
                            </div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $item->published_at?->format('d.m.Y') }}
                            </p>
                        </div>

                        @if($item->short_description)
                            <p class="mt-2 text-sm text-gray-500">
                                {{ Str::limit($item->short_description, 80) }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</x-app>
