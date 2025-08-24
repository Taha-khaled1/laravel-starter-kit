<?php

namespace App\Http\Resources;

use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    { 
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'email'                => $this->email,
            'phone'                => $this->phone,
            'status'               => $this->status,
            'type'                 => $this->type,
            'image'                => $this->image ? asset($this->image) : null,
            'email_verified_at'    => $this->email_verified_at,
            'fcm'                  => $this->fcm,
            'device_id'            => $this->device_id,          
            'country' => $this->country ?
                [
                    'id' => $this->country->id,
                    'name_ar' => $this->country->name_ar,
                    'name_en' => $this->country->name_en,
                    'code' => $this->country->code,
                    'symbol_ar' => $this->country->symbol_ar,
                    'symbol_en' => $this->country->symbol_en,
                ]  : null,


            'city' => $this->city ?
                [
                    'id' => $this->city->id,
                    'name_ar' => $this->city->name_ar,
                    'name_en' => $this->city->name_en,
                ] : null,

        ];
    }
}
