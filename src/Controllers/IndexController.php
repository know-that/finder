<?php

namespace KnowThat\Finder\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use KnowThat\Finder\Services\FileService;
use KnowThat\Finder\ViewTrait;
use RuntimeException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

class IndexController
{
    use ViewTrait;

    /**
     * 文件路径前缀
     * @var string
     */
    readonly string $base;

    /**
     * 文件路径前缀名称
     * @var string
     */
    readonly string $baseName;

    public function __construct()
    {
        $base = config('know-that.finder.base');
        $this->base = $base === '/' ? '' : $base;
        $names = array_filter(explode('/', $this->base));
        $this->baseName = $names[count($names) - 1] ?? '根';
    }

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
                    } catch (RuntimeException) {
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
        } catch (DirectoryNotFoundException) {
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
            $data = (new FileService)->find($finder)->getIterator();
        } catch (DirectoryNotFoundException) {
            $data = Collection::make();
        }

        $contents = $data->get('contents');
        if ($data->get('contents')) {
            preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/', $contents, $headings);
            $contentItems = [];
            foreach ($headings as $heading) {
                foreach ($heading as $headItem) {
                    $level = 'error';
                    preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i', $headItem, $current);
                    $current[2] = $level;
                    if (!empty($current[4])) {
                        $contentItems[] = [
                            'date'      => $current[1],
                            'level'     => $current[2],
                            'type'      => $current[3],
                            'content'   => $current[0],
                        ];
                    }
                }
            }
            $data->put('content_items', $contentItems);
        }

        return response()->json($data);
    }
}
