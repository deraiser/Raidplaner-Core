<?php
namespace rp\system\form\builder\field\raid;

use rp\data\character\Character;
use rp\data\character\CharacterList;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\data\IStorableObject;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\TDefaultIdFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\IFormDocument;
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
 * @author      Marco Daries
 * @package     Daries\RP\System\Form\Builder\Field\Raid
 */

class RaidItemsFormField extends AbstractFormField
{

    use TDefaultIdFormField;

    /**
     * character objects
     * @var Character[]
     */
    protected ?array $characters = null;

    /**
     * @inheritDoc
     */
    protected $templateApplication = 'rp';

    /**
     * @inheritDoc
     */
    protected $templateName = '__raidItemsFormField';

    /**
     * @inheritDoc
     */
    protected $value = [];

    public function getCharacters(): array
    {
        if ($this->characters === null) {
            $this->characters = [];

            $list = new CharacterList();
            $list->getConditionBuilder()->add('isDisabled = ?', [0]);
            $list->sqlOrderBy = 'characterName ASC';
            $list->readObjects();
            foreach ($list as $character) {
                $this->characters[$character->characterID] = $character;
            }
        }

        return $this->characters;
    }

    /**
     * @inheritDoc
     */
    protected static function getDefaultId(): string
    {
        return 'raidItems';
    }

    /**
     * Returns all point accounts.
     */
    public function getPointAccounts(): array
    {
        return PointAccountCache::getInstance()->getPointAccounts();
    }

    /**
     * @inheritDoc
     */
    public function hasSaveValue(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function populate(): self
    {
        parent::populate();

        $this->getDocument()->getDataHandler()->addProcessor(new CustomFormDataProcessor(
                'raidItems',
                function (IFormDocument $document, array $parameters) {
                    if ($this->checkDependencies() && $this->getValue() !== null && !empty($this->getValue())) {
                        $parameters[$this->getObjectProperty()] = $this->getValue();
                    }

                    return $parameters;
                }
        ));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readValue(): void
    {
        if ($this->getDocument()->hasRequestData($this->getPrefixedId()) && \is_array($this->getDocument()->getRequestData($this->getPrefixedId()))) {
            $this->value = $this->getDocument()->getRequestData($this->getPrefixedId());
        } else {
            $this->value = [];
        }
    }

    /**
     * @inheritDoc
     */
    public function updatedObject(array $data, IStorableObject $object, $loadValues = true): self
    {
        if ($loadValues) {
            $sql = "SELECT      item_to_raid.characterID, item_to_raid.itemID, item.itemName, item_to_raid.pointAccountID, item_to_raid.points
                    FROM        rp" . WCF_N . "_item_to_raid item_to_raid
                    LEFT JOIN   rp" . WCF_N . "_item item
                    ON          (item_to_raid.itemID = item.itemID)
                    WHERE       item_to_raid.raidID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$object->getObjectID()]);

            $items = [];
            while ($row = $statement->fetchArray()) {
                $character = CharacterRuntimeCache::getInstance()->getObject($row['characterID']);
                $pointAccount = PointAccountCache::getInstance()->getPointAccountByID($row['pointAccountID']);

                $items[] = [
                    'characterID' => $character->characterID,
                    'characterName' => $character->getTitle(),
                    'itemID' => $row['itemID'],
                    'itemName' => $row['itemName'],
                    'pointAccountID' => $pointAccount->pointAccountID,
                    'pointAccountName' => $pointAccount->getTitle(),
                    'points' => $row['points']
                ];
            }

            $this->value($items);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        if ($this->isRequired() && ($this->getValue() === null || empty($this->getValue()))) {
            $this->addValidationError(new FormFieldValidationError('empty'));
        } else {
            $items = [];
            foreach ($this->getValue() as $item) {
                if (!\is_array($item)) continue;
                foreach (['characterID', 'itemID', 'pointAccountID', 'points'] as $key) {
                    if (!\array_key_exists($key, $item)) continue;
                }
                $items[] = $item;
            }

            $this->value($items);
        }

        parent::validate();
    }

    /**
     * @inheritDoc
     */
    public function value($value): self
    {
        if (!\is_array($value)) $value = [];
        return parent::value($value);
    }
}
