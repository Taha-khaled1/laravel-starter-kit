<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_two' => $this->phone_two,
            'social_media' => [
                'whatsapp' => $this->whatsapp,
                'snapchat' => $this->snapchat,
                'twitter' => $this->twitter,
                'facebook' => $this->facebook,
                'instagram' => $this->instagram,
                'linkedin' => $this->linkedin,
                'tiktok' => $this->tiktok,
                'youtube' => $this->youtube,
            ],
            'apps' => [
                'google_play' => $this->google_play,
                'app_store' => $this->app_store,
            ],
            'info' => $this->info,
            'logo' => $this->logo,
            'background_image' => $this->background_image,
        ];
    }
}
