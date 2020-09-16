<?php

declare(strict_types=1);

namespace EonX\EasyCore\Bridge;

interface BridgeConstantsInterface
{
    /**
     * @var string
     */
    public const PARAM_ELASTICSEARCH_HOST = 'easy_core.elasticsearch_host';

    /**
     * @var string
     */
    public const PARAM_TRIM_STRINGS_EXCEPT = 'easy_core.trim_strings.except';
}
