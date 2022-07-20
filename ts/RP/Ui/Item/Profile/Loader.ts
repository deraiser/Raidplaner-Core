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
 * Handles the item list in the character profile.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Item/Profile/Loader
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackSetup, ResponseData } from "WoltLabSuite/Core/Ajax/Data";
import * as Core from "WoltLabSuite/Core/Core";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as Language from "WoltLabSuite/Core/Language";

class ItemCharacterProfileLoader {
    protected readonly _characterID: number;
    protected readonly _container: HTMLElement;
    protected readonly _loadButton: HTMLButtonElement;
    protected readonly _noMoreEntries: HTMLElement;
    protected readonly _options: AjaxParameters;

    /**
     * Initializes a new ItemCharacterProfileLoader object.
     */
    constructor(characterID: number) {
        this._container = document.getElementById("itemList")!;
        this._characterID = characterID;

        this._options = {
            parameters: {},
        };

        if (!this._characterID) {
            throw new Error("[Daries/RP/Item/Character/Profile/Loader] Invalid parameter 'characterID' given.");
        }

        const loadButtonList = document.createElement("li");
        loadButtonList.className = "itemListMore showMore";
        this._noMoreEntries = document.createElement("small");
        this._noMoreEntries.innerHTML = Language.get("rp.character.item.noMoreEntries");
        this._noMoreEntries.style.display = "none";
        loadButtonList.appendChild(this._noMoreEntries);

        this._loadButton = document.createElement("button");
        this._loadButton.className = "small";
        this._loadButton.innerHTML = Language.get("rp.character.item.more");
        this._loadButton.addEventListener("click", () => this._loadItems());
        this._loadButton.style.display = "none";
        loadButtonList.appendChild(this._loadButton);
        this._container.after(loadButtonList);

        if (document.querySelectorAll("#itemList > li").length === 1) {
            this._noMoreEntries.style.display = "";
        } else {
            this._loadButton.style.display = "";
        }
    }

    /**
     * Load a list of items.
     */
    protected _loadItems(): void {
        this._options.parameters.characterID = this._characterID;
        this._options.parameters.lastItemOffset = ~~this._container.dataset.lastItemOffset!;
        Ajax.api(this, {
            parameters: this._options.parameters
        });
    }

    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                actionName: "load",
                className: "\\rp\\data\\item\\ItemAction",
            },
        };
    }

    _ajaxSuccess(data: AjaxResponse): void {
        if (data.returnValues.template) {
            document
                .querySelector("#itemList > li:last-child")!
                .insertAdjacentHTML("beforebegin", data.returnValues.template);

            this._container.dataset.lastItemOffset = data.returnValues.lastItemOffset.toString();
            
            DomUtil.hide(this._noMoreEntries);
            DomUtil.show(this._loadButton);
        } else {
            DomUtil.show(this._noMoreEntries);
            DomUtil.hide(this._loadButton);
        }
    }
}

Core.enableLegacyInheritance(ItemCharacterProfileLoader);

export = ItemCharacterProfileLoader;

interface AjaxParameters {
    parameters: {
        [key: string]: number | string;
    };
}

interface AjaxResponse extends ResponseData {
    returnValues: {
        template?: string;
        lastItemOffset: number;
    };
}