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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "./Action/DeleteAction", "./Action/DisableAction", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Dropdown/Simple"], function (require, exports, tslib_1, Core, DeleteAction_1, DisableAction_1, EventHandler, Language, Simple_1) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    DeleteAction_1 = tslib_1.__importDefault(DeleteAction_1);
    DisableAction_1 = tslib_1.__importDefault(DisableAction_1);
    EventHandler = tslib_1.__importStar(EventHandler);
    Language = tslib_1.__importStar(Language);
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class AcpUiCharacterEditor {
        /**
         * Initializes the edit dropdown for each character.
         */
        constructor() {
            document.querySelectorAll(".jsCharacterRow").forEach((characterRow) => this.initCharacter(characterRow));
            EventHandler.add("info.daries.rp.acp.character", "refresh", (data) => this.refreshCharacters(data));
        }
        /**
         * Initializes the edit dropdown for a character.
         */
        initCharacter(characterRow) {
            const characterId = ~~characterRow.dataset.objectId;
            const dropdownId = `characterListDropdown${characterId}`;
            const dropdownMenu = Simple_1.default.getDropdownMenu(dropdownId);
            if (dropdownMenu.childElementCount === 0) {
                const toggleButton = characterRow.querySelector(".dropdownToggle");
                toggleButton.classList.add("disabled");
                return;
            }
            const editLink = dropdownMenu.querySelector(".jsEditLink");
            if (editLink !== null) {
                const toggleButton = characterRow.querySelector(".dropdownToggle");
                toggleButton.addEventListener("dblclick", (event) => {
                    event.preventDefault();
                    editLink.click();
                });
            }
            const enableCharacter = dropdownMenu.querySelector(".jsEnable");
            if (enableCharacter !== null) {
                new DisableAction_1.default(enableCharacter, characterId, characterRow);
            }
            const deleteCharacter = dropdownMenu.querySelector(".jsDelete");
            if (deleteCharacter !== null) {
                new DeleteAction_1.default(deleteCharacter, characterId, characterRow);
            }
        }
        refreshCharacters(data) {
            document.querySelectorAll(".jsCharacterRow").forEach((characterRow) => {
                const characterId = ~~characterRow.dataset.objectId;
                if (data.characterIds.includes(characterId)) {
                    const characterStatusIcons = characterRow.querySelector(".characterStatusIcons");
                    const isDisabled = !Core.stringToBool(characterRow.dataset.enabled);
                    let iconIsDisabled = characterRow.querySelector(".jsCharacterDisabled");
                    if (isDisabled && iconIsDisabled === null) {
                        iconIsDisabled = document.createElement("span");
                        iconIsDisabled.className = "icon icon16 fa-power-off jsCharacterDisabled jsTooltip";
                        iconIsDisabled.title = Language.get("rp.acp.character.isDisabled");
                        characterStatusIcons.appendChild(iconIsDisabled);
                    }
                    else if (!isDisabled && iconIsDisabled !== null) {
                        iconIsDisabled.remove();
                    }
                }
            });
        }
    }
    return AcpUiCharacterEditor;
});
