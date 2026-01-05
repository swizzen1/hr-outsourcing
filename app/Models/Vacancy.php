<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $company_id
 * @property string $title
 * @property string $description
 * @property string|null $location
 * @property string $employment_type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $expiration_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @method static Builder<static>|Vacancy newModelQuery()
 * @method static Builder<static>|Vacancy newQuery()
 * @method static Builder<static>|Vacancy publicVisible()
 * @method static Builder<static>|Vacancy query()
 * @method static Builder<static>|Vacancy whereCompanyId($value)
 * @method static Builder<static>|Vacancy whereCreatedAt($value)
 * @method static Builder<static>|Vacancy whereDescription($value)
 * @method static Builder<static>|Vacancy whereEmploymentType($value)
 * @method static Builder<static>|Vacancy whereExpirationDate($value)
 * @method static Builder<static>|Vacancy whereId($value)
 * @method static Builder<static>|Vacancy whereLocation($value)
 * @method static Builder<static>|Vacancy wherePublishedAt($value)
 * @method static Builder<static>|Vacancy whereStatus($value)
 * @method static Builder<static>|Vacancy whereTitle($value)
 * @method static Builder<static>|Vacancy whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'location',
        'employment_type',
        'status',
        'published_at',
        'expiration_date',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expiration_date' => 'date',
    ];

    public function scopePublicVisible(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function (Builder $query) use ($today) {
                $query->whereNull('expiration_date')
                    ->orWhere('expiration_date', '>=', $today);
            });
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
