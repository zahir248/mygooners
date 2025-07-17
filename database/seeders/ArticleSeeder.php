<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Arsenal Signs New Striker in Record Deal',
                'content' => 'Arsenal Football Club has today announced the signing of a world-class striker in what is being called the biggest transfer of the season. The player, who has been on the radar of top European clubs, has finally chosen to join the Gunners in a deal worth over £100 million.

The new signing brings with them an impressive goal-scoring record and is expected to bolster Arsenal\'s attacking options significantly. Manager Mikel Arteta expressed his delight at securing the player\'s signature, stating that this signing represents the club\'s ambition to compete at the highest level.

Fans have been eagerly awaiting this announcement, and the Emirates Stadium is expected to be packed for the player\'s debut. The striker is set to wear the number 9 shirt and will be available for selection in the upcoming Premier League fixtures.',
                'excerpt' => 'Arsenal has completed the signing of a world-class striker in what is being called the biggest transfer of the season.',
                'cover_image' => null,
                'category' => 'Transfer News',
                'tags' => ['Arsenal', 'Transfer', 'Striker', 'Premier League'],
                'youtube_video_id' => null,
                'is_featured' => true,
                'status' => 'published',
                'meta_title' => 'Arsenal Signs New Striker in Record Deal',
                'meta_description' => 'Arsenal has completed the signing of a world-class striker in what is being called the biggest transfer of the season.',
                'keywords' => 'arsenal, transfer, striker, premier league, signing',
                'views_count' => 1543,
                'published_at' => now()->subHours(2),
            ],
            [
                'title' => 'Match Report: Arsenal vs Manchester United',
                'content' => 'In a thrilling encounter at the Emirates Stadium, Arsenal secured a crucial victory over Manchester United in a match that had everything - goals, drama, and controversy. The Gunners showed their title credentials with a dominant performance that left the Red Devils struggling to cope.

The match started at a frenetic pace with both teams looking to assert their authority early on. Arsenal\'s midfield trio controlled the tempo of the game, while their attacking players constantly threatened the United defense. The breakthrough came in the 25th minute when a well-worked move resulted in the opening goal.

Despite United\'s attempts to get back into the game, Arsenal maintained their composure and added a second goal before half-time. The second half saw more of the same, with Arsenal continuing to dominate possession and create chances. The final score of 3-1 reflected Arsenal\'s superiority on the day.

This victory sends a strong message to the rest of the Premier League that Arsenal are serious title contenders this season.',
                'excerpt' => 'Arsenal secured a crucial victory over Manchester United in a thrilling encounter at the Emirates Stadium.',
                'cover_image' => null,
                'category' => 'Match Reports',
                'tags' => ['Arsenal', 'Manchester United', 'Match Report', 'Premier League'],
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'is_featured' => true,
                'status' => 'published',
                'meta_title' => 'Match Report: Arsenal vs Manchester United',
                'meta_description' => 'Arsenal secured a crucial victory over Manchester United in a thrilling encounter at the Emirates Stadium.',
                'keywords' => 'arsenal, manchester united, match report, premier league, victory',
                'views_count' => 2156,
                'published_at' => now()->subHours(6),
            ],
            [
                'title' => 'Training Ground Updates: Player Fitness and Tactics',
                'content' => 'Behind the scenes at Arsenal\'s training ground, the coaching staff have been working tirelessly to prepare the squad for the upcoming fixtures. The latest training sessions have focused on improving player fitness levels and implementing new tactical approaches.

Several key players have been working on their recovery and conditioning, with the medical team closely monitoring their progress. The coaching staff have also been experimenting with different formations and tactical setups to maximize the team\'s potential.

Young players have been given opportunities to impress during these sessions, with some showing real promise for the future. The atmosphere around the training ground has been positive, with players and staff working together towards common goals.

These training ground updates provide insight into the preparation that goes into every match, highlighting the dedication and professionalism of everyone involved with the club.',
                'excerpt' => 'Latest updates from Arsenal\'s training ground including player fitness and tactical preparations.',
                'cover_image' => null,
                'category' => 'Training',
                'tags' => ['Arsenal', 'Training', 'Fitness', 'Tactics'],
                'youtube_video_id' => null,
                'is_featured' => false,
                'status' => 'draft',
                'meta_title' => 'Training Ground Updates: Player Fitness and Tactics',
                'meta_description' => 'Latest updates from Arsenal\'s training ground including player fitness and tactical preparations.',
                'keywords' => 'arsenal, training, fitness, tactics, preparation',
                'views_count' => 0,
                'published_at' => null,
            ],
        ];

        foreach ($articles as $articleData) {
            $articleData['slug'] = Str::slug($articleData['title']);
            Article::create($articleData);
        }
    }
}
