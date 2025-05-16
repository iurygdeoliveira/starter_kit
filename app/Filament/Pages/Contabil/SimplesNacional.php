<?php

declare(strict_types = 1);

namespace App\Filament\Pages\Contabil;

use App\Enums\Periodicity;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SimplesNacional extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.contabil.simples-nacional';

    #[\Override]
    public function getBreadcrumbs(): array
    {
        return [
            '/'              => 'Contábil',
            url()->current() => 'Simples Nacional',
        ];
    }

    public function table(Table $table): Table
    {
        $currentDate    = Carbon::now();
        $currentMonth   = $currentDate->month;
        $currentYear    = $currentDate->year;
        $currentQuarter = ceil($currentMonth / 3);

        return $table
            ->query(
                Task::query()
                    ->where(function (Builder $query) use ($currentMonth, $currentYear, $currentQuarter): void {
                        // Tarefas mensais - mostrar no mês atual
                        $query->where('periodicity', Periodicity::Mensal->value)
                            ->whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear);

                        // Tarefas trimestrais - mostrar no trimestre atual
                        $query->orWhere(function (Builder $subQuery) use ($currentQuarter, $currentYear): void {
                            $startMonth = ($currentQuarter - 1) * 3 + 1;
                            $endMonth   = $currentQuarter * 3;

                            $subQuery->where('periodicity', Periodicity::Trimestral->value)
                                ->whereMonth('created_at', '>=', $startMonth)
                                ->whereMonth('created_at', '<=', $endMonth)
                                ->whereYear('created_at', $currentYear);
                        });

                        // Tarefas anuais - mostrar uma vez por ano
                        $query->orWhere(function (Builder $subQuery) use ($currentYear): void {
                            $subQuery->where('periodicity', Periodicity::Anual->value)
                                ->whereYear('created_at', $currentYear);
                        });
                    })
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Tarefa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('periodicity')
                    ->label('Periodicidade')
                    ->badge()
                    ->color(fn (Periodicity $state): string => match ($state) {
                        Periodicity::Mensal     => 'info',
                        Periodicity::Trimestral => 'warning',
                        Periodicity::Anual      => 'success',
                    }),
                TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->date('d/m/Y')
                    ->sortable(),
            ]);
    }
}
