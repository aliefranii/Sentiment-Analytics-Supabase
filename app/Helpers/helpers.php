<?php

if (!function_exists('parseManualMarkdown')) {
    /**
     * Mengubah format mirip WhatsApp menjadi HTML tanpa dependensi.
     *
     * @param string|null $text
     * @return string
     */
    function parseManualMarkdown(?string $text): string
    {
        if (is_null($text)) {
            return '';
        }

        // 1. Amankan seluruh input untuk mencegah XSS (PENTING!)
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // 2. Ubah format ala WhatsApp menjadi HTML
        // Penting: Proses yang lebih spesifik (3 simbol) dulu, baru yang umum.
        $text = preg_replace('/```(.*?)```/', '<code class="bg-gray-200 p-1 rounded text-sm">$1</code>', $text); // Monospace
        $text = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $text);       // *Tebal*
        $text = preg_replace('/_(.*?)_/', '<em>$1</em>', $text);             // _Miring_
        $text = preg_replace('/~(.*?)~/', '<del>$1</del>', $text);             // ~Coret~

        // 3. Ubah URL mentah menjadi link yang bisa diklik
        $text = preg_replace(
            '/(https|http|ftp):\/\/[^\s<]+/',
            '<a href="$0" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">$0</a>',
            $text
        );
        $text = nl2br($text);

        return $text;
    }
}