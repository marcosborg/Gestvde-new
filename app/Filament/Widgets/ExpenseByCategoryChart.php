<?php

namespace App\Filament\Widgets;

use App\Models\VehicleExpense;
use Filament\Widgets\ChartWidget;

class ExpenseByCategoryChart extends ChartWidget
{
    protected ?string $heading = null;

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $totals = VehicleExpense::query()
            ->leftJoin('expense_categories', 'vehicle_expenses.expense_category_id', '=', 'expense_categories.id')
            ->whereBetween('expense_date', [$start, $end])
            ->selectRaw('COALESCE(expense_categories.name, vehicle_expenses.category) as category_name, SUM(vehicle_expenses.amount) as total')
            ->groupBy('category_name')
            ->orderBy('category_name')
            ->get();

        $labels = [];
        $data = [];

        foreach ($totals as $row) {
            $labels[] = $row->category_name ?? __('admin.expense_categories.other');
            $data[] = (float) $row->total;
        }

        return [
            'datasets' => [
                [
                    'label' => __('admin.labels.value'),
                    'data' => $data,
                    'backgroundColor' => 'rgba(234, 179, 8, 0.6)',
                    'borderColor' => 'rgba(234, 179, 8, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getHeading(): ?string
    {
        return __('admin.headings.expense_by_category');
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
