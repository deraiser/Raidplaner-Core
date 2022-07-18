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
 * Manages the raid items entered form field.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Form/Builder/Field/Raid/Items
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import * as Core from "WoltLabSuite/Core/Core";
import DomChangeListener from "WoltLabSuite/Core/Dom/Change/Listener";
import * as DomTraverse from "WoltLabSuite/Core/Dom/Traverse";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as Language from "WoltLabSuite/Core/Language";
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";
import { ItemData } from "./Data";

class RaidItems<TItemData extends ItemData = ItemData> {
    protected readonly addButton: HTMLAnchorElement;
    protected readonly form: HTMLFormElement;
    protected readonly formFieldId: string;
    protected readonly itemCharacter: HTMLSelectElement;
    protected readonly itemList: HTMLUListElement;
    protected readonly itemName: HTMLInputElement;
    protected readonly itemPointAccount: HTMLSelectElement;
    protected readonly itemPoints: HTMLInputElement;

    protected static readonly pointsRegExp = new RegExp(
        /^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$/
    );

    constructor(
        formFieldId: string,
        existingItems: TItemData[],
    ) {
        this.formFieldId = formFieldId;

        this.itemList = document.getElementById(`${this.formFieldId}_itemList`) as HTMLUListElement;
        if (this.itemList === null) {
            throw new Error(`Cannot find item list for items field with id '${this.formFieldId}'.`);
        }

        this.itemName = document.getElementById(`${this.formFieldId}_itemName`) as HTMLInputElement;
        if (this.itemName === null) {
            throw new Error(`Cannot find item name for items field with id '${this.formFieldId}'.`);
        }

        this.itemPointAccount = document.getElementById(`${this.formFieldId}_pointAccount`) as HTMLSelectElement;
        if (this.itemPointAccount === null) {
            throw new Error(`Cannot find item point account for items field with id '${this.formFieldId}'.`);
        }

        this.itemCharacter = document.getElementById(`${this.formFieldId}_character`) as HTMLSelectElement;
        if (this.itemCharacter === null) {
            throw new Error(`Cannot find item character for items field with id '${this.formFieldId}'.`);
        }

        this.itemPoints = document.getElementById(`${this.formFieldId}_points`) as HTMLInputElement;
        if (this.itemPoints === null) {
            throw new Error(`Cannot find item points for items field with id '${this.formFieldId}'.`);
        }

        this.addButton = document.getElementById(`${this.formFieldId}_addButton`) as HTMLAnchorElement;
        if (this.addButton === null) {
            throw new Error(`Cannot find add button for items field with id '${this.formFieldId}'.`);
        }
        this.addButton.addEventListener("click", (ev) => this.addItem(ev));

        this.form = this.itemList.closest("form")!;
        if (this.form === null) {
            throw new Error(`Cannot find form element for items field with id '${this.formFieldId}'.`);
        }
        this.form.addEventListener("submit", () => this.submit());

        existingItems.forEach((data) => this.addItemByData(data));

        DomChangeListener.trigger();
    }

    /**
      * Adds a set of item.
      *
      * If the item data is invalid, an error message is shown and no item set is added.
      */
    protected addItem(event: Event): void {
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

        DomChangeListener.trigger();
    }

    /**
      * Adds a item to the item list using the given item data.
      */
    protected addItemByData(itemData: TItemData): void {
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
    protected emptyInput(): void {
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
    protected removeItem(event: Event): void {
        const item = (event.currentTarget as HTMLElement).closest("li")!;

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
    protected submit(): void {
        DomTraverse.childrenByTag(this.itemList, "LI").forEach((listItem, index) => {
            const itemID = document.createElement("input");
            itemID.type = "hidden";
            itemID.name = `${this.formFieldId}[${index}][itemID]`;
            itemID.value = listItem.dataset.itemId!;
            this.form.appendChild(itemID);

            const itemName = document.createElement("input");
            itemName.type = "hidden";
            itemName.name = `${this.formFieldId}[${index}][itemName]`;
            itemName.value = listItem.dataset.itemName!;
            this.form.appendChild(itemName);

            const itemPointAccountID = document.createElement("input");
            itemPointAccountID.type = "hidden";
            itemPointAccountID.name = `${this.formFieldId}[${index}][pointAccountID]`;
            itemPointAccountID.value = listItem.dataset.itemPointAccountId!;
            this.form.appendChild(itemPointAccountID);

            const itemPointAccountName = document.createElement("input");
            itemPointAccountName.type = "hidden";
            itemPointAccountName.name = `${this.formFieldId}[${index}][pointAccountName]`;
            itemPointAccountName.value = listItem.dataset.itemPointAccountName!;
            this.form.appendChild(itemPointAccountName);

            const itemCharacterID = document.createElement("input");
            itemCharacterID.type = "hidden";
            itemCharacterID.name = `${this.formFieldId}[${index}][characterID]`;
            itemCharacterID.value = listItem.dataset.itemCharacterId!;
            this.form.appendChild(itemCharacterID);

            const itemCharacterName = document.createElement("input");
            itemCharacterName.type = "hidden";
            itemCharacterName.name = `${this.formFieldId}[${index}][characterName]`;
            itemCharacterName.value = listItem.dataset.itemCharacterName!;
            this.form.appendChild(itemCharacterName);

            const itemPoints = document.createElement("input");
            itemPoints.type = "hidden";
            itemPoints.name = `${this.formFieldId}[${index}][points]`;
            itemPoints.value = listItem.dataset.itemPoints!;
            this.form.appendChild(itemPoints);
        });
    }

    /**
      * Returns `true` if the currently entered item data is valid.
      * Otherwise `false` is returned and relevant error messages are
      * shown.
      */
    protected validateInput(): boolean {
        return this.validateItemName() && this.validateItemPoints();
    }

    /**
      * Returns `true` if the currently entered item name is
      * valid. Otherwise `false` is returned and an error message is
      * shown.
      */
    protected validateItemName(): boolean {
        const itemName = this.itemName.value;

        if (itemName === "") {
            DomUtil.innerError(this.itemName, Language.get("wcf.global.form.error.empty"));

            return false;
        }

        // check if item has already been added
        const duplicate = DomTraverse.childrenByTag(this.itemList, "LI").some(
            (listItem) => (
                (listItem.dataset.itemName === this.itemName.value) &&
                (listItem.dataset.itemCharacterId === this.itemCharacter.value)
            )
        );

        if (duplicate) {
            DomUtil.innerError(
                this.itemName,
                Language.get("rp.raid.item.error.duplicate"),
            );

            return false;
        }

        // remove outdated errors
        DomUtil.innerError(this.itemName, "");

        return true;
    }

    /**
      * Returns `true` if the currently entered item points is
      * valid. Otherwise `false` is returned and an error message is
      * shown.
      */
    protected validateItemPoints(): boolean {
        const itemPoints = this.itemPoints.value;

        if (itemPoints === "") {
            DomUtil.innerError(this.itemPoints, Language.get("wcf.global.form.error.empty"));

            return false;
        }

        if (!RaidItems.pointsRegExp.test(itemPoints)) {
            DomUtil.innerError(this.itemPoints, Language.get("rp.raid.item.points.error.format"));

            return false;
        }

        // remove outdated errors
        DomUtil.innerError(this.itemPoints, "");

        return true;
    }
}

Core.enableLegacyInheritance(RaidItems);

export = RaidItems;