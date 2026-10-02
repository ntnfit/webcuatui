<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Audit row for one execution of the news auto-poster. */
class NewsRun extends Model
{
    use HasFactory;

    public const STATUS_RUNNING = 'running';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_ABORTED = 'aborted';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function addNote(string $line): void
    {
        $this->notes = trim(($this->notes ? $this->notes."\n" : '').$line);
    }
}
