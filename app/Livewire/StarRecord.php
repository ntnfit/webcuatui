<?php

namespace App\Livewire;

use App\Actions\Star;
use App\Actions\Unstar;
use Livewire\Component;

class StarRecord extends Component
{
    public Model $record;

    public $isStarred;

    public $starsCount;

    public function mount()
    {
        $this->isStarred = $this->isStarred();
        $this->starsCount = $this->record->getStarsCount();
    }

    public function isStarred(): bool
    {
        return $this->record->stars()->where('ip', request()->ip())->exists();
    }

    public function toggleStar(): void
    {
        if ($this->isStarred()) {
            app(Unstar::class)($this->record);

            $this->starsCount = $this->record->getStarsCount();
            $this->isStarred = false;

            return;
        }

        app(Star::class)($this->record);

        $this->starsCount = $this->record->getStarsCount();
        $this->isStarred = true;
    }

    public function render()
    {
        return view('livewire.star-record');
    }
}
