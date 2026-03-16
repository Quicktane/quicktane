<?php

declare(strict_types=1);

namespace Quicktane\CMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Quicktane\CMS\Models\Page;

class DefaultPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'identifier' => 'home',
                'title' => 'Home Page',
                'content' => '<h1>Welcome to our store</h1><p>Browse our latest products and find amazing deals.</p>',
                'meta_title' => 'Home',
                'is_active' => true,
                'sort_order' => 0,
                'layout' => 'one_column',
            ],
            [
                'identifier' => 'about-us',
                'title' => 'About Us',
                'content' => '<h1>About Us</h1><p>We are a leading e-commerce platform dedicated to providing quality products.</p>',
                'meta_title' => 'About Us',
                'is_active' => true,
                'sort_order' => 1,
                'layout' => 'one_column',
            ],
            [
                'identifier' => 'no-route',
                'title' => '404 Not Found',
                'content' => '<h1>Page Not Found</h1><p>The page you are looking for does not exist.</p>',
                'meta_title' => '404 Not Found',
                'is_active' => true,
                'sort_order' => 99,
                'layout' => 'one_column',
            ],
            [
                'identifier' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us.</p>',
                'meta_title' => 'Privacy Policy',
                'is_active' => true,
                'sort_order' => 2,
                'layout' => 'one_column',
            ],
            [
                'identifier' => 'terms-of-service',
                'title' => 'Terms of Service',
                'content' => '<h1>Terms of Service</h1><p>Please read our terms of service carefully.</p>',
                'meta_title' => 'Terms of Service',
                'is_active' => true,
                'sort_order' => 3,
                'layout' => 'one_column',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::query()->firstOrCreate(
                ['identifier' => $pageData['identifier']],
                $pageData,
            );
        }
    }
}
