<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Posts;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;
use Workbench\App\Filament\Resources\Posts\Pages\CreatePost;
use Workbench\App\Filament\Resources\Posts\Pages\EditPost;
use Workbench\App\Filament\Resources\Posts\Pages\ListPosts;
use Workbench\App\Filament\Resources\Posts\Pages\ViewPost;

/**
 * Exercises every money component against the same Post model the test suite
 * uses, so what you click here is what the tests cover.
 *
 * Post only has two money attributes, so the read-only variants below re-render
 * them under a distinct name via getStateUsing(). Form fields deliberately do
 * not do this: two inputs sharing one state path would fight over the value.
 */
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),

            // Currency fixed on the field, with rules that parse localized input.
            MoneyInput::make('price')
                ->currency('USD')
                ->minValue(100)
                ->maxValue(500000),

            // Currency read from the record's own column, switchable in the UI.
            MoneyInput::make('amount')
                ->currencyColumn('amount_currency')
                ->currencySwitcherEnabled(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title'),
            MoneyEntry::make('price')->currency('USD'),
            MoneyEntry::make('amount')->currencyColumn('amount_currency'),
            MoneyEntry::make('price_no_symbol')
                ->label('Price (no symbol)')
                ->state(fn (Post $record) => $record->price)
                ->currency('USD')
                ->hideCurrencySymbol(),
            MoneyEntry::make('price_short')
                ->label('Price (short)')
                ->state(fn (Post $record) => $record->price)
                ->currency('USD')
                ->short(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->limit(30),
            MoneyColumn::make('price')->currency('USD')->sortable(),
            MoneyColumn::make('amount')->currencyColumn('amount_currency')->sortable(),
            MoneyColumn::make('price_sv')
                ->label('Price (sv_SE)')
                ->getStateUsing(fn (Post $record) => $record->price)
                ->currency('SEK')
                ->locale('sv_SE'),
            MoneyColumn::make('price_short')
                ->label('Price (short)')
                ->getStateUsing(fn (Post $record) => $record->price)
                ->currency('USD')
                ->short(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'view'   => ViewPost::route('/{record}'),
            'edit'   => EditPost::route('/{record}/edit'),
        ];
    }
}
