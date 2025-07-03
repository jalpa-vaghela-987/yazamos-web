<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'country' => $this->country,
            'currency' => $this->currency,
            'code' => $this->code,
            'symbol' => $this->symbol,
            'thousand_separator' => $this->thousand_separator,
            'decimal_separator' => $this->decimal_separator
        ];
    }
}
