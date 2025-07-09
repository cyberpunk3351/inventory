<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Carbon\Carbon;

class Device extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'first_deployed_at',
        'health_lifecycle_value',
        'health_lifecycle_unit',
    ];

    protected $casts = [
        'first_deployed_at' => 'date',
    ];

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_deployed_at' => $this->first_deployed_at?->format('Y-m-d'),
            'health_lifecycle_value' => $this->health_lifecycle_value,
            'health_lifecycle_unit' => $this->health_lifecycle_unit,
            'device_health' => $this->getDeviceHealthAttribute(),
        ];
    }

    /**
     * Calculate and return the device health status
     */
    public function getDeviceHealthAttribute(): string
    {
        if (!$this->first_deployed_at) {
            return 'N/A';
        }

        $deployedAt = Carbon::parse($this->first_deployed_at);
        $now = Carbon::now();

        // Calculate lifecycle end date
        $lifecycleEnd = match($this->health_lifecycle_unit) {
            'year' => $deployedAt->copy()->addYears($this->health_lifecycle_value),
            'month' => $deployedAt->copy()->addMonths($this->health_lifecycle_value),
            'day' => $deployedAt->copy()->addDays($this->health_lifecycle_value),
        };

        // Calculate total lifecycle duration in days
        $totalLifecycleDays = $deployedAt->diffInDays($lifecycleEnd);

        // Calculate days passed since deployment
        $daysPassed = $deployedAt->diffInDays($now);

        // Calculate percentage of lifecycle passed
        $percentagePassed = $totalLifecycleDays > 0 ? ($daysPassed / $totalLifecycleDays) * 100 : 0;

        return match(true) {
            $percentagePassed <= 25 => 'Perfect',
            $percentagePassed <= 50 => 'Good',
            $percentagePassed <= 75 => 'Fair',
            default => 'Poor',
        };
    }

    /**
     * Get the lifecycle display string
     */
    public function getLifecycleDisplayAttribute(): string
    {
        return $this->health_lifecycle_value . ' ' . str($this->health_lifecycle_unit)->plural($this->health_lifecycle_value);
    }

    /**
     * Scope to filter by health status
     */
    public function scopeByHealth($query, $health)
    {
        // Since health is calculated, we need to filter in PHP
        // This is a simplified approach - for better performance, consider storing health in DB
        return $query->get()->filter(function ($device) use ($health) {
            return $device->device_health === $health;
        });
    }
}
