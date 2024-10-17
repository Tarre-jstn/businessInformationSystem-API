<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class BusinessNameChanged extends Mailable
{
    public $oldName;
    public $newName;

    /**
     * Create a new message instance.
     *
     * @param string $oldName
     * @param string $newName
     */
    public function __construct($oldName, $newName)
    {
        $this->oldName = $oldName;
        $this->newName = $newName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Business Name Change Notification')
                    ->view('emails.business-name-changed')
                    ->with([
                        'oldName' => $this->oldName,
                        'newName' => $this->newName,
                    ]);
    }
}
