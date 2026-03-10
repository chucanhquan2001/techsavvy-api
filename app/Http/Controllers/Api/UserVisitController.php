<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\UserVisit\Commands\TrackUserVisitCommand;
use App\Application\UserVisit\Usecases\TrackUserVisitUseCase;
use App\Services\RequestInfoExtractor;
use Illuminate\Http\Request;
use App\Enums\HttpStatus;
use App\Helpers\ApiResponse;
use Throwable;

class UserVisitController extends Controller
{
    public function __construct(
        private RequestInfoExtractor $extractor
    ) {}

    /**
     * Track user visit - POST /api/track-visit
     *
     * Server will automatically extract all information from request
     */
    public function track(Request $request, TrackUserVisitUseCase $useCase)
    {
        try {
            // Extract all visitor information from request
            $data = $this->extractor->extract($request);

            // Create command with extracted data
            $command = new TrackUserVisitCommand(
                ipAddress: $data['ip_address'],
                userAgent: $data['user_agent'],
                referer: $data['referer'],
                fullUrl: $data['full_url'],
                deviceType: $data['device_type'],
                browser: $data['browser'],
                browserVersion: $data['browser_version'],
                platform: $data['platform'],
                platformVersion: $data['platform_version'],
                countryCode: null,  // Could add geoip lookup here
                countryName: null,
                fbclid: $data['fbclid'],
                gclid: $data['gclid'],
                utmSource: $data['utm_source'],
                utmMedium: $data['utm_medium'],
                utmCampaign: $data['utm_campaign'],
                utmContent: $data['utm_content'],
                utmTerm: $data['utm_term']
            );

            $userVisit = $useCase->execute($command);

            return ApiResponse::ok(
                $userVisit->toArray(),
                'Visit tracked successfully',
                HttpStatus::CREATED
            );
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to track visit', $e);
        }
    }

    /**
     * Get visit by ID - GET /api/track-visit/{id}
     */
    public function show($id)
    {
        // Could add show functionality here
        return ApiResponse::ok(['id' => $id], 'Visit details');
    }
}
