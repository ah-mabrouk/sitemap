<?php

namespace SolutionPlus\Sitemap\Http\Controllers\Website;

use Illuminate\Support\Facades\Storage;
use SolutionPlus\Sitemap\Http\Controllers\Controller;
use SolutionPlus\Sitemap\Helpers\SitemapHelperFunctions;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $filePath = SitemapHelperFunctions::getSitemapFilePath();

        $xml = Storage::disk('public')->get($filePath);

        return response($xml);
    }
}
