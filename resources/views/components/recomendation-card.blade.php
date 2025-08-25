@props(['rows' => [], 'client' => 'WAJO'])

<div class="w-full h-fit bg-white rounded-lg shadow-md flex flex-col">
    <div class="px-6 py-4">
        <p class="text-sm font-bold text-gray-900">Recap of weekly action recommendations</p>
        <span class="text-sm text-gray-500">Recap of weekly action recommendations by Jubir.Ai</span>
    </div>

    <div class="border-t border-gray-200"></div>

    <div class="relative overflow-x-auto sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-sm text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 w-1/3">Recommendations Policy</th>
                    <th class="px-6 py-3 w-1/3">Action</th>
                    <th class="px-6 py-3 w-1/3">Recommendations Content</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="odd:bg-white even:bg-gray-100 border-b border-gray-200">
                        <td class="px-6 py-2 whitespace-normal break-words">
                            {{ $row['recommendation'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-normal break-words">
                            {{ $row['action'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-normal break-words">
                            {{ $row['content'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-6 text-center text-gray-500">
                            Belum ada data rekomendasi untuk client {{ $client }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
