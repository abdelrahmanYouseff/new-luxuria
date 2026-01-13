<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class SitemapController extends Controller
{
    /**
     * Generate and return the sitemap XML
     */
    public function index()
    {
        // Start XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static pages with their priorities and change frequencies
        $staticPages = [
            [
                'url' => route('home'),
                'priority' => '1.0',
                'changefreq' => 'daily',
                'lastmod' => now()->toAtomString(),
            ],
            [
                'url' => route('about'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString(),
            ],
            [
                'url' => route('contact'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString(),
            ],
            [
                'url' => route('privacy'),
                'priority' => '0.5',
                'changefreq' => 'yearly',
                'lastmod' => now()->toAtomString(),
            ],
            [
                'url' => route('coupons.index'),
                'priority' => '0.9',
                'changefreq' => 'weekly',
                'lastmod' => now()->toAtomString(),
            ],
            [
                'url' => route('vehicles.index.public'),
                'priority' => '0.9',
                'changefreq' => 'daily',
                'lastmod' => now()->toAtomString(),
            ],
        ];

        // Add static pages to sitemap
        foreach ($staticPages as $page) {
            $xml .= $this->buildUrlEntry($page);
        }

        // Add dynamic vehicle pages
        // Include visible vehicles and vehicles where visibility is not set (null)
        $vehicles = Vehicle::where(function($query) {
                $query->where('is_visible', true)
                      ->orWhereNull('is_visible');
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($vehicles as $vehicle) {
            $xml .= $this->buildUrlEntry([
                'url' => route('cars.show', $vehicle->id),
                'priority' => '0.7',
                'changefreq' => 'weekly',
                'lastmod' => $vehicle->updated_at ? $vehicle->updated_at->toAtomString() : now()->toAtomString(),
            ]);
        }

        // Close XML
        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Build a URL entry for the sitemap
     */
    private function buildUrlEntry(array $data): string
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($data['url'], ENT_XML1, 'UTF-8') . "</loc>\n";
        $xml .= "    <lastmod>" . htmlspecialchars($data['lastmod'], ENT_XML1, 'UTF-8') . "</lastmod>\n";
        $xml .= "    <changefreq>" . htmlspecialchars($data['changefreq'], ENT_XML1, 'UTF-8') . "</changefreq>\n";
        $xml .= "    <priority>" . htmlspecialchars($data['priority'], ENT_XML1, 'UTF-8') . "</priority>\n";
        $xml .= "  </url>\n";
        
        return $xml;
    }
}
