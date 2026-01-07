<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateDocumentationPDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:generate-pdf 
                            {file=SOFTWARE_DEVELOPER_OFFBOARDING_DOCUMENTATION.md : The markdown file to convert}
                            {--output= : Output PDF filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PDF from markdown documentation file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inputFile = $this->argument('file');
        $outputFile = $this->option('output') ?: str_replace('.md', '.pdf', $inputFile);

        // Check if input file exists
        if (!File::exists($inputFile)) {
            $this->error("File not found: {$inputFile}");
            return 1;
        }

        $this->info("Reading markdown file: {$inputFile}");

        // Read markdown content
        $markdown = File::get($inputFile);

        // Convert markdown to HTML
        $html = $this->markdownToHtml($markdown);

        // Generate PDF
        $this->info("Generating PDF: {$outputFile}");
        $this->info("This may take a minute for large documents...");

        try {
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('isPhpEnabled', false);
            $pdf->setOption('isJavascriptEnabled', false);
            
            $this->info("Rendering PDF (this may take 30-60 seconds)...");
            
            // Save PDF
            $pdf->save($outputFile);

            $this->info("✅ PDF generated successfully: {$outputFile}");
            $this->info("File size: " . $this->formatBytes(File::size($outputFile)));
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to generate PDF: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Convert markdown to HTML
     */
    private function markdownToHtml($markdown)
    {
        // Basic markdown to HTML conversion
        $html = $markdown;

        // Headers - no page breaks, just regular headers
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $html);
        $html = preg_replace('/^##### (.+)$/m', '<h5>$1</h5>', $html);
        $html = preg_replace('/^###### (.+)$/m', '<h6>$1</h6>', $html);

        // Bold
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $html);

        // Italic
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/_(.+?)_/', '<em>$1</em>', $html);

        // Code blocks (preserve formatting)
        $html = preg_replace_callback('/```(\w+)?\n(.*?)```/s', function($matches) {
            $code = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');
            return '<pre style="background-color: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; border-left: 4px solid #3498db; font-family: \'Courier New\', monospace; white-space: pre-wrap; word-wrap: break-word;"><code>' . $code . '</code></pre>';
        }, $html);
        $html = preg_replace('/`([^`]+)`/', '<code style="background-color: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: \'Courier New\', monospace;">$1</code>', $html);

        // Links
        $html = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $html);

        // Lists
        $html = preg_replace('/^\* (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/^\d+\. (.+)$/m', '<li>$1</li>', $html);

        // Wrap consecutive list items in ul/ol
        $html = preg_replace('/(<li>.*<\/li>\n?)+/s', '<ul>$0</ul>', $html);
        $html = preg_replace('/<\/ul>\s*<ul>/', '', $html);

        // Horizontal rules
        $html = preg_replace('/^---$/m', '<hr>', $html);
        $html = preg_replace('/^\*\*\*$/m', '<hr>', $html);

        // Paragraphs (lines that aren't already HTML tags)
        $lines = explode("\n", $html);
        $html = '';
        $inList = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) {
                $html .= "\n";
                continue;
            }
            
            // If it's already an HTML tag, keep it as is
            if (preg_match('/^<[^>]+>/', $line) || preg_match('/^<\/[^>]+>/', $line)) {
                $html .= $line . "\n";
            } else {
                // Wrap in paragraph if not already wrapped
                if (!preg_match('/^<p>/', $line) && !preg_match('/^<h[1-6]>/', $line) && !preg_match('/^<ul>/', $line) && !preg_match('/^<ol>/', $line)) {
                    $html .= '<p>' . $line . '</p>' . "\n";
                } else {
                    $html .= $line . "\n";
                }
            }
        }

        // Wrap in HTML document
        $html = $this->wrapInHtmlDocument($html);

        return $html;
    }

    /**
     * Wrap HTML content in a complete HTML document with styles
     */
    private function wrapInHtmlDocument($content)
    {
        $styles = '
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
            }
            h1 {
                color: #2c3e50;
                border-bottom: 3px solid #3498db;
                padding-bottom: 10px;
                margin-top: 30px;
            }
            h2 {
                color: #34495e;
                border-bottom: 2px solid #ecf0f1;
                padding-bottom: 8px;
                margin-top: 25px;
            }
            h3 {
                color: #7f8c8d;
                margin-top: 20px;
            }
            h4, h5, h6 {
                color: #95a5a6;
                margin-top: 15px;
            }
            code {
                background-color: #f4f4f4;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: "Courier New", monospace;
                font-size: 0.9em;
            }
            pre {
                background-color: #f4f4f4;
                padding: 15px;
                border-radius: 5px;
                overflow-x: auto;
                border-left: 4px solid #3498db;
            }
            pre code {
                background-color: transparent;
                padding: 0;
            }
            ul, ol {
                margin: 10px 0;
                padding-left: 30px;
            }
            li {
                margin: 5px 0;
            }
            a {
                color: #3498db;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
            }
            table th, table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            table th {
                background-color: #3498db;
                color: white;
            }
            table tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            hr {
                border: none;
                border-top: 2px solid #ecf0f1;
                margin: 20px 0;
            }
            p {
                margin: 10px 0;
            }
            strong {
                color: #2c3e50;
            }
            /* Keep headers with their content - prevent headers from breaking away from content */
            h1, h2, h3, h4, h5, h6 {
                page-break-inside: avoid;
                break-inside: avoid;
                page-break-after: avoid;
                break-after: avoid;
            }
            /* Ensure content immediately after headers stays together */
            h1 + *, h2 + *, h3 + *, h4 + *, h5 + *, h6 + * {
                page-break-before: avoid;
            }
            @media print {
                body {
                    max-width: 100%;
                    padding: 10px;
                }
                h1, h2, h3, h4, h5, h6 {
                    page-break-after: avoid !important;
                    break-after: avoid !important;
                }
            }
        </style>';

        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Developer Offboarding Documentation</title>
    ' . $styles . '
</head>
<body>
    ' . $content . '
</body>
</html>';
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

