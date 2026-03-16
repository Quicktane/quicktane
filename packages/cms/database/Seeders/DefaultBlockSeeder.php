<?php

declare(strict_types=1);

namespace Quicktane\CMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Quicktane\CMS\Models\Block;

class DefaultBlockSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            [
                'identifier' => 'footer-links',
                'title' => 'Footer Links',
                'content' => '<ul><li><a href="/about-us">About Us</a></li><li><a href="/privacy-policy">Privacy Policy</a></li><li><a href="/terms-of-service">Terms of Service</a></li></ul>',
                'is_active' => true,
            ],
            [
                'identifier' => 'contact-info',
                'title' => 'Contact Information',
                'content' => '<p>Email: support@quicktane.local<br>Phone: +1 (555) 123-4567</p>',
                'is_active' => true,
            ],
        ];

        foreach ($blocks as $blockData) {
            Block::query()->firstOrCreate(
                ['identifier' => $blockData['identifier']],
                $blockData,
            );
        }
    }
}
