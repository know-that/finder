<?php

namespace KnowThat\Finder\Services;

use Carbon\Carbon;
use Countable;
use Generator;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;
use KnowThat\Finder\Constants\FileTypeConstant;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileService implements IteratorAggregate, Countable, JsonSerializable
{
    /**
     * 文件路径前缀
     * @var string
     */
    private $base;

    /**
     * 当前 data
     * @var Collection
     */
    public $data;

    public function __construct()
    {
        $base = config('know-that.finder.base');
        $this->base = $base === '/' ? '' : $base;
    }

    /**
     * 字节转换可读形式
     *
     * @param $filesize
     * @return string
     */
    public function getByteSize($filesize): string
    {
        if ($filesize >= 1073741824) {
            //转成GB
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            //转成MB
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            //转成KB
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            //不转换直接输出
            $filesize .= ' byte';
        }
        return $filesize;
    }

    /**
     * 获取文件数据（多个）
     *
     * @param Finder $finder
     * @return FileService
     */
    public function get(Finder $finder): self
    {
        $data = Collection::make();
        foreach ($finder as $file) {
            $data->push($this->getFileData($file));
        }
        $this->data = $data;

        return $this;
    }

    /**
     * 获取文件数据单个
     *
     * @param Finder $finder
     * @return $this
     */
    public function find(Finder $finder): self
    {
        $data = Collection::make();
        foreach ($finder as $file) {
            $data = $this->getFileData($file, true);
            break;
        }

        $this->data = $data;
        return $this;
    }

    /**
     * 生成内容明细
     * @return $this
     */
    public function generateContentItems(): self
    {
        $contents = $this->data->get('contents');

        if ($contents) {
            // 正则匹配
            preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/', $contents, $data);
            $contentItems = [];
            foreach ($this->yieldContents($data) as $item) {
                $contentItems[] = $item;
            }
            $this->data->put('content_items', $contentItems);
        }

        return $this;
    }

    /**
     * 获取文件数组信息
     *
     * @param SplFileInfo $file
     * @param bool $isContents
     * @return Collection
     */
    private function getFileData(SplFileInfo $file, bool $isContents = false): Collection
    {
        $filepath = $file->getPath();
        $relativePath = str_replace($this->base, '', $filepath);
        $name = $file->getFilename();
        $isWritable = $file->isWritable();
        $isReadable = $file->isReadable();
        $isExecutable = $file->isExecutable();
        try {
            $type = $file->getType();
            $size = $file->getSize();
            $aTime = $file->getATime();
            $cTime = $file->getCTime();
            $mTime = $file->getMTime();
        } catch (RuntimeException $e) {
            $type = '-';
            $size = '-';
            $aTime = '-';
            $cTime = '-';
            $mTime = '-';
        }
        $data =  [
            'real_path'             => $file->getPath(), // 绝对路径
            'path'                  => $relativePath . '/' . $name,
            'relative_path'         => $relativePath, // 相对路径
            'name'                  => $file->getFilename(), // 文件名
            'type'                  => $type,
            'type_text'             => FileTypeConstant::text($type) ?? '-',
            'size'                  => $size,
            'size_text'             => $this->getByteSize($size),
            'is_readable'           => (int) $isReadable,
            'is_readable_text'      => $isReadable ? '可读' : '不可读',
            'is_writable'           => (int) $isWritable,
            'is_writable_text'      => $isWritable ? '可写' : '不可写',
            'is_executable'         => (int) $isExecutable,
            'is_executable_text'    => $isExecutable ? '可执行' : '不可执行',
            'a_time'                => Carbon::createFromTimestamp($aTime)->toDateTimeString(), // 上次访问时间
            'c_time'                => Carbon::createFromTimestamp($cTime)->toDateTimeString(), // 创建时间
            'm_time'                => Carbon::createFromTimestamp($mTime)->toDateTimeString(), // 上次修改时间
        ];

        if ($isContents) {
            $data['contents'] = stripslashes($file->getContents());
        }

        return Collection::make((object) $data);
    }

    /**
     * contents 生成器
     *
     * @param array $data
     * @return Generator
     */
    private function yieldContents(array $data): Generator
    {
        foreach ($data as $item) {
            foreach ($item as $value) {
                $level = 'error';
                preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i', $value, $current);
                $current[2] = $level;
                if (!empty($current[4])) {
                    yield [
                        'date'      => $current[1],
                        'level'     => $current[2],
                        'type'      => $current[3],
                        'content'   => $current[0],
                    ];
                }
            }
        }
    }

    /**
     * @param $get
     * @return mixed
     */
    public function __get($get)
    {
        return $this->data->get($get);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->data->get($name) !== null;
    }

    /**
     * @return Collection
     */
    public function getIterator(): Collection
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    /**
     * 转json的时候序列化
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->data->toArray();
    }
}
