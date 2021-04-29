<?php

use Illuminate\Database\Seeder;

use App\DocketFieldGridValue;
class DocketFieldGridValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocketFieldGridValue::create([
          'sent_docket_id' => 653,
           'docket_field_grid_id' => 17,
            'value' => 'Tshering Lama',
            'order' => '1-1',
        ]);

        DocketFieldGridValue::create([
            'sent_docket_id' => 653,
            'docket_field_grid_id' => 17,
            'value' => 'Aarju Gaire',
            'order' => '1-2',
        ]);

        DocketFieldGridValue::create([
            'sent_docket_id' => 653,
            'docket_field_grid_id' => 18,
            'value' => \Carbon\Carbon::parse('1991-08-08'),
            'order' => '2-1',
        ]);

        DocketFieldGridValue::create([
            'sent_docket_id' => 653,
            'docket_field_grid_id' => 18,
            'value' => \Carbon\Carbon::parse('1995-08-08'),
            'order' => '2-2',
        ]);

        DocketFieldGridValue::create([
            'sent_docket_id' => 653,
            'docket_field_grid_id' => 19,
            'value' => 'Makalbari',
            'order' => '1-1',
        ]);

        DocketFieldGridValue::create([
            'sent_docket_id' => 653,
            'docket_field_grid_id' => 19,
            'value' => 'Koteshwor',
            'order' => '1-2',
        ]);

        DocketFieldGridValue::create([
            'sent_docket_id' => 653,
            'docket_field_grid_id' => 20,
            'value' => \Carbon\Carbon::parse('2002-08-08'),
            'order' => '2-1',
        ]);
        DocketFieldGridValue::create([
            'sent_docket_id' => 653,
            'docket_field_grid_id' => 20,
            'value' => \Carbon\Carbon::parse('2003-08-08'),
            'order' => '2-2',
        ]);

    }
}
