<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Traits\ApiGlobalFunctions;


class BaseController extends Controller
{
    use ApiGlobalFunctions;

    public function cmsPage($slug)
    {
        try {
            $pages = Page::where(['slug' => $slug, 'status' => '1'])->first();
            if ($pages) {
                return $this->sendResponse($pages, $this->messageDefault('list_found'));
            } else {
                return $this->sendError($this->messageDefault('oops'), '', '200');
            }
        } catch (\Exception $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', '200');
        }
    }

}
