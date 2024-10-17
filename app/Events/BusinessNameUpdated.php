<?php

namespace App\Events;

use App\Models\Business;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusinessNameUpdated
{
    use Dispatchable, SerializesModels;

    public $business; // Store the business model
    public $oldName;
    public $newName;
    public $changes;
    public $oldImage;
    public $ignoreImageChange; // New property to track whether to ignore the image change

    /**
     * Create a new event instance.
     *
     * @param Business $business
     * @param string $oldName
     * @param string $newName
     * @param array $changes
     * @param string|null $oldImage
     * @param bool $ignoreImageChange
     */
    public function __construct(Business $business, $oldName, $newName, $changes, $oldImage = null, $ignoreImageChange = false)
    {
        $this->business = $business; // Store the business model
        $this->oldName = $oldName;
        $this->newName = $newName;
        $this->changes = $changes;
        $this->oldImage = $oldImage;
        $this->ignoreImageChange = $ignoreImageChange; // Set the new property
    }
}
