<?php

namespace SolutionPlus\Sitemap\Http\Controllers\Website;

use SolutionPlus\Sitemap\Helpers\GenerateSitemapHelper;
use SolutionPlus\Sitemap\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $filePath = GenerateSitemapHelper::getSitemapFilePath();

        $xml = Storage::disk('public')->get($filePath);

        return response($xml);
    }
}
