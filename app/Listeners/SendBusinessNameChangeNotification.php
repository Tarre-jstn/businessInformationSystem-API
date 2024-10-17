<?php

namespace App\Listeners;

use App\Events\BusinessNameUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SendBusinessNameChangeNotification
{
    public function handle(BusinessNameUpdated $event)
{
    $cacheKey = 'business_update_' . $event->business->business_id;
    if (Cache::has($cacheKey)) {
        return;
    }
    Cache::put($cacheKey, true, 10);

    $customers = User::where('user_type', 'customer')->get();
    $businessLogoUrl = asset(Storage::url('business_logos/' . $event->business->business_image));

    foreach ($customers as $customer) {
        $emailBody = "<html><body style='font-family: Roboto, sans-serif; color: #333;'>";
        $emailBody .= "<div style='padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-width: 600px; margin: auto; background-color: #f9f9f9;'>";

        $emailBody .= "<div style='display: flex; align-items: center; margin-bottom: 20px;'>";
        $emailBody .= "<img src='{$businessLogoUrl}' alt='Business Logo' style='width: 80px; height: 80px; border-radius: 50%; margin-right: 20px; object-fit: contain; aspect-ratio: 1/1;'>";
        $emailBody .= "<h2 style='color: #0056b3;'>{$event->newName} Information Updated</h2>";
        $emailBody .= "</div>";

        $emailBody .= "<p style='font-size: 16px;'><strong>{$event->newName}</strong> has updated their business information. Please see the details below:</p>";

        $imageChange = '';

        foreach ($event->changes as $field => $change) {
            if ($field === 'business_Address') {
                $emailBody .= "<p style='font-size: 14px;'><strong>Business Address</strong> changed from <span style='color: #d9534f;'>{$change['old']}</span> to <span style='color: #5cb85c;'>{$change['new']}</span>.</p>";
            } elseif ($field === 'business_image' && !$event->ignoreImageChange) {
                $oldImageUrl = isset($change['old']) ? asset(Storage::url('business_logos/archive/' . $change['old'])) : 'No previous logo';
                $newImageUrl = isset($change['new']) ? asset(Storage::url('business_logos/' . $change['new'])) : 'No new logo';

                if ($oldImageUrl || $newImageUrl) {
                    $imageChange = "<p style='font-size: 14px;'><strong>Logo has been updated:</strong></p>";
                    $imageChange .= "<div style='display: flex; align-items: center;'>";

                    // Old Logo
                    $imageChange .= $oldImageUrl ? "<div style='margin-right: 20px; text-align: center;'><strong>Old Logo</strong><br><img src='{$oldImageUrl}' alt='Old Logo' style='width: 96px; height: 96px; border-radius: 50%; border: 1px solid #ddd; object-fit: cover; max-width: 100%; max-height: 96px;'></div>" : '';

                    // New Logo
                    $imageChange .= $newImageUrl ? "<div style='text-align: center;'><strong>New Logo</strong><br><img src='{$newImageUrl}' alt='New Logo' style='width: 96px; height: 96px; border-radius: 50%; border: 1px solid #ddd; object-fit: cover; max-width: 100%; max-height: 96px;'></div>" : '';

                    $imageChange .= "</div>";
                }
            } else {
                $fieldLabel = ucfirst(str_replace('_', ' ', $field));
                $emailBody .= "<p style='font-size: 14px;'><strong>{$fieldLabel}</strong> changed from <span style='color: #d9534f;'>{$change['old']}</span> to <span style='color: #5cb85c;'>{$change['new']}</span>.</p>";
            }
        }

        if (!empty($imageChange)) {
            $emailBody .= "<hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>";
            $emailBody .= $imageChange;
        }

        $emailBody .= "</div></body></html>";


        try {
            Mail::html($emailBody, function ($message) use ($customer, $event) {
                $message->to($customer->email)
                    ->subject("Business Information Change Notification for {$event->newName}");
            });
        } catch (\Exception $e) {
            \Log::error('Error sending email to ' . $customer->email . ': ' . $e->getMessage());
        }
    }
}

}
