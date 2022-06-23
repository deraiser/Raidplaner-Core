<?php

namespace rp\data\classification;

use rp\system\cache\builder\ClassificationCacheBuilder;
use wcf\system\SingletonFactory;

/*  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Manages the classification cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Classification
 */
class ClassificationCache extends SingletonFactory
{
    /**
     * cached classifications
     * @var Classification[]
     */
    protected array $cachedClassifications = [];

    /**
     * cached classification ids with classification identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * Returns the classification with the given classification id or `null` if no such classification exists.
     */
    public function getClassificationByID(int $classificationID): ?Classification
    {
        return $this->cachedClassifications[$classificationID] ?? null;
    }

    /**
     * Returns the classification with the given classification identifier or `null` if no such classification exists.
     */
    public function getClassificationByIdentifier(string $identifier): ?Classification
    {
        return $this->getClassificationByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all classifications.
     * 
     * @return  Classification[]
     */
    public function getClassifications(): array
    {
        return $this->cachedClassifications;
    }

    /**
     * Returns the classification with the given classification ids.
     * 
     * @return	Classification[]
     */
    public function getClassificationsByID(array $classificationIDs): array
    {
        $returnValues = [];

        foreach ($classificationIDs as $classificationID) {
            $returnValues[] = $this->getClassificationByID($classificationID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->cachedClassifications = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'classification');
        $this->cachedIdentifier = ClassificationCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'identifier');
    }
}
