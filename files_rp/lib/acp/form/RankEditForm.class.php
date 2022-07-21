<?php

namespace rp\acp\form;

use rp\data\rank\Rank;
use wcf\system\exception\IllegalLinkException;

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
 * Shows the rank edit form.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Form
 */
class RankEditForm extends RankAddForm
{
    /**
     * @inheritDoc
     */
    public string $activeMenuItem = 'rp.acp.menu.link.rank.list';

    /**
     * @inheritDoc
     */
    public string $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new Rank($_REQUEST['id']);
            if (!$this->formObject->rankID) {
                throw new IllegalLinkException();
            }
        }
    }
}
