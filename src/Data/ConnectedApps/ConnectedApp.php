<?php

namespace InterWorks\Tableau\Data\ConnectedApps;

use Carbon\Carbon;

class ConnectedApp
{
    /**
     * The constructor for the ConnectedApplication data transfer object.
     *
     * @param string                             $name                  The name of the connected application.
     * @param boolean                            $enabled               Whether the connected application is enabled.
     * @param string                             $clientId              The client ID of the connected application.
     * @param array<string>                      $projectIds            Array of project IDs this app has access to.
     * @param Carbon                             $createdAt             When the connected application was created.
     * @param ConnectedAppSecretsCollection|null $secrets               The secrets information for the connected application.
     * @param string|null                        $domainSafelist        The domain safelist for the connected application.
     * @param boolean|null                       $unrestrictedEmbedding Whether unrestricted embedding is allowed.
     */
    public function __construct(
        public readonly string $name,
        public readonly bool $enabled,
        public readonly string $clientId,
        public readonly array $projectIds,
        public readonly Carbon $createdAt,
        public readonly ?ConnectedAppSecretsCollection $secrets = null,
        public readonly ?string $domainSafelist = null,
        public readonly ?bool $unrestrictedEmbedding = null,
    ) {
        //
    }

    /**
     * Create a ConnectedApplication from XML data
     *
     * @param array $data The XML data as an associative array.
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $projectIds = [];
        if (isset($data['projectIds']['projectId'])) {
            $projectIds = is_array($data['projectIds']['projectId'])
                ? $data['projectIds']['projectId']
                : [$data['projectIds']['projectId']];
        }

        return new self(
            name: $data['name'],
            enabled: filter_var($data['enabled'], FILTER_VALIDATE_BOOLEAN),
            clientId: $data['clientId'],
            projectIds: $projectIds,
            createdAt: Carbon::parse($data['createdAt']),
            domainSafelist: $data['domainSafelist'] ?? null,
            unrestrictedEmbedding: isset($data['unrestrictedEmbedding'])
                ? filter_var($data['unrestrictedEmbedding'], FILTER_VALIDATE_BOOLEAN)
                : null,
            secrets: isset($data['secret']) ? ConnectedAppSecretsCollection::fromArray(['secret' => $data['secret']]) : null,
        );
    }
}
