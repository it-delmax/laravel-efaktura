<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class AcceptRejectResponseDto extends BaseDTO
{
    public ?ChangeStatusInvoiceResponseDto $invoice = null;
    public ?bool $success = null;
    public ?string $message = null;
    public ?string $httpStatus = null;

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (isset($data['invoice']) && is_array($data['invoice'])) {
            $this->invoice = ChangeStatusInvoiceResponseDto::fromArray($data['invoice']);
        }
    }
}
