<?php

namespace App\Services;

use App\GoogleProject;
use Google_Service_CloudResourceManager;
use GuzzleHttp\Client;

class GoogleApi
{
    protected $googleProject;
    protected $googleClient;

    public function __construct(GoogleProject $googleProject) {
        $this->googleProject = $googleProject;
        $this->googleClient = new \Google_Client();
        $this->googleClient->setAuthConfig($googleProject->service_account_json);
        $this->googleClient->addScope('https://www.googleapis.com/auth/cloud-platform');
    }

    public function token()
    {
        return $this->googleClient->fetchAccessTokenWithAssertion()['access_token'];
    }

    public function getProject()
    {
        return $this->request('https://cloudresourcemanager.googleapis.com/v1/projects/' . $this->googleProject->project_id);
    }

    protected function request($endpoint, $method = 'GET', $data = [])
    {
        $options = [
            'headers' => [
                'Authorization' => "Bearer {$this->token()}",
            ],
        ];

        if (! empty($data)) {
            $options['json'] = $data;
        }

        $response = (new Client())->request($method, $endpoint, $options);

        return json_decode((string) $response->getBody(), true);
    }
}
