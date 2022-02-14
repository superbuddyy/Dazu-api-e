<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use DateTime;
use Google_Client;
use Google_Service_Analytics;
use Illuminate\Support\Arr;

class GoogleAnalytics
{
    /**
     * @var Google_Service_Analytics
     */
    private $analytics;

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        $client = new Google_Client();
        $client->setApplicationName("Hello Analytics Reporting");
        $client->setAuthConfig(__dir__ . '/key.json');
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $this->analytics = new Google_Service_Analytics($client);
    }

    public function getPageViews(string $fromDate, string $toDate, string $page)
    {
        $result = $this->analytics->data_ga->get(
            'ga:236974596',
            $fromDate,
            $toDate,
            'ga:pageviews',
            ['filters' => 'ga:pagePath==' . $page, 'dimensions' => 'ga:date']
        );

        $dates = array_map(function ($date) {
           return DateTime::createFromFormat('Ymd', $date[0])->format('Y-m-d');
        }, $result->getRows());

        $data = array_map(function ($data) {
            return $data[1];
        }, $result->getRows());

        return [$dates, $data];
    }
}
