<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace YouduPhp\Youdu\Kernel\Media;

use YouduPhp\Youdu\Kernel\Exception\LogicException;
use YouduPhp\Youdu\Kernel\HttpClient\AbstractClient;

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

        // 加密文件
        $tmpFile = $this->config->getTmpPath() . '/' . uniqid('youdu_');
        $packedContents = $this->fileGetContents($file);

        try {
            // 保存加密文件
            if (file_put_contents($tmpFile, $packedContents) === false) {
                throw new LogicException('Create tmpfile failed', 1);
            }

            $parameters = [
                'type' => $fileType ?? 'file',
                'name' => basename($file),
            ];

            // 开始上传
            return $this->httpUpload('/cgi/media/upload', $tmpFile, $parameters)->throw()->json('mediaId');
        } finally {
            if (is_file($tmpFile)) {
                unlink($tmpFile);
            }
        }
    }

    /**
     * 下载文件.
     */
    public function download(string $mediaId, string $savePath): bool
    {
        $parameters = ['mediaId' => $mediaId];
        $response = $this->httpPostJson('/cgi/media/get', $parameters);

        $fileInfo = $this->packer->unpack($response->getHeaderLine('Encrypt'));
        $fileInfo = json_decode($fileInfo, true, 512, JSON_THROW_ON_ERROR);
        $fileContent = $response->body(true);

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
        $parameters = ['mediaId' => $mediaId];

        return $this->httpPostJson('/cgi/media/search', $parameters)->throw()->json();
    }
}
