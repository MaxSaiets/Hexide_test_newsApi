<x-app>
    <div class="text-center py-16">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">📰 News API</h1>
        <p class="text-lg text-gray-600 mb-8">API для управління новинами з блоковою структурою контенту</p>

        <div class="flex justify-center gap-4">
            <a href="{{ route('news.index') }}"
                class="rounded-md bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700">
                Переглянути новини
            </a>
            <a href="/api/documentation"
                class="rounded-md border border-indigo-600 px-6 py-3 text-sm font-medium text-indigo-600 hover:bg-indigo-50">
                Swagger Docs
            </a>
        </div>
    </div>
</x-app>
