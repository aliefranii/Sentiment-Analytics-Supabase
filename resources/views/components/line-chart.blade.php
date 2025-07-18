<div class="h-[450px] flex flex-col lg:flex-row gap-4">
    <!-- 24 Hour Sentiment Trend Container -->
    <div class="flex-1 bg-white rounded-lg shadow-md py-4 md:py-6">
        <div class="flex justify-between items-start w-full px-4 mb-2">
            <div>
                <p class="text-sm font-bold text-gray-900">24 Hour Sentiment Trend</p>
                <span class="text-sm text-gray-500">Sentiment analysis across different platforms</span>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="flex flex-col items-center justify-center">
            <div class="relative w-full h-[300px] px-4 pt-4 overflow-x-auto">
                <!-- Canvas yang mengisi penuh kontainer dan bisa di-scroll horizontal -->
                <canvas id="sentiment24hChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
