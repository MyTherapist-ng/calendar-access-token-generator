<?php

namespace App;

use Google\Service\Calendar as ServiceCalendar;
use Google_Client;

class Calendar
{
    protected $client;

    public function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName(config('app.name'));
        $client->setScopes([ServiceCalendar::CALENDAR, ServiceCalendar::CALENDAR_EVENTS]);
        $client->setAuthConfig(storage_path('keys/client_secret.json'));
        $client->setAccessType('offline');

        return $client;
    }

    /**
    * Returns an authorized API client.
    * @return Google_Client the authorized client object
    */
    public function oauth()
    {
        $client = $this->getClient();

        // Load previously authorized credentials from a file.
        $credentialsPath = storage_path('keys/client_secret_generated.json');

        if (!file_exists($credentialsPath)) {
            return false;
        }

        $accessToken = json_decode(file_get_contents($credentialsPath), true);
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }

    /**
    * Returns an authorized API client.
    * @return ServiceCalendar the authorized client object
    */
    public static function client()
    {
        if (isset(static::$client)) {
            return static::$client;
        }

        return static::$client = new ServiceCalendar((new static())->oauth());
    }
}
