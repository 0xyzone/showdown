<?php

namespace Tests\Feature;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntryFeeSuffixTest extends TestCase
{
    use RefreshDatabase;

    public function test_tournament_entry_fee_suffix_formatting(): void
    {
        $t1 = Tournament::create([
            'name' => 'Individual Cup',
            'slug' => 'individual-cup',
            'entry_fee' => 100,
            'entry_fee_suffix' => 'head',
        ]);

        $this->assertEquals('Rs. 100/head', $t1->formatted_entry_fee);

        $t2 = Tournament::create([
            'name' => 'Squad Championship',
            'slug' => 'squad-championship',
            'entry_fee' => 500,
            'entry_fee_suffix' => 'team',
        ]);

        $this->assertEquals('Rs. 500/team', $t2->formatted_entry_fee);
    }
}
