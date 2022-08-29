<?php

declare(strict_types=1);
/**
 * This file is part of youduphp/youdu.
 *
 * @link     https://github.com/youduphp/youdu
 * @document https://github.com/youduphp/youdu/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
beforeEach(function () {
    $this->config = makeConfig();
});

it('asserts config.api')->expect(fn () => $this->config->getApi())->toEqual(getenv('YOUDU_API'));
it('asserts config.buin')->expect(fn () => $this->config->getBuin())->toBeInt()->toEqual(getenv('YOUDU_BUIN'));
it('asserts config.app_id')->expect(fn () => $this->config->getAppId())->toEqual(getenv('YOUDU_DEFAULT_APP_ID'));
it('asserts config.aes_key')->expect(fn () => $this->config->getAesKey())->toEqual(getenv('YOUDU_DEFAULT_AES_KEY'));
