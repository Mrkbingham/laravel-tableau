<?php

namespace InterWorks\Tableau\Data\ConnectedApps;

use Illuminate\Support\Collection;

class ConnectedAppSecretsCollection
{
    /**
     * The constructor for the ConnectedAppSecretsCollection data transfer object.
     *
     * @param Collection $secrets The collection of connected app secrets.
     *
     * @return void
     */
    public function __construct(
        public readonly Collection $secrets,
    ) {

    }

    /**
     * Create a ConnectedApplicationCollection from an array
     *
     * @param array $data The array to parse.
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $secrets = [];

        if (isset($data['secret'])) {
            $secretData = $data['secret'];

            // Handle single secret vs array of secrets
            if (isset($secretData['name'])) {
                // Single secret
                $secrets[] = ConnectedAppSecret::fromArray($secretData);
            } else {
                // Array of secrets
                foreach ($secretData as $secret) {
                    $secrets[] = ConnectedAppSecret::fromArray($secret);
                }
            }
        }

        return new self(
            secrets: collect($secrets),
        );
    }

    /**
     * Get all secrets in the collection.
     *
     * @return Collection<ConnectedAppSecret>
     */
    public function all(): Collection
    {
        return $this->secrets;
    }
}
