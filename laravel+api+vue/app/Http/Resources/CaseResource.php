<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $cards = $this->cards->isEmpty() ? null : new CardsResource($this->cards);
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'client_email' => $this->client_email,
            'website'      => $this->website,
            'country'      => $this->country,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'user'         => new UserResource($this->user),
            'cards'        => $cards,
        ];
    }
}
