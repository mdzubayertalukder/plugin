<?php

namespace Plugin\Saas\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Domain;

class CustomDomain extends Model
{
    protected $table = "tl_saas_custom_domain";

    public function saasAccount(){
        return $this->hasOne(SaasAccount::class,'id','store_id');
    }

    public function domain(){
        return $this->hasOne(Domain::class,'tenant_id','tenant_id');
    }
}
