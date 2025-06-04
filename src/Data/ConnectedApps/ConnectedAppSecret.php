<?php

namespace InterWorks\Tableau\Data\ConnectedApps;

use Carbon\Carbon;

class ConnectedAppSecret
{
    /**
     * The constructor for the ConnectedAppSecret data transfer object.
     *
     * @param string $id        The ID of the secret.
     * @param Carbon $createdAt When the secret was created.
     */
    public function __construct(
        public readonly string $id,
        public readonly Carbon $createdAt,
    ) {
        //
    }

    /**
     * Create a ConnectedAppSecret from XML data
     *
     * @param array $data The XML data as an associative array.
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            createdAt: Carbon::parse($data['createdAt']),
        );
    }
}
