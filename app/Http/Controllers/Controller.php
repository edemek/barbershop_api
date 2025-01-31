<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class Controller
{
    //
        /**
     * @param $error
     * @param int $code
     * @return JsonResponse
     */
    public function sendError($error, int $code = 200): JsonResponse
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

        /**
     * @param $result
     * @param $message
     * @return JsonResponse
     */
    public function sendResponse($result, $message): JsonResponse
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

        /**
     * Example:
     * only=name;rate;total_reviews;categories.has_media;categories.media;categories.media.url
     * @param Request $request
     * @param Model $model
     */
    protected function filterModel(Request $request, Model $model): void
    {
        $only = $this->getOnlyArray($request);
        $model->setVisible(array_keys($only));
        foreach ($only as $key => $filter) {
            if (is_array($filter) && count($filter) > 0) {
                foreach ($filter as $key1 => $filter1) {
                    if (is_array($filter1) && count($filter1) > 0) {
                        $model->getRelations()[$key]->map(function ($item) use ($key1, $filter1) {
                            $item->getRelations()[$key1]->each->setVisible(array_keys($filter1));
                        });
                    }
                }
                $model->getRelations()[$key]->each->setVisible(array_keys($filter));
            }
        }
    }

        /**
     * @param Request $request
     * @return array
     */
    protected function getOnlyArray(Request $request): array
    {
        $only = [];
        if ($request->has('only')) {
            $only = $request->get('only');
            $only = explode(';', $only);
            $only = $this->buildOnlyTree($only, '.');
        }
        return $only;
    }

    protected function buildOnlyTree($categoryLines, $separator): array
    {
        $catTree = array();
        foreach ($categoryLines as $catLine) {
            $path = explode($separator, $catLine);
            $node = &$catTree;
            foreach ($path as $cat) {
                $cat = trim($cat);
                if (!isset($node[$cat])) {
                    $node[$cat] = array();
                }
                $node = &$node[$cat];
            }
        }
        return $catTree;
    }
}
