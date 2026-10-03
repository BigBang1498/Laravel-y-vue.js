<?php

namespace App\Services;

use App\Enums\DocumentDirection;
use Illuminate\Support\Facades\DB;

class FolioGeneratorService
{
    public function generateNextFolio(DocumentDirection $direction, ?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $prefix = $direction === DocumentDirection::RECEIVED ? 'UNI' : 'UNI-SAL';

        return DB::transaction(function () use ($prefix, $year) {
            $sequence = DB::table('folio_sequences')
                ->where('prefix', $prefix)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                DB::table('folio_sequences')->insert([
                    'prefix' => $prefix,
                    'year' => $year,
                    'current_number' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $nextNumber = 1;
            } else {
                $nextNumber = $sequence->current_number + 1;
                DB::table('folio_sequences')
                    ->where('id', $sequence->id)
                    ->update([
                        'current_number' => $nextNumber,
                        'updated_at' => now(),
                    ]);
            }

            $formattedNumber = str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);

            return "{$prefix}-{$year}-{$formattedNumber}";
        });
    }
}
