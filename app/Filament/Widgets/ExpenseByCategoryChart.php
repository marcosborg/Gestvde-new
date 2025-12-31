<?php

namespace App\Filament\Widgets;

use App\Models\VehicleExpense;
use Filament\Widgets\ChartWidget;

class ExpenseByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Despesa por Categoria';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $categories = [
            'fuel' => 'Combustivel',
            'tolls' => 'Portagens',
            'insurance' => 'Seguro',
            'maintenance' => 'Manutencao',
            'inspection' => 'Inspecao',
            'taxes' => 'Impostos',
            'parking' => 'Estacionamento',
            'fines' => 'Multas',
            'depreciation' => 'Depreciacao',
            'other' => 'Outro',
        ];

        $totals = VehicleExpense::query()
            ->whereBetween('expense_date', [$start, $end])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->all();

        $labels = [];
        $data = [];

        foreach ($categories as $key => $label) {
            $labels[] = $label;
            $data[] = (float) ($totals[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Valor',
                    'data' => $data,
                    'backgroundColor' => 'rgba(234, 179, 8, 0.6)',
                    'borderColor' => 'rgba(234, 179, 8, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
