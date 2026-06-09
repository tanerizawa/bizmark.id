<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewLandingController extends Controller
{
    public function home()
    {
        $locale = app()->getLocale();

        try {
            $latestArticles = cache()->remember("new.landing.articles.{$locale}", 600, function () {
                return \App\Models\Article::published()
                    ->orderBy('published_at', 'desc')
                    ->take(5)
                    ->get();
            });
        } catch (Throwable $e) {
            Log::warning('New landing articles cache unavailable', ['locale' => $locale]);
            $latestArticles = collect();
        }

        $services = config('services_data', []);
        $testimonials = config('landing.testimonials', []);
        $faq = config('landing.faq', []);
        $clients = config('landing.clients', []);
        $contact = config('landing_metrics.contact', []);
        $stats = config('landing_metrics.stats', []);

        return view('new.pages.home', compact(
            'latestArticles', 'services', 'testimonials', 'faq',
            'clients', 'contact', 'stats', 'locale'
        ));
    }

    public function services()
    {
        $services = config('services_data', []);
        return view('new.pages.services', compact('services'));
    }

    public function process()
    {
        return view('new.pages.process');
    }

    public function pricing()
    {
        return view('new.pages.pricing');
    }

    public function about()
    {
        return view('new.pages.about');
    }

    public function blog()
    {
        $articles = \App\Models\Article::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        return view('new.pages.blog', compact('articles'));
    }

    public function contact()
    {
        return view('new.pages.contact');
    }
}
