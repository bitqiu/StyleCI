<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three LTD <support@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\StyleCI\Presenters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use McCool\LaravelAutoPresenter\BasePresenter;
use McCool\LaravelAutoPresenter\Facades\AutoPresenter;

/**
 * This is the repo presenter class.
 *
 * @property int                    $id
 * @property int                    $user_id
 * @property UserPresenter          $user
 * @property Collection             $analyses
 * @property AnalysisPresenter|null $last_analysis
 * @property string                 $name
 * @property string                 $default_branch
 * @property string                 $token
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author Joseph Cohen <joe@alt-three.com>
 */
class RepoPresenter extends BasePresenter implements Arrayable
{
    /**
     * Get the last analysis.
     *
     * This includes pending analyses too.
     *
     * @return \StyleCI\StyleCI\Presenters\AnalysisPresenter|null
     */
    public function last_analysis()
    {
        if ($analysis = $this->wrappedObject->last_analysis) {
            return AutoPresenter::decorate($analysis);
        }
    }

    /**
     * Get the last analysis that's actually been completed.
     *
     * @return \StyleCI\StyleCI\Presenters\AnalysisPresenter|null
     */
    public function last_completed()
    {
        if ($analysis = $this->wrappedObject->last_completed) {
            return AutoPresenter::decorate($analysis);
        }
    }

    /**
     * Convert presented repo to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $analysis = $this->last_analysis();
        $completed = $this->last_completed();

        return [
            'id'             => $this->wrappedObject->id,
            'name'           => $this->wrappedObject->name,
            'last_analysis'  => $analysis ? $analysis->toArray() : null,
            'last_completed' => $completed ? $completed->toArray() : null,
            'link'           => route('repo_path', $this->wrappedObject->id),
        ];
    }
}
