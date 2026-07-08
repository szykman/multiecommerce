<?php

namespace App\Services;

use App\Models\Store;

class TenantManager
{
    protected ?Store $store = null;

    public function setStore(Store $store): void
    {
        $this->store = $store;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function id(): ?int
    {
        return $this->store?->id;
    }
}

