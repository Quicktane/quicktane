<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Quicktane\Core\Product\Dto\AttributeOptionDto;
use Quicktane\Core\Product\Dto\CreateAttributeDto;
use Quicktane\Core\Product\Enums\AttributeType;
use Quicktane\Core\Product\Managers\AttributeManager;

class TestAttributeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:attribute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        AttributeManager $attributeManager,
    ) {
        $attribute = $attributeManager->create(
            CreateAttributeDto::fromArray([
                'name' => 'Color',
                'slug' => 'color',
                'type' => AttributeType::SELECT,
            ]),
            //todo create collection for DTOs and create all method "from" in dto (ex.: fromArray, fromCollection, fromRequest)
            collect([
                AttributeOptionDto::fromArray([
                    'name' => 'Black',
                    'slug' => 'black',
                ]),
                AttributeOptionDto::fromArray([
                    'name' => 'Red',
                    'slug' => 'red',
                ]),
                AttributeOptionDto::fromArray([
                    'name' => 'Green',
                    'slug' => 'green',
                ]),
            ])
        );
    }
}
