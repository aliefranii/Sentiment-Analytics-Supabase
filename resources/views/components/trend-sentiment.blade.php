<div class="flex flex-col lg:flex-row gap-4">
    <div class="flex-1 bg-white rounded-lg shadow-md py-4 md:py-6">
        <div class="flex justify-between items-start w-full px-4 mb-2">
            <div>
                <p class="text-sm font-bold text-gray-900">24 Hour Sentiment Trend</p>
                <span class="text-sm text-gray-500">Sentiment analysis across different platforms</span>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="flex flex-col items-center justify-center">
            <div class="h-[380px] relative px-4 pt-4">
                <!-- Canvas yang mengisi penuh kontainer -->
                <canvas id="sentiment24hChart" class="w-full h-full object-contain"></canvas>
            </div>
        </div>

    </div>

    {{-- container live updates --}}
    <div class="w-[500px] flex-none bg-white rounded-lg shadow-md py-4 md:py-6">
        <div class="flex justify-between items-start w-full px-4 mb-2">
            <div>
                <p class="text-sm font-bold text-gray-900">Sentiment Distribution</p>
                <span class="text-sm text-gray-500">Overall sentiment breakdown across all sources</span>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="flex flex-col items-center justify-center">
            <div class="h-[300px] relative w-full">
                <!-- Canvas yang mengisi penuh kontainer -->
                {{-- <canvas id="sentiment24hChart" class="w-full h-full object-contain"></canvas> --}}
            </div>
        </div>
    </div>
</div>
