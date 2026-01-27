<?php

use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\MorphMany;

test('companies expose a documents morph relation', function () {
    $company = new Company;

    expect($company->documents())->toBeInstanceOf(MorphMany::class);
});
