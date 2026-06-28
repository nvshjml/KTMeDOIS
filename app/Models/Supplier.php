<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier_details';

    protected $primaryKey = 'SUPPLIERID';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'SUPPLIERID',
        'SUPPLIER_COMP_REG_NO',
        'SUPPLIER_COMP_NAME',
        'SUPPLIER_CTC_NO',
        'SUPPLIER_CTC_PERSON',
        'SUPPLIER_EMAIL_ADD',
        'SUPPLIER_EXPIRED_DATE',
        'SUPPLIER_CTC_STATUS',
        'supplier_name',
        'billing_address',
        'vendor_number',
        'contact_person',
        'supplier_phone',
        'supplier_email',
        'password_hash',
        'reset_token_hash',
        'reset_token_created_at',
        'supplier_status',
        'inactive_date',
    ];

    protected $hidden = ['password_hash', 'reset_token_hash'];

    protected $casts = [
        'SUPPLIER_EXPIRED_DATE' => 'date',
        'reset_token_created_at' => 'datetime',
    ];

    private const PROFILE_DEFAULTS = [
        'V001' => [
            'company_registration_no' => '202001001001',
            'billing_address' => 'No. 12, Jalan Teknologi, 63000 Cyberjaya, Selangor',
        ],
        'V002' => [
            'company_registration_no' => '201902002002',
            'billing_address' => 'Lot 55, Jalan Perusahaan, 41000 Klang, Selangor',
        ],
        'V003' => [
            'company_registration_no' => '201705005005',
            'billing_address' => 'Block C, Jalan Industri, 81200 Johor Bahru, Johor',
        ],
        'V004' => [
            'company_registration_no' => '202103003003',
            'billing_address' => 'No. 7, Jalan Ampang Hilir, 55000 Kuala Lumpur',
        ],
        'V005' => [
            'company_registration_no' => '201804004004',
            'billing_address' => 'Lot 18, Kawasan Perindustrian Prai, 13600 Perai, Pulau Pinang',
        ],
    ];

    public function getConnectionName()
    {
        return app()->environment('testing') ? config('database.default') : 'supplier';
    }

    public function isActive(): bool
    {
        return strtolower((string) $this->supplier_status) === 'active';
    }

    public function getSupplierIdAttribute(): ?string
    {
        return $this->attributes['SUPPLIERID'] ?? null;
    }

    public function setSupplierIdAttribute(?string $value): void
    {
        $this->attributes['SUPPLIERID'] = $value;
    }

    public function getVendorNumberAttribute(): ?string
    {
        return $this->attributes['SUPPLIERID'] ?? null;
    }

    public function setVendorNumberAttribute(?string $value): void
    {
        $this->attributes['SUPPLIERID'] = $value;
    }

    public function getSupplierNameAttribute(): ?string
    {
        return $this->attributes['SUPPLIER_COMP_NAME'] ?? null;
    }

    public function setSupplierNameAttribute(?string $value): void
    {
        $this->attributes['SUPPLIER_COMP_NAME'] = $value;
    }

    public function getBillingAddressAttribute(): ?string
    {
        return $this->attributes['SUPPLIER_COMP_REG_NO'] ?? null;
    }

    public function setBillingAddressAttribute(?string $value): void
    {
        $this->attributes['SUPPLIER_COMP_REG_NO'] = $value;
    }

    public function getContactPersonAttribute(): ?string
    {
        return $this->attributes['SUPPLIER_CTC_PERSON'] ?? null;
    }

    public function setContactPersonAttribute(?string $value): void
    {
        $this->attributes['SUPPLIER_CTC_PERSON'] = $value;
    }

    public function getSupplierPhoneAttribute(): ?string
    {
        return $this->attributes['SUPPLIER_CTC_NO'] ?? null;
    }

    public function setSupplierPhoneAttribute(?string $value): void
    {
        $this->attributes['SUPPLIER_CTC_NO'] = $value;
    }

    public function getSupplierEmailAttribute(): ?string
    {
        return $this->attributes['SUPPLIER_EMAIL_ADD'] ?? null;
    }

    public function setSupplierEmailAttribute(?string $value): void
    {
        $this->attributes['SUPPLIER_EMAIL_ADD'] = $value;
    }

    public function getSupplierStatusAttribute(): ?string
    {
        return $this->attributes['SUPPLIER_CTC_STATUS'] ?? null;
    }

    public function getCompanyRegistrationNoAttribute(): ?string
    {
        return $this->attributes['SUPPLIER_COMP_REG_NO']
            ?? $this->defaultProfileValue('company_registration_no');
    }

    public function getDisplayBillingAddressAttribute(): ?string
    {
        return $this->attributes['billing_address']
            ?? $this->defaultProfileValue('billing_address');
    }

    public function setSupplierStatusAttribute(?string $value): void
    {
        $this->attributes['SUPPLIER_CTC_STATUS'] = $value;
    }

    public function getInactiveDateAttribute()
    {
        return $this->SUPPLIER_EXPIRED_DATE;
    }

    public function setInactiveDateAttribute($value): void
    {
        $this->attributes['SUPPLIER_EXPIRED_DATE'] = $value;
    }

    public function getNameAttribute(): ?string
    {
        return $this->supplier_name;
    }

    public function getUsernameAttribute(): ?string
    {
        return $this->vendor_number;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->supplier_email;
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'supplier_id', 'SUPPLIERID');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'supplier_id', 'SUPPLIERID');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'supplier_id', 'SUPPLIERID');
    }

    private function defaultProfileValue(string $key): ?string
    {
        return self::PROFILE_DEFAULTS[$this->vendor_number][$key] ?? null;
    }
}
