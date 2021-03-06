<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\StyleCI\Events\Analysis;

use StyleCI\StyleCI\Models\Analysis;

/**
 * This is the analysis has started event class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class AnalysisHasStartedEvent implements AnalysisEventInterface
{
    /**
     * The analysis object.
     *
     * @var \StyleCI\StyleCI\Models\Analysis
     */
    public $analysis;

    /**
     * Create a new analysis has started event instance.
     *
     * @param \StyleCI\StyleCI\Models\Analysis $analysis
     *
     * @return void
     */
    public function __construct(Analysis $analysis)
    {
        $this->analysis = $analysis;
    }
}
