<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Services;

use Illuminate\Support\Facades\Cache;
use ItDelmax\LaravelEfaktura\Contracts\EfakturaClientInterface;
use ItDelmax\LaravelEfaktura\DTO\CompanyAccountOnEfakturaDto;
use ItDelmax\LaravelEfaktura\DTO\EfakturaVersionDto;
use ItDelmax\LaravelEfaktura\DTO\MiniCompanyDto;
use ItDelmax\LaravelEfaktura\Exceptions\EfakturaException;

class PublicApiService
{
    public function __construct(
        protected EfakturaClientInterface $client,
        protected array $cacheConfig = []
    ) {}

    /**
     * Get eFaktura version
     *
     * @throws EfakturaException
     */
    public function getVersion(): EfakturaVersionDto
    {
        $response = $this->client->get('/api/publicApi/getEfakturaVersion');

        return EfakturaVersionDto::fromArray($response);
    }

    /**
     * Get unit of measures (cached)
     *
     * @throws EfakturaException
     */
    public function getUnitMeasures(): array
    {
        $cacheKey = ($this->cacheConfig['prefix'] ?? 'efaktura_') . 'unit_measures';
        $ttl = $this->cacheConfig['ttl']['unit_measures'] ?? 86400;

        if ($this->cacheConfig['enabled'] ?? false) {
            return Cache::remember($cacheKey, $ttl, function () {
                return $this->client->get('/api/publicApi/get-unit-measures');
            });
        }

        return $this->client->get('/api/publicApi/get-unit-measures');
    }

    /**
     * Get all registered companies (cached)
     *
     * @return MiniCompanyDto[]
     * @throws EfakturaException
     */
    public function getAllCompanies(bool $includeAllStatuses = false): array
    {
        $cacheKey = ($this->cacheConfig['prefix'] ?? 'efaktura_') . 'companies_' . ($includeAllStatuses ? 'all' : 'active');
        $ttl = $this->cacheConfig['ttl']['companies'] ?? 86400;

        if ($this->cacheConfig['enabled'] ?? false) {
            return Cache::remember($cacheKey, $ttl, function () use ($includeAllStatuses) {
                return $this->fetchAllCompanies($includeAllStatuses);
            });
        }

        return $this->fetchAllCompanies($includeAllStatuses);
    }

    protected function fetchAllCompanies(bool $includeAllStatuses): array
    {
        $response = $this->client->get('/api/publicApi/getAllCompanies', [
            'includeAllStatuses' => $includeAllStatuses ? 'true' : 'false',
        ]);

        return MiniCompanyDto::collection($response);
    }

    /**
     * Download all companies as file
     *
     * @throws EfakturaException
     */
    public function downloadAllCompanies(bool $includeAllStatuses = false): string
    {
        return $this->client->getFile('/api/publicApi/downloadAllCompanies', [
            'includeAllStatuses' => $includeAllStatuses ? 'true' : 'false',
        ]);
    }

    /**
     * Subscribe for the next day to receive invoice status change notifications
     *
     * @throws EfakturaException
     */
    public function subscribe(): string
    {
        $response = $this->client->post('/api/publicApi/subscribe');

        return $response['raw'] ?? '';
    }

    /**
     * Update company information
     *
     * @throws EfakturaException
     */
    public function updateCompany(): array
    {
        return $this->client->put('/api/publicApi/company/update-company');
    }

    /**
     * Check if company has active eFaktura account
     *
     * @throws EfakturaException
     */
    public function checkIfCompanyRegistered(
        ?string $companyId = null,
        ?string $pib = null,
        ?string $mb = null,
        ?string $jbkjs = null
    ): CompanyAccountOnEfakturaDto {
        $data = array_filter([
            'companyId' => $companyId,
            'pib' => $pib,
            'mb' => $mb,
            'jbkjs' => $jbkjs,
        ], fn($value) => $value !== null);

        $response = $this->client->post(
            '/api/publicApi/Company/CheckIfCompanyRegisteredOnEfaktura',
            $data
        );

        return CompanyAccountOnEfakturaDto::fromArray($response);
    }

    /**
     * Check if company is registered by PIB
     *
     * @throws EfakturaException
     */
    public function checkByPib(string $pib): CompanyAccountOnEfakturaDto
    {
        return $this->checkIfCompanyRegistered(pib: $pib);
    }

    /**
     * Check if company is registered by MB (matični broj)
     *
     * @throws EfakturaException
     */
    public function checkByMb(string $mb): CompanyAccountOnEfakturaDto
    {
        return $this->checkIfCompanyRegistered(mb: $mb);
    }

    /**
     * Check if company is registered by JBKJS
     *
     * @throws EfakturaException
     */
    public function checkByJbkjs(string $jbkjs): CompanyAccountOnEfakturaDto
    {
        return $this->checkIfCompanyRegistered(jbkjs: $jbkjs);
    }

    /**
     * Find company by PIB from cached list
     *
     * @throws EfakturaException
     */
    public function findCompanyByPib(string $pib): ?MiniCompanyDto
    {
        $companies = $this->getAllCompanies();

        foreach ($companies as $company) {
            if ($company->pib === $pib) {
                return $company;
            }
        }

        return null;
    }

    /**
     * Clear cached data
     */
    public function clearCache(): void
    {
        $prefix = $this->cacheConfig['prefix'] ?? 'efaktura_';

        Cache::forget($prefix . 'companies_all');
        Cache::forget($prefix . 'companies_active');
        Cache::forget($prefix . 'unit_measures');
        Cache::forget($prefix . 'vat_exemptions');
    }
}
