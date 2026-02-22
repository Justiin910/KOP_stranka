<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    private const API_URL = 'https://api.mymemory.translated.net/get';
    private const SOURCE_LANGUAGE = 'sk'; // Notifications stored in Slovak
    private const CACHE_DURATION = 60 * 60 * 24; // 24 hours

    /**
     * Translate text using MyMemory API (free, no key required)
     * 
     * @param string $text Text to translate
     * @param string $targetLanguage Target language code (e.g., 'en', 'de', 'fr')
     * @return string Translated text or original if same language
     */
    public function translate(string $text, string $targetLanguage): string
    {
        // If target is Slovak (source language), return original
        if ($targetLanguage === self::SOURCE_LANGUAGE) {
            return $text;
        }

        // Create cache key based on text and target language
        $cacheKey = 'translation_' . md5($text . $targetLanguage);

        // Check cache first
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            // Map language codes to MyMemory format
            $languageMap = [
                'en' => 'en',
                'de' => 'de',
                'fr' => 'fr',
                'es' => 'es',
                'it' => 'it',
                'pl' => 'pl',
                'cs' => 'cs',
                'sk' => 'sk',
            ];

            $targetLangCode = $languageMap[$targetLanguage] ?? $targetLanguage;

            // Call MyMemory API
            $response = Http::timeout(30)->withoutVerifying()->get(self::API_URL, [
                'q' => $text,
                'langpair' => self::SOURCE_LANGUAGE . '|' . $targetLangCode,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Check if translation was successful
                if (isset($responseData['responseStatus']) && $responseData['responseStatus'] == 200) {
                    if (isset($responseData['responseData']['translatedText'])) {
                        $translatedText = trim($responseData['responseData']['translatedText']);
                        
                        // Cache the translation
                        Cache::put($cacheKey, $translatedText, self::CACHE_DURATION);
                        return $translatedText;
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Translation error: ' . $e->getMessage());
        }

        // Return original text if translation fails
        return $text;
    }

    /**
     * Translate notification object
     * 
     * @param array $notification Notification data with title and message
     * @param string $targetLanguage Target language code
     * @return array Translated notification
     */
    public function translateNotification(array $notification, string $targetLanguage): array
    {
        $notification['title'] = $this->translate($notification['title'], $targetLanguage);
        $notification['message'] = $this->translate($notification['message'], $targetLanguage);
        return $notification;
    }
}

