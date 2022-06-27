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
 * Reads character specific content of the selected tab.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Character/Profile/TabMenu
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackSetup, ResponseData } from "WoltLabSuite/Core/Ajax/Data";
import * as Core from "WoltLabSuite/Core/Core";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import TabMenuSimple from "WoltLabSuite/Core/Ui/TabMenu/Simple";

class TabMenu extends TabMenuSimple {
    protected readonly characterID: number;
    protected readonly contents = new Map<string, boolean>();

    constructor(container: HTMLElement, characterID: number) {
        super(container);

        this.characterID = characterID;
        this.rebuild();

        const activeMenuItem = container.dataset.active;

        this.getContainers().forEach((container: HTMLElement) => {
            const containerID = container.dataset.name!;

            if (activeMenuItem === containerID) {
                this.contents.set(containerID, true);
            } else {
                this.contents.set(containerID, false);
            }
        });

        const activeTab = this.getActiveTab();
        const activeTabName = activeTab.dataset.name;

        if (activeMenuItem !== activeTabName) {
            this.select(null, activeTab);
        }
    }

    select(name: number | string | null, tab?: HTMLLIElement, disableEvent?: boolean): void {
        super.select(name, tab, disableEvent);

        const container = this.getContainers().get(tab!.dataset.name!);
        const containerID = container?.dataset.name;

        const hasContent = this.contents.get(containerID!);
        if (!hasContent) {
            Ajax.api(this, {
                actionName: "getContent",
                parameters: {
                    characterID: this.characterID,
                    containerID: containerID,
                    menuItem: container?.dataset.menuItem
                }
            });
        }
    }

    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                className: "rp\\data\\character\\profile\\menu\\item\\CharacterProfileMenuItemAction"
            }
        };
    }
    
    _ajaxSuccess(data: AjaxResponse): void {
        const containerID = data.returnValues.containerID;
        
        this.contents.set(containerID, true);
        
        DomUtil.insertHtml(data.returnValues.template!, document.getElementById(containerID)!, 'append');
    }
}

Core.enableLegacyInheritance(TabMenu);

export = TabMenu;

interface AjaxResponse extends ResponseData {
    returnValues: {
        template?: string;
        containerID: string;
    };
}