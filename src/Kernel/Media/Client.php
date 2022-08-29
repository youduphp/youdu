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

use YouduPhp\Youdu\Kernel\Exception\Exception;
use YouduPhp\Youdu\Kernel\HttpClient\BaseClient;

class Client extends BaseClient
{
    /**
     * 上传文件.
     *
     * @param string $fileType image代表图片、file代表普通文件、voice代表语音、video代表视频
     */
    public function upload(string $file = '', string $fileType = 'file'): string
    {
        if (! in_array($fileType, ['file', 'voice', 'video', 'image'])) {
            throw new Exception('Unsupport file type ' . $fileType, 1);
        }

        if (preg_match('/^https?:\/\//i', $file)) { // 远程文件
            $contextOptions = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $originalContent = file_get_contents($file, false, $contextOptions);
        } else { // 本地文件
            $originalContent = file_get_contents($file);
        }

        // 加密文件
        $tmpFile = $this->config->getTmpPath() . '/' . uniqid('youdu_');

        try {
            $encryptedFile = $this->packer->pack($originalContent);
            $encryptedMsg = $this->packer->pack(json_encode([
                'type' => $fileType ?? 'file',
                'name' => basename($file),
            ], JSON_THROW_ON_ERROR));

            // 保存加密文件
            if (file_put_contents($tmpFile, $encryptedFile) === false) {
                throw new Exception('Create tmpfile failed', 1);
            }

            // 封装上传参数
            $parameters = [
                'file' => $this->makeUploadFile(realpath($tmpFile)),
                'encrypt' => $encryptedMsg,
                'buin' => $this->config->getBuin(),
                'appId' => $this->config->getAppId(),
            ];

            // 开始上传
            return $this->httpUpload('/cgi/media/upload', $parameters)->throw()->json('mediaId');
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

        $resp = $this->httpPost('/cgi/media/get', $parameters)->throw();
        $fileInfo = $this->packer->unpack($resp->getHeaderLine('Encrypt'));
        $fileInfo = json_decode($fileInfo, true, 512, JSON_THROW_ON_ERROR);
        $fileContent = $this->packer->unpack($resp->getBody()->getContents());

        $saveAs = rtrim($savePath, '/') . '/' . $fileInfo['name'];
        $saved = file_put_contents($saveAs, $fileContent);

        if (! $saved) {
            throw new Exception(sprintf('Save %s failed', $saveAs), 1);
        }

        return true;
    }

    /**
     * 素材文件信息.
     */
    public function info(string $mediaId = ''): array
    {
        $parameters = [
            'mediaId' => $mediaId,
        ];

        return $this->httpPost('/cgi/media/search', $parameters)->throw()->json();
    }
}
