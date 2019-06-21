<?php

namespace App\Console\Commands;

use App\Http\Helpers\InfusionsoftHelper;
use App\Tag;
use Illuminate\Console\Command;

class SyncTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync tags with Infusionsoft';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $infusionsoft = new InfusionsoftHelper();

        $tags = $infusionsoft->getAllTags();

        if (!$tags) {
            $this->warn("Failed to fetch tags from API");
            return;
        }

        Tag::truncate();

        $data = [];

        foreach ($tags->toArray() as $tag) {
            $data[] = [
                'tag' => $tag->name,
                'tag_id' => $tag->id
            ];
        }

        Tag::insert($data);
    }
}
