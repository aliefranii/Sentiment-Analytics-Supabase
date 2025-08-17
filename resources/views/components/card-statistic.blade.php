<div>
    <!-- Wrapper Flex untuk menyusun card secara horizontal dengan jarak antar card -->
    <div class="w-full grid sm:grid-cols-2 md:grid-cols-3 lg:w-full grid-cols-3 gap-4">
        <!-- Card Pertama (Overall Sentiment) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 flex flex-col">
            <div class="p-2 flex justify-between items-center">
                <div>
                    <p class="text-[12px] font-semibold text-gray-900 mb-[4px]">TOTAL NEWS</p>
                    <p class="text-xl font-bold text-gray-700 mb-[4px]">{{ $totalNews }}</p>
                    <div class="flex items-center text-[12px]">
                        @if ($percentageChangeNews >= 0)
                            <!-- Positif (Kenaikan) -->
                            <i class="ph-lg ph-trend-up text-green-500"></i>
                            <span class="text-green-500 ml-[4px]">{{ number_format($percentageChangeNews, 2) }}% </span>
                            <span class="text-gray-500 ml-[4px]">from last week</span>
                        @else
                            <!-- Negatif (Penurunan) -->
                            <i class="ph-lg ph-trend-down text-red-500"></i>
                            <span class="text-red-500 ml-[4px]">{{ number_format($percentageChangeNews, 2) }}% </span>
                            <span class="text-gray-500 ml-[4px]">from last week</span>
                        @endif
                    </div>
                </div>
                <!-- Ikon Senyum (Happy Face) di tengah atas-bawah card -->
                <div
                    class="flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-500 rounded-full ml-[32px]">
                    <i class="ph-xl ph-note"></i>
                </div>
            </div>
        </div>

        <!-- Card Kedua (Total Mentions) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 flex flex-col">
            <!-- Card Header (Total Mentions) -->
            <div class="p-2 flex justify-between items-center">
                <div>
                    <p class="text-[12px] font-semibold text-gray-900 mb-[4px]">ENGANGEMENT RATE</p>
                    <p class="text-xl font-bold text-gray-700 mb-[4px]">
                        {{ number_format($engagementRateThisMonth, 0) }}%</p>
                    <div class="flex items-center text-[12px]">
                        @if ($percentageChangeEngagementRate >= 0)
                            <!-- Positif (Kenaikan) -->
                            <i class="ph-lg ph-trend-up text-green-500"></i>
                            <span
                                class="text-green-500 ml-[4px]">{{ number_format($percentageChangeEngagementRate, 2) }}%
                            </span>
                            <span class="text-gray-500 ml-[4px]">from last month</span>
                        @else
                            <!-- Negatif (Penurunan) -->
                            <i class="ph-lg ph-trend-down text-red-500"></i>
                            <span class="text-red-500 ml-[4px]">{{ number_format($percentageChangeEngagementRate, 2) }}%
                            </span>
                            <span class="text-gray-500 ml-[4px]">from last month</span>
                        @endif
                    </div>
                </div>
                <!-- Ikon Senyum (Happy Face) di tengah atas-bawah card -->
                <div
                    class="flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-500 rounded-full ml-[32px]">
                    <i class="ph-xl ph-chat-circle-text"></i>
                </div>
            </div>
        </div>

        <!-- Card Ketiga (Engagement Rate) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 flex flex-col">
            <!-- Card Header (Engagement Rate) -->
            <div class="p-2 flex justify-between items-center">
                <div>
                    <p class="text-[12px] font-semibold text-gray-900 mb-[4px]">OVERALL SENTIMENT</p>
                    <p class="text-xl font-bold text-gray-700 mb-[4px]">8.4%</p>
                    <div class="flex items-center text-green-500 text-[12px]">
                        <i class="ph-lg ph-trend-up"></i>
                        <span class="ml-[4px]">+1.8% </span>
                        <span class="text-gray-500 ml-[4px]">from last month</span>
                    </div>
                </div>
                <!-- Ikon Senyum (Happy Face) di tengah atas-bawah card -->
                <div
                    class="flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-500 rounded-full ml-[32px]">
                    <i class="ph-xl ph-thumbs-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>
