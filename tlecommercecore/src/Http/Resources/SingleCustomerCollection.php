<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleCustomerCollection extends JsonResource
{

    public function toArray($request)
    {
        return [
            'image' => getFilePath($this->image, false),
            'name' => $this->name,
            'email' => $this->email,
            'id' => $this->id,
            'uid' => $this->uid,
            'phone_with_code' => $this->phone_code . '' . $this->phone,
            'phone_code' => $this->phone_code,
            'phone' => $this->phone,
            'verified_at' => $this->verified_at
        ];
    }
}
