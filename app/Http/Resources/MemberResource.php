<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Calculate;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'userid' => $this->id,
            'total_unit_per_uid' => Calculate::roundDown($this->pivot->unit, 4),
            'total_amountrupiah_per_uid' => Calculate::roundDown($this->pivot->unit * $request->nab, 2)
        ];
    }
}
