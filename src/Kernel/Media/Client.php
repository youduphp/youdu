<?php

declare(strict_types=1);
/**
 * This file is part of youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\Media;

use YouduPhp\Youdu\Kernel\Exception\LogicException;
use YouduPhp\Youdu\Kernel\HttpClient\AbstractClient;
use YouduPhp\Youdu\Kernel\HttpClient\Response;

class Client extends AbstractClient
{
    /**
     * 上传文件.
     *
     * @param string $fileType image代表图片、file代表普通文件、voice代表语音、video代表视频
     */
    public function upload(string $file = '', string $fileType = 'file'): string
    {
        if (! in_array($fileType, ['file', 'voice', 'video', 'image'])) {
            throw new LogicException('Un-support file type ' . $fileType, 1);
        }

        $parameters = [
            'type' => $fileType ?? 'file',
            'name' => basename($file),
        ];

        // 开始上传
        return $this->httpUpload('/cgi/media/upload', $file, $parameters)->throw()->json('mediaId');
    }

    /**
     * 下载文件.
     */
    public function download(string $mediaId, string $savePath): bool
    {
        $parameters = ['mediaId' => $mediaId];
        $fileInfo = [];
        $fileContent = $this->httpPostJson('/cgi/media/get', $parameters)
            ->tap(function (Response $response) use (&$fileInfo) {
                $fileInfo = $this->packer->unpack($response->header('Encrypt'));
                $fileInfo = json_decode($fileInfo, true, 512, JSON_THROW_ON_ERROR);
            })
            ->body(true);

        $saveAs = rtrim($savePath, '/') . '/' . $fileInfo['name'];
        $saved = file_put_contents($saveAs, $fileContent);

        if (! $saved) {
            throw new LogicException(sprintf('Save %s failed', $saveAs), 1);
        }

        return true;
    }

    /**
     * 素材文件信息.
     */
    public function info(string $mediaId = ''): array
    {
        return $this->httpPostJson('/cgi/media/search', ['mediaId' => $mediaId])->throw()->json();
    }
}
