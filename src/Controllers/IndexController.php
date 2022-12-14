<?php

namespace KnowThat\Finder\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use KnowThat\Finder\Services\FileService;
use RuntimeException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

class IndexController extends Controller
{
    /**
     * 列表
     * @return Response
     */
    public function index(): Response
    {
        return response()->view($this->viewPrefix . 'index');
    }

    /**
     * 文件目录
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function catalogues(Request $request): JsonResponse
    {
        $path = $request->input('path', '');
        try {
            $finder = new Finder();
            $finder->depth(0)
                ->ignoreDotFiles(false)
                ->ignoreUnreadableDirs()
                ->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
                    try {
                        $typeA = $a->getType();
                        $typeB = $b->getType();
                    } catch (RuntimeException $e) {
                        $typeA = null;
                        $typeB = null;
                    }
                    $strCaseCmp = strcasecmp($typeA, $typeB);
                    if ($strCaseCmp === 0) {
                        return strcasecmp($a->getFilename(), $b->getFilename());
                    }
                    return $strCaseCmp;
                })
                ->in($this->base . $path);
            if ($finder->hasResults()) {
                $data = (new FileService)->get($finder);
            } else {
                $data = [];
            }
        } catch (DirectoryNotFoundException $e) {
            $data =  [];
        }

        return response()->json($data);
    }

    /**
     * 文件内容
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function contents(Request $request): JsonResponse
    {
        $path = $request->input('path');
        $name = $request->input('name');
        try {
            $finder = new Finder();
            $finder->depth(0)->files()->name($name)->ignoreDotFiles(false)->in($this->base . $path);
            $file = (new FileService)->find($finder)->generateContentItems();
            $data = $file->getIterator();
        } catch (DirectoryNotFoundException $e) {
            $data = Collection::make();
        }

        return response()->json($data);
    }
}
