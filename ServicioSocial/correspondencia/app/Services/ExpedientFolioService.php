<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ExpedientFolioService
{
    public function generateNextCode(): string
    {
        $year = (int) date('Y');
        $prefix = 'EXP';

        return DB::transaction(function () use ($prefix, $year) {
            $sequence = DB::table('folio_sequences')
                ->where('prefix', $prefix)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($sequence) {
                $nextNumber = $sequence->current_number + 1;
                DB::table('folio_sequences')
                    ->where('id', $sequence->id)
                    ->update([
                        'current_number' => $nextNumber,
                        'updated_at' => now(),
                    ]);
            } else {
                $nextNumber = 1;
                DB::table('folio_sequences')->insert([
                    'prefix' => $prefix,
                    'year' => $year,
                    'current_number' => $nextNumber,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $formattedNumber = str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
            return "{$prefix}-{$year}-{$formattedNumber}";
        });
    }
}
