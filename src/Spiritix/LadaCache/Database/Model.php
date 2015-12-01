<?php
/**
 * This file is part of the spiritix/lada-cache package.
 *
 * @copyright Copyright (c) Matthias Isler <mi@matthias-isler.ch>
 * @license   MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiritix\LadaCache\Database;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Spiritix\LadaCache\Reflector\Model as ModelReflector;

/**
 * Overrides Laravel's model class.
 *
 * @package Spiritix\LadaCache\Database
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
class Model extends EloquentModel
{
    /**
     * The "booting" method of the model.
     *
     * We'll hook into all model events here for invalidating cache items.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        $manager = app()->make('LadaCache');

        static::created(function($model) use($manager) {
            $cache = $manager->resolve(new ModelReflector($model));
            $cache->invalidate();
        });

        static::updated(function($model) use($manager) {
            $cache = $manager->resolve(new ModelReflector($model));
            $cache->invalidate();
        });

        static::deleted(function($model) use($manager) {
            $cache = $manager->resolve(new ModelReflector($model));
            $cache->invalidate();
        });

        static::saved(function($model) use($manager) {
            $cache = $manager->resolve(new ModelReflector($model));
            $cache->invalidate();
        });
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        return new QueryBuilder($conn, $grammar, $conn->getPostProcessor());
    }
}