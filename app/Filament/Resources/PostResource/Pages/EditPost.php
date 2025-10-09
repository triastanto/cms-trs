<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[On('set-featured-image')]
    public function setFeaturedImage($mediaId): void
    {
        $this->record->setFeaturedImage($mediaId);

        // Refresh the form to show updated badge
        $this->fillForm();
    }
}
