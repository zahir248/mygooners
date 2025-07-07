<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $search = $request->get('search');
        
        // Sample articles data
        $articles = collect([
            (object) [
                'id' => 1,
                'title' => 'Arsenal Signs New Striker in Record Deal',
                'slug' => 'arsenal-signs-new-striker-record-deal',
                'excerpt' => 'Arsenal has completed the signing of a world-class striker in what is being called the biggest transfer of the season.',
                'content' => '<p>Arsenal Football Club has today announced the signing of...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&h=400&fit=crop',
                'category' => 'Transfer News',
                'tags' => ['Arsenal', 'Transfer', 'Striker', 'Premier League'],
                'published_at' => now()->subHours(2),
                'views_count' => 1543,
                'is_featured' => true,
                'youtube_video_id' => null
            ],
            (object) [
                'id' => 2,
                'title' => 'Match Report: Arsenal vs Manchester United',
                'slug' => 'match-report-arsenal-vs-manchester-united',
                'excerpt' => 'A thrilling encounter at the Emirates Stadium as Arsenal secured a crucial victory against Manchester United.',
                'content' => '<p>What a match! Arsenal showed great character to come from behind...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=800&h=400&fit=crop',
                'category' => 'Match Reports',
                'tags' => ['Arsenal', 'Manchester United', 'Match Report', 'Emirates Stadium'],
                'published_at' => now()->subHours(6),
                'views_count' => 2156,
                'is_featured' => true,
                'youtube_video_id' => 'dQw4w9WgXcQ'
            ],
            (object) [
                'id' => 3,
                'title' => 'Training Ground Updates and Player Fitness',
                'slug' => 'training-ground-updates-player-fitness',
                'excerpt' => 'Latest updates from the Arsenal training ground including player fitness reports and tactical preparations.',
                'content' => '<p>The Arsenal training ground has been buzzing with activity...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&h=400&fit=crop',
                'category' => 'Training',
                'tags' => ['Arsenal', 'Training', 'Fitness', 'Tactics'],
                'published_at' => now()->subHours(12),
                'views_count' => 876,
                'is_featured' => false,
                'youtube_video_id' => null
            ],
            (object) [
                'id' => 4,
                'title' => 'Arsenal Women Team Secures Important Victory',
                'slug' => 'arsenal-women-team-secures-important-victory',
                'excerpt' => 'Arsenal Women continue their impressive form with a commanding performance in the league.',
                'content' => '<p>Arsenal Women showed their class once again...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1577223625816-7546f30a2b62?w=800&h=400&fit=crop',
                'category' => 'Women\'s Team',
                'tags' => ['Arsenal Women', 'Victory', 'League', 'Football'],
                'published_at' => now()->subDay(),
                'views_count' => 1234,
                'is_featured' => false,
                'youtube_video_id' => null
            ],
            (object) [
                'id' => 5,
                'title' => 'Transfer Rumours: Latest Updates',
                'slug' => 'transfer-rumours-latest-updates',
                'excerpt' => 'Round-up of the latest transfer rumours and speculation surrounding Arsenal.',
                'content' => '<p>The transfer window is heating up with several rumours...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1486286701208-1d58e9338013?w=800&h=400&fit=crop',
                'category' => 'Transfer Rumours',
                'tags' => ['Arsenal', 'Transfer', 'Rumours', 'Window'],
                'published_at' => now()->subDays(2),
                'views_count' => 2890,
                'is_featured' => false,
                'youtube_video_id' => null
            ]
        ]);

        // Filter by category if provided
        if ($category) {
            $articles = $articles->filter(function ($article) use ($category) {
                return strtolower($article->category) === strtolower($category);
            });
        }

        // Filter by search if provided
        if ($search) {
            $articles = $articles->filter(function ($article) use ($search) {
                return str_contains(strtolower($article->title), strtolower($search)) ||
                       str_contains(strtolower($article->excerpt), strtolower($search));
            });
        }

        // Get categories for filter
        $categories = [
            'Transfer News',
            'Match Reports',
            'Training',
            'Women\'s Team',
            'Transfer Rumours',
            'Analysis',
            'History'
        ];

        // Paginate results (simulate pagination)
        $articles = $articles->take(10);
        
        return view('client.blog.index', compact('articles', 'categories', 'category', 'search'));
    }

    public function show($slug)
    {
        // Sample article data
        $article = (object) [
            'id' => 1,
            'title' => 'Arsenal Signs New Striker in Record Deal',
            'slug' => 'arsenal-signs-new-striker-record-deal',
            'excerpt' => 'Arsenal has completed the signing of a world-class striker in what is being called the biggest transfer of the season.',
            'content' => '<p>Arsenal Football Club has today announced the signing of a world-class striker in what is being hailed as the biggest transfer of the season. The deal, reportedly worth £85 million, brings the prolific goal scorer to the Emirates Stadium on a five-year contract.</p>

<p>The 26-year-old forward has been a long-term target for Arsenal manager Mikel Arteta, who has been looking to strengthen the club\'s attacking options. The player scored 28 goals in 34 league appearances last season and has been one of Europe\'s most sought-after talents.</p>

<p>"We are delighted to welcome our new striker to Arsenal," said Arteta in a press conference. "He is a player who brings not only goals but also the winning mentality that we need to compete at the highest level. His experience and quality will be invaluable to our squad."</p>

<p>The signing represents Arsenal\'s continued ambition to compete for major trophies and demonstrates the club\'s commitment to backing their manager in the transfer market. The player is expected to make his debut in the upcoming match against Liverpool.</p>

<p>Arsenal fans have been eagerly awaiting this announcement, with many taking to social media to express their excitement about the new addition to the squad. The striker will wear the number 9 shirt, which has been vacant since the departure of the previous striker last season.</p>',
            'cover_image' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=1200&h=600&fit=crop',
            'category' => 'Transfer News',
            'tags' => ['Arsenal', 'Transfer', 'Striker', 'Premier League'],
            'published_at' => now()->subHours(2),
            'views_count' => 1543,
            'is_featured' => true,
            'youtube_video_id' => null,
            'meta_title' => 'Arsenal Signs New Striker in Record Deal - MyGooners',
            'meta_description' => 'Arsenal has completed the signing of a world-class striker in what is being called the biggest transfer of the season.'
        ];

        // Related articles
        $relatedArticles = collect([
            (object) [
                'id' => 2,
                'title' => 'Match Report: Arsenal vs Manchester United',
                'slug' => 'match-report-arsenal-vs-manchester-united',
                'excerpt' => 'A thrilling encounter at the Emirates Stadium.',
                'cover_image' => 'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=400&h=300&fit=crop',
                'category' => 'Match Reports',
                'published_at' => now()->subHours(6)
            ],
            (object) [
                'id' => 3,
                'title' => 'Training Ground Updates and Player Fitness',
                'slug' => 'training-ground-updates-player-fitness',
                'excerpt' => 'Latest updates from the Arsenal training ground.',
                'cover_image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400&h=300&fit=crop',
                'category' => 'Training',
                'published_at' => now()->subHours(12)
            ]
        ]);

        return view('client.blog.show', compact('article', 'relatedArticles'));
    }

    public function category($category)
    {
        return $this->index(request()->merge(['category' => $category]));
    }
} 