<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Service\AnalyticsData;
use Google\Client;
use Google\Service\AnalyticsData\DateRange;
use Google\Service\AnalyticsData\Metric;
use Google\Service\AnalyticsData\RunReportRequest;

use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class AnalyticsController extends Controller
{
    protected $analytics;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(app_path('google-analytics/capstone-website.json'));
        $client->addScope(AnalyticsData::ANALYTICS_READONLY); // Use AnalyticsData for scope
        $this->analytics = new AnalyticsData($client); // Use AnalyticsData for instantiation
    }

    public function index(Request $request)
{
    // Get the number of days from the query parameter, defaulting to 7 if not provided
    $days = $request->query('days', 7);

    // Fetch total visitors, page views, and sessions for the specified number of days
    $analyticsData = Analytics::fetchTotalVisitorsAndPageViews(Period::days($days));

    // Calculate total page views and visitors
    $totalPageViews = $analyticsData->sum('screenPageViews');
    $totalVisitors = $analyticsData->sum('activeUsers');

    // Set the date range dynamically
    $propertyId = '460219113';
    $dateRange = new DateRange();
    $dateRange->setStartDate("${days}daysAgo");
    $dateRange->setEndDate("today");


    $metric1 = new Metric();
    $metric1->setName('userEngagementDuration');
    
    $metric2 = new Metric();
    $metric2->setName('activeUsers');
    
    $metric3 = new Metric();
    $metric3->setName('bounceRate');
    
    $metric4 = new Metric();
    $metric4->setName('sessions');
    
    $metric5 = new Metric();
    $metric5->setName('newUsers');
    
    $metric6 = new Metric();
    $metric6->setName('totalUsers');

    $request = new RunReportRequest();
    $request->setDateRanges([$dateRange]);
    $request->setMetrics([$metric1, $metric2, $metric3, $metric4, $metric5, $metric6]);

    try {
        $response = $this->analytics->properties->runReport("properties/$propertyId", $request);

        // Process the response to calculate the necessary data
        $retentionRates = [];
        $totalEngagementTime = 0;
        $totalActiveUsers = 0;
        $bounceRate = 0;
        $totalSessions = 0;
        $newUsers = 0;
        $totalUsers = 0;

        foreach ($response->getRows() as $row) {
            $totalEngagementTime += $row->getMetricValues()[0]->getValue();
            $totalActiveUsers += $row->getMetricValues()[1]->getValue(); 
            $bounceRate = $row->getMetricValues()[2]->getValue();
            $totalSessions = $row->getMetricValues()[3]->getValue();
            $newUsers = $row->getMetricValues()[4]->getValue();
            $totalUsers = $row->getMetricValues()[5]->getValue();

            $retentionRate = $totalUsers > 0 ? (($totalUsers - $newUsers) / $totalUsers) * 100 : 0;
            $retentionRates[] = number_format($retentionRate, 2);
        }

        // Pad the retentionRates array if it has fewer than the required days
        while (count($retentionRates) < $days) {
            $retentionRates[] = 0;
        }

        $averageEngagementTimePerActiveUser = $totalActiveUsers > 0 ? 
            ($totalEngagementTime / $totalActiveUsers) : 0;

        $formattedAverageEngagementTime = floor($averageEngagementTimePerActiveUser / 60) . 'm ' . ($averageEngagementTimePerActiveUser % 60) . 's';
        $formattedBounceRate = number_format($bounceRate, 2);

        return response()->json([
            'analyticsData' => $analyticsData,
            'totalPageViews' => $totalPageViews,
            'totalVisitors' => $totalVisitors,
            'bounceRate' => $formattedBounceRate,
            'averageEngagementTimePerActiveUser' => $formattedAverageEngagementTime,
            'totalSessions' => $totalSessions,
            'retentionRates' => $retentionRates,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}