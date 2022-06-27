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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Ui/TabMenu/Simple"], function (require, exports, tslib_1, Ajax, Core, Util_1, Simple_1) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    Util_1 = tslib_1.__importDefault(Util_1);
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class TabMenu extends Simple_1.default {
        constructor(container, characterID) {
            super(container);
            this.contents = new Map();
            this.characterID = characterID;
            this.rebuild();
            const activeMenuItem = container.dataset.active;
            this.getContainers().forEach((container) => {
                const containerID = container.dataset.name;
                if (activeMenuItem === containerID) {
                    this.contents.set(containerID, true);
                }
                else {
                    this.contents.set(containerID, false);
                }
            });
            const activeTab = this.getActiveTab();
            const activeTabName = activeTab.dataset.name;
            if (activeMenuItem !== activeTabName) {
                this.select(null, activeTab);
            }
        }
        select(name, tab, disableEvent) {
            super.select(name, tab, disableEvent);
            const container = this.getContainers().get(tab.dataset.name);
            const containerID = container === null || container === void 0 ? void 0 : container.dataset.name;
            const hasContent = this.contents.get(containerID);
            if (!hasContent) {
                Ajax.api(this, {
                    actionName: "getContent",
                    parameters: {
                        characterID: this.characterID,
                        containerID: containerID,
                        menuItem: container === null || container === void 0 ? void 0 : container.dataset.menuItem
                    }
                });
            }
        }
        _ajaxSetup() {
            return {
                data: {
                    className: "rp\\data\\character\\profile\\menu\\item\\CharacterProfileMenuItemAction"
                }
            };
        }
        _ajaxSuccess(data) {
            const containerID = data.returnValues.containerID;
            this.contents.set(containerID, true);
            Util_1.default.insertHtml(data.returnValues.template, document.getElementById(containerID), 'append');
        }
    }
    Core.enableLegacyInheritance(TabMenu);
    return TabMenu;
});
