<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\ProductVariationsTypesEnum;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class ProductVariations extends EditRecord
{
    protected static string $resource = ProductResource::class;
    
    protected static ?string $navigationIcon = 'heroicon-s-document';

    protected static ?string $title = "Variation";
    

    public function form(Form $form): Form
    {
        $types = $this->record->variationTypes;
        $fields = [];
        foreach ($types as $i => $type) {
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.id')
                ->hidden(); // This should remain hidden
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.name') // Remove space after '.name'
                ->label($type->name); // Displaying the name of the variation type as the label
        }
        

        return $form
            ->schema([
                Repeater::make('variations')
                ->label(false)
                    ->collapsible()
                    ->defaultItems(1)
                    ->schema([
                        Section::make()
                            ->schema($fields)
                            ->columns(3),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric(),
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                    ])
                    ->columns(2)
                    ->columnSpan(2)
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure variations are not null before calling toArray()
        $variations = $this->record->variations ? $this->record->variations->toArray() : [];
        
        $data['variations'] = $this->mergeCartesianWithExisting(
            $this->record->variationTypes, 
            $variations
        );
        
        return $data;
    }
    

    protected function mergeCartesianWithExisting($variationsTypes, $existingData)
    {
        $defaultQuantity = $this->record->quantity;
        $defaultPrice = $this->record->price;
        $cartesianProduct = $this->cartesianProduct($variationsTypes, $defaultQuantity, $defaultPrice);
        $mergeResult = [];
    
        // Iterate over each combination in the cartesian product
        foreach ($cartesianProduct as $product) {
            // Collect variation option IDs for each combination
            $optionIds = collect($product)
                ->filter(fn($value, $key) => str_starts_with($key, 'variation_type_'))
                ->map(fn($option) => $option['id'])
                ->values()
                ->toArray();
    
            // Look for a matching entry in existing data based on option IDs
            $match = array_filter($existingData, function ($existingOption) use ($optionIds) {
                // Ensure we're comparing the 'variation_type_option_ids' correctly
                return isset($existingOption['variation_type_option_ids']) &&
                    $existingOption['variation_type_option_ids'] === $optionIds;
            });
    
            // If a match exists, use its quantity and price
            if (!empty($match)) {
                $existingEntry = reset($match);  // Get the first matched entry
                $product['id']= $existingEntry['id'];
                $product['quantity'] = $existingEntry['quantity'];
                $product['price'] = $existingEntry['price'];
            } else {
                // If no match, use default quantity and price
                $product['quantity'] = $defaultQuantity;
                $product['price'] = $defaultPrice;
            }
    
            // Add the merged product combination to the result
            $mergeResult[] = $product;
        }
    
        return $mergeResult;
    }
    


private function cartesianProduct($variationsTypes, $defaultQuantity = null, $defaultPrice = null): array
{
    $result = [[]]; // Initialize with an empty array for combinations
    foreach ($variationsTypes as $index => $variationsType) {
        $temp = [];
        foreach ($variationsType->options as $option) {
            foreach ($result as $combination) {
                $newCombination = $combination + [
                    'variation_type_' . ($variationsType->id) => [
                        'id' => $option->id,
                        'name' => $option->name, // Correctly use the option name
                        'label' => $variationsType->name // Use the label from the variation type
                    ],
                ];
                $temp[] = $newCombination;
            }
        }

        $result = $temp;
    }

    // Add default quantity and price after generating combinations
    foreach ($result as &$combination) {
        if (count($combination) === count($variationsTypes)) {
            $combination['quantity'] = $defaultQuantity;
            $combination['price'] = $defaultPrice;
        }
    }

    return $result; // Return the complete list of combinations
}

protected function mutateFormDataBeforeSave(array $data): array
{
    $formatteData = [];
    foreach ($data['variations'] as $option) {
        $variationTypeOptionIds = [];
        foreach ($this->record->variationTypes as $i => $variationsType) {
            $variationTypeOptionIds[] = $option['variation_type_' . $variationsType->id];
        }
        $quantity = $option['quantity'];
        $price = $option['price'];
        $formatteData[] = [
            // 'id' => $option['id'] ?? null,
            'variation_type_option_ids' => $variationTypeOptionIds,
            'quantity' => $quantity,
            'price' => $price,
        ];
    }
    $data['variations'] = $formatteData;
    return $data;
}


protected function handleRecordUpdate(Model $record, array $data): Model
{
    $variations =$data['variations'];
    unset($data['variations']);
    
   
    $variations =  collect($variations)->map(function($variations){
        return [
            // 'id' => $variations['id'],
            'variation_type_option_ids' => json_encode($variations['variation_type_option_ids']),
            'quantity' => $variations['quantity'],
            'price' => $variations['price'],
        ];
    })->toArray();
    $record->variations()->delete();

    $record->variations()->upsert($variations,['id'],
    ['variation_type_option_ids','quantity','price']);
    return $record;
}



    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
