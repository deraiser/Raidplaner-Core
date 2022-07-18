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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Dom/Change/Listener", "WoltLabSuite/Core/Dom/Traverse", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Confirmation"], function (require, exports, tslib_1, Ajax, Core, Listener_1, DomTraverse, Util_1, Language, UiConfirmation) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    Listener_1 = tslib_1.__importDefault(Listener_1);
    DomTraverse = tslib_1.__importStar(DomTraverse);
    Util_1 = tslib_1.__importDefault(Util_1);
    Language = tslib_1.__importStar(Language);
    UiConfirmation = tslib_1.__importStar(UiConfirmation);
    class RaidItems {
        constructor(formFieldId, existingItems) {
            this.formFieldId = formFieldId;
            this.itemList = document.getElementById(`${this.formFieldId}_itemList`);
            if (this.itemList === null) {
                throw new Error(`Cannot find item list for items field with id '${this.formFieldId}'.`);
            }
            this.itemName = document.getElementById(`${this.formFieldId}_itemName`);
            if (this.itemName === null) {
                throw new Error(`Cannot find item name for items field with id '${this.formFieldId}'.`);
            }
            this.itemPointAccount = document.getElementById(`${this.formFieldId}_pointAccount`);
            if (this.itemPointAccount === null) {
                throw new Error(`Cannot find item point account for items field with id '${this.formFieldId}'.`);
            }
            this.itemCharacter = document.getElementById(`${this.formFieldId}_character`);
            if (this.itemCharacter === null) {
                throw new Error(`Cannot find item character for items field with id '${this.formFieldId}'.`);
            }
            this.itemPoints = document.getElementById(`${this.formFieldId}_points`);
            if (this.itemPoints === null) {
                throw new Error(`Cannot find item points for items field with id '${this.formFieldId}'.`);
            }
            this.addButton = document.getElementById(`${this.formFieldId}_addButton`);
            if (this.addButton === null) {
                throw new Error(`Cannot find add button for items field with id '${this.formFieldId}'.`);
            }
            this.addButton.addEventListener("click", (ev) => this.addItem(ev));
            this.form = this.itemList.closest("form");
            if (this.form === null) {
                throw new Error(`Cannot find form element for items field with id '${this.formFieldId}'.`);
            }
            this.form.addEventListener("submit", () => this.submit());
            existingItems.forEach((data) => this.addItemByData(data));
            Listener_1.default.trigger();
        }
        /**
          * Adds a set of item.
          *
          * If the item data is invalid, an error message is shown and no item set is added.
          */
        addItem(event) {
            event.preventDefault();
            event.stopPropagation();
            // validate data
            if (!this.validateInput()) {
                return;
            }
            Ajax.apiOnce({
                data: {
                    actionName: "searchItem",
                    className: "\\rp\\data\\raid\\RaidAction",
                    parameters: {
                        itemName: this.itemName.value,
                        pointAccountID: this.itemPointAccount.value,
                        characterID: this.itemCharacter.value,
                        points: this.itemPoints.value
                    }
                },
                success: (data) => this.addItemByData(data.returnValues)
            });
            // empty fields
            this.emptyInput();
            this.itemName.focus();
            Listener_1.default.trigger();
        }
        /**
          * Adds a item to the item list using the given item data.
          */
        addItemByData(itemData) {
            // add item to list
            const listItem = document.createElement("li");
            listItem.dataset.itemId = itemData.itemID.toString();
            listItem.dataset.itemName = itemData.itemName;
            listItem.dataset.itemPointAccountId = itemData.pointAccountID.toString();
            listItem.dataset.itemPointAccountName = itemData.pointAccountName;
            listItem.dataset.itemCharacterId = itemData.characterID.toString();
            listItem.dataset.itemCharacterName = itemData.characterName;
            listItem.dataset.itemPoints = itemData.points.toString();
            listItem.innerHTML = ` ${Language.get("rp.raid.item.form.field", {
                itemName: itemData.itemName,
                pointAccountName: itemData.pointAccountName,
                characterName: itemData.characterName,
                points: itemData.points,
            })}`;
            // add delete button
            const deleteButton = document.createElement("span");
            deleteButton.className = "icon icon16 fa-times pointer jsTooltip";
            deleteButton.title = Language.get("wcf.global.button.delete");
            deleteButton.addEventListener("click", (ev) => this.removeItem(ev));
            listItem.insertAdjacentElement("afterbegin", deleteButton);
            this.itemList.appendChild(listItem);
        }
        /**
          * Empties the input fields.
          */
        emptyInput() {
            this.itemName.value = "";
            this.itemPointAccount.options.selectedIndex = 0;
            this.itemCharacter.options.selectedIndex = 0;
            this.itemPoints.value = "";
        }
        /**
          * Removes a item by clicking on its delete button.
          *
          * @param  {Event}     event   delete button click event
          */
        removeItem(event) {
            const item = event.currentTarget.closest("li");
            UiConfirmation.show({
                confirm: () => {
                    item.remove();
                },
                message: Language.get("rp.raid.items.delete.confirmMessages"),
            });
        }
        /**
          * Adds all necessary (hidden) form fields to the form when
          * submitting the form.
          */
        submit() {
            DomTraverse.childrenByTag(this.itemList, "LI").forEach((listItem, index) => {
                const itemID = document.createElement("input");
                itemID.type = "hidden";
                itemID.name = `${this.formFieldId}[${index}][itemID]`;
                itemID.value = listItem.dataset.itemId;
                this.form.appendChild(itemID);
                const itemName = document.createElement("input");
                itemName.type = "hidden";
                itemName.name = `${this.formFieldId}[${index}][itemName]`;
                itemName.value = listItem.dataset.itemName;
                this.form.appendChild(itemName);
                const itemPointAccountID = document.createElement("input");
                itemPointAccountID.type = "hidden";
                itemPointAccountID.name = `${this.formFieldId}[${index}][pointAccountID]`;
                itemPointAccountID.value = listItem.dataset.itemPointAccountId;
                this.form.appendChild(itemPointAccountID);
                const itemPointAccountName = document.createElement("input");
                itemPointAccountName.type = "hidden";
                itemPointAccountName.name = `${this.formFieldId}[${index}][pointAccountName]`;
                itemPointAccountName.value = listItem.dataset.itemPointAccountName;
                this.form.appendChild(itemPointAccountName);
                const itemCharacterID = document.createElement("input");
                itemCharacterID.type = "hidden";
                itemCharacterID.name = `${this.formFieldId}[${index}][characterID]`;
                itemCharacterID.value = listItem.dataset.itemCharacterId;
                this.form.appendChild(itemCharacterID);
                const itemCharacterName = document.createElement("input");
                itemCharacterName.type = "hidden";
                itemCharacterName.name = `${this.formFieldId}[${index}][characterName]`;
                itemCharacterName.value = listItem.dataset.itemCharacterName;
                this.form.appendChild(itemCharacterName);
                const itemPoints = document.createElement("input");
                itemPoints.type = "hidden";
                itemPoints.name = `${this.formFieldId}[${index}][points]`;
                itemPoints.value = listItem.dataset.itemPoints;
                this.form.appendChild(itemPoints);
            });
        }
        /**
          * Returns `true` if the currently entered item data is valid.
          * Otherwise `false` is returned and relevant error messages are
          * shown.
          */
        validateInput() {
            return this.validateItemName() && this.validateItemPoints();
        }
        /**
          * Returns `true` if the currently entered item name is
          * valid. Otherwise `false` is returned and an error message is
          * shown.
          */
        validateItemName() {
            const itemName = this.itemName.value;
            if (itemName === "") {
                Util_1.default.innerError(this.itemName, Language.get("wcf.global.form.error.empty"));
                return false;
            }
            // check if item has already been added
            const duplicate = DomTraverse.childrenByTag(this.itemList, "LI").some((listItem) => ((listItem.dataset.itemName === this.itemName.value) &&
                (listItem.dataset.itemCharacterId === this.itemCharacter.value)));
            if (duplicate) {
                Util_1.default.innerError(this.itemName, Language.get("rp.raid.item.error.duplicate"));
                return false;
            }
            // remove outdated errors
            Util_1.default.innerError(this.itemName, "");
            return true;
        }
        /**
          * Returns `true` if the currently entered item points is
          * valid. Otherwise `false` is returned and an error message is
          * shown.
          */
        validateItemPoints() {
            const itemPoints = this.itemPoints.value;
            if (itemPoints === "") {
                Util_1.default.innerError(this.itemPoints, Language.get("wcf.global.form.error.empty"));
                return false;
            }
            if (!RaidItems.pointsRegExp.test(itemPoints)) {
                Util_1.default.innerError(this.itemPoints, Language.get("rp.raid.item.points.error.format"));
                return false;
            }
            // remove outdated errors
            Util_1.default.innerError(this.itemPoints, "");
            return true;
        }
    }
    RaidItems.pointsRegExp = new RegExp(/^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$/);
    Core.enableLegacyInheritance(RaidItems);
    return RaidItems;
});
