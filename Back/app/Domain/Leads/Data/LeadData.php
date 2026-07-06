<?php

namespace App\Domain\Leads\Data;

final readonly class LeadData
{
    public function __construct(
        public ?string $name,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $company,
        public ?string $phone,
        public ?string $email,
        public ?string $service,
        public ?string $serviceSlug,
        public ?string $projectSize,
        public ?string $propertyType,
        public array $details,
        public ?string $message,
        public string $source,
        public string $ipHash,
        public ?string $userAgent,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'company' => $this->company,
            'phone' => $this->phone,
            'email' => $this->email,
            'service' => $this->service,
            'service_slug' => $this->serviceSlug,
            'project_size' => $this->projectSize,
            'property_type' => $this->propertyType,
            'details' => $this->details,
            'message' => $this->message,
            'source' => $this->source,
            'ip_hash' => $this->ipHash,
            'user_agent' => $this->userAgent,
            'privacy_accepted_at' => now(),
        ];
    }
}
