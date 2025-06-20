<?php

namespace InterWorks\Tableau\Data\ConnectedApps;

use Illuminate\Support\Collection;

class ConnectedAppsCollection
{
    /**
     * The constructor for the ConnectedAppsCollection data transfer object.
     *
     * @param Collection $connectedApps The collection of connected applications.
     *
     * @return void
     */
    public function __construct(
        public readonly Collection $connectedApps,
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
        $applications = [];

        if (isset($data['connectedApplications']['connectedApplication'])) {
            $appData = $data['connectedApplications']['connectedApplication'];

            // Handle single application vs array of applications
            if (isset($appData['name'])) {
                // Single application
                $applications[] = ConnectedApp::fromArray($appData);
            } else {
                // Array of applications
                foreach ($appData as $app) {
                    $applications[] = ConnectedApp::fromArray($app);
                }
            }
        }

        return new self(
            connectedApps: collect($applications),
        );
    }

    /**
     * Get all connected applications
     *
     * @return Collection<ConnectedApp>
     */
    public function all(): Collection
    {
        return $this->connectedApps;
    }

    /**
     * Get enabled connected applications only
     *
     * @return Collection<ConnectedApp>
     */
    public function enabled(): Collection
    {
        return $this->connectedApps->filter(fn($app) => $app->enabled);
    }

    /**
     * Get disabled connected applications only
     *
     * @return Collection<ConnectedApp>
     */
    public function disabled(): Collection
    {
        return $this->connectedApps->filter(fn($app) => !$app->enabled);
    }
}
