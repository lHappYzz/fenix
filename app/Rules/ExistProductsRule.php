<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class ExistProductsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }

        if (!is_array($value) || $value === []) {
            $fail('The :attribute must be array and not empty.');
            return;
        }

        if ($value !== array_filter($value, fn (mixed $value): bool => is_int($value['quantity']))) {
            $fail('The :attribute\'s quantity field must contain only integers.');
            return;
        }

        if (
            Product::query()
                ->whereIn('id', Arr::pluck($value,'product_id'))
                ->count() !== count($value)
        ) {
            $fail('The :attribute must contains only valid product ids.');
        }
    }

}
