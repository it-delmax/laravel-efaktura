<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class SimplePurchaseInvoiceDto extends BaseDTO
{
    public ?int $purchaseInvoiceId = null;
    public ?string $invoiceNumber = null;
    public ?string $invoiceTypeCode = null;
    public ?string $invoiceTypeName = null;
    public ?string $issueDate = null;
    public ?string $dueDate = null;
    public ?string $taxPointDate = null;
    public ?string $currencyCode = null;
    public ?string $documentCurrencyCode = null;
    public ?float $currencyExchangeRate = null;
    public ?string $status = null;
    public ?string $cirInvoiceId = null;
    public ?string $cirStatus = null;
    public ?string $note = null;
    public ?string $paymentMeansCode = null;
    public ?string $paymentId = null;
    public ?string $paymentAccountId = null;
    public ?string $paymentAccountName = null;
    public ?string $contractDocumentReference = null;
    public ?string $orderReference = null;
    public ?string $originatorDocumentReference = null;
    public ?string $purchaseOrderReference = null;
    public ?string $salesOrderReference = null;
    public ?string $despatchDocumentReference = null;
    public ?string $receiptDocumentReference = null;
    public ?string $additionalDocumentReference = null;
    public ?string $billingReferenceInvoiceId = null;
    public ?string $billingReferenceIssueDate = null;

    public ?PartyDto $accountingSupplierParty = null;
    public ?PartyDto $accountingCustomerParty = null;
    public ?PartyDto $deliveryParty = null;
    public ?PartyDto $payeeParty = null;

    public ?float $taxExclusiveAmount = null;
    public ?float $taxInclusiveAmount = null;
    public ?float $allowanceTotalAmount = null;
    public ?float $chargeTotalAmount = null;
    public ?float $prepaidAmount = null;
    public ?float $payableRoundingAmount = null;
    public ?float $payableAmount = null;

    /** @var TaxSubtotalDto[] */
    public ?array $taxSubtotals = null;

    /** @var InvoiceLineDto[] */
    public ?array $invoiceLines = null;

    public ?string $creationDate = null;
    public ?string $lastModifiedDate = null;
    public ?string $deliveredDate = null;
    public ?string $seenDate = null;
    public ?string $approvedDate = null;
    public ?string $rejectedDate = null;
    public ?string $cancelledDate = null;
    public ?string $cancelComments = null;
    public ?string $rejectComments = null;
    public ?string $approveComments = null;

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (isset($data['accountingSupplierParty']) && is_array($data['accountingSupplierParty'])) {
            $this->accountingSupplierParty = PartyDto::fromArray($data['accountingSupplierParty']);
        }
        if (isset($data['accountingCustomerParty']) && is_array($data['accountingCustomerParty'])) {
            $this->accountingCustomerParty = PartyDto::fromArray($data['accountingCustomerParty']);
        }
        if (isset($data['deliveryParty']) && is_array($data['deliveryParty'])) {
            $this->deliveryParty = PartyDto::fromArray($data['deliveryParty']);
        }
        if (isset($data['payeeParty']) && is_array($data['payeeParty'])) {
            $this->payeeParty = PartyDto::fromArray($data['payeeParty']);
        }
        if (isset($data['taxSubtotals']) && is_array($data['taxSubtotals'])) {
            $this->taxSubtotals = TaxSubtotalDto::collection($data['taxSubtotals']);
        }
        if (isset($data['invoiceLines']) && is_array($data['invoiceLines'])) {
            $this->invoiceLines = InvoiceLineDto::collection($data['invoiceLines']);
        }
    }
}
