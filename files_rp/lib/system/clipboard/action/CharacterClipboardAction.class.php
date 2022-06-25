<?php

namespace rp\system\clipboard\action;

use rp\data\character\Character;
use rp\data\character\CharacterAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\clipboard\ClipboardEditorItem;
use wcf\system\WCF;

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
 * Prepares clipboard editor items for character objects.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Clipboard\Action
 */
class CharacterClipboardAction extends AbstractClipboardAction
{
    /**
     * @inheritDoc
     */
    protected $actionClassActions = [
        'delete',
    ];

    /**
     * @inheritDoc
     */
    protected $supportedActions = [
        'delete',
    ];

    /**
     * @inheritDoc
     */
    public function execute($objects, ClipboardAction $action): ?ClipboardEditorItem
    {
        $item = parent::execute($objects, $action);

        if ($item === null) {
            return null;
        }

        // handle actions
        switch ($action->actionName) {
            case 'delete':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.info.daries.rp.character.delete.confirmMessage',
                        [
                            'count' => $item->getCount(),
                        ]
                    )
                );
                break;
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): string
    {
        return CharacterAction::class;
    }

    /**
     * @inheritDoc
     */
    public function getTypeName(): string
    {
        return 'info.daries.rp.character';
    }

    /**
     * Returns the ids of the characters that can be deleted.
     *
     * @return	int[]
     */
    protected function validateDelete(): array
    {
        $objectIDs = [];

        /** @var Character $character */
        foreach ($this->objects as $character) {
            if ($character->canDelete()) {
                $objectIDs[] = $character->characterID;
            }
        }

        return $objectIDs;
    }
}
