<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TranslationService;

class TranslationController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Translate text using MyMemory API via TranslationService
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:2000',
            'targetLanguage' => 'required|string|in:sk,en,cs,pl,de,fr,it,es',
        ]);

        $text = $request->input('text');
        $targetLanguage = $request->input('targetLanguage');

        try {
            $translatedText = $this->translationService->translate($text, $targetLanguage);
            
            return response()->json([
                'translatedText' => $translatedText,
                'success' => true,
            ]);
        } catch (\Exception $e) {
            // Return original text on error
            return response()->json([
                'translatedText' => $text,
                'success' => true,
                'error' => 'Translation service unavailable',
            ]);
        }
    }
}

