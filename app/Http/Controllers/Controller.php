<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


abstract class Controller
{
    use ValidatesRequests;
       /**
     * @param $result
     * @param $message
     * @return JsonResponse
     */
    public function sendResponse($result, $message): JsonResponse
    {
        $response = [
            'message' => $message,
            'data' => $result,
        ];
        return response()->json($response);
    }

    /**
     * @param $error
     * @param int $code
     * @return JsonResponse
     */
    public function sendError($error, int $code = 200): JsonResponse
    {
        $err = [ 
            "message" => $error,
            "code"=> $code,
        ] ;
        
        return response()->json($err);
    }

    /**
     * Example:
     * only=name;rate;total_reviews;categories.has_media;categories.media;categories.media.url
     * @param Request $request
     * @param Collection $collection
     */
    protected function filterCollection(Request $request, Collection $collection): void
    {
        $only = $this->getOnlyArray($request);
        $collection->each->setVisible(array_keys($only));
        $collection->map(function ($item) use ($only) {
            foreach ($only as $key => $filter) {
                try {
                    if (is_array($filter) && count($filter) > 0) {
                        foreach ($filter as $key1 => $filter1) {
                            if (is_array($filter1) && count($filter1) > 0) {
                                $item->getRelations()[$key]->map(function ($item) use ($key1, $filter1) {
                                    $item->getRelations()[$key1]->each->setVisible(array_keys($filter1));
                                });
                            }
                        }
                        if (isset($item->getRelations()[$key]) && $item->getRelations()[$key] instanceof Collection) {
                            $item->getRelations()[$key]->each->setVisible(array_keys($filter));
                        } else if (isset($item->getRelations()[$key])) {
                            $item->getRelations()[$key]->setVisible(array_keys($filter));
                        } else if ($item->$key instanceof Model) {
                            $item->$key->setVisible(array_keys($filter));
                        }
                    }
                } catch (Exception) {
                    dd($key);
                }
            }
            return $item;
        });
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
}
