<?php

namespace App\Http\Resources;

use App\Support\Calculators\CalculatorProfileBuilder;
use App\Support\MultilingualContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCalculatorProfileResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $locale = $request->string('locale')->toString();
        $locale = in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';

        return app(CalculatorProfileBuilder::class)->build($this->resource, $locale);
    }
}
