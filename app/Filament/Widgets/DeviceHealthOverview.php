<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeviceHealthOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $devices = Device::all();

        $healthCounts = [
            'Perfect' => 0,
            'Good' => 0,
            'Fair' => 0,
            'Poor' => 0,
            'N/A' => 0,
        ];

        foreach ($devices as $device) {
            $health = $device->device_health;
            $healthCounts[$health]++;
        }

        return [
            Stat::make('Perfect Health', $healthCounts['Perfect'])
                ->description('Up to 25% of lifecycle')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Good Health', $healthCounts['Good'])
                ->description('26% to 50% of lifecycle')
                ->descriptionIcon('heroicon-m-face-smile')
                ->color('primary'),

            Stat::make('Fair Health', $healthCounts['Fair'])
                ->description('51% to 75% of lifecycle')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Poor Health', $healthCounts['Poor'])
                ->description('76% or more of lifecycle')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
