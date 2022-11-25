<?php

namespace KnowThat\Finder\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use KnowThat\Finder\Enums\FileTypeEnum;
use KnowThat\Finder\Services\FileService;
use KnowThat\Finder\ViewTrait;
use RuntimeException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Throwable;

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
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $path = $request->input('path', '');

        try {
            $finder = new Finder();
            $finder->depth(0)
                ->ignoreDotFiles(false)
                ->ignoreUnreadableDirs()
                ->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
                    $strCaseCmp = strcasecmp($a->getType(), $b->getType());
                    if ($strCaseCmp === 0) {
                        return strcasecmp($a->getFilename(), $b->getFilename());
                    }
                    return $strCaseCmp;
                })
                ->in($this->base . $path);
            $data = [];
            if ($finder->hasResults()) {
                $service = new FileService();
                foreach ($finder as $file) {
                    $filepath = $file->getPath();
                    $relativePath = str_replace($this->base, '', $filepath);
                    $name = $file->getFilename();
                    $type = $file->getType();
                    try {
                        $size = $file->getSize();
                        $aTime = $file->getATime();
                        $cTime = $file->getCTime();
                        $mTime = $file->getMTime();
                        $perms = $file->getPerms();
                    } catch (RuntimeException) {
                        $size = '-';
                        $aTime = '-';
                        $cTime = '-';
                        $mTime = '-';
                        $perms = '-';
                    }
                    $data[] = [
                        'real_path'     => $file->getPath(), // 绝对路径
                        'path'          => $relativePath . '/' . $name,
                        'relative_path' => $relativePath, // 相对路径
                        'name'          => $file->getFilename(), // 文件名
                        'type'          => $type,
                        'type_text'     => FileTypeEnum::tryFrom($type)?->text() ?? '-',
                        'size'          => $size,
                        'size_text'     => $service->getByteSize($size),
                        'perms'         => $perms,
                        'a_time'        => Carbon::createFromTimestamp($aTime)->toDateTimeString(), // 上次访问时间
                        'c_time'        => Carbon::createFromTimestamp($cTime)->toDateTimeString(), // 创建时间
                        'm_time'        => Carbon::createFromTimestamp($mTime)->toDateTimeString(), // 上次修改时间
                    ];
                }
            }
        } catch (DirectoryNotFoundException) {
            $data = [];
        }

        // 面包屑
        $prev = '';
        $locations = [
            [
                'url'   => '/',
                'name'  => $this->baseName
            ]
        ];
        $relativePaths = explode('/', trim($path, '/'));
        foreach (array_filter($relativePaths) as $relativePath) {
            $item = [
                'url'   => $prev . '/' . $relativePath,
                'name'  => $relativePath
            ];
            $locations[] = $item;

            if (!empty($relativePath)) {
                $prev = $item['url'];
            }
        }

        return response()->view($this->viewPrefix . 'index', [
            'locations' => $locations,
            'data'  => $data
        ]);
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
            $service = new FileService();

            foreach ($finder as $file) {
                $filepath = $file->getPath();
                $relativePath = str_replace($this->base, '', $filepath);
                $name = $file->getFilename();
                $type = $file->getType();
                $size = $file->getSize();
                $data = [
                    'real_path'     => $file->getPath(), // 绝对路径
                    'path'          => $relativePath . '/' . $name,
                    'relative_path' => $relativePath, // 相对路径
                    'name'          => $file->getFilename(), // 文件名
                    'type'          => $type,
                    'type_text'     => FileTypeEnum::tryFrom($type)->text() ?? '',
                    'size'          => $file->getSize(),
                    'size_text'     => $service->getByteSize($size),
                    'a_time'        => Carbon::createFromTimestamp($file->getATime())->toDateTimeString(), // 上次访问时间
                    'c_time'        => Carbon::createFromTimestamp($file->getCTime())->toDateTimeString(), // 创建时间
                    'm_time'        => Carbon::createFromTimestamp($file->getMTime())->toDateTimeString(), // 上次修改时间
                    'contents'      => $file->getContents() // 文件内容
                ];
                break;
            }
        } catch (DirectoryNotFoundException) {
            $data = [];
        }

        return response()->json($data ?? []);
    }
}
