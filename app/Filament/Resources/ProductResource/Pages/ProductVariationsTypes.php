<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\ProductVariationsTypesEnum;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class ProductVariationsTypes extends EditRecord
{
    protected static string $resource = ProductResource::class;
    
    protected static ?string $navigationIcon = 'heroicon-s-credit-card';

    protected static ?string $title = "Variation Types";

    public function form(Form $form): Form
    {
        return $form
                ->schema([
                   Repeater::make('variationTypes')
                   ->relationship()
                   ->label(false)
                   ->collapsible()
                   ->defaultItems(1)
                   ->addActionLabel("Add New variation type")
                   ->columns(2)
                   ->columnSpan(2)
                   ->schema(
                    [
                        TextInput::make('name')
                        ->required(),
                        Select::make('type')
                        ->options(ProductVariationsTypesEnum::label())
                        ->required(),
                        Repeater::make('options')
                        ->relationship()
                        ->collapsible()
                        ->schema(
                            [
                                TextInput::make('name')
                                ->required()
                                ->columns(2)
                                ->columnSpan(2),
                                SpatieMediaLibraryFileUpload::make('image')
                                ->image()
                                ->multiple()
                                ->openable()
                                ->panelLayout('grid')
                                ->collection('image')
                                ->reorderable()
                                ->appendFiles()
                                ->preserveFilenames()
                                ->columnSpan(2)


                                
                            ]
                        )->columnSpan(2)
                    ]
                   )
                ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
