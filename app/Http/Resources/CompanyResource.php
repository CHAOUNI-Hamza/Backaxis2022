<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'logo' => $this->logo,
            'photo_carousel' => $this->photo_carousel,
            'description_agency' => $this->description_agency,
            'photo_agency' => $this->photo_agency,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'localisation' => $this->localisation,
            'social' => json_decode($this->social),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
