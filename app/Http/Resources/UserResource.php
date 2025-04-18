<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'              => $this->name,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password'          => $this->password,
            'remember_token'    => $this->remember_token,
        ];
    }
}
