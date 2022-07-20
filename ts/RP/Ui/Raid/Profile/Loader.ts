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
 * Handles the raid list in the character profile.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Raid/Profile/Loader
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackSetup, ResponseData } from "WoltLabSuite/Core/Ajax/Data";
import * as Core from "WoltLabSuite/Core/Core";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as Language from "WoltLabSuite/Core/Language";

class RaidCharacterProfileLoader {
    protected readonly _characterID: number;
    protected readonly _container: HTMLElement;
    protected readonly _loadButton: HTMLButtonElement;
    protected readonly _noMoreEntries: HTMLElement;
    protected readonly _options: AjaxParameters;

    /**
     * Initializes a new RaidCharacterProfileLoader object.
     */
    constructor(characterID: number) {
        this._container = document.getElementById("raidList")!;
        this._characterID = characterID;

        this._options = {
            parameters: {},
        };

        if (!this._characterID) {
            throw new Error("[Daries/RP/Raid/Character/Profile/Loader] Invalid parameter 'characterID' given.");
        }

        const loadButtonList = document.createElement("li");
        loadButtonList.className = "raidListMore showMore";
        this._noMoreEntries = document.createElement("small");
        this._noMoreEntries.innerHTML = Language.get("rp.character.raid.noMoreEntries");
        this._noMoreEntries.style.display = "none";
        loadButtonList.appendChild(this._noMoreEntries);

        this._loadButton = document.createElement("button");
        this._loadButton.className = "small";
        this._loadButton.innerHTML = Language.get("rp.character.raid.more");
        this._loadButton.addEventListener("click", () => this._loadRaids());
        this._loadButton.style.display = "none";
        loadButtonList.appendChild(this._loadButton);
        this._container.appendChild(loadButtonList);

        if (document.querySelectorAll("#raidList > li").length === 1) {
            this._noMoreEntries.style.display = "";
        } else {
            this._loadButton.style.display = "";
        }
    }

    /**
     * Load a list of raids.
     */
    protected _loadRaids(): void {
        this._options.parameters.characterID = this._characterID;
        this._options.parameters.lastRaidTime = ~~this._container.dataset.lastRaidTime!;
        Ajax.api(this, {
            parameters: this._options.parameters
        });
    }

    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                actionName: "load",
                className: "\\rp\\data\\raid\\RaidAction",
            },
        };
    }

    _ajaxSuccess(data: AjaxResponse): void {
        if (data.returnValues.template) {
            document
                .querySelector("#raidList > li:last-child")!
                .insertAdjacentHTML("beforebegin", data.returnValues.template);

            this._container.dataset.lastRaidTime = data.returnValues.lastRaidTime.toString();
            
            DomUtil.hide(this._noMoreEntries);
            DomUtil.show(this._loadButton);
        } else {
            DomUtil.show(this._noMoreEntries);
            DomUtil.hide(this._loadButton);
        }
    }
}

Core.enableLegacyInheritance(RaidCharacterProfileLoader);

export = RaidCharacterProfileLoader;

interface AjaxParameters {
    parameters: {
        [key: string]: number | string;
    };
}

interface AjaxResponse extends ResponseData {
    returnValues: {
        template?: string;
        lastRaidTime: number;
    };
}