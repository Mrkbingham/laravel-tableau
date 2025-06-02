<?php

use InterWorks\Tableau\TableauAPI;
use InterWorks\Tableau\Tests\Mocks\TableauMock;

describe('APIResourcesTest', function () {

    beforeEach(function () {
        TableauMock::mockAuthentication();
        $this->api = new TableauAPI();
        $this->api->authenticateWithPAT('test-pat-token', 'test-content-url');
    });

    describe('Views API Mocking', function () {
        it('successfully mocks views list operations', function () {
            TableauMock::mockViewsOperations();

            $views = $this->api->getViews();

            expect($views)->toBeArray();
            expect($views['views']['view'])->toHaveCount(3);
            expect($views['views']['view'][0]['name'])->toBe('Sales Dashboard');
            expect($views['views']['view'][1]['name'])->toBe('Marketing Report');
            expect($views['views']['view'][2]['name'])->toBe('Financial Overview');
        });

        it('successfully mocks individual view operations', function () {
            TableauMock::mockViewsOperations();

            $view = $this->api->getView('view-1-id');

            expect($view['view']['id'])->toBe('view-1-id');
            expect($view['view']['name'])->toBe('Sales Dashboard');
            expect($view['view']['contentUrl'])->toBe('sales-dashboard');
        });

        it('handles view not found scenarios', function () {
            TableauMock::mockViewsOperations();

            expect(fn() => $this->api->getView('non-existent-view'))
                ->toThrow(\InterWorks\Tableau\Exceptions\APIException::class);
        });

        it('mocks view export functionality', function () {
            TableauMock::mockViewExports();

            $imageContent = $this->api->getViewImage('view-1-id');
            expect($imageContent)->toBe('mock-view-image-content');

            $pdfContent = $this->api->getViewPDF('view-1-id');
            expect($pdfContent)->toBe('mock-view-pdf-content');
        });
    });

    describe('Datasources API Mocking', function () {
        it('successfully mocks datasources list operations', function () {
            TableauMock::mockDatasourcesOperations();

            $datasources = $this->api->getDatasources();

            expect($datasources)->toBeArray();
            expect($datasources['datasources']['datasource'])->toHaveCount(2);
            expect($datasources['datasources']['datasource'][0]['name'])->toBe('Sample Datasource 1');
            expect($datasources['datasources']['datasource'][0]['type'])->toBe('hyper');
            expect($datasources['datasources']['datasource'][1]['type'])->toBe('sqlserver');
        });

        it('successfully mocks individual datasource operations', function () {
            TableauMock::mockDatasourcesOperations();

            $datasource = $this->api->getDatasource('datasource-1-id');

            expect($datasource['datasource']['id'])->toBe('datasource-1-id');
            expect($datasource['datasource']['name'])->toBe('Sample Datasource 1');
            expect($datasource['datasource']['type'])->toBe('hyper');
        });

        it('handles datasource not found scenarios', function () {
            TableauMock::mockDatasourcesOperations();

            expect(fn() => $this->api->getDatasource('non-existent-datasource'))
                ->toThrow(\InterWorks\Tableau\Exceptions\APIException::class);
        });

        it('mocks datasource download functionality', function () {
            TableauMock::mockDatasourcesOperations();

            $content = $this->api->downloadDatasource('datasource-1-id');
            expect($content)->toBe('mock-datasource-content');
        });

        it('mocks datasource deletion', function () {
            TableauMock::mockDatasourcesOperations();

            // Should not throw an exception
            $result = $this->api->deleteDatasource('datasource-1-id');
            expect($result)->toBeTrue();
        });
    });

    describe('Users API Mocking', function () {
        it('successfully mocks users list operations', function () {
            TableauMock::mockUsersOperations();

            $users = $this->api->getUsers();

            expect($users)->toBeArray();
            expect($users['users']['user'])->toHaveCount(3);
            expect($users['users']['user'][0]['name'])->toBe('jane.doe@example.com');
            expect($users['users']['user'][0]['siteRole'])->toBe('Viewer');
            expect($users['users']['user'][1]['siteRole'])->toBe('Creator');
            expect($users['users']['user'][2]['siteRole'])->toBe('SiteAdministrator');
        });

        it('successfully mocks individual user operations', function () {
            TableauMock::mockUsersOperations();

            $user = $this->api->getUser('user-1-id');

            expect($user['user']['id'])->toBe('user-1-id');
            expect($user['user']['name'])->toBe('jane.doe@example.com');
            expect($user['user']['siteRole'])->toBe('Viewer');
        });

        it('handles user not found scenarios', function () {
            TableauMock::mockUsersOperations();

            expect(fn() => $this->api->getUser('non-existent-user'))
                ->toThrow(\InterWorks\Tableau\Exceptions\APIException::class);
        });

        it('mocks user update functionality', function () {
            TableauMock::mockUsersOperations();

            $updatedUser = $this->api->updateUser('user-1-id', ['siteRole' => 'Creator']);
            expect($updatedUser['user']['id'])->toBe('user-1-id');
        });

        it('mocks user deletion', function () {
            TableauMock::mockUsersOperations();

            // Should not throw an exception
            $result = $this->api->deleteUser('user-1-id');
            expect($result)->toBeTrue();
        });
    });

    describe('Projects API Mocking', function () {
        it('successfully mocks projects list operations', function () {
            TableauMock::mockProjectsOperations();

            $projects = $this->api->getProjects();

            expect($projects)->toBeArray();
            expect($projects['projects']['project'])->toHaveCount(2);
            expect($projects['projects']['project'][0]['name'])->toBe('Test Project');
            expect($projects['projects']['project'][0]['contentPermissions'])->toBe('ManagedByOwner');
            expect($projects['projects']['project'][1]['name'])->toBe('Production Project');
        });

        it('successfully mocks individual project operations', function () {
            TableauMock::mockProjectsOperations();

            $project = $this->api->getProject('87654321-4321-4321-4321-210987654321');

            expect($project['project']['id'])->toBe('87654321-4321-4321-4321-210987654321');
            expect($project['project']['name'])->toBe('Test Project');
            expect($project['project']['description'])->toBe('A test project for mocking');
        });

        it('handles project not found scenarios', function () {
            TableauMock::mockProjectsOperations();

            expect(fn() => $this->api->getProject('non-existent-project'))
                ->toThrow(\InterWorks\Tableau\Exceptions\APIException::class);
        });

        it('mocks project update functionality', function () {
            TableauMock::mockProjectsOperations();

            $updatedProject = $this->api->updateProject('87654321-4321-4321-4321-210987654321', [
                'name' => 'Updated Test Project'
            ]);
            expect($updatedProject['project']['id'])->toBe('87654321-4321-4321-4321-210987654321');
        });

        it('mocks project deletion', function () {
            TableauMock::mockProjectsOperations();

            // Should not throw an exception
            $result = $this->api->deleteProject('87654321-4321-4321-4321-210987654321');
            expect($result)->toBeTrue();
        });
    });

    describe('Comprehensive Resource Mocking', function () {
        it('mocks all resources together', function () {
            TableauMock::mockAllResources();

            // Test that all resources work together
            $workbooks = $this->api->getWorkbooks();
            expect($workbooks)->toBeArray();

            $views = $this->api->getViews();
            expect($views)->toBeArray();

            $datasources = $this->api->getDatasources();
            expect($datasources)->toBeArray();

            $users = $this->api->getUsers();
            expect($users)->toBeArray();

            $projects = $this->api->getProjects();
            expect($projects)->toBeArray();

            $serverInfo = $this->api->getServerInfo();
            expect($serverInfo)->toBeArray();
        });

        it('maintains consistent data relationships', function () {
            TableauMock::mockAllResources();

            $workbooks = $this->api->getWorkbooks();
            $views = $this->api->getViews();

            // Verify that views reference valid workbook IDs
            $workbookIds = array_map(
                fn($wb) => $wb['id'], 
                $workbooks['workbooks']['workbook']
            );

            foreach ($views['views']['view'] as $view) {
                expect($workbookIds)->toContain($view['workbook']['id']);
            }
        });
    });
});
