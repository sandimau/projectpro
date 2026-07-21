<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Support\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $companyId = Tenant::id();
            if ($companyId) {
                $builder->where($builder->getModel()->getTable().'.company_id', $companyId);
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->company_id) && Tenant::id()) {
                $model->company_id = Tenant::id();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
