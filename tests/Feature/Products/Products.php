<?php

namespace Tests\Feature\Products;

use Illuminate\Foundation\Testing\WithFaker;
use Quicktane\Core\Category\Services\CategorySyncService;
use Tests\TestCase;

class Products extends TestCase
{
    use WithFaker;

    protected CategorySyncService $categoryService;

    public function __construct(string $name)
    {
        parent::__construct($name);
    }
}
